{{-- ==========================================================================
   Zenius Right Column (Profile Pembimbing)
   ========================================================================== --}}

<!-- 1. Profile Pembimbing Card -->
<div class="zenius-side-card zenius-profile-card">
    <div class="zenius-avatar-ring">
        <span class="avatar-initials">{{ strtoupper(substr(auth()->user()->nama, 0, 2)) }}</span>
    </div>
    <h3 class="zenius-profile-name">{{ auth()->user()->nama }}</h3>
    <span class="zenius-profile-role">Pembimbing Lapangan ASN</span>
    <span class="zenius-profile-dept">{{ $pembimbingBidang }}</span>
</div>

