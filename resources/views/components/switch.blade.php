@props([
    'name',
    'label' => '',
    'checked' => false,
    'id' => null,
    'value' => 1,
    'includeHidden' => true,
])

@php
    $inputId = $id ?: $name;
@endphp

<div class="custom-control custom-switch">
    @php $isArrayName = str_ends_with($name, '[]'); @endphp
    @if($includeHidden && !$isArrayName)
        <input type="hidden" name="{{ $name }}" value="0">
    @endif
    <input type="checkbox" class="custom-control-input" id="{{ $inputId }}" name="{{ $name }}" value="{{ $value }}" {{ $checked ? 'checked' : '' }} {{ $attributes }}>
    @if($label !== '')
        <label class="custom-control-label" for="{{ $inputId }}">{!! $label !!}</label>
    @endif
</div>


