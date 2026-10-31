@php
    $sortQuery = $sort !== 'name_asc' ? $sort : null;

    $baseQuery = array_filter([
        'q' => $search,
        'brand' => $brandId ?: null,
        'coverage' => $coverage,
        'sort' => $sortQuery,
        'page' => request('page'),
    ], fn ($value) => $value !== null && $value !== '');

    $closeUrl = route('admin.car-models', $baseQuery);
    $brandOptions = $brands->pluck('name', 'id')->all();
    $brandName = $brands->firstWhere('id', $brandId)?->name;

    $coverageLabels = [
        'with_cars' => 'Punya Mobil',
        'without_cars' => 'Belum Ada Mobil',
    ];

    $coverageLabel = $coverageLabels[$coverage] ?? null;
    $hasFilters = $search !== '' || $brandId > 0 || $coverage !== '';

    $percentage = fn (int $value, int $total): int => $total > 0
        ? (int) round($value / $total * 100)
        : 0;

    $statCards = [
        [
            'key' => 'total',
            'label' => 'Total Model',
            'meta' => 'Seluruh model terdaftar',
            'icon' => 'fas fa-list',
            'tone' => 'primary',
            'value' => $stats['total'],
            'percentage' => $stats['total'] > 0 ? 100 : 0,
            'coverage' => '',
            'href' => route('admin.car-models', array_filter(['q' => $search, 'brand' => $brandId ?: null, 'sort' => $sortQuery])),
        ],
        [
            'key' => 'with_cars',
            'label' => 'Punya Mobil',
            'meta' => 'Sudah ada unit',
            'icon' => 'fas fa-car-side',
            'tone' => 'success',
            'value' => $stats['with_cars'],
            'percentage' => $percentage($stats['with_cars'], $stats['total']),
            'coverage' => 'with_cars',
            'href' => route('admin.car-models', array_filter(['q' => $search, 'brand' => $brandId ?: null, 'sort' => $sortQuery, 'coverage' => 'with_cars'])),
        ],
        [
            'key' => 'without_cars',
            'label' => 'Belum Ada Mobil',
            'meta' => 'Perlu dilengkapi',
            'icon' => 'fas fa-circle-exclamation',
            'tone' => 'warning',
            'value' => $stats['without_cars'],
            'percentage' => $percentage($stats['without_cars'], $stats['total']),
            'coverage' => 'without_cars',
            'href' => route('admin.car-models', array_filter(['q' => $search, 'brand' => $brandId ?: null, 'sort' => $sortQuery, 'coverage' => 'without_cars'])),
        ],
        [
            'key' => 'brands_covered',
            'label' => 'Merek Terisi',
            'meta' => 'Dari '.number_format($stats['brands_total'], 0, ',', '.').' merek',
            'icon' => 'fas fa-trademark',
            'tone' => 'info',
            'value' => $stats['brands_covered'],
            'percentage' => $percentage($stats['brands_covered'], $stats['brands_total']),
            'coverage' => 'brands',
            'href' => route('admin.brands', ['coverage' => 'with_models']),
        ],
    ];

    $initialsFor = static function (?string $name): string {
        $parts = array_slice(array_filter(preg_split('/\s+/', trim((string) $name)) ?: []), 0, 2);
        $letters = array_map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)), $parts);

        return implode('', $letters) ?: '?';
    };
@endphp

