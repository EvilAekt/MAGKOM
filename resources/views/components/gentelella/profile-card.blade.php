@props([
    'name',
    'role' => null,
    'roleType' => 'primary',
    'avatarUrl' => null,
    'initials' => null,
    'institution' => null,
    'major' => null,
    'status' => null,
    'statusType' => 'success',
])

@php
    $displayInitials = $initials;
    if (!$displayInitials && $name) {
        $words = explode(' ', trim($name));
        $displayInitials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
    }

    $roleBadgeClass = match($roleType) {
        'admin' => 'x_badge_purple',
        'pembimbing' => 'x_badge_warning',
        'peserta' => 'x_badge_info',
        'superadmin' => 'x_badge_danger',
        default => 'x_badge_primary',
    };

    $statusClass = match(strtolower((string) $statusType)) {
        'aktif', 'success' => 'gentelella-status-success',
        'pending', 'warning' => 'gentelella-status-warning',
        'nonaktif', 'danger' => 'gentelella-status-danger',
        default => 'gentelella-status-info',
    };
@endphp

<div {{ $attributes->merge(['class' => 'gentelella-profile-card profile_view']) }}>
    <div class="gentelella-profile-cover"></div>
    <div class="gentelella-profile-body">
        <div class="gentelella-profile-avatar-wrap">
            @if($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="{{ $name }}" class="gentelella-profile-avatar-img">
            @else
                <div class="gentelella-profile-avatar-initials">
                    {{ $displayInitials ?: 'MK' }}
                </div>
            @endif

            @if(isset($avatarAction))
                <div class="gentelella-profile-avatar-action">
                    {{ $avatarAction }}
                </div>
            @endif
        </div>

        <div class="gentelella-profile-meta">
            <h4 class="gentelella-profile-name" title="{{ $name }}">{{ $name }}</h4>

            @if($role)
                <div class="gentelella-profile-role-row">
                    <span class="x_badge {{ $roleBadgeClass }}">{{ $role }}</span>
                    @if($status)
                        <span class="gentelella-status-pill {{ $statusClass }}">● {{ $status }}</span>
                    @endif
                </div>
            @endif

            @if($institution || $major)
                <p class="gentelella-profile-institution">
                    @if($institution)
                        <strong>{{ $institution }}</strong>
                    @endif
                    @if($institution && $major) &bull; @endif
                    @if($major)
                        <span>{{ $major }}</span>
                    @endif
                </p>
            @endif
        </div>

        @if(isset($slot) && trim($slot))
            <div class="gentelella-profile-extra">
                {{ $slot }}
            </div>
        @endif
    </div>

    @if(isset($actions) && trim($actions))
        <div class="gentelella-profile-footer">
            {{ $actions }}
        </div>
    @endif
</div>
