<div>
    <div class="container my-5">
        <h2 class="mb-4">{{ __('Stock Profitability Report') }}</h2>

        <div class="card">
            <div class="card-header bg-dark text-white">{{ __('Profitability Summary by Equipment') }}</div>
            <div class="card-body">
                @if (empty($reportData))
                    <div class="alert alert-info">{{ __('No equipment data available to generate report.') }}</div>
                @else
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Equipment') }}</th>
                                <th>{{ __('Initial Cost') }}</th>
                                <th>{{ __('Total Revenue') }}</th>
                                <th>{{ __('Total Maintenance Cost') }}</th>
                                <th>{{ __('Net Profit / Loss') }}</th>
                                <th>{{ __('ROI (%)') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportData as $data)
                                @php
                                    $netProfit = $data['net_profit'];
                                    $netProfitColor = $netProfit >= 0 ? 'text-success' : 'text-danger';
                                    $roiColor = $data['roi'] >= 0 ? 'text-success' : 'text-danger';
                                @endphp
                                <tr>
                                    <td>{{ $data['name'] }}</td>
                                    <td>${{ number_format($data['initial_cost'], 2) }}</td>
                                    <td>${{ number_format($data['total_revenue'], 2) }}</td>
                                    <td>${{ number_format($data['total_maintenance_cost'], 2) }}</td>
                                    <td class="{{ $netProfitColor }}">
                                        ${{ number_format($netProfit, 2) }}
                                    </td>
                                    <td class="{{ $roiColor }}">
                                        {{ number_format($data['roi'], 2) }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @php
                                $totalInitialCost = collect($reportData)->sum('initial_cost');
                                $totalRevenue = collect($reportData)->sum('total_revenue');
                                $totalMaintenance = collect($reportData)->sum('total_maintenance_cost');
                                $totalNetProfit = collect($reportData)->sum('net_profit');
                                $totalNetProfitColor = $totalNetProfit >= 0 ? 'text-success' : 'text-danger';
                            @endphp
                            <tr class="table-primary fw-bold">
                                <td>{{ __('TOTALS') }}</td>
                                <td>${{ number_format($totalInitialCost, 2) }}</td>
                                <td>${{ number_format($totalRevenue, 2) }}</td>
                                <td>${{ number_format($totalMaintenance, 2) }}</td>
                                <td class="{{ $totalNetProfitColor }}">
                                    ${{ number_format($totalNetProfit, 2) }}
                                </td>
                                <td>N/A</td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
