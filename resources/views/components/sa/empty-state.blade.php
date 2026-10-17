@props(['icon' => 'inbox', 'text' => 'Belum ada data'])
<div class="sa-empty">
    <x-sa.icon :name="$icon" :size="24" />
    <p>{{ $text }}</p>
</div>
