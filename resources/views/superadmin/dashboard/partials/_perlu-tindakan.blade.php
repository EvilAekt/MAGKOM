@if($perluTindakan->isNotEmpty())
    <section class="sa-attention" aria-labelledby="sa-attention-title">
        <h3 class="sa-attention__title" id="sa-attention-title">
            <x-sa.icon name="alert" :size="18" />
            {{ ($totalAntreanDiajukan > 0 || $totalWaitingList > 0) ? 'Perhatian Antrean Pendaftaran' : 'Perlu tindakan' }}
        </h3>
        <div class="sa-attention__chips">
            @foreach($perluTindakan as $item)
                <x-sa.action-chip :href="route($item['route'])" :label="$item['label']" :count="$item['jumlah']" :tone="$item['tone']" />
            @endforeach
        </div>
    </section>
@else
    <section class="sa-attention sa-attention--clear">
        <x-sa.badge tone="success">Tidak ada yang perlu ditindaklanjuti</x-sa.badge>
    </section>
@endif
