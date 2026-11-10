@props([
    'action' => '',
    'method' => 'GET',
    'id' => null,
])

<form action="{{ $action }}" method="{{ $method }}" {{ $id ? 'id='.$id : '' }} {{ $attributes->merge(['class' => 'gentelella-filter-bar']) }}>
    {{ $slot }}
</form>
