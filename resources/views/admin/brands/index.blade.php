@php
    $sortQuery = $sort !== 'name_asc' ? $sort : null;

    $baseQuery = array_filter([
        'q' => $search,
        'coverage' => $coverage,
        'sort' => $sortQuery,
        'page' => request('page'),
    ], fn ($value) => $value !== null && $value !== '');

    $closeUrl = route('admin.brands', $baseQuery);

    $coverageLabels = [
        'with_models' => 'Memiliki Model',
        'without_models' => 'Belum Ada Model',
        'with_cars' => 'Memiliki Mobil',
    ];

    $coverageLabel = $coverageLabels[$coverage] ?? null;
    $hasFilters = $search !== '' || $coverage !== '';

    $percentage = fn (int $value): int => $stats['total'] > 0
        ? (int) round($value / $stats['total'] * 100)
        : 0;

    $statCards = [
        [
            'key' => 'total',
            'label' => 'Total Merek',
            'meta' => 'Seluruh merek terdaftar',
            'icon' => 'fas fa-trademark',
            'tone' => 'primary',
            'value' => $stats['total'],
            'percentage' => $stats['total'] > 0 ? 100 : 0,
            'coverage' => '',
            'href' => route('admin.brands', array_filter(['q' => $search, 'sort' => $sortQuery])),
        ],
        [
            'key' => 'with_models',
            'label' => 'Memiliki Model',
            'meta' => 'Siap dikelola',
            'icon' => 'fas fa-layer-group',
            'tone' => 'success',
            'value' => $stats['with_models'],
            'percentage' => $percentage($stats['with_models']),
            'coverage' => 'with_models',
            'href' => route('admin.brands', array_filter(['q' => $search, 'sort' => $sortQuery, 'coverage' => 'with_models'])),
        ],
        [
            'key' => 'without_models',
            'label' => 'Belum Ada Model',
            'meta' => 'Perlu dilengkapi',
            'icon' => 'fas fa-triangle-exclamation',
            'tone' => 'warning',
            'value' => $stats['without_models'],
            'percentage' => $percentage($stats['without_models']),
            'coverage' => 'without_models',
            'href' => route('admin.brands', array_filter(['q' => $search, 'sort' => $sortQuery, 'coverage' => 'without_models'])),
        ],
        [
            'key' => 'with_cars',
            'label' => 'Memiliki Mobil',
            'meta' => 'Sudah ada unit',
            'icon' => 'fas fa-car-side',
            'tone' => 'info',
            'value' => $stats['with_cars'],
            'percentage' => $percentage($stats['with_cars']),
            'coverage' => 'with_cars',
            'href' => route('admin.brands', array_filter(['q' => $search, 'sort' => $sortQuery, 'coverage' => 'with_cars'])),
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

        /* Ringkasan merek */
        .brand-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .brand-stat {
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

        .brand-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(23, 53, 43, 0.12);
            border-color: var(--border-color);
            color: inherit;
        }

        .brand-stat.is-active {
            border-color: var(--stat-color);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--stat-color) 14%, transparent);
        }

        .brand-stat-primary { --stat-color: var(--bs-primary); --stat-soft: rgba(29, 91, 66, 0.1); }
        .brand-stat-success { --stat-color: var(--bs-success); --stat-soft: rgba(77, 138, 99, 0.12); }
        .brand-stat-warning { --stat-color: var(--bs-warning); --stat-soft: rgba(189, 138, 56, 0.14); }
        .brand-stat-info { --stat-color: var(--bs-info); --stat-soft: rgba(79, 130, 144, 0.12); }

        .brand-stat-icon {
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

        .brand-stat-copy {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
            min-width: 0;
        }

        .brand-stat-copy strong {
            color: var(--text-primary);
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .brand-stat-copy span {
            color: var(--text-muted);
            font-size: 0.76rem;
            font-weight: 500;
            margin-top: 0.2rem;
        }

        .brand-stat-copy small {
            color: var(--text-muted);
            font-size: 0.65rem;
            margin-top: 0.12rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .brand-stat-progress {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            background: var(--bg-tertiary);
        }

        .brand-stat-progress span {
            display: block;
            height: 100%;
            background: var(--stat-color);
            transition: width 0.3s ease;
        }

        /* Filter */
        .brand-filter-card {
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .brand-filter-card:hover,
        .brand-panel:hover {
            transform: none;
        }

        .brand-filter {
            display: grid;
            grid-template-columns: minmax(240px, 1fr) 190px auto auto;
            gap: 0.65rem;
            align-items: center;
        }

        .brand-search {
            position: relative;
            display: flex;
            align-items: center;
        }

        .brand-search > i {
            position: absolute;
            left: 0.9rem;
            color: var(--text-muted);
            font-size: 0.85rem;
            pointer-events: none;
        }

        .brand-search input {
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

        .brand-search input:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(29, 91, 66, 0.1);
        }

        .brand-search input::placeholder {
            color: var(--text-muted);
        }

        .brand-search-clear {
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

        .brand-search-clear:hover {
            background: var(--hover-bg);
            color: var(--bs-danger);
        }

        .brand-select {
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

        .brand-select:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(29, 91, 66, 0.1);
        }

        .brand-filter-submit {
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

        .brand-filter-submit:hover {
            background: #164934;
            transform: translateY(-1px);
        }

        .brand-filter-reset {
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

        .brand-filter-reset:hover {
            border-color: var(--bs-danger);
            color: var(--bs-danger);
        }

        .brand-chips {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.45rem;
            margin-top: 0.85rem;
            padding-top: 0.85rem;
            border-top: 1px solid var(--border-light);
        }

        .brand-chips-label {
            color: var(--text-muted);
            font-size: 0.74rem;
            font-weight: 600;
        }

        .brand-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.55rem;
            border-radius: 999px;
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            font-size: 0.74rem;
        }

        .brand-chip > i {
            color: var(--bs-primary);
            font-size: 0.65rem;
        }

        .brand-chip a {
            display: grid;
            place-items: center;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            color: var(--text-muted);
        }

        .brand-chip a:hover {
            background: var(--bg-secondary);
            color: var(--bs-danger);
        }

        .brand-chips-clear {
            margin-left: 0.25rem;
            color: var(--bs-danger);
            font-size: 0.74rem;
            font-weight: 600;
            text-decoration: none;
        }

        .brand-chips-clear:hover {
            text-decoration: underline;
        }

        /* Panel daftar */
        .brand-panel {
            padding: 0;
            overflow: hidden;
        }

        .brand-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.15rem 1.35rem;
            border-bottom: 1px solid var(--border-light);
        }

        .brand-panel-head h5 {
            margin: 0 0 0.15rem;
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 700;
        }

        .brand-panel-head p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.76rem;
        }

        .brand-sort {
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

        .brand-table {
            margin: 0;
            min-width: 820px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .brand-table thead th {
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

        .brand-table tbody td {
            padding: 0.95rem 1.1rem;
            border: none;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .brand-table tbody tr:last-child td {
            border-bottom: none;
        }

        .brand-table tbody tr:hover td {
            background: var(--hover-bg);
        }

        .brand-table tbody tr.is-incomplete td:first-child {
            box-shadow: inset 3px 0 0 var(--bs-warning);
        }

        .brand-cell {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            min-width: 235px;
        }

        .brand-mark {
            width: 48px;
            height: 48px;
            flex-shrink: 0;
            border: 1px solid var(--border-light);
            border-radius: 10px;
            background: #fff;
            overflow: hidden;
            display: grid;
            place-items: center;
        }

        .brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 6px;
        }

        .brand-initial {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            background: rgba(29, 91, 66, 0.1);
            color: var(--bs-primary);
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .brand-cell-copy {
            min-width: 0;
        }

        .brand-name {
            color: var(--text-primary);
            font-size: 0.9rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .brand-slug {
            display: inline-block;
            margin-top: 0.18rem;
            color: var(--text-muted);
            font-size: 0.7rem;
            line-height: 1.3;
            word-break: break-all;
        }

        .brand-count {
            display: inline-flex;
            align-items: baseline;
            gap: 0.3rem;
            color: var(--text-primary);
            text-decoration: none;
        }

        .brand-count strong {
            font-size: 0.92rem;
            font-weight: 700;
            line-height: 1;
        }

        .brand-count span {
            color: var(--text-muted);
            font-size: 0.7rem;
        }

        .brand-count:hover strong {
            color: var(--bs-primary);
        }

        .brand-count.is-empty strong,
        .brand-count.is-empty span {
            color: var(--text-muted);
            font-weight: 500;
        }

        .brand-health {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.28rem 0.58rem;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .brand-health.is-active {
            background: rgba(77, 138, 99, 0.14);
            color: var(--bs-success);
        }

        .brand-health.is-model {
            background: rgba(189, 138, 56, 0.14);
            color: var(--bs-warning);
        }

        .brand-health.is-empty {
            background: rgba(185, 88, 76, 0.12);
            color: var(--bs-danger);
        }

        .brand-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.35rem;
        }

        .brand-row-action {
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

        .brand-row-action:hover {
            border-color: var(--bs-primary);
            background: var(--hover-bg);
            color: var(--bs-primary);
        }

        .brand-row-action.is-danger:hover {
            border-color: var(--bs-danger);
            background: rgba(185, 88, 76, 0.06);
            color: var(--bs-danger);
        }

        .brand-row-action.is-disabled {
            border-color: var(--border-light);
            background: var(--bg-tertiary);
            color: var(--text-muted);
            opacity: 0.55;
            cursor: not-allowed;
        }

        .brand-pagination {
            display: flex;
            justify-content: flex-end;
            padding: 1rem 1.35rem;
            border-top: 1px solid var(--border-light);
            background: var(--bg-secondary);
        }

        .brand-panel-note {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.7rem 1.35rem;
            border-top: 1px solid var(--border-light);
            background: var(--bg-secondary);
            color: var(--text-muted);
            font-size: 0.72rem;
        }

        .brand-panel-note i {
            color: var(--bs-info);
        }

        /* Modal merek */
        .brand-modal .modal-content-custom {
            max-width: 560px;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
        }

        .brand-modal-intro {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.9rem 1rem;
            margin-bottom: 1.15rem;
            border: 1px solid var(--border-light);
            border-radius: 9px;
            background: var(--bg-tertiary);
        }

        .brand-modal-intro-icon {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border-radius: 9px;
            background: rgba(29, 91, 66, 0.1);
            color: var(--bs-primary);
        }

        .brand-modal-intro strong {
            display: block;
            color: var(--text-primary);
            font-size: 0.82rem;
            font-weight: 700;
        }

        .brand-modal-intro span {
            display: block;
            margin-top: 0.15rem;
            color: var(--text-muted);
            font-size: 0.72rem;
            line-height: 1.5;
        }

        .brand-modal-meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .brand-modal-meta div {
            padding: 0.6rem 0.7rem;
            border: 1px solid var(--border-light);
            border-radius: 7px;
            background: var(--bg-secondary);
            min-width: 0;
        }

        .brand-modal-meta span {
            display: block;
            color: var(--text-muted);
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .brand-modal-meta strong {
            display: block;
            margin-top: 0.15rem;
            color: var(--text-primary);
            font-size: 0.8rem;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        /* Unggah logo */
        .brand-logo-field {
            position: relative;
            margin-bottom: 1rem;
        }

        .brand-file-input {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .brand-logo-picker {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.8rem;
            border: 1.5px dashed var(--border-color);
            border-radius: 9px;
            background: var(--bg-secondary);
            cursor: pointer;
            transition: border-color 0.2s ease, background 0.2s ease;
        }

        .brand-logo-picker:hover {
            border-color: var(--bs-primary);
            background: var(--hover-bg);
        }

        .brand-file-input:focus-visible + .brand-logo-picker {
            outline: 2px solid var(--bs-primary);
            outline-offset: 2px;
        }

        .brand-logo-field.has-error .brand-logo-picker {
            border-color: var(--bs-danger);
        }

        .brand-logo-preview {
            display: grid;
            place-items: center;
            width: 56px;
            height: 56px;
            flex-shrink: 0;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            background: #fff;
            overflow: hidden;
        }

        .brand-logo-preview img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 6px;
        }

        .brand-logo-preview i {
            color: var(--text-muted);
            font-size: 1.2rem;
        }

        .brand-logo-copy {
            display: flex;
            flex-direction: column;
            gap: 0.12rem;
            min-width: 0;
        }

        .brand-logo-copy strong {
            color: var(--text-primary);
            font-size: 0.78rem;
            font-weight: 700;
        }

        .brand-logo-hint {
            color: var(--text-muted);
            font-size: 0.68rem;
            line-height: 1.4;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .brand-logo-button {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 0.2rem;
            color: var(--bs-primary);
            font-size: 0.72rem;
            font-weight: 700;
        }

        @media (max-width: 1199.98px) {
            .brand-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .brand-filter {
                grid-template-columns: minmax(200px, 1fr) 1fr auto auto;
            }
        }

        @media (max-width: 767.98px) {
            .brand-stats {
                gap: 0.65rem;
            }

            .brand-stat {
                padding: 0.85rem 0.9rem 1.1rem;
            }

            .brand-stat-icon {
                width: 36px;
                height: 36px;
                font-size: 0.85rem;
            }

            .brand-stat-copy strong {
                font-size: 1.15rem;
            }

            .brand-stat-copy span {
                font-size: 0.68rem;
            }

            .brand-stat-copy small {
                font-size: 0.62rem;
                white-space: normal;
                overflow: visible;
            }

            .brand-filter {
                grid-template-columns: 1fr 1fr;
            }

            .brand-search,
            .brand-select {
                grid-column: 1 / -1;
            }

            .brand-filter-submit {
                grid-column: 1;
            }

            .brand-filter-reset {
                grid-column: 2;
                width: 100%;
            }

            .brand-panel-head {
                padding: 1rem;
            }

            /* Tabel menjadi kartu di layar kecil */
            .brand-table {
                min-width: 0;
            }

            .brand-table thead {
                display: none;
            }

            .brand-table,
            .brand-table tbody,
            .brand-table tr,
            .brand-table td {
                display: block;
                width: 100%;
            }

            .brand-table tbody tr {
                padding: 1rem;
                border-bottom: 1px solid var(--border-light);
            }

            .brand-table tbody tr:last-child {
                border-bottom: none;
            }

            .brand-table tbody td {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 0.35rem 0;
                border: none;
                background: transparent;
            }

            .brand-table tbody tr:hover td {
                background: transparent;
            }

            .brand-table tbody tr.is-incomplete td:first-child {
                box-shadow: none;
            }

            .brand-table tbody td:first-child {
                display: block;
                padding: 0 0 0.7rem;
            }

            .brand-table tbody td:not(:first-child)::before {
                content: attr(data-label);
                color: var(--text-muted);
                font-size: 0.68rem;
                font-weight: 700;
                letter-spacing: 0.05em;
                text-transform: uppercase;
            }

            .brand-table tbody td[colspan] {
                display: block;
            }

            .brand-table tbody td[colspan]::before {
                content: none;
            }

            .brand-table tbody td[data-label="Aksi"] {
                padding-top: 0.75rem;
            }

            .brand-cell {
                min-width: 0;
            }

            .brand-actions {
                flex-wrap: wrap;
            }

            .brand-pagination {
                justify-content: center;
                padding: 0.9rem;
            }
        }

        @media (max-width: 575.98px) {
            .brand-filter {
                grid-template-columns: minmax(0, 1fr) 42px;
            }

            .brand-search,
            .brand-select {
                grid-column: 1 / -1;
            }

            .brand-filter-submit {
                grid-column: 1;
            }

            .brand-filter-reset {
                grid-column: 2;
                width: 42px;
            }

            .brand-modal .modal-content-custom {
                padding: 1.1rem;
                max-height: calc(100vh - 1rem);
            }

            .brand-modal-meta {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
@endpush

<x-layouts.app title="Merek - RodaTa Admin">
    <x-layout.page-header title="Merek" subtitle="Kelola merek beserta model dan unit yang terhubung">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" :href="route('admin.brands', $baseQuery + ['create' => 1])">
                Tambah Merek
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Ringkasan merek --}}
    <div class="brand-stats">
        @foreach ($statCards as $card)
            <a href="{{ $card['href'] }}"
               class="brand-stat brand-stat-{{ $card['tone'] }} {{ $coverage === $card['coverage'] ? 'is-active' : '' }}">
                <span class="brand-stat-icon">
                    <i class="{{ $card['icon'] }}"></i>
                </span>
                <span class="brand-stat-copy">
                    <strong>{{ number_format($card['value'], 0, ',', '.') }}</strong>
                    <span>{{ $card['label'] }}</span>
                    <small>{{ $card['meta'] }}</small>
                </span>
                <span class="brand-stat-progress" aria-hidden="true">
                    <span style="width: {{ $card['percentage'] }}%;"></span>
                </span>
            </a>
        @endforeach
    </div>

    {{-- Filter --}}
    <x-layout.modern-card class="brand-filter-card">
        <form method="GET" action="{{ route('admin.brands') }}" class="brand-filter">
            @if ($coverage !== '')
                <input type="hidden" name="coverage" value="{{ $coverage }}">
            @endif

            <div class="brand-search">
                <i class="fas fa-search"></i>
                <input type="search" id="q" name="q" value="{{ $search }}"
                       placeholder="Cari nama atau slug merek..." aria-label="Cari merek">
                @if ($search !== '')
                    <a href="{{ route('admin.brands', array_filter(['coverage' => $coverage, 'sort' => $sortQuery])) }}"
                       class="brand-search-clear" aria-label="Hapus pencarian">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>

            <select id="sort" name="sort" class="brand-select" aria-label="Urutkan merek">
                @foreach ($sortOptions as $sortValue => $sortLabel)
                    <option value="{{ $sortValue }}" @selected($sort === $sortValue)>
                        {{ $sortLabel }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="brand-filter-submit">
                <i class="fas fa-filter"></i>
                Tampilkan
            </button>

            <a href="{{ route('admin.brands') }}" class="brand-filter-reset" title="Reset filter" aria-label="Reset filter">
                <i class="fas fa-rotate-left"></i>
            </a>
        </form>

        @if ($hasFilters)
            <div class="brand-chips">
                <span class="brand-chips-label">Filter aktif:</span>

                @if ($search !== '')
                    <span class="brand-chip">
                        <i class="fas fa-search"></i>
                        “{{ $search }}”
                        <a href="{{ route('admin.brands', array_filter(['coverage' => $coverage, 'sort' => $sortQuery])) }}"
                           aria-label="Hapus filter pencarian">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if ($coverageLabel)
                    <span class="brand-chip">
                        <i class="fas fa-trademark"></i>
                        {{ $coverageLabel }}
                        <a href="{{ route('admin.brands', array_filter(['q' => $search, 'sort' => $sortQuery])) }}"
                           aria-label="Hapus filter kelompok merek">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                <a href="{{ route('admin.brands') }}" class="brand-chips-clear">Bersihkan semua</a>
            </div>
        @endif
    </x-layout.modern-card>

    {{-- Daftar merek --}}
    <x-layout.modern-card :padding="false" class="brand-panel">
        <div class="brand-panel-head">
            <div>
                <h5>Daftar Merek</h5>
                <p>
                    @if ($brands->total() > 0)
                        Menampilkan {{ $brands->firstItem() }}–{{ $brands->lastItem() }} dari
                        {{ number_format($brands->total(), 0, ',', '.') }} merek
                    @elseif ($hasFilters)
                        Tidak ada merek yang cocok dengan filter
                    @else
                        Belum ada merek terdaftar
                    @endif
                </p>
            </div>
            <span class="brand-sort">
                <i class="fas fa-arrow-down-wide-short"></i>
                {{ $sortOptions[$sort] }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table brand-table">
                <thead>
                    <tr>
                        <th>Merek</th>
                        <th>Model</th>
                        <th>Mobil</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($brands as $brand)
                        @php
                            $brandLogo = $brand->logoUrl();
                            $brandInitials = $initialsFor($brand->name);
                        @endphp
                        <tr class="{{ $brand->car_models_count === 0 ? 'is-incomplete' : '' }}">
                            <td data-label="Merek">
                                <div class="brand-cell">
                                    <span class="brand-mark">
                                        @if ($brandLogo)
                                            <img src="{{ $brandLogo }}" alt="Logo {{ $brand->name }}"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';">
                                            <span class="brand-initial" style="display: none;">{{ $brandInitials }}</span>
                                        @else
                                            <span class="brand-initial">{{ $brandInitials }}</span>
                                        @endif
                                    </span>
                                    <div class="brand-cell-copy">
                                        <div class="brand-name">{{ $brand->name }}</div>
                                        <span class="brand-slug">/{{ $brand->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Model">
                                @if ($brand->car_models_count > 0)
                                    <a href="{{ route('admin.car-models', ['brand' => $brand->id]) }}"
                                       class="brand-count" title="Lihat model {{ $brand->name }}">
                                        <strong>{{ number_format($brand->car_models_count, 0, ',', '.') }}</strong>
                                        <span>model</span>
                                    </a>
                                @else
                                    <span class="brand-count is-empty">
                                        <strong>0</strong>
                                        <span>model</span>
                                    </span>
                                @endif
                            </td>
                            <td data-label="Mobil">
                                @if ($brand->cars_count > 0)
                                    <a href="{{ route('admin.cars', ['brand' => $brand->slug]) }}"
                                       class="brand-count" title="Lihat mobil {{ $brand->name }}">
                                        <strong>{{ number_format($brand->cars_count, 0, ',', '.') }}</strong>
                                        <span>mobil</span>
                                    </a>
                                @else
                                    <span class="brand-count is-empty">
                                        <strong>0</strong>
                                        <span>mobil</span>
                                    </span>
                                @endif
                            </td>
                            <td data-label="Status">
                                @if ($brand->car_models_count === 0)
                                    <span class="brand-health is-empty">
                                        <i class="fas fa-circle-exclamation"></i>
                                        Belum ada model
                                    </span>
                                @elseif ($brand->cars_count === 0)
                                    <span class="brand-health is-model">
                                        <i class="fas fa-layer-group"></i>
                                        Model saja
                                    </span>
                                @else
                                    <span class="brand-health is-active">
                                        <i class="fas fa-circle-check"></i>
                                        Aktif
                                    </span>
                                @endif
                            </td>
                            <td data-label="Aksi">
                                <div class="brand-actions">
                                    <a href="{{ route('admin.brands', $baseQuery + ['edit' => $brand->id]) }}"
                                       class="brand-row-action" title="Edit {{ $brand->name }}"
                                       aria-label="Edit {{ $brand->name }}">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    @if ($brand->car_models_count > 0)
                                        <span class="brand-row-action is-disabled"
                                              title="Hapus model terlebih dahulu"
                                              aria-label="Merek masih memiliki model, tidak dapat dihapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </span>
                                    @else
                                        <a href="{{ route('admin.brands', $baseQuery + ['delete' => $brand->id]) }}"
                                           class="brand-row-action is-danger" title="Hapus {{ $brand->name }}"
                                           aria-label="Hapus {{ $brand->name }}">
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
                                    icon="fas fa-trademark"
                                    :title="$hasFilters ? 'Tidak ada merek yang cocok' : 'Belum ada merek'"
                                    :description="$hasFilters
                                        ? 'Coba ubah kata kunci atau bersihkan filter untuk melihat merek lainnya.'
                                        : 'Tambahkan merek pertama untuk mulai mengelola model dan stok mobil.'"
                                    :action-label="$hasFilters ? 'Bersihkan Filter' : 'Tambah Merek'"
                                    :action-href="$hasFilters ? route('admin.brands') : route('admin.brands', ['create' => 1])"
                                    :action-icon="$hasFilters ? 'fas fa-rotate-left' : 'fas fa-plus'"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($brands->getCollection()->contains(fn ($brand) => $brand->car_models_count > 0))
            <div class="brand-panel-note">
                <i class="fas fa-circle-info"></i>
                Merek yang masih memiliki model tidak dapat dihapus.
            </div>
        @endif

        @if ($brands->hasPages())
            <div class="brand-pagination">
                {{ $brands->links() }}
            </div>
        @endif
    </x-layout.modern-card>

    @if ($showCreateModal && ! $editingBrand && ! $deletingBrand)
        <x-layout.modal-form
            title="Tambah Merek"
            :action="route('admin.brands.store')"
            :close-url="$closeUrl"
            submit-label="Simpan Merek"
            close-label="Batal"
            enctype="multipart/form-data"
            class="brand-modal"
        >
            <input type="hidden" name="return_q" value="{{ $search }}">
            <input type="hidden" name="return_coverage" value="{{ $coverage }}">
            <input type="hidden" name="return_sort" value="{{ $sortQuery }}">
            <input type="hidden" name="return_page" value="{{ request('page') }}">

            <div class="brand-modal-intro">
                <span class="brand-modal-intro-icon">
                    <i class="fas fa-trademark"></i>
                </span>
                <div>
                    <strong>Tambah merek baru</strong>
                    <span>Merek akan tersedia saat menambahkan model dan unit mobil.</span>
                </div>
            </div>

            <x-form.input
                name="name"
                label="Nama Merek"
                :value="old('name')"
                placeholder="Toyota"
                required
                :error="$errors->first('name')"
            />

            <div class="brand-logo-field {{ $errors->has('logo') ? 'has-error' : '' }}"
                 x-data='{
                     preview: null,
                     filename: null,
                     pick(event) {
                         const file = event.target.files[0];
                         if (!file) return;
                         this.filename = file.name;
                         this.preview = URL.createObjectURL(file);
                     }
                 }'>
                <input type="file" id="create-brand-logo" name="logo"
                       accept="image/png,image/jpeg,image/webp,image/svg+xml"
                       class="brand-file-input" x-on:change="pick($event)">
                <label for="create-brand-logo" class="brand-logo-picker">
                    <span class="brand-logo-preview">
                        <i class="fas fa-trademark" x-show="!preview"></i>
                        <template x-if="preview">
                            <img :src="preview" alt="Pratinjau logo" x-on:error="preview = null">
                        </template>
                    </span>
                    <span class="brand-logo-copy">
                        <strong>Logo merek</strong>
                        <span class="brand-logo-hint"
                              x-text="filename ?? 'PNG, JPG, WEBP, atau SVG. Maks. 2 MB.'">PNG, JPG, WEBP, atau SVG. Maks. 2 MB.</span>
                        <span class="brand-logo-button">
                            <i class="fas fa-upload"></i>
                            Pilih Logo
                        </span>
                    </span>
                </label>
            </div>
            @error('logo')
                <div class="invalid-feedback d-block mb-3">{{ $message }}</div>
            @enderror
        </x-layout.modal-form>
    @endif

    @if ($editingBrand)
        <x-layout.modal-form
            title="Edit Merek"
            :action="route('admin.brands.update', $editingBrand)"
            method="PUT"
            :close-url="$closeUrl"
            submit-label="Simpan Perubahan"
            close-label="Batal"
            enctype="multipart/form-data"
            class="brand-modal"
        >
            <input type="hidden" name="return_q" value="{{ $search }}">
            <input type="hidden" name="return_coverage" value="{{ $coverage }}">
            <input type="hidden" name="return_sort" value="{{ $sortQuery }}">
            <input type="hidden" name="return_page" value="{{ request('page') }}">

            <div class="brand-modal-intro">
                <span class="brand-modal-intro-icon">
                    <i class="fas fa-pen"></i>
                </span>
                <div>
                    <strong>Perbarui data merek</strong>
                    <span>Perubahan nama dan logo akan langsung tampil di katalog.</span>
                </div>
            </div>

            <div class="brand-modal-meta">
                <div>
                    <span>Slug</span>
                    <strong>/{{ $editingBrand->slug }}</strong>
                </div>
                <div>
                    <span>Model</span>
                    <strong>{{ number_format($editingBrand->car_models_count, 0, ',', '.') }}</strong>
                </div>
                <div>
                    <span>Mobil</span>
                    <strong>{{ number_format($editingBrand->cars_count, 0, ',', '.') }}</strong>
                </div>
            </div>

            <x-form.input
                name="name"
                label="Nama Merek"
                :value="old('name', $editingBrand->name)"
                placeholder="Toyota"
                required
                :error="$errors->first('name')"
            />

            <div class="brand-logo-field {{ $errors->has('logo') ? 'has-error' : '' }}"
                 x-data='{
                     preview: null,
                     filename: null,
                     pick(event) {
                         const file = event.target.files[0];
                         if (!file) return;
                         this.filename = file.name;
                         this.preview = URL.createObjectURL(file);
                     }
                 }'>
                <input type="file" id="edit-brand-logo" name="logo"
                       accept="image/png,image/jpeg,image/webp,image/svg+xml"
                       class="brand-file-input" x-on:change="pick($event)">
                <label for="edit-brand-logo" class="brand-logo-picker">
                    <span class="brand-logo-preview">
                        @if ($editingBrand->logoUrl())
                            <img src="{{ $editingBrand->logoUrl() }}" alt="Logo {{ $editingBrand->name }}"
                                 x-show="!preview" x-on:error="$el.style.display = 'none'">
                        @else
                            <i class="fas fa-trademark" x-show="!preview"></i>
                        @endif
                        <template x-if="preview">
                            <img :src="preview" alt="Pratinjau logo" x-on:error="preview = null">
                        </template>
                    </span>
                    <span class="brand-logo-copy">
                        <strong>Ganti logo</strong>
                        <span class="brand-logo-hint"
                              x-text="filename ?? 'Biarkan kosong jika tidak ingin mengganti logo.'">Biarkan kosong jika tidak ingin mengganti logo.</span>
                        <span class="brand-logo-button">
                            <i class="fas fa-upload"></i>
                            Pilih Logo Baru
                        </span>
                    </span>
                </label>
            </div>
            @error('logo')
                <div class="invalid-feedback d-block mb-3">{{ $message }}</div>
            @enderror
        </x-layout.modal-form>
    @endif

    @if ($deletingBrand && ! $editingBrand)
        <x-ui.confirm-modal
            :show="true"
            title="Hapus Merek"
            :message="$deletingBrand->car_models_count > 0
                ? 'Merek '.$deletingBrand->name.' masih memiliki '.$deletingBrand->car_models_count.' model. Hapus model terlebih dahulu sebelum menghapus merek.'
                : 'Hapus merek '.$deletingBrand->name.'? Tindakan ini tidak dapat dibatalkan.'"
            confirm-text="Hapus Merek"
            cancel-text="Batal"
            :action="route('admin.brands.destroy', $deletingBrand)"
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
