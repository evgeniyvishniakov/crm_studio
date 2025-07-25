@php use Illuminate\Support\Str; @endphp
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ $projectName ?? 'Проект' }} - {{ __('messages.inventory') }} {{ $inventory->formatted_date ?? $inventory->date }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #222; }
        h1 { font-size: 20px; margin-bottom: 10px; }
        .meta { margin-bottom: 15px; }
        .meta span { display: inline-block; margin-right: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #bbb; padding: 6px 8px; text-align: left; }
        th { background: #f7f7f7; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .status-success { color: #28a745; }
        .status-warning { color: #ffc107; }
        .status-danger { color: #dc3545; }
    </style>
</head>
<body>
    <h1>{{ $projectName ?? 'Проект' }} - {{ __('messages.inventory') }} {{ $inventory->formatted_date ?? $inventory->date }}</h1>
    <div class="meta">
        <span><b>{{ __('messages.date') }}:</b> {{ $inventory->formatted_date ?? $inventory->date }}</span>
        <span><b>{{ __('messages.responsible') }}:</b> {{ $inventory->user->name ?? '—' }}</span>
    </div>
    @if($inventory->notes)
        <div style="margin-bottom: 10px;"><b>{{ __('messages.notes') }}:</b> {{ $inventory->notes }}</div>
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
                <td>{{ $item->product->name }}</td>
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
            <tr><td colspan="5">{{ __('messages.no_discrepancies') }}</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html> 