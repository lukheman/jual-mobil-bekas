@props([
    'title',
    'subtitle' => null
])

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div class="page-header-copy">
        <h1 class="mb-1" style="color: var(--text-primary);">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-muted mb-0">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($actions))
        <div class="page-header-actions">
            {{ $actions }}
        </div>
    @endif
</div>

<style>
    .page-header {
        gap: 1rem;
    }

    .page-header-copy {
        min-width: 0;
    }

    @media (max-width: 767.98px) {
        .page-header {
            flex-wrap: wrap;
            align-items: flex-start !important;
        }

        .page-header-copy {
            flex: 1 1 100%;
        }

        .page-header-copy h1 {
            font-size: 1.65rem;
        }

        .page-header-actions {
            width: 100%;
        }

        .page-header-actions > div {
            display: grid !important;
            grid-template-columns: 1fr;
            gap: 0.5rem;
            width: 100%;
        }

        .page-header-actions .btn {
            width: 100%;
        }
    }
</style>
