<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Invoice') }} #{{ $invoice->uuid }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            font-size: 14px;
            line-height: 24px;
            color: #555;
            border: 1px solid #eee;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 32px;
            line-height: 32px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        .invoice-box .notes,
        .invoice-box .photos,
        .invoice-box .payments-history {
            margin-top: 30px;
        }

        .invoice-box .photos img {
            max-width: 150px;
            margin: 5px;
            border: 1px solid #ddd;
        }

        .balance-due {
            font-size: 16px;
            color: red;
            font-weight: bold;
        }

        .status-paid {
            color: green;
            font-weight: bold;
        }

        /** Estilos de Impressão **/
        @media print {
            .invoice-box {
                border: none;
                box-shadow: none;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                @if ($invoice->tenant->logo_path ?? null)
                                    <img src="{{ asset('storage/' . $invoice->tenant->logo_path) }}"
                                        style="max-width: 150px;">
                                @else
                                    <h5 class="status-paid">{{ $invoice->tenant->name }}</h5>
                                @endif
                            </td>
                            <td>
                                <strong>{{ __('Invoice') }} #:</strong> {{ $invoice->uuid }}<br>
                                <strong>{{ __('Created') }}:</strong> {{ $invoice->created_at->format('Y-m-d') }}<br>
                                <strong>{{ __('Due Date') }}:</strong> {{ $invoice->due_date->format('Y-m-d') }}<br>
                                <strong>{{ __('Status') }}:</strong> <span
                                    class="{{ $invoice->status == 'paid' ? 'status-paid' : 'balance-due' }}">{{ __(ucfirst($invoice->status)) }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>{{ __('Billed By') }}:</strong><br>
                                {{ $invoice->tenant->name }}<br>
                                {{ $invoice->tenant->address }}<br>
                                {{ $invoice->tenant->city }}, {{ $invoice->tenant->state }}
                                {{ $invoice->tenant->zipcode }}<br>
                                {{ $invoice->tenant->email }}
                            </td>
                            <td>
                                <strong>{{ __('Bill To') }}:</strong><br>
                                {{ $invoice->bill_to_name }}<br>
                                {{ $invoice->bill_to_email }}<br>
                                {{ $invoice->bill_to_phone }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            @if ($invoice->rental)
                <tr class="heading">
                    <td>
                        {{ __('Rental Details') }}
                    </td>
                    <td></td>
                </tr>
                <tr class="details">
                    <td>
                        {{ __('Start Date') }}: {{ $invoice->rental->start_date->format('d/m/Y') }}<br>
                        {{ __('End Date') }}: {{ $invoice->rental->end_date->format('d/m/Y') }}<br>
                    </td>
                    <td></td>
                </tr>
            @endif

            <tr class="heading">
                <td>{{ __('Description') }}</td>
                <td>{{ __('Amount') }}</td>
            </tr>
            @foreach ($invoice->items as $item)
                <tr class="item">
                    <td>{{ $item->description }}</td>
                    <td>${{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach

            <tr class="total">
                <td></td>
                <td>{{ __('Subtotal') }}: ${{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            <tr class="total">
                <td></td>
                <td>{{ __('Tax') }} ({{ number_format($invoice->tax_rate * 100, 2) }}%):
                    ${{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            <tr class="total">
                <td></td>
                <td><strong>{{ __('Total') }}: ${{ number_format($invoice->total, 2) }}</strong></td>
            </tr>
            <tr class="total">
                <td></td>
                <td class="status-paid">{{ __('Paid Amount') }}: ${{ number_format($invoice->paid_amount, 2) }}</td>
            </tr>
            @if ($invoice->status != 'paid')
                <tr class="total">
                    <td></td>
                    <td class="balance-due">{{ __('Balance Due') }}:
                        ${{ number_format($invoice->total - $invoice->paid_amount, 2) }}</td>
                </tr>
            @endif
        </table>

        @if ($invoice->rental && (!empty($invoice->rental->start_photos) || !empty($invoice->rental->end_photos)))
            <div class="photos">
                <h4>{{ __('Condition Photos') }}</h4>

                <strong>{{ __('Start of Rental') }}:</strong><br>
                @foreach ($invoice->rental->start_photos as $photoBlock)
                    <img src="{{ asset('storage/' . $photoBlock['path']) }}">
                    <span
                        style="display: inline-block; font-size: 11px; margin-right: 15px;">{{ $photoBlock['label'] ?? '' }}</span>
                @endforeach
                <br><br>
                <strong>{{ __('End of Rental') }}:</strong><br>
                @foreach ($invoice->rental->end_photos as $photoBlock)
                    <img src="{{ asset('storage/' . $photoBlock['path']) }}">
                    <span
                        style="display: inline-block; font-size: 11px; margin-right: 15px;">{{ $photoBlock['label'] ?? '' }}</span>
                @endforeach
            </div>
        @endif

        @if (!$invoice->payments->isEmpty())
            <div class="payments-history">
                <h4>{{ __('Payment History') }}</h4>
                <table>
                    <tr class="heading">
                        <td>{{ __('Date') }}</td>
                        <td>{{ __('Amount') }}</td>
                        <td>{{ __('Notes') }}</td>
                    </tr>
                    @foreach ($invoice->payments as $payment)
                        <tr class="item">
                            <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                            <td>${{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->notes }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        @if (!empty($invoice->notes))
            <div class="notes">
                <strong>{{ __('Notes') }}:</strong><br>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
