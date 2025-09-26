<?php

namespace App\Livewire;

use App\Models\StockItem;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockSaleManager extends Component
{
    // Inventory and Cart State
    public $stockItems;
    public $itemsToSell = [];
    public $totalAmount = 0;

    // Form Inputs
    public $selectedItemId;
    public $quantity = 1;
    public $customers;
    public $customerId; // Selected Customer ID (Mandatory for Invoice)

    protected function rules()
    {
        return [
            'selectedItemId' => 'required|exists:stock_items,id',
            'quantity' => 'required|integer|min:1',
            // CRUCIAL: Customer is required for credit sale/invoice generation
            'customerId' => 'required|exists:customers,id',
        ];
    }

    public function mount()
    {
        // Load only items that have stock
        $this->stockItems = StockItem::where('quantity', '>', 0)->get();
        // Load all customers for the dropdown
        $this->customers = Customer::all();
    }

    /**
     * Adds an item to the sale cart and validates available stock.
     */
    public function addItemToSale()
    {
        // Only validate item and quantity first
        $this->validate(['selectedItemId' => $this->rules()['selectedItemId'], 'quantity' => $this->rules()['quantity']]);

        $item = $this->stockItems->find($this->selectedItemId);

        // Critical: Check available stock
        if ($this->quantity > $item->quantity) {
            session()->flash('error', __('The requested quantity exceeds available stock.'));
            return;
        }

        // Add item to cart (allows adding the same item multiple times)
        $this->itemsToSell[] = [
            'id' => $item->id,
            'name' => $item->name,
            'unit' => $item->unit,
            'unit_price' => $item->unit_price,
            'quantity' => $this->quantity,
            'amount' => $item->unit_price * $this->quantity,
        ];

        $this->calculateTotal();
        $this->reset(['selectedItemId', 'quantity']);
    }

    /**
     * Removes an item from the sale cart.
     */
    public function removeItem($index)
    {
        if (isset($this->itemsToSell[$index])) {
            unset($this->itemsToSell[$index]);
            $this->itemsToSell = array_values($this->itemsToSell);
            $this->calculateTotal();
        }
    }

    /**
     * Calculates the total price of all items in the cart.
     */
    public function calculateTotal()
    {
        $this->totalAmount = collect($this->itemsToSell)->sum('amount');
    }

    /**
     * Finalizes the sale, decrements stock, and creates an Invoice (Receivable).
     */
    public function finalizeSale()
    {
        // 1. Validate mandatory fields (including customer)
        if (empty($this->itemsToSell)) {
            session()->flash('error', __('The cart is empty. Please add items.'));
            return;
        }
        $this->validate(['customerId' => $this->rules()['customerId']]); // Validates customer ID

        DB::beginTransaction();
        try {
            $customer = $this->customers->find($this->customerId);

            // --- INVOICE CREATION (RECEIVABLE) ---
            $invoice = Invoice::create([
                'uuid' => (string) Str::uuid(),
                'tenant_id' => auth()->user()->tenant_id,
                'customer_id' => $this->customerId,
                'bill_to_name' => $customer->name,
                'bill_to_email' => $customer->email,
                'bill_to_phone' => $customer->phone,
                'tax_rate' => 0, // Tax is typically calculated on the total amount
                'subtotal' => $this->totalAmount,
                'tax_amount' => 0,
                'total' => $this->totalAmount,
                'paid_amount' => 0, // Nothing paid yet
                'status' => 'unpaid', // Marks as Receivable
                'due_date' => Carbon::today()->addDays(30),
                'notes' => __('Invoice generated from quick stock sale.'),
            ]);

            // --- DECREMENT STOCK AND CREATE INVOICE ITEMS ---
            foreach ($this->itemsToSell as $item) {

                // 1. Decrement Stock (Crucial for inventory control)
                StockItem::where('id', $item['id'])->decrement('quantity', $item['quantity']);

                // 2. Create Invoice Item
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => 'Stock Sale: ' . $item['name'] . ' (Unit: ' . $item['unit'] . ')',
                    'quantity' => $item['quantity'],
                    'rate' => $item['unit_price'],
                    'amount' => $item['amount'],
                    'stock_item_id' => $item['id'], // Link to stock item for traceability
                ]);
            }

            DB::commit();
            session()->flash('success', __('Invoice created successfully! Customer now owes $') . number_format($this->totalAmount, 2));

            // Clear the form and reset state
            $this->itemsToSell = [];
            $this->calculateTotal();
            $this->reset(['customerId', 'selectedItemId', 'quantity']);
            $this->mount(); // Refresh stock item list and customers
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Stock Sale Invoice Creation Failed: ' . $e->getMessage());
            session()->flash('error', __('A database error occurred. Sale was not finalized.'));
        }
    }

    public function render()
    {
        return view('livewire.stock-sale-manager');
    }
}
