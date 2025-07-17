@php use Illuminate\Support\Str; @endphp
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Инвентаризация №{{ $inventory->id }}</title>
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
    <h1>Инвентаризация №{{ $inventory->id }}</h1>
    <div class="meta">
        <span><b>Дата:</b> {{ $inventory->formatted_date ?? $inventory->date }}</span>
        <span><b>Ответственный:</b> {{ $inventory->user->name ?? '—' }}</span>
    </div>
    @if($inventory->notes)
        <div style="margin-bottom: 10px;"><b>Примечания:</b> {{ $inventory->notes }}</div>
    @endif
    <h3>Товары с расхождениями</h3>
    <table>
        <thead>
            <tr>
                <th>Товар</th>
                <th>Склад</th>
                <th>Кол</th>
                <th>Разница</th>
                <th>Статус</th>
            </tr>
        </thead>
        <tbody>
        @forelse($discrepancies as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->warehouse_qty }} шт</td>
                <td>{{ $item->actual_qty }} шт</td>
                <td class="{{ $item->difference > 0 ? 'text-success' : 'text-danger' }}">
                    {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }} шт
                </td>
                <td>
                    @if($item->difference == 0)
                        <span class="status-success">Совпадает</span>
                    @elseif($item->difference > 0)
                        <span class="status-warning">Лишнее</span>
                    @else
                        <span class="status-danger">Не хватает</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Нет расхождений</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html> 