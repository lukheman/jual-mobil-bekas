@props([
    'title' => '',
    'closeUrl' => null
])

<div class="modal-backdrop-custom">
    <div class="modal-content-custom">
        <div class="modal-header-custom">
            <h5 class="modal-title-custom">
                {{ $title }}
            </h5>
            @if($closeUrl)
                <a href="{{ $closeUrl }}" class="modal-close-btn" aria-label="Close">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </div>

        {{ $slot }}
    </div>
</div>
