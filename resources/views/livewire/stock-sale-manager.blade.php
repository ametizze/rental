<div>
    <div class="container my-5">
        <h2 class="mb-4">{{ __('Quick Stock Sale') }}</h2>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">{{ __('New Sale Transaction') }}</div>
            <div class="card-body">
                <div class="row g-3 border-bottom pb-4 mb-4">
                    <div class="col-12">
                        <label class="form-label">{{ __('Associate Customer') }} <span
                                class="text-danger">*</span></label>
                        <select class="form-select" wire:model.defer="customerId">
                            <option value="">{{ __('Select Customer') }}</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        @error('customerId')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 border-bottom pb-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Select Item') }}</label>
                        <select class="form-select" wire:model.defer="selectedItemId">
                            <option value="">{{ __('Choose a stock item') }}</option>
                            @foreach ($stockItems as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }} ({{ $item->quantity }} {{ $item->unit }} in stock) -
                                    ${{ $item->unit_price }}
                                </option>
                            @endforeach
                        </select>
                        @error('selectedItemId')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('Quantity') }}</label>
                        <input type="number" class="form-control" wire:model.defer="quantity" min="1"
                            placeholder="1">
                        @error('quantity')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-secondary w-100"
                            wire:click="addItemToSale">{{ __('Add to Cart') }}</button>
                    </div>
                </div>

                <h4 class="mb-3">{{ __('Sale Cart') }}</h4>
                <ul class="list-group mb-4">
                    @forelse ($itemsToSell as $index => $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $item['name'] }}</strong> ({{ $item['quantity'] }} x
                                ${{ number_format($item['unit_price'], 2) }})
                            </div>
                            <div>
                                <span class="me-3">${{ number_format($item['amount'], 2) }}</span>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    wire:click="removeItem({{ $index }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">{{ __('No items in cart.') }}</li>
                    @endforelse
                </ul>

                <div class="text-end">
                    <h3>{{ __('Total Due') }}: <strong>${{ number_format($totalAmount, 2) }}</strong></h3>
                    <button type="button" class="btn btn-success btn-lg mt-3"
                        wire:click="finalizeSale">{{ __('Finalize Sale') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
