@props([
    'title' => '',
    'action' => '',
    'method' => 'POST',
    'closeUrl' => null,
    'submitLabel' => 'Simpan',
    'closeLabel' => 'Cancel',
    'submitVariant' => 'primary'
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

        <form method="POST" action="{{ $action }}">
            @csrf
            @if(strtoupper($method) !== 'POST')
                @method($method)
            @endif

            {{ $slot }}

            <div class="d-flex justify-content-end gap-2 mt-4">
                @if($closeUrl)
                    <x-ui.button :href="$closeUrl" variant="secondary">
                        {{ $closeLabel }}
                    </x-ui.button>
                @endif
                <x-ui.button type="submit" variant="{{ $submitVariant }}">
                    {{ $submitLabel }}
                </x-ui.button>
            </div>
        </form>
    </div>
</div>