@push('styles')
    <style>
        /* Tombol mengikuti palet tema */
        .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: var(--bs-primary);
            --bs-btn-border-color: var(--bs-primary);
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #164934;
            --bs-btn-hover-border-color: #164934;
            --bs-btn-focus-shadow-rgb: 29, 91, 66;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #164934;
            --bs-btn-active-border-color: #164934;
            --bs-btn-disabled-bg: var(--bs-primary);
            --bs-btn-disabled-border-color: var(--bs-primary);
        }

        .btn-secondary {
            --bs-btn-color: var(--text-primary);
            --bs-btn-bg: var(--bg-tertiary);
            --bs-btn-border-color: var(--border-color);
            --bs-btn-hover-color: var(--text-primary);
            --bs-btn-hover-bg: var(--hover-bg);
            --bs-btn-hover-border-color: var(--border-color);
            --bs-btn-focus-shadow-rgb: 29, 91, 66;
            --bs-btn-active-color: var(--text-primary);
            --bs-btn-active-bg: var(--hover-bg);
            --bs-btn-active-border-color: var(--border-color);
            --bs-btn-disabled-color: var(--text-muted);
            --bs-btn-disabled-bg: var(--bg-tertiary);
            --bs-btn-disabled-border-color: var(--border-color);
        }

        /* Ringkasan model */
        .model-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .model-stat {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.9rem;
            padding: 1.1rem 1.15rem 1.35rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border-light);
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            text-decoration: none;
            color: inherit;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .model-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(23, 53, 43, 0.12);
            border-color: var(--border-color);
            color: inherit;
        }

        .model-stat.is-active {
            border-color: var(--stat-color);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--stat-color) 14%, transparent);
        }

        .model-stat-primary { --stat-color: var(--bs-primary); --stat-soft: rgba(29, 91, 66, 0.1); }
        .model-stat-success { --stat-color: var(--bs-success); --stat-soft: rgba(77, 138, 99, 0.12); }
        .model-stat-warning { --stat-color: var(--bs-warning); --stat-soft: rgba(189, 138, 56, 0.14); }
        .model-stat-info { --stat-color: var(--bs-info); --stat-soft: rgba(79, 130, 144, 0.12); }

        .model-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            background: var(--stat-soft);
            color: var(--stat-color);
            font-size: 1rem;
        }

        .model-stat-copy {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
            min-width: 0;
        }

        .model-stat-copy strong {
            color: var(--text-primary);
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .model-stat-copy span {
            color: var(--text-muted);
            font-size: 0.76rem;
            font-weight: 500;
            margin-top: 0.2rem;
        }

        .model-stat-copy small {
            color: var(--text-muted);
            font-size: 0.65rem;
            margin-top: 0.12rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .model-stat-progress {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            background: var(--bg-tertiary);
        }

        .model-stat-progress span {
            display: block;
            height: 100%;
            background: var(--stat-color);
            transition: width 0.3s ease;
        }

        /* Filter */
        .model-filter-card {
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .model-filter-card:hover,
        .model-panel:hover {
            transform: none;
        }

        .model-filter {
            display: grid;
            grid-template-columns: minmax(220px, 1fr) 190px 190px auto auto;
            gap: 0.65rem;
            align-items: center;
        }

        .model-search {
            position: relative;
            display: flex;
            align-items: center;
        }

        .model-search > i {
            position: absolute;
            left: 0.9rem;
            color: var(--text-muted);
            font-size: 0.85rem;
            pointer-events: none;
        }

        .model-search input {
            width: 100%;
            height: 42px;
            padding: 0.55rem 2.3rem 0.55rem 2.4rem;
            border: 1px solid var(--border-color);
            border-radius: 7px;
            background: var(--input-bg);
            color: var(--text-primary);
            font-size: 0.85rem;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .model-search input:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(29, 91, 66, 0.1);
        }

        .model-search input::placeholder {
            color: var(--text-muted);
        }

        .model-search-clear {
            position: absolute;
            right: 0.7rem;
            display: grid;
            place-items: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            color: var(--text-muted);
            text-decoration: none;
        }

        .model-search-clear:hover {
            background: var(--hover-bg);
            color: var(--bs-danger);
        }

        .model-select {
            height: 42px;
            padding: 0.55rem 2rem 0.55rem 0.8rem;
            border: 1px solid var(--border-color);
            border-radius: 7px;
            background-color: var(--input-bg);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2371817a' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 14px 10px;
            color: var(--text-primary);
            font-size: 0.85rem;
            appearance: none;
        }

        .model-select:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(29, 91, 66, 0.1);
        }

        .model-filter-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            height: 42px;
            padding: 0 1.1rem;
            border: none;
            border-radius: 7px;
            background: var(--bs-primary);
            color: #fff;
            font-size: 0.82rem;
            font-weight: 600;
            white-space: nowrap;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .model-filter-submit:hover {
            background: #164934;
            transform: translateY(-1px);
        }

        .model-filter-reset {
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            border: 1px solid var(--border-color);
            border-radius: 7px;
            background: var(--bg-secondary);
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .model-filter-reset:hover {
            border-color: var(--bs-danger);
            color: var(--bs-danger);
        }

        .model-chips {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.45rem;
            margin-top: 0.85rem;
            padding-top: 0.85rem;
            border-top: 1px solid var(--border-light);
        }

        .model-chips-label {
            color: var(--text-muted);
            font-size: 0.74rem;
            font-weight: 600;
        }

        .model-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.55rem;
            border-radius: 999px;
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            font-size: 0.74rem;
        }

        .model-chip > i {
            color: var(--bs-primary);
            font-size: 0.65rem;
        }

        .model-chip a {
            display: grid;
            place-items: center;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            color: var(--text-muted);
        }

        .model-chip a:hover {
            background: var(--bg-secondary);
            color: var(--bs-danger);
        }

        .model-chips-clear {
            margin-left: 0.25rem;
            color: var(--bs-danger);
            font-size: 0.74rem;
            font-weight: 600;
            text-decoration: none;
        }

        .model-chips-clear:hover {
            text-decoration: underline;
        }

        /* Panel daftar */
        .model-panel {
            padding: 0;
            overflow: hidden;
        }

        .model-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.15rem 1.35rem;
            border-bottom: 1px solid var(--border-light);
        }

        .model-panel-head h5 {
            margin: 0 0 0.15rem;
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 700;
        }

        .model-panel-head p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.76rem;
        }

        .model-sort {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            font-size: 0.74rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .model-table {
            margin: 0;
            min-width: 820px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .model-table thead th {
            padding: 0.7rem 1.1rem;
            border: none;
            border-bottom: 1px solid var(--border-light);
            background: var(--bg-tertiary);
            color: var(--text-muted);
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .model-table tbody td {
            padding: 0.95rem 1.1rem;
            border: none;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .model-table tbody tr:last-child td {
            border-bottom: none;
        }

        .model-table tbody tr:hover td {
            background: var(--hover-bg);
        }

        .model-table tbody tr.is-empty-row td:first-child {
            box-shadow: inset 3px 0 0 var(--bs-warning);
        }

        .model-cell {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            min-width: 220px;
        }

        .model-mark {
            width: 44px;
            height: 44px;
            flex-shrink: 0;
            border: 1px solid var(--border-light);
            border-radius: 10px;
            background: rgba(29, 91, 66, 0.1);
            color: var(--bs-primary);
            display: grid;
            place-items: center;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .model-cell-copy {
            min-width: 0;
        }

        .model-name {
            color: var(--text-primary);
            font-size: 0.9rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .model-slug {
            display: inline-block;
            margin-top: 0.18rem;
            color: var(--text-muted);
            font-size: 0.7rem;
            line-height: 1.3;
            word-break: break-all;
        }

        .model-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            min-width: 0;
            color: var(--text-primary);
            text-decoration: none;
        }

        .model-brand-mark {
            width: 28px;
            height: 28px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            border: 1px solid var(--border-light);
            border-radius: 7px;
            background: #fff;
            overflow: hidden;
            color: var(--bs-primary);
            font-size: 0.68rem;
            font-weight: 700;
        }

        .model-brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 4px;
        }

        .model-brand span {
            font-size: 0.82rem;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .model-brand:hover span {
            color: var(--bs-primary);
        }

        .model-count {
            display: inline-flex;
            align-items: baseline;
            gap: 0.3rem;
            color: var(--text-primary);
            text-decoration: none;
        }

        .model-count strong {
            font-size: 0.92rem;
            font-weight: 700;
            line-height: 1;
        }

        .model-count span {
            color: var(--text-muted);
            font-size: 0.7rem;
        }

        .model-count:hover strong {
            color: var(--bs-primary);
        }

        .model-count.is-empty strong,
        .model-count.is-empty span {
            color: var(--text-muted);
            font-weight: 500;
        }

        .model-health {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.28rem 0.58rem;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .model-health.is-active {
            background: rgba(77, 138, 99, 0.14);
            color: var(--bs-success);
        }

        .model-health.is-empty {
            background: rgba(189, 138, 56, 0.14);
            color: var(--bs-warning);
        }

        .model-health.is-archived {
            background: rgba(79, 130, 144, 0.14);
            color: var(--bs-info);
        }

        .model-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.35rem;
        }

        .model-row-action {
            display: grid;
            place-items: center;
            width: 32px;
            height: 32px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: var(--bg-secondary);
            color: var(--text-secondary);
            font-size: 0.72rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .model-row-action:hover {
            border-color: var(--bs-primary);
            background: var(--hover-bg);
            color: var(--bs-primary);
        }

        .model-row-action.is-danger:hover {
            border-color: var(--bs-danger);
            background: rgba(185, 88, 76, 0.06);
            color: var(--bs-danger);
        }

        .model-row-action.is-disabled {
            border-color: var(--border-light);
            background: var(--bg-tertiary);
            color: var(--text-muted);
            opacity: 0.55;
            cursor: not-allowed;
        }

        .model-pagination {
            display: flex;
            justify-content: flex-end;
            padding: 1rem 1.35rem;
            border-top: 1px solid var(--border-light);
            background: var(--bg-secondary);
        }

        .model-panel-note {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.7rem 1.35rem;
            border-top: 1px solid var(--border-light);
            background: var(--bg-secondary);
            color: var(--text-muted);
            font-size: 0.72rem;
        }

        .model-panel-note i {
            color: var(--bs-info);
        }

        /* Modal model */
        .model-modal .modal-content-custom {
            max-width: 540px;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
        }

        .model-modal-intro {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.9rem 1rem;
            margin-bottom: 1.15rem;
            border: 1px solid var(--border-light);
            border-radius: 9px;
            background: var(--bg-tertiary);
        }

        .model-modal-intro-icon {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border-radius: 9px;
            background: rgba(29, 91, 66, 0.1);
            color: var(--bs-primary);
        }

        .model-modal-intro strong {
            display: block;
            color: var(--text-primary);
            font-size: 0.82rem;
            font-weight: 700;
        }

        .model-modal-intro span {
            display: block;
            margin-top: 0.15rem;
            color: var(--text-muted);
            font-size: 0.72rem;
            line-height: 1.5;
        }

        .model-modal-meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .model-modal-meta div {
            padding: 0.6rem 0.7rem;
            border: 1px solid var(--border-light);
            border-radius: 7px;
            background: var(--bg-secondary);
            min-width: 0;
        }

        .model-modal-meta span {
            display: block;
            color: var(--text-muted);
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .model-modal-meta strong {
            display: block;
            margin-top: 0.15rem;
            color: var(--text-primary);
            font-size: 0.8rem;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        @media (max-width: 1199.98px) {
            .model-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .model-filter {
                grid-template-columns: minmax(200px, 1fr) 1fr 1fr auto auto;
            }
        }

        @media (max-width: 991.98px) {
            .model-filter {
                grid-template-columns: 1fr 1fr;
            }

            .model-search {
                grid-column: 1 / -1;
            }

            .model-filter-submit {
                grid-column: 1;
            }

            .model-filter-reset {
                grid-column: 2;
                width: 100%;
            }
        }

        @media (max-width: 767.98px) {
            .model-stats {
                gap: 0.65rem;
            }

            .model-stat {
                padding: 0.85rem 0.9rem 1.1rem;
            }

            .model-stat-icon {
                width: 36px;
                height: 36px;
                font-size: 0.85rem;
            }

            .model-stat-copy strong {
                font-size: 1.15rem;
            }

            .model-stat-copy span {
                font-size: 0.68rem;
            }

            .model-stat-copy small {
                font-size: 0.62rem;
                white-space: normal;
                overflow: visible;
            }

            .model-panel-head {
                padding: 1rem;
            }

            /* Tabel menjadi kartu di layar kecil */
            .model-table {
                min-width: 0;
            }

            .model-table thead {
                display: none;
            }

            .model-table,
            .model-table tbody,
            .model-table tr,
            .model-table td {
                display: block;
                width: 100%;
            }

            .model-table tbody tr {
                padding: 1rem;
                border-bottom: 1px solid var(--border-light);
            }

            .model-table tbody tr:last-child {
                border-bottom: none;
            }

            .model-table tbody td {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 0.35rem 0;
                border: none;
                background: transparent;
            }

            .model-table tbody tr:hover td {
                background: transparent;
            }

            .model-table tbody tr.is-empty-row td:first-child {
                box-shadow: none;
            }

            .model-table tbody td:first-child {
                display: block;
                padding: 0 0 0.7rem;
            }

            .model-table tbody td:not(:first-child)::before {
                content: attr(data-label);
                color: var(--text-muted);
                font-size: 0.68rem;
                font-weight: 700;
                letter-spacing: 0.05em;
                text-transform: uppercase;
            }

            .model-table tbody td[colspan] {
                display: block;
            }

            .model-table tbody td[colspan]::before {
                content: none;
            }

            .model-table tbody td[data-label="Aksi"] {
                padding-top: 0.75rem;
            }

            .model-cell {
                min-width: 0;
            }

            .model-actions {
                flex-wrap: wrap;
            }

            .model-pagination {
                justify-content: center;
                padding: 0.9rem;
            }
        }

        @media (max-width: 575.98px) {
            .model-filter {
                grid-template-columns: minmax(0, 1fr) 42px;
            }

            .model-search,
            .model-select {
                grid-column: 1 / -1;
            }

            .model-filter-submit {
                grid-column: 1;
            }

            .model-filter-reset {
                grid-column: 2;
                width: 42px;
            }

            .model-modal .modal-content-custom {
                padding: 1.1rem;
                max-height: calc(100vh - 1rem);
            }

            .model-modal-meta {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
@endpush

<x-layouts.app title="Model Mobil - RodaTa Admin">
    <x-layout.page-header title="Model Mobil" subtitle="Kelola model yang terhubung dengan merek dan unit mobil">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" :href="route('admin.car-models', $baseQuery + ['create' => 1])">
                Tambah Model
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Ringkasan model --}}
    <div class="model-stats">
        @foreach ($statCards as $card)
            <a href="{{ $card['href'] }}"
               class="model-stat model-stat-{{ $card['tone'] }} {{ $coverage === $card['coverage'] ? 'is-active' : '' }}">
                <span class="model-stat-icon">
                    <i class="{{ $card['icon'] }}"></i>
                </span>
                <span class="model-stat-copy">
                    <strong>{{ number_format($card['value'], 0, ',', '.') }}</strong>
                    <span>{{ $card['label'] }}</span>
                    <small>{{ $card['meta'] }}</small>
                </span>
                <span class="model-stat-progress" aria-hidden="true">
                    <span style="width: {{ $card['percentage'] }}%;"></span>
                </span>
            </a>
        @endforeach
    </div>

    {{-- Filter --}}
    <x-layout.modern-card class="model-filter-card">
        <form method="GET" action="{{ route('admin.car-models') }}" class="model-filter">
            @if ($coverage !== '')
                <input type="hidden" name="coverage" value="{{ $coverage }}">
            @endif

            <div class="model-search">
                <i class="fas fa-search"></i>
                <input type="search" id="q" name="q" value="{{ $search }}"
                       placeholder="Cari model, slug, atau merek..." aria-label="Cari model">
                @if ($search !== '')
                    <a href="{{ route('admin.car-models', array_filter(['brand' => $brandId ?: null, 'coverage' => $coverage, 'sort' => $sortQuery])) }}"
                       class="model-search-clear" aria-label="Hapus pencarian">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>

            <select id="brand" name="brand" class="model-select" aria-label="Filter merek">
                <option value="">Semua merek</option>
                @foreach ($brands as $brandOption)
                    <option value="{{ $brandOption->id }}" @selected($brandId === $brandOption->id)>
                        {{ $brandOption->name }} ({{ $brandOption->car_models_count }})
                    </option>
                @endforeach
            </select>

            <select id="sort" name="sort" class="model-select" aria-label="Urutkan model">
                @foreach ($sortOptions as $sortValue => $sortLabel)
                    <option value="{{ $sortValue }}" @selected($sort === $sortValue)>
                        {{ $sortLabel }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="model-filter-submit">
                <i class="fas fa-filter"></i>
                Tampilkan
            </button>

            <a href="{{ route('admin.car-models') }}" class="model-filter-reset" title="Reset filter" aria-label="Reset filter">
                <i class="fas fa-rotate-left"></i>
            </a>
        </form>

        @if ($hasFilters)
            <div class="model-chips">
                <span class="model-chips-label">Filter aktif:</span>

                @if ($search !== '')
                    <span class="model-chip">
                        <i class="fas fa-search"></i>
                        “{{ $search }}”
                        <a href="{{ route('admin.car-models', array_filter(['brand' => $brandId ?: null, 'coverage' => $coverage, 'sort' => $sortQuery])) }}"
                           aria-label="Hapus filter pencarian">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if ($brandName)
                    <span class="model-chip">
                        <i class="fas fa-trademark"></i>
                        {{ $brandName }}
                        <a href="{{ route('admin.car-models', array_filter(['q' => $search, 'coverage' => $coverage, 'sort' => $sortQuery])) }}"
                           aria-label="Hapus filter merek">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if ($coverageLabel)
                    <span class="model-chip">
                        <i class="fas fa-car-side"></i>
                        {{ $coverageLabel }}
                        <a href="{{ route('admin.car-models', array_filter(['q' => $search, 'brand' => $brandId ?: null, 'sort' => $sortQuery])) }}"
                           aria-label="Hapus filter ketersediaan mobil">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                <a href="{{ route('admin.car-models') }}" class="model-chips-clear">Bersihkan semua</a>
            </div>
        @endif
    </x-layout.modern-card>

    {{-- Daftar model --}}
    <x-layout.modern-card :padding="false" class="model-panel">
        <div class="model-panel-head">
            <div>
                <h5>Daftar Model</h5>
                <p>
                    @if ($carModels->total() > 0)
                        Menampilkan {{ $carModels->firstItem() }}–{{ $carModels->lastItem() }} dari
                        {{ number_format($carModels->total(), 0, ',', '.') }} model
                    @elseif ($hasFilters)
                        Tidak ada model yang cocok dengan filter
                    @else
                        Belum ada model terdaftar
                    @endif
                </p>
            </div>
            <span class="model-sort">
                <i class="fas fa-arrow-down-wide-short"></i>
                {{ $sortOptions[$sort] }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table model-table">
                <thead>
                    <tr>
                        <th>Model</th>
                        <th>Merek</th>
                        <th>Mobil</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($carModels as $carModel)
                        @php
                            $brandLogo = $carModel->brand?->logoUrl();
                            $modelInitials = $initialsFor($carModel->name);
                        @endphp
                        <tr class="{{ $carModel->cars_count === 0 ? 'is-empty-row' : '' }}">
                            <td data-label="Model">
                                <div class="model-cell">
                                    <span class="model-mark">{{ $modelInitials }}</span>
                                    <div class="model-cell-copy">
                                        <div class="model-name">{{ $carModel->name }}</div>
                                        <span class="model-slug">/{{ $carModel->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Merek">
                                @if ($carModel->brand)
                                    <a href="{{ route('admin.car-models', ['brand' => $carModel->brand->id]) }}"
                                       class="model-brand" title="Lihat semua model {{ $carModel->brand->name }}">
                                        <span class="model-brand-mark">
                                            @if ($brandLogo)
                                                <img src="{{ $brandLogo }}" alt="Logo {{ $carModel->brand->name }}"
                                                     onerror="this.style.display='none'; this.parentElement.textContent='{{ $initialsFor($carModel->brand->name) }}';">
                                            @else
                                                {{ $initialsFor($carModel->brand->name) }}
                                            @endif
                                        </span>
                                        <span>{{ $carModel->brand->name }}</span>
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td data-label="Mobil">
                                @if ($carModel->cars_count > 0)
                                    <a href="{{ route('admin.cars', ['q' => $carModel->name]) }}"
                                       class="model-count" title="Lihat mobil {{ $carModel->name }}">
                                        <strong>{{ number_format($carModel->cars_count, 0, ',', '.') }}</strong>
                                        <span>mobil</span>
                                    </a>
                                @else
                                    <span class="model-count is-empty">
                                        <strong>0</strong>
                                        <span>mobil</span>
                                    </span>
                                @endif
                            </td>
                            <td data-label="Status">
                                @if ($carModel->cars_count > 0)
                                    <span class="model-health is-active">
                                        <i class="fas fa-circle-check"></i>
                                        Punya mobil
                                    </span>
                                @elseif ($carModel->all_cars_count > 0)
                                    <span class="model-health is-archived">
                                        <i class="fas fa-box-archive"></i>
                                        Hanya arsip
                                    </span>
                                @else
                                    <span class="model-health is-empty">
                                        <i class="fas fa-circle-exclamation"></i>
                                        Belum ada mobil
                                    </span>
                                @endif
                            </td>
                            <td data-label="Aksi">
                                <div class="model-actions">
                                    <a href="{{ route('admin.car-models', $baseQuery + ['edit' => $carModel->id]) }}"
                                       class="model-row-action" title="Edit {{ $carModel->name }}"
                                       aria-label="Edit {{ $carModel->name }}">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    @if ($carModel->all_cars_count > 0)
                                        <span class="model-row-action is-disabled"
                                              title="Hapus mobil terlebih dahulu"
                                              aria-label="Model masih digunakan mobil, tidak dapat dihapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </span>
                                    @else
                                        <a href="{{ route('admin.car-models', $baseQuery + ['delete' => $carModel->id]) }}"
                                           class="model-row-action is-danger" title="Hapus {{ $carModel->name }}"
                                           aria-label="Hapus {{ $carModel->name }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-ui.empty-state
                                    icon="fas fa-list"
                                    :title="$hasFilters ? 'Tidak ada model yang cocok' : 'Belum ada model'"
                                    :description="$hasFilters
                                        ? 'Coba ubah kata kunci atau bersihkan filter untuk melihat model lainnya.'
                                        : 'Tambahkan model pertama untuk mulai mengelola stok mobil.'"
                                    :action-label="$hasFilters ? 'Bersihkan Filter' : 'Tambah Model'"
                                    :action-href="$hasFilters ? route('admin.car-models') : route('admin.car-models', ['create' => 1])"
                                    :action-icon="$hasFilters ? 'fas fa-rotate-left' : 'fas fa-plus'"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($carModels->getCollection()->contains(fn ($carModel) => $carModel->all_cars_count > 0))
            <div class="model-panel-note">
                <i class="fas fa-circle-info"></i>
                Model yang masih digunakan mobil tidak dapat dihapus.
            </div>
        @endif

        @if ($carModels->hasPages())
            <div class="model-pagination">
                {{ $carModels->links() }}
            </div>
        @endif
    </x-layout.modern-card>

    @if ($showCreateModal && ! $editingCarModel && ! $deletingCarModel)
        <x-layout.modal-form
            title="Tambah Model"
            :action="route('admin.car-models.store')"
            :close-url="$closeUrl"
            submit-label="Simpan Model"
            close-label="Batal"
            class="model-modal"
        >
            <input type="hidden" name="return_q" value="{{ $search }}">
            <input type="hidden" name="return_brand" value="{{ $brandId ?: '' }}">
            <input type="hidden" name="return_coverage" value="{{ $coverage }}">
            <input type="hidden" name="return_sort" value="{{ $sortQuery }}">
            <input type="hidden" name="return_page" value="{{ request('page') }}">

            <div class="model-modal-intro">
                <span class="model-modal-intro-icon">
                    <i class="fas fa-list"></i>
                </span>
                <div>
                    <strong>Tambah model baru</strong>
                    <span>Model akan tersedia saat menambahkan unit mobil pada merek terkait.</span>
                </div>
            </div>

            <x-form.select
                name="brand_id"
                label="Merek"
                :options="$brandOptions"
                :selected="old('brand_id', $brandId ?: null)"
                placeholder="Pilih merek"
                required
                :error="$errors->first('brand_id')"
            />

            <x-form.input
                name="name"
                label="Nama Model"
                :value="old('name')"
                placeholder="Avanza"
                hint="Gunakan nama model tanpa merek, contoh: Avanza, Xpander, HR-V."
                required
                :error="$errors->first('name')"
            />
        </x-layout.modal-form>
    @endif

    @if ($editingCarModel)
        <x-layout.modal-form
            title="Edit Model"
            :action="route('admin.car-models.update', $editingCarModel)"
            method="PUT"
            :close-url="$closeUrl"
            submit-label="Simpan Perubahan"
            close-label="Batal"
            class="model-modal"
        >
            <input type="hidden" name="return_q" value="{{ $search }}">
            <input type="hidden" name="return_brand" value="{{ $brandId ?: '' }}">
            <input type="hidden" name="return_coverage" value="{{ $coverage }}">
            <input type="hidden" name="return_sort" value="{{ $sortQuery }}">
            <input type="hidden" name="return_page" value="{{ request('page') }}">

            <div class="model-modal-intro">
                <span class="model-modal-intro-icon">
                    <i class="fas fa-pen"></i>
                </span>
                <div>
                    <strong>Perbarui data model</strong>
                    <span>Perubahan nama akan memperbarui slug dan tampilan di katalog.</span>
                </div>
            </div>

            <div class="model-modal-meta">
                <div>
                    <span>Merek</span>
                    <strong>{{ $editingCarModel->brand?->name ?? '—' }}</strong>
                </div>
                <div>
                    <span>Slug</span>
                    <strong>/{{ $editingCarModel->slug }}</strong>
                </div>
                <div>
                    <span>Mobil</span>
                    <strong>{{ number_format($editingCarModel->cars_count, 0, ',', '.') }}</strong>
                </div>
            </div>

            <x-form.select
                name="brand_id"
                label="Merek"
                :options="$brandOptions"
                :selected="old('brand_id', $editingCarModel->brand_id)"
                placeholder="Pilih merek"
                required
                :error="$errors->first('brand_id')"
            />

            <x-form.input
                name="name"
                label="Nama Model"
                :value="old('name', $editingCarModel->name)"
                placeholder="Avanza"
                required
                :error="$errors->first('name')"
            />
        </x-layout.modal-form>
    @endif

    @if ($deletingCarModel && ! $editingCarModel)
        <x-ui.confirm-modal
            :show="true"
            title="Hapus Model"
            :message="$deletingCarModel->all_cars_count > 0
                ? 'Model '.$deletingCarModel->name.' masih digunakan oleh '.$deletingCarModel->all_cars_count.' mobil. Pindahkan atau hapus mobil tersebut terlebih dahulu.'
                : 'Hapus model '.$deletingCarModel->name.'? Tindakan ini tidak dapat dibatalkan.'"
            confirm-text="Hapus Model"
            cancel-text="Batal"
            :action="route('admin.car-models.destroy', $deletingCarModel)"
            method="DELETE"
            :cancel-url="$closeUrl"
            variant="danger"
            icon="fas fa-trash-can"
            :form-data="[
                'return_q' => $search,
                'return_brand' => $brandId ?: '',
                'return_coverage' => $coverage,
                'return_sort' => $sortQuery,
                'return_page' => request('page'),
            ]"
        />
    @endif
</x-layouts.app>
