@props([
    'headers' => [],
    'striped' => true,
    'hover' => true,
    'responsive' => true,
])

<div class="{{ $responsive ? 'table-responsive-gentelella' : '' }}">
    <table {{ $attributes->merge(['class' => 'table jambo_table' . ($striped ? ' table-striped' : '') . ($hover ? ' table-hover' : '') . ' bulk_action']) }}>
        @if(isset($header) || !empty($headers))
            <thead>
                <tr class="headings">
                    @if(isset($header))
                        {{ $header }}
                    @else
                        @foreach($headers as $th)
                            <th scope="col" class="column-title">{{ $th }}</th>
                        @endforeach
                    @endif
                </tr>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        @else
            {{ $slot }}
        @endif
    </table>
</div>

@if(isset($pagination))
    <div class="gentelella-pagination-wrapper" style="margin-top: 1rem; display: flex; justify-content: flex-end;">
        {{ $pagination }}
    </div>
@endif
