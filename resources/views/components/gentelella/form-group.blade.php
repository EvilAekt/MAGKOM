@props([
    'label' => null,
    'name' => null,
    'required' => false,
    'help' => null,
    'icon' => null,
    'col' => 'col-md-12 col-sm-12',
])

<div class="gentelella-form-group {{ $col }} @if($name) @error($name) has-error @enderror @endif">
    @if($label)
        <label @if($name) for="{{ $name }}" @endif class="gentelella-form-label" style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B); margin-bottom: 6px;">
            {{ $label }}
            @if($required)
                <span class="required-dot" style="color: #DC2626; margin-left: 2px;">*</span>
            @endif
        </label>
    @endif

    <div class="gentelella-input-wrap {{ $icon ? 'has-icon-left' : '' }}" style="position: relative;">
        @if($icon)
            <span class="gentelella-input-icon" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--neutral-400, #94A3B8); pointer-events: none; display: flex; align-items: center;">
                {!! $icon !!}
            </span>
        @endif

        {{ $slot }}
    </div>

    @if($name)
        @error($name)
            <span class="gentelella-form-error" role="alert" style="display: block; font-size: 0.75rem; color: #DC2626; margin-top: 4px; font-weight: 600;">
                {{ $message }}
            </span>
        @enderror
    @endif

    @if($help && (!$name || !$errors->has($name)))
        <small class="gentelella-form-help" style="display: block; font-size: 0.75rem; color: var(--neutral-500, #64748B); margin-top: 4px;">
            {{ $help }}
        </small>
    @endif
</div>
