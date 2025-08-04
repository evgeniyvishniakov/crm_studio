<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ $projectName ?? 'Проект' }} - {{ __('messages.inventory') }} {{ $inventory->formatted_date ?? $inventory->date }}</title>
                    <link rel="stylesheet" href="{{ asset('client/css/common.css') }}">
</head>
<body class="pdf-document">
    <h1>{{ $projectName ?? 'Проект' }} - {{ __('messages.inventory') }} {{ $inventory->formatted_date ?? $inventory->date }}</h1>
    
    <div class="meta">
        <span><b>{{ __('messages.date') }}:</b> {{ $inventory->formatted_date ?? $inventory->date }}</span>
        <span><b>{{ __('messages.responsible') }}:</b> {{ $inventory->user->name ?? '—' }}</span>
    </div>
    
    @if($inventory->notes)
        <div class="notes">
            <b>{{ __('messages.notes') }}:</b> {{ $inventory->notes }}
        </div>
    @endif
    
    <h3>{{ __('messages.products_with_discrepancies') }}</h3>
    <table>
        <thead>
            <tr>
                <th>{{ __('messages.product') }}</th>
                <th>{{ __('messages.warehouse_short') }}</th>
                <th>{{ __('messages.quantity_short') }}</th>
                <th>{{ __('messages.difference_short') }}</th>
                <th>{{ __('messages.status') }}</th>
            </tr>
        </thead>
        <tbody>
        @forelse($discrepancies as $item)
            <tr>
                <td><strong>{{ $item->product->name }}</strong></td>
                <td>{{ $item->warehouse_qty }} {{ __('messages.units') }}</td>
                <td>{{ $item->actual_qty }} {{ __('messages.units') }}</td>
                <td class="{{ $item->difference > 0 ? 'text-success' : 'text-danger' }}">
                    {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }} {{ __('messages.units') }}
                </td>
                <td>
                    @if($item->difference == 0)
                        <span class="status-success">{{ __('messages.matches_status') }}</span>
                    @elseif($item->difference > 0)
                        <span class="status-warning">{{ __('messages.overage_status') }}</span>
                    @else
                        <span class="status-danger">{{ __('messages.shortage_status') }}</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #28a745; font-weight: 600;">
                    {{ __('messages.no_discrepancies') }}
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html> 