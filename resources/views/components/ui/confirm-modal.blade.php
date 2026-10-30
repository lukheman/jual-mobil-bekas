@props([
    'show' => false,
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed? This action cannot be undone.',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'variant' => 'danger',
    'icon' => 'fas fa-exclamation-triangle',
    'action' => null,
    'method' => 'DELETE',
    'cancelUrl' => null,
    'formData' => []
])

@php
    $iconColors = [
        'danger' => 'var(--bs-danger)',
        'warning' => 'var(--bs-warning)',
        'primary' => 'var(--bs-primary)',
    ];
    $iconColor = $iconColors[$variant] ?? $iconColors['danger'];
@endphp

@if ($show)
    <div class="modal-backdrop-custom">
        <div class="modal-content-custom">
            <div class="modal-header-custom">
                <h5 class="modal-title-custom">
                    <i class="{{ $icon }} me-2" style="color: {{ $iconColor }};"></i>
                    {{ $title }}
                </h5>
                @if($cancelUrl)
                    <a href="{{ $cancelUrl }}" class="modal-close-btn" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>

            <p style="color: var(--text-secondary);">
                {{ $message }}
            </p>

            {{ $slot }}

            <form method="POST" action="{{ $action }}">
                @csrf
                @if(strtoupper($method) !== 'POST')
                    @method($method)
                @endif

                @foreach ($formData as $field => $value)
                    @if ($value !== null && $value !== '')
                        <input type="hidden" name="{{ $field }}" value="{{ $value }}">
                    @endif
                @endforeach

                <div class="d-flex justify-content-end gap-2 mt-4">
                    @if($cancelUrl)
                        <x-ui.button :href="$cancelUrl" variant="secondary">
                            {{ $cancelText }}
                        </x-ui.button>
                    @endif

                    <button type="submit" class="btn btn-modern btn-{{ $variant }}">
                        {{ $confirmButton ?? $confirmText }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
