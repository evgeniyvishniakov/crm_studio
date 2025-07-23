@extends('client.layouts.app')

@php
use App\Helpers\CurrencyHelper;
$currency = session('currency', 'UAH');
$currencySymbol = CurrencyHelper::getSymbol($currency);
@endphp

@section('content')
<div class="dashboard-container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Тест валюты</h4>
                </div>
                <div class="card-body">
                    <p><strong>Текущая валюта:</strong> {{ $currency }} ({{ $currencySymbol }})</p>
                    
                    <h5>Примеры форматирования:</h5>
                    <ul>
                        <li>1000 = {{ CurrencyHelper::format(1000, $currency) }}</li>
                        <li>1500.50 = {{ CurrencyHelper::format(1500.50, $currency) }}</li>
                        <li>25000 = {{ CurrencyHelper::format(25000, $currency) }}</li>
                    </ul>
                    
                    <h5>Доступные валюты:</h5>
                    <ul>
                        @foreach(CurrencyHelper::getAvailableCurrencies() as $code => $name)
                            <li>{{ $code }} - {{ $name }} ({{ CurrencyHelper::getName($code) }})</li>
                        @endforeach
                    </ul>
                    
                    <p><a href="{{ route('client.settings.index') }}#language" class="btn btn-primary">Изменить валюту в настройках</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 