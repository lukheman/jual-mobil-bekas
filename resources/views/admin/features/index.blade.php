@php
    $sortQuery = $sort !== 'name_asc' ? $sort : null;

    $baseQuery = array_filter([
        'q' => $search,
        'coverage' => $coverage,
        'sort' => $sortQuery,
        'page' => request('page'),
    ], fn ($value) => $value !== null && $value !== '');

    $closeUrl = route('admin.features', $baseQuery);

    $coverageLabels = [
        'used' => 'Digunakan',
        'unused' => 'Belum Digunakan',
    ];

    $coverageLabel = $coverageLabels[$coverage] ?? null;
    $hasFilters = $search !== '' || $coverage !== '';

    $percentage = fn (int $value, int $total): int => $total > 0
        ? (int) round($value / $total * 100)
        : 0;

    $statCards = [
        [
            'key' => 'total',
            'label' => 'Total Fitur',
            'meta' => 'Seluruh fitur terdaftar',
            'icon' => 'fas fa-sliders',
            'tone' => 'primary',
            'value' => $stats['total'],
            'percentage' => $stats['total'] > 0 ? 100 : 0,
            'coverage' => '',
            'href' => route('admin.features', array_filter(['q' => $search, 'sort' => $sortQuery])),
        ],
        [
            'key' => 'used',
            'label' => 'Digunakan',
            'meta' => 'Dipakai minimal satu mobil',
            'icon' => 'fas fa-circle-check',
            'tone' => 'success',
            'value' => $stats['used'],
            'percentage' => $percentage($stats['used'], $stats['total']),
            'coverage' => 'used',
            'href' => route('admin.features', array_filter(['q' => $search, 'sort' => $sortQuery, 'coverage' => 'used'])),
        ],
        [
            'key' => 'unused',
            'label' => 'Belum Digunakan',
            'meta' => 'Belum dipakai unit mana pun',
            'icon' => 'fas fa-circle-minus',
            'tone' => 'warning',
            'value' => $stats['unused'],
            'percentage' => $percentage($stats['unused'], $stats['total']),
            'coverage' => 'unused',
            'href' => route('admin.features', array_filter(['q' => $search, 'sort' => $sortQuery, 'coverage' => 'unused'])),
        ],
        [
            'key' => 'assignments',
            'label' => 'Total Pemasangan',
            'meta' => 'Di seluruh unit mobil',
            'icon' => 'fas fa-link',
            'tone' => 'info',
            'value' => $stats['assignments'],
            'percentage' => $stats['assignments'] > 0 ? 100 : 0,
            'coverage' => 'assignments',
            'href' => route('admin.features', array_filter(['q' => $search, 'coverage' => $coverage, 'sort' => 'cars_desc'])),
        ],
    ];
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

        /* Ringkasan fitur */
        .feature-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .feature-stat {
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

        .feature-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(23, 53, 43, 0.12);
            border-color: var(--border-color);
            color: inherit;
        }

        .feature-stat.is-active {
            border-color: var(--stat-color);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--stat-color) 14%, transparent);
        }

        .feature-stat-primary { --stat-color: var(--bs-primary); --stat-soft: rgba(29, 91, 66, 0.1); }
        .feature-stat-success { --stat-color: var(--bs-success); --stat-soft: rgba(77, 138, 99, 0.12); }
        .feature-stat-warning { --stat-color: var(--bs-warning); --stat-soft: rgba(189, 138, 56, 0.14); }
        .feature-stat-info { --stat-color: var(--bs-info); --stat-soft: rgba(79, 130, 144, 0.12); }

        .feature-stat-icon {
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

        .feature-stat-copy {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
            min-width: 0;
        }

        .feature-stat-copy strong {
            color: var(--text-primary);
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .feature-stat-copy span {
            color: var(--text-muted);
            font-size: 0.76rem;
            font-weight: 500;
            margin-top: 0.2rem;
        }

        .feature-stat-copy small {
            color: var(--text-muted);
            font-size: 0.65rem;
            margin-top: 0.12rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .feature-stat-progress {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            background: var(--bg-tertiary);
        }

        .feature-stat-progress span {
            display: block;
            height: 100%;
            background: var(--stat-color);
            transition: width 0.3s ease;
        }

        /* Filter */
        .feature-filter-card {
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .feature-filter-card:hover,
        .feature-panel:hover {
            transform: none;
        }

        .feature-filter {
            display: grid;
            grid-template-columns: minmax(220px, 1fr) 190px 210px auto auto;
            gap: 0.65rem;
            align-items: center;
        }

        .feature-search {
            position: relative;
            display: flex;
            align-items: center;
        }

        .feature-search > i {
            position: absolute;
            left: 0.9rem;
            color: var(--text-muted);
            font-size: 0.85rem;
            pointer-events: none;
        }

        .feature-search input {
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

        .feature-search input:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(29, 91, 66, 0.1);
        }

        .feature-search input::placeholder {
            color: var(--text-muted);
        }

        .feature-search-clear {
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

        .feature-search-clear:hover {
            background: var(--hover-bg);
            color: var(--bs-danger);
        }

        .feature-select {
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

        .feature-select:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(29, 91, 66, 0.1);
        }

        .feature-filter-submit {
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

        .feature-filter-submit:hover {
            background: #164934;
            transform: translateY(-1px);
        }

        .feature-filter-reset {
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

        .feature-filter-reset:hover {
            border-color: var(--bs-danger);
            color: var(--bs-danger);
        }

        .feature-chips {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.45rem;
            margin-top: 0.85rem;
            padding-top: 0.85rem;
            border-top: 1px solid var(--border-light);
        }

        .feature-chips-label {
            color: var(--text-muted);
            font-size: 0.74rem;
            font-weight: 600;
        }

        .feature-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.55rem;
            border-radius: 999px;
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            font-size: 0.74rem;
        }

        .feature-chip > i {
            color: var(--bs-primary);
            font-size: 0.65rem;
        }

        .feature-chip a {
            display: grid;
            place-items: center;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            color: var(--text-muted);
        }

        .feature-chip a:hover {
            background: var(--bg-secondary);
            color: var(--bs-danger);
        }

        .feature-chips-clear {
            margin-left: 0.25rem;
            color: var(--bs-danger);
            font-size: 0.74rem;
            font-weight: 600;
            text-decoration: none;
        }

        .feature-chips-clear:hover {
            text-decoration: underline;
        }

        /* Panel daftar */
        .feature-panel {
            padding: 0;
            overflow: hidden;
        }

        .feature-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.15rem 1.35rem;
            border-bottom: 1px solid var(--border-light);
        }

        .feature-panel-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 0;
        }

        .feature-panel-icon {
            display: grid;
            place-items: center;
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            border-radius: 10px;
            background: rgba(29, 91, 66, 0.1);
            color: var(--bs-primary);
            font-size: 0.95rem;
        }

        .feature-panel-head h5 {
            margin: 0 0 0.15rem;
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 700;
        }

        .feature-panel-head p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.76rem;
        }

        .feature-sort {
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

        .feature-table {
            margin: 0;
            min-width: 720px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .feature-table thead th {
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

        .feature-table tbody td {
            padding: 0.95rem 1.1rem;
            border: none;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .feature-table tbody tr:last-child td {
            border-bottom: none;
        }

        .feature-table tbody tr:hover td {
            background: var(--hover-bg);
        }

        .feature-table tbody tr.is-unused td:first-child {
            box-shadow: inset 3px 0 0 var(--bs-warning);
        }

        .feature-name {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            min-width: 220px;
        }

        .feature-icon {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border-radius: 9px;
            color: var(--bs-primary);
            background: rgba(29, 91, 66, 0.1);
            font-size: 0.78rem;
        }

        .feature-cell-copy {
            min-width: 0;
        }

        .feature-cell-copy strong {
            display: block;
            color: var(--text-primary);
            font-size: 0.9rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .feature-cell-copy span {
            display: block;
            margin-top: 0.15rem;
            color: var(--text-muted);
            font-size: 0.7rem;
        }

        .feature-usage {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            width: 140px;
        }

        .feature-usage-head {
            display: inline-flex;
            align-items: baseline;
            gap: 0.3rem;
        }

        .feature-usage-head strong {
            color: var(--text-primary);
            font-size: 0.92rem;
            font-weight: 700;
            line-height: 1;
        }

        .feature-usage-head span {
            color: var(--text-muted);
            font-size: 0.7rem;
        }

        .feature-usage-track {
            height: 5px;
            border-radius: 999px;
            background: var(--bg-tertiary);
            overflow: hidden;
        }

        .feature-usage-track span {
            display: block;
            height: 100%;
            border-radius: 999px;
            background: var(--bs-primary);
            transition: width 0.3s ease;
        }

        .feature-usage.is-empty .feature-usage-head strong,
        .feature-usage.is-empty .feature-usage-head span {
            color: var(--text-muted);
            font-weight: 500;
        }

        .feature-health {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.28rem 0.58rem;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .feature-health.is-used {
            background: rgba(77, 138, 99, 0.14);
            color: var(--bs-success);
        }

        .feature-health.is-unused {
            background: rgba(189, 138, 56, 0.14);
            color: var(--bs-warning);
        }

        .feature-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.35rem;
        }

        .feature-row-action {
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

        .feature-row-action:hover {
            border-color: var(--bs-primary);
            background: var(--hover-bg);
            color: var(--bs-primary);
        }

        .feature-row-action.is-danger:hover {
            border-color: var(--bs-danger);
            background: rgba(185, 88, 76, 0.06);
            color: var(--bs-danger);
        }

        .feature-pagination {
            display: flex;
            justify-content: flex-end;
            padding: 1rem 1.35rem;
            border-top: 1px solid var(--border-light);
            background: var(--bg-secondary);
        }

        .feature-panel-note {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.7rem 1.35rem;
            border-top: 1px solid var(--border-light);
            background: var(--bg-secondary);
            color: var(--text-muted);
            font-size: 0.72rem;
        }

        .feature-panel-note i {
            color: var(--bs-info);
        }

        /* Modal fitur */
        .feature-modal .modal-content-custom {
            max-width: 520px;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
        }

        .feature-modal-intro {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.9rem 1rem;
            margin-bottom: 1.15rem;
            border: 1px solid var(--border-light);
            border-radius: 9px;
            background: var(--bg-tertiary);
        }

        .feature-modal-intro-icon {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border-radius: 9px;
            background: rgba(29, 91, 66, 0.1);
            color: var(--bs-primary);
        }

        .feature-modal-intro strong {
            display: block;
            color: var(--text-primary);
            font-size: 0.82rem;
            font-weight: 700;
        }

        .feature-modal-intro span {
            display: block;
            margin-top: 0.15rem;
            color: var(--text-muted);
            font-size: 0.72rem;
            line-height: 1.5;
        }

        .feature-modal-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .feature-modal-meta div {
            padding: 0.6rem 0.7rem;
            border: 1px solid var(--border-light);
            border-radius: 7px;
            background: var(--bg-secondary);
            min-width: 0;
        }

        .feature-modal-meta span {
            display: block;
            color: var(--text-muted);
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .feature-modal-meta strong {
            display: block;
            margin-top: 0.15rem;
            color: var(--text-primary);
            font-size: 0.8rem;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        @media (max-width: 1199.98px) {
            .feature-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .feature-filter {
                grid-template-columns: minmax(200px, 1fr) 1fr 1fr auto auto;
            }
        }

        @media (max-width: 991.98px) {
            .feature-filter {
                grid-template-columns: 1fr 1fr;
            }

            .feature-search {
                grid-column: 1 / -1;
            }

            .feature-filter-submit {
                grid-column: 1;
            }

            .feature-filter-reset {
                grid-column: 2;
                width: 100%;
            }
        }

        @media (max-width: 767.98px) {
            .feature-stats {
                gap: 0.65rem;
            }

            .feature-stat {
                padding: 0.85rem 0.9rem 1.1rem;
            }

            .feature-stat-icon {
                width: 36px;
                height: 36px;
                font-size: 0.85rem;
            }

            .feature-stat-copy strong {
                font-size: 1.15rem;
            }

            .feature-stat-copy span {
                font-size: 0.68rem;
            }

            .feature-stat-copy small {
                font-size: 0.62rem;
                white-space: normal;
                overflow: visible;
            }

            .feature-panel-head {
                padding: 1rem;
            }

            /* Tabel menjadi kartu di layar kecil */
            .feature-table {
                min-width: 0;
            }

            .feature-table thead {
                display: none;
            }

            .feature-table,
            .feature-table tbody,
            .feature-table tr,
            .feature-table td {
                display: block;
                width: 100%;
            }

            .feature-table tbody tr {
                padding: 1rem;
                border-bottom: 1px solid var(--border-light);
            }

            .feature-table tbody tr:last-child {
                border-bottom: none;
            }

            .feature-table tbody td {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 0.35rem 0;
                border: none;
                background: transparent;
            }

            .feature-table tbody tr:hover td {
                background: transparent;
            }

            .feature-table tbody tr.is-unused td:first-child {
                box-shadow: none;
            }

            .feature-table tbody td:first-child {
                display: block;
                padding: 0 0 0.7rem;
            }

            .feature-table tbody td:not(:first-child)::before {
                content: attr(data-label);
                color: var(--text-muted);
                font-size: 0.68rem;
                font-weight: 700;
                letter-spacing: 0.05em;
                text-transform: uppercase;
            }

            .feature-table tbody td[colspan] {
                display: block;
            }

            .feature-table tbody td[colspan]::before {
                content: none;
            }

            .feature-table tbody td[data-label="Aksi"] {
                padding-top: 0.75rem;
            }

            .feature-name {
                min-width: 0;
            }

            .feature-usage {
                width: 160px;
            }

            .feature-actions {
                flex-wrap: wrap;
            }

            .feature-pagination {
                justify-content: center;
                padding: 0.9rem;
            }
        }

        @media (max-width: 575.98px) {
            .feature-filter {
                grid-template-columns: minmax(0, 1fr) 42px;
            }

            .feature-search,
            .feature-select {
                grid-column: 1 / -1;
            }

            .feature-filter-submit {
                grid-column: 1;
            }

            .feature-filter-reset {
                grid-column: 2;
                width: 42px;
            }

            .feature-modal .modal-content-custom {
                padding: 1.1rem;
                max-height: calc(100vh - 1rem);
            }
        }
    </style>
@endpush

<x-layouts.app title="Fitur - RodaTa Admin">
    <x-layout.page-header title="Fitur Mobil" subtitle="Kelola fasilitas dan keunggulan yang tampil pada unit mobil">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" :href="route('admin.features', $baseQuery + ['create' => 1])">
                Tambah Fitur
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Ringkasan fitur --}}
    <div class="feature-stats">
        @foreach ($statCards as $card)
            <a href="{{ $card['href'] }}"
               class="feature-stat feature-stat-{{ $card['tone'] }} {{ $coverage === $card['coverage'] ? 'is-active' : '' }}">
                <span class="feature-stat-icon">
                    <i class="{{ $card['icon'] }}"></i>
                </span>
                <span class="feature-stat-copy">
                    <strong>{{ number_format($card['value'], 0, ',', '.') }}</strong>
                    <span>{{ $card['label'] }}</span>
                    <small>{{ $card['meta'] }}</small>
                </span>
                <span class="feature-stat-progress" aria-hidden="true">
                    <span style="width: {{ $card['percentage'] }}%;"></span>
                </span>
            </a>
        @endforeach
    </div>

    {{-- Filter --}}
    <x-layout.modern-card class="feature-filter-card">
        <form method="GET" action="{{ route('admin.features') }}" class="feature-filter">
            <div class="feature-search">
                <i class="fas fa-search"></i>
                <input type="search" id="q" name="q" value="{{ $search }}"
                       placeholder="Cari nama fitur..." aria-label="Cari fitur">
                @if ($search !== '')
                    <a href="{{ route('admin.features', array_filter(['coverage' => $coverage, 'sort' => $sortQuery])) }}"
                       class="feature-search-clear" aria-label="Hapus pencarian">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>

            <select id="coverage" name="coverage" class="feature-select" aria-label="Filter penggunaan fitur">
                <option value="">Semua penggunaan</option>
                <option value="used" @selected($coverage === 'used')>Digunakan</option>
                <option value="unused" @selected($coverage === 'unused')>Belum digunakan</option>
            </select>

            <select id="sort" name="sort" class="feature-select" aria-label="Urutkan fitur">
                @foreach ($sortOptions as $sortValue => $sortLabel)
                    <option value="{{ $sortValue }}" @selected($sort === $sortValue)>
                        {{ $sortLabel }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="feature-filter-submit">
                <i class="fas fa-filter"></i>
                Tampilkan
            </button>

            <a href="{{ route('admin.features') }}" class="feature-filter-reset" title="Reset filter" aria-label="Reset filter">
                <i class="fas fa-rotate-left"></i>
            </a>
        </form>

        @if ($hasFilters)
            <div class="feature-chips">
                <span class="feature-chips-label">Filter aktif:</span>

                @if ($search !== '')
                    <span class="feature-chip">
                        <i class="fas fa-search"></i>
                        “{{ $search }}”
                        <a href="{{ route('admin.features', array_filter(['coverage' => $coverage, 'sort' => $sortQuery])) }}"
                           aria-label="Hapus filter pencarian">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if ($coverageLabel)
                    <span class="feature-chip">
                        <i class="fas fa-link"></i>
                        {{ $coverageLabel }}
                        <a href="{{ route('admin.features', array_filter(['q' => $search, 'sort' => $sortQuery])) }}"
                           aria-label="Hapus filter penggunaan">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                <a href="{{ route('admin.features') }}" class="feature-chips-clear">Bersihkan semua</a>
            </div>
        @endif
    </x-layout.modern-card>

    {{-- Daftar fitur --}}
    <x-layout.modern-card :padding="false" class="feature-panel">
        <div class="feature-panel-head">
            <div class="feature-panel-title">
                <span class="feature-panel-icon">
                    <i class="fas fa-sliders"></i>
                </span>
                <div>
                    <h5>Daftar Fitur</h5>
                    <p>
                        @if ($features->total() > 0)
                            Menampilkan {{ $features->firstItem() }}–{{ $features->lastItem() }} dari
                            {{ number_format($features->total(), 0, ',', '.') }} fitur
                        @elseif ($hasFilters)
                            Tidak ada fitur yang cocok dengan filter
                        @else
                            Belum ada fitur terdaftar
                        @endif
                    </p>
                </div>
            </div>
            <span class="feature-sort">
                <i class="fas fa-arrow-down-wide-short"></i>
                {{ $sortOptions[$sort] }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table feature-table">
                <thead>
                    <tr>
                        <th>Fitur</th>
                        <th>Penggunaan</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($features as $feature)
                        <tr class="{{ $feature->cars_count > 0 ? '' : 'is-unused' }}">
                            <td data-label="Fitur">
                                <div class="feature-name">
                                    <span class="feature-icon"><i class="fas fa-check"></i></span>
                                    <div class="feature-cell-copy">
                                        <strong>{{ $feature->name }}</strong>
                                        <span>ID #{{ str_pad((string) $feature->id, 3, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Penggunaan">
                                <div class="feature-usage {{ $feature->cars_count > 0 ? '' : 'is-empty' }}">
                                    <div class="feature-usage-head">
                                        <strong>{{ number_format($feature->cars_count, 0, ',', '.') }}</strong>
                                        <span>mobil</span>
                                    </div>
                                    <div class="feature-usage-track" aria-hidden="true">
                                        <span style="width: {{ $feature->cars_count > 0 ? max(6, (int) round($feature->cars_count / $maxUsage * 100)) : 0 }}%;"></span>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Status">
                                @if ($feature->cars_count > 0)
                                    <span class="feature-health is-used">
                                        <i class="fas fa-circle-check"></i>
                                        Dipakai
                                    </span>
                                @else
                                    <span class="feature-health is-unused">
                                        <i class="fas fa-circle-minus"></i>
                                        Belum dipakai
                                    </span>
                                @endif
                            </td>
                            <td data-label="Aksi">
                                <div class="feature-actions">
                                    <a href="{{ route('admin.features', $baseQuery + ['edit' => $feature->id]) }}"
                                       class="feature-row-action" title="Edit {{ $feature->name }}"
                                       aria-label="Edit {{ $feature->name }}">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="{{ route('admin.features', $baseQuery + ['delete' => $feature->id]) }}"
                                       class="feature-row-action is-danger" title="Hapus {{ $feature->name }}"
                                       aria-label="Hapus {{ $feature->name }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-ui.empty-state
                                    icon="fas fa-list-check"
                                    :title="$hasFilters ? 'Tidak ada fitur yang cocok' : 'Belum ada fitur'"
                                    :description="$hasFilters
                                        ? 'Coba ubah kata kunci atau bersihkan filter untuk melihat fitur lainnya.'
                                        : 'Tambahkan fitur untuk memperkaya informasi setiap unit mobil.'"
                                    :action-label="$hasFilters ? 'Bersihkan Filter' : 'Tambah Fitur'"
                                    :action-href="$hasFilters ? route('admin.features') : route('admin.features', ['create' => 1])"
                                    :action-icon="$hasFilters ? 'fas fa-rotate-left' : 'fas fa-plus'"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($features->getCollection()->contains(fn ($feature) => $feature->cars_count > 0))
            <div class="feature-panel-note">
                <i class="fas fa-circle-info"></i>
                Menghapus fitur akan melepaskannya dari semua mobil yang memakainya.
            </div>
        @endif

        @if ($features->hasPages())
            <div class="feature-pagination">
                {{ $features->links() }}
            </div>
        @endif
    </x-layout.modern-card>

    @if ($showCreateModal && ! $editingFeature && ! $deletingFeature)
        <x-layout.modal-form
            title="Tambah Fitur"
            :action="route('admin.features.store')"
            :close-url="$closeUrl"
            submit-label="Simpan Fitur"
            close-label="Batal"
            class="feature-modal"
        >
            <input type="hidden" name="return_q" value="{{ $search }}">
            <input type="hidden" name="return_coverage" value="{{ $coverage }}">
            <input type="hidden" name="return_sort" value="{{ $sortQuery }}">
            <input type="hidden" name="return_page" value="{{ request('page') }}">

            <div class="feature-modal-intro">
                <span class="feature-modal-intro-icon">
                    <i class="fas fa-sliders"></i>
                </span>
                <div>
                    <strong>Tambah fitur baru</strong>
                    <span>Fitur akan tersedia sebagai pilihan saat menambahkan atau mengedit unit mobil.</span>
                </div>
            </div>

            <x-form.input
                name="name"
                label="Nama Fitur"
                :value="old('name')"
                placeholder="Bebas banjir"
                hint="Gunakan nama singkat yang mudah dipahami, contoh: Kamera belakang, Servis rutin."
                required
                :error="$errors->first('name')"
            />
        </x-layout.modal-form>
    @endif

    @if ($editingFeature)
        <x-layout.modal-form
            title="Edit Fitur"
            :action="route('admin.features.update', $editingFeature)"
            method="PUT"
            :close-url="$closeUrl"
            submit-label="Simpan Perubahan"
            close-label="Batal"
            class="feature-modal"
        >
            <input type="hidden" name="return_q" value="{{ $search }}">
            <input type="hidden" name="return_coverage" value="{{ $coverage }}">
            <input type="hidden" name="return_sort" value="{{ $sortQuery }}">
            <input type="hidden" name="return_page" value="{{ request('page') }}">

            <div class="feature-modal-intro">
                <span class="feature-modal-intro-icon">
                    <i class="fas fa-pen"></i>
                </span>
                <div>
                    <strong>Perbarui data fitur</strong>
                    <span>Perubahan nama akan langsung terlihat pada semua mobil yang memakai fitur ini.</span>
                </div>
            </div>

            <div class="feature-modal-meta">
                <div>
                    <span>Digunakan pada</span>
                    <strong>{{ number_format($editingFeature->cars_count, 0, ',', '.') }} mobil</strong>
                </div>
                <div>
                    <span>Status</span>
                    <strong>{{ $editingFeature->cars_count > 0 ? 'Dipakai' : 'Belum dipakai' }}</strong>
                </div>
            </div>

            <x-form.input
                name="name"
                label="Nama Fitur"
                :value="old('name', $editingFeature->name)"
                placeholder="Bebas banjir"
                required
                :error="$errors->first('name')"
            />
        </x-layout.modal-form>
    @endif

    @if ($deletingFeature && ! $editingFeature)
        <x-ui.confirm-modal
            :show="true"
            title="Hapus Fitur"
            :message="$deletingFeature->cars_count > 0
                ? 'Fitur '.$deletingFeature->name.' sedang dipakai oleh '.$deletingFeature->cars_count.' mobil. Fitur akan dilepas dari semua mobil tersebut.'
                : 'Hapus fitur '.$deletingFeature->name.'? Tindakan ini tidak dapat dibatalkan.'"
            confirm-text="Hapus Fitur"
            cancel-text="Batal"
            :action="route('admin.features.destroy', $deletingFeature)"
            method="DELETE"
            :cancel-url="$closeUrl"
            variant="danger"
            icon="fas fa-trash-can"
            :form-data="[
                'return_q' => $search,
                'return_coverage' => $coverage,
                'return_sort' => $sortQuery,
                'return_page' => request('page'),
            ]"
        />
    @endif
</x-layouts.app>
