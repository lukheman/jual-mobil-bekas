@php
    $baseQuery = array_filter([
        'q' => $search,
        'status' => $status,
        'brand' => $brand,
        'page' => request('page'),
        'arsip' => $showArchived ? 1 : null,
    ], fn ($value) => $value !== null && $value !== '');

    $closeUrl = route('admin.cars', $baseQuery);

    $statCards = [
        [
            'key' => 'total',
            'label' => 'Total Unit',
            'icon' => 'fas fa-car-side',
            'tone' => 'primary',
            'value' => $stats['total'],
            'percentage' => $stats['total'] > 0 ? 100 : 0,
            'href' => route('admin.cars', array_filter(['q' => $search, 'brand' => $brand])),
        ],
        [
            'key' => 'available',
            'label' => 'Tersedia',
            'icon' => 'fas fa-circle-check',
            'tone' => 'success',
            'value' => $stats['available'],
            'percentage' => $stats['total'] > 0 ? (int) round($stats['available'] / $stats['total'] * 100) : 0,
            'href' => route('admin.cars', array_filter(['q' => $search, 'brand' => $brand, 'status' => 'available'])),
        ],
        [
            'key' => 'reserved',
            'label' => 'Dipesan',
            'icon' => 'fas fa-clock',
            'tone' => 'warning',
            'value' => $stats['reserved'],
            'percentage' => $stats['total'] > 0 ? (int) round($stats['reserved'] / $stats['total'] * 100) : 0,
            'href' => route('admin.cars', array_filter(['q' => $search, 'brand' => $brand, 'status' => 'reserved'])),
        ],
        [
            'key' => 'sold',
            'label' => 'Terjual',
            'icon' => 'fas fa-flag-checkered',
            'tone' => 'danger',
            'value' => $stats['sold'],
            'percentage' => $stats['total'] > 0 ? (int) round($stats['sold'] / $stats['total'] * 100) : 0,
            'href' => route('admin.cars', array_filter(['q' => $search, 'brand' => $brand, 'status' => 'sold'])),
        ],
    ];

    $brandName = $brands->firstWhere('slug', $brand)?->name;
    $statusLabel = collect($statuses)->firstWhere('value', $status)?->label();

    $statusClasses = [
        'available' => 'is-available',
        'reserved' => 'is-reserved',
        'sold' => 'is-sold',
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

        /* Ringkasan stok */
        .inventory-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .inventory-stat {
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

        .inventory-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(23, 53, 43, 0.12);
            border-color: var(--border-color);
            color: inherit;
        }

        .inventory-stat.is-active {
            border-color: var(--stat-color);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--stat-color) 14%, transparent);
        }

        .inventory-stat-primary { --stat-color: var(--bs-primary); --stat-soft: rgba(29, 91, 66, 0.1); }
        .inventory-stat-success { --stat-color: var(--bs-success); --stat-soft: rgba(77, 138, 99, 0.12); }
        .inventory-stat-warning { --stat-color: var(--bs-warning); --stat-soft: rgba(189, 138, 56, 0.14); }
        .inventory-stat-danger { --stat-color: var(--bs-danger); --stat-soft: rgba(185, 88, 76, 0.12); }

        .inventory-stat-icon {
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

        .inventory-stat-copy {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
            min-width: 0;
        }

        .inventory-stat-copy strong {
            color: var(--text-primary);
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .inventory-stat-copy span {
            color: var(--text-muted);
            font-size: 0.76rem;
            font-weight: 500;
            margin-top: 0.2rem;
        }

        .inventory-stat-progress {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            background: var(--bg-tertiary);
        }

        .inventory-stat-progress span {
            display: block;
            height: 100%;
            background: var(--stat-color);
            transition: width 0.3s ease;
        }

        /* Tab stok / arsip */
        .inventory-tabbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .inventory-tabs {
            display: inline-flex;
            gap: 0.25rem;
            padding: 0.3rem;
            background: var(--bg-tertiary);
            border-radius: 8px;
        }

        .inventory-tab {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.5rem 0.9rem;
            border-radius: 6px;
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .inventory-tab:hover {
            color: var(--bs-primary);
        }

        .inventory-tab.is-active {
            background: var(--bg-secondary);
            color: var(--text-primary);
            box-shadow: 0 1px 3px rgba(23, 53, 43, 0.12);
        }

        .inventory-tab span {
            display: inline-grid;
            place-items: center;
            min-width: 22px;
            height: 20px;
            padding: 0 0.35rem;
            border-radius: 999px;
            background: var(--bg-tertiary);
            color: var(--text-muted);
            font-size: 0.7rem;
        }

        .inventory-tab.is-active span {
            background: var(--bs-primary);
            color: #fff;
        }

        .inventory-tab-note {
            color: var(--text-muted);
            font-size: 0.76rem;
        }

        .inventory-tab-note i {
            margin-right: 0.3rem;
            color: var(--bs-primary);
        }

        /* Filter */
        .inventory-filter-card {
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .inventory-filter-card:hover,
        .inventory-panel:hover {
            transform: none;
        }

        .inventory-filter {
            display: grid;
            grid-template-columns: minmax(220px, 1fr) 180px 180px auto auto;
            gap: 0.65rem;
            align-items: center;
        }

        .inventory-search {
            position: relative;
            display: flex;
            align-items: center;
        }

        .inventory-search > i {
            position: absolute;
            left: 0.9rem;
            color: var(--text-muted);
            font-size: 0.85rem;
            pointer-events: none;
        }

        .inventory-search input {
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

        .inventory-search input:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(29, 91, 66, 0.1);
        }

        .inventory-search input::placeholder {
            color: var(--text-muted);
        }

        .inventory-search-clear {
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

        .inventory-search-clear:hover {
            background: var(--hover-bg);
            color: var(--bs-danger);
        }

        .inventory-select {
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

        .inventory-select:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(29, 91, 66, 0.1);
        }

        .inventory-filter-submit {
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

        .inventory-filter-submit:hover {
            background: #164934;
            transform: translateY(-1px);
        }

        .inventory-filter-reset {
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

        .inventory-filter-reset:hover {
            border-color: var(--bs-danger);
            color: var(--bs-danger);
        }

        .inventory-chips {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.45rem;
            margin-top: 0.85rem;
            padding-top: 0.85rem;
            border-top: 1px solid var(--border-light);
        }

        .inventory-chips-label {
            color: var(--text-muted);
            font-size: 0.74rem;
            font-weight: 600;
        }

        .inventory-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.55rem;
            border-radius: 999px;
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            font-size: 0.74rem;
        }

        .inventory-chip > i {
            color: var(--bs-primary);
            font-size: 0.65rem;
        }

        .inventory-chip a {
            display: grid;
            place-items: center;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            color: var(--text-muted);
        }

        .inventory-chip a:hover {
            background: var(--bg-secondary);
            color: var(--bs-danger);
        }

        .inventory-chips-clear {
            margin-left: 0.25rem;
            color: var(--bs-danger);
            font-size: 0.74rem;
            font-weight: 600;
            text-decoration: none;
        }

        .inventory-chips-clear:hover {
            text-decoration: underline;
        }

        /* Panel daftar */
        .inventory-panel {
            padding: 0;
            overflow: hidden;
        }

        .inventory-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.15rem 1.35rem;
            border-bottom: 1px solid var(--border-light);
        }

        .inventory-panel-head h5 {
            margin: 0 0 0.15rem;
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 700;
        }

        .inventory-panel-head p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.76rem;
        }

        .inventory-sort {
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

        .inventory-table {
            margin: 0;
            min-width: 760px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .inventory-table thead th {
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

        .inventory-table tbody td {
            padding: 1rem 1.1rem;
            border: none;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .inventory-table tbody tr:last-child td {
            border-bottom: none;
        }

        .inventory-table tbody tr:hover td {
            background: var(--hover-bg);
        }

        .inventory-table tbody tr.is-archived td {
            background: color-mix(in srgb, var(--bg-tertiary) 55%, var(--bg-secondary));
        }

        .inventory-table tbody tr.is-archived:hover td {
            background: var(--hover-bg);
        }

        .car-cell {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            min-width: 260px;
        }

        .car-thumb {
            width: 82px;
            height: 60px;
            border-radius: 7px;
            overflow: hidden;
            background: var(--bg-tertiary);
            display: grid;
            place-items: center;
            color: var(--text-muted);
            flex-shrink: 0;
            border: 1px solid var(--border-light);
        }

        .car-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .car-cell-copy {
            min-width: 0;
        }

        .car-cell-title {
            color: var(--text-primary);
            font-size: 0.88rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .car-cell-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.55rem;
            margin-top: 0.28rem;
            color: var(--text-muted);
            font-size: 0.72rem;
        }

        .car-cell-meta i {
            margin-right: 0.18rem;
            font-size: 0.68rem;
        }

        .car-cell-location {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.25rem;
            color: var(--text-secondary);
            font-size: 0.74rem;
        }

        .car-cell-location > i {
            color: var(--bs-primary);
            font-size: 0.68rem;
        }

        .car-photo-warning {
            color: var(--bs-warning);
            font-size: 0.7rem;
            font-weight: 500;
        }

        .car-photo-warning i {
            margin-right: 0.15rem;
        }

        .car-price {
            color: var(--text-primary);
            font-size: 0.88rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .car-price-note {
            display: block;
            margin-top: 0.15rem;
            color: var(--text-muted);
            font-size: 0.7rem;
            white-space: nowrap;
        }

        .status-form {
            margin: 0;
        }

        .status-select {
            min-width: 104px;
            padding: 0.32rem 1.55rem 0.32rem 0.62rem;
            border: 1px solid transparent;
            border-radius: 999px;
            background-color: var(--bg-tertiary);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2371817a' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.55rem center;
            background-size: 11px 8px;
            font-size: 0.72rem;
            font-weight: 700;
            appearance: none;
            color: var(--text-secondary);
            cursor: pointer;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .status-select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(29, 91, 66, 0.12);
        }

        .status-select.is-available {
            background-color: rgba(77, 138, 99, 0.13);
            color: var(--bs-success);
        }

        .status-select.is-reserved {
            background-color: rgba(189, 138, 56, 0.16);
            color: var(--bs-warning);
        }

        .status-select.is-sold {
            background-color: rgba(185, 88, 76, 0.13);
            color: var(--bs-danger);
        }

        .featured-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            min-width: 86px;
            padding: 0.32rem 0.6rem;
            border: 1px solid var(--border-color);
            border-radius: 999px;
            background: transparent;
            color: var(--text-muted);
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .featured-pill:hover {
            border-color: var(--bs-warning);
            color: var(--bs-warning);
        }

        .featured-pill.is-featured {
            border-color: rgba(189, 138, 56, 0.35);
            background: rgba(189, 138, 56, 0.14);
            color: var(--bs-warning);
        }

        .row-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.35rem;
        }

        .row-actions form {
            margin: 0;
        }

        .row-action {
            display: grid;
            place-items: center;
            width: 32px;
            height: 32px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: var(--bg-secondary);
            color: var(--text-secondary);
            font-size: 0.78rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .row-action:hover {
            border-color: var(--bs-primary);
            background: var(--hover-bg);
            color: var(--bs-primary);
        }

        .row-action-success:hover {
            border-color: var(--bs-success);
            color: var(--bs-success);
        }

        .row-action-danger:hover {
            border-color: var(--bs-danger);
            color: var(--bs-danger);
            background: rgba(185, 88, 76, 0.08);
        }

        .inventory-pagination {
            display: flex;
            justify-content: flex-end;
            padding: 1rem 1.35rem;
            border-top: 1px solid var(--border-light);
            background: var(--bg-secondary);
        }

        .inventory-pagination .pagination {
            margin-bottom: 0;
            gap: 0.25rem;
        }

        .inventory-pagination .page-link {
            min-width: 34px;
            padding: 0.42rem 0.6rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: var(--bg-secondary);
            color: var(--text-secondary);
            font-size: 0.75rem;
            font-weight: 600;
            text-align: center;
        }

        .inventory-pagination .page-link:hover {
            border-color: var(--bs-primary);
            background: var(--hover-bg);
            color: var(--bs-primary);
        }

        .inventory-pagination .page-item.active .page-link {
            border-color: var(--bs-primary);
            background: var(--bs-primary);
            color: #fff;
        }

        .inventory-pagination .page-item.disabled .page-link {
            border-color: var(--border-light);
            background: var(--bg-tertiary);
            color: var(--text-muted);
        }

        @media (max-width: 1199.98px) {
            .inventory-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .inventory-filter {
                grid-template-columns: minmax(200px, 1fr) 1fr 1fr auto;
            }

            .inventory-filter-submit {
                grid-column: 1 / 3;
            }

            .inventory-filter-reset {
                grid-column: 4;
                grid-row: 2;
            }
        }

        @media (max-width: 767.98px) {
            .inventory-stats {
                gap: 0.65rem;
            }

            .inventory-stat {
                padding: 0.85rem 0.9rem 1.1rem;
            }

            .inventory-stat-icon {
                width: 36px;
                height: 36px;
                font-size: 0.85rem;
            }

            .inventory-stat-copy strong {
                font-size: 1.15rem;
            }

            .inventory-stat-copy span {
                font-size: 0.68rem;
            }

            .inventory-tabbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .inventory-tab-note {
                font-size: 0.7rem;
            }

            .inventory-filter {
                grid-template-columns: 1fr 1fr;
            }

            .inventory-search {
                grid-column: 1 / -1;
            }

            .inventory-filter-submit {
                grid-column: 1;
            }

            .inventory-filter-reset {
                grid-column: 2;
                grid-row: auto;
                width: 100%;
            }

            .inventory-panel-head {
                padding: 1rem;
            }

            .inventory-sort {
                display: none;
            }

            /* Tabel menjadi kartu di layar kecil */
            .inventory-table {
                min-width: 0;
            }

            .inventory-table thead {
                display: none;
            }

            .inventory-table,
            .inventory-table tbody,
            .inventory-table tr,
            .inventory-table td {
                display: block;
                width: 100%;
            }

            .inventory-table tbody tr {
                padding: 1rem;
                border-bottom: 1px solid var(--border-light);
            }

            .inventory-table tbody tr:last-child {
                border-bottom: none;
            }

            .inventory-table tbody td {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 0.35rem 0;
                border: none;
                background: transparent;
            }

            .inventory-table tbody tr:hover td,
            .inventory-table tbody tr.is-archived td,
            .inventory-table tbody tr.is-archived:hover td {
                background: transparent;
            }

            .inventory-table tbody td:first-child {
                display: block;
                padding: 0 0 0.7rem;
            }

            .inventory-table tbody td:not(:first-child)::before {
                content: attr(data-label);
                color: var(--text-muted);
                font-size: 0.68rem;
                font-weight: 700;
                letter-spacing: 0.05em;
                text-transform: uppercase;
            }

            .inventory-table tbody td[data-label="Aksi"] {
                padding-top: 0.75rem;
            }

            .inventory-table .status-form {
                display: flex;
                justify-content: flex-end;
            }

            .inventory-table .featured-pill {
                min-width: auto;
            }

            .car-cell {
                min-width: 0;
            }

            .inventory-pagination {
                justify-content: center;
                padding: 0.9rem;
            }
        }

        @media (max-width: 575.98px) {
            .inventory-filter {
                grid-template-columns: minmax(0, 1fr) 42px;
            }

            .inventory-search,
            .inventory-select {
                grid-column: 1 / -1;
            }

            .inventory-filter-submit {
                grid-column: 1;
            }

            .inventory-filter-reset {
                grid-column: 2;
                width: 42px;
            }

            .car-thumb {
                width: 64px;
                height: 50px;
            }
        }
    </style>
@endpush

<x-layouts.app title="Mobil - RodaTa Admin">
    <x-layout.page-header title="Mobil" subtitle="Kelola stok, status, dan unit unggulan showroom">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" :href="route('admin.cars.create')">
                Tambah Mobil
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Ringkasan stok --}}
    <div class="inventory-stats">
        @foreach ($statCards as $card)
            <a href="{{ $card['href'] }}"
               class="inventory-stat inventory-stat-{{ $card['tone'] }} {{ ($card['key'] === 'total' && $status === '') || $card['key'] === $status ? 'is-active' : '' }}">
                <span class="inventory-stat-icon">
                    <i class="{{ $card['icon'] }}"></i>
                </span>
                <span class="inventory-stat-copy">
                    <strong>{{ number_format($card['value'], 0, ',', '.') }}</strong>
                    <span>{{ $card['label'] }}</span>
                </span>
                <span class="inventory-stat-progress" aria-hidden="true">
                    <span style="width: {{ $card['percentage'] }}%;"></span>
                </span>
            </a>
        @endforeach
    </div>

    <div class="inventory-tabbar">
        <nav class="inventory-tabs" aria-label="Kelompok stok mobil">
            <a href="{{ route('admin.cars', array_filter(['q' => $search, 'status' => $status, 'brand' => $brand])) }}"
               class="inventory-tab {{ $showArchived ? '' : 'is-active' }}">
                Stok Aktif
                <span>{{ number_format($stats['total'], 0, ',', '.') }}</span>
            </a>
            <a href="{{ route('admin.cars', array_filter(['q' => $search, 'status' => $status, 'brand' => $brand, 'arsip' => 1])) }}"
               class="inventory-tab {{ $showArchived ? 'is-active' : '' }}">
                Arsip
                <span>{{ number_format($archivedCount, 0, ',', '.') }}</span>
            </a>
        </nav>
        <span class="inventory-tab-note">
            <i class="fas fa-circle-info"></i>
            {{ $showArchived ? 'Unit diarsipkan tidak tampil di katalog.' : 'Unit aktif tampil di katalog publik.' }}
        </span>
    </div>

    {{-- Filter --}}
    <x-layout.modern-card class="inventory-filter-card">
        <form method="GET" action="{{ route('admin.cars') }}" class="inventory-filter">
            @if ($showArchived)
                <input type="hidden" name="arsip" value="1">
            @endif

            <div class="inventory-search">
                <i class="fas fa-search"></i>
                <input type="search" id="q" name="q" value="{{ $search }}"
                       placeholder="Cari nama, merek, atau model..." aria-label="Cari mobil">
                @if ($search !== '')
                    <a href="{{ route('admin.cars', array_filter(['status' => $status, 'brand' => $brand, 'arsip' => $showArchived ? 1 : null])) }}"
                       class="inventory-search-clear" aria-label="Hapus pencarian">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>

            <select id="status" name="status" class="inventory-select" aria-label="Filter status">
                <option value="">Semua status</option>
                @foreach ($statuses as $statusOption)
                    <option value="{{ $statusOption->value }}" @selected($status === $statusOption->value)>
                        {{ $statusOption->label() }}
                    </option>
                @endforeach
            </select>

            <select id="brand" name="brand" class="inventory-select" aria-label="Filter merek">
                <option value="">Semua merek</option>
                @foreach ($brands as $brandOption)
                    <option value="{{ $brandOption->slug }}" @selected($brand === $brandOption->slug)>
                        {{ $brandOption->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="inventory-filter-submit">
                <i class="fas fa-filter"></i>
                Tampilkan
            </button>

            <a href="{{ route('admin.cars', $showArchived ? ['arsip' => 1] : []) }}"
               class="inventory-filter-reset" title="Reset filter" aria-label="Reset filter">
                <i class="fas fa-rotate-left"></i>
            </a>
        </form>

        @if ($search !== '' || $status !== '' || $brand !== '')
            <div class="inventory-chips">
                <span class="inventory-chips-label">Filter aktif:</span>

                @if ($search !== '')
                    <span class="inventory-chip">
                        <i class="fas fa-search"></i>
                        “{{ $search }}”
                        <a href="{{ route('admin.cars', array_filter(['status' => $status, 'brand' => $brand, 'arsip' => $showArchived ? 1 : null])) }}"
                           aria-label="Hapus filter pencarian">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if ($statusLabel)
                    <span class="inventory-chip">
                        <i class="fas fa-circle"></i>
                        {{ $statusLabel }}
                        <a href="{{ route('admin.cars', array_filter(['q' => $search, 'brand' => $brand, 'arsip' => $showArchived ? 1 : null])) }}"
                           aria-label="Hapus filter status">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if ($brandName)
                    <span class="inventory-chip">
                        <i class="fas fa-trademark"></i>
                        {{ $brandName }}
                        <a href="{{ route('admin.cars', array_filter(['q' => $search, 'status' => $status, 'arsip' => $showArchived ? 1 : null])) }}"
                           aria-label="Hapus filter merek">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                <a href="{{ route('admin.cars', $showArchived ? ['arsip' => 1] : []) }}" class="inventory-chips-clear">
                    Bersihkan semua
                </a>
            </div>
        @endif
    </x-layout.modern-card>

    {{-- Daftar mobil --}}
    <x-layout.modern-card :padding="false" class="inventory-panel">
        <div class="inventory-panel-head">
            <div>
                <h5>{{ $showArchived ? 'Mobil Diarsipkan' : 'Daftar Stok' }}</h5>
                <p>
                    @if ($cars->total() > 0)
                        Menampilkan {{ $cars->firstItem() }}–{{ $cars->lastItem() }} dari
                        {{ number_format($cars->total(), 0, ',', '.') }} unit
                    @else
                        Tidak ada unit yang cocok dengan filter
                    @endif
                </p>
            </div>
            <span class="inventory-sort">
                <i class="fas fa-arrow-down-wide-short"></i>
                Terbaru
            </span>
        </div>

        <div class="table-responsive">
            <table class="table inventory-table">
                <thead>
                    <tr>
                        <th>Mobil</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th class="text-center">Unggulan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cars as $car)
                        @php
                            $name = trim(($car->carModel?->brand?->name ?? '').' '.($car->carModel?->name ?? '').' '.($car->variant ?? ''));
                        @endphp
                        <tr class="{{ $car->trashed() ? 'is-archived' : '' }}">
                            <td>
                                <div class="car-cell">
                                    <div class="car-thumb">
                                        @if ($car->primaryPhoto?->url())
                                            <img src="{{ $car->primaryPhoto->thumbnailUrl() }}" alt="{{ $name }}">
                                        @else
                                            <i class="fas fa-car"></i>
                                        @endif
                                    </div>
                                    <div class="car-cell-copy">
                                        <div class="car-cell-title">{{ $name }}</div>
                                        <div class="car-cell-meta">
                                            <span><i class="fas fa-calendar-alt"></i> {{ $car->year }}</span>
                                            <span><i class="fas fa-gauge-high"></i> {{ number_format($car->mileage, 0, ',', '.') }} km</span>
                                            <span><i class="fas fa-gear"></i> {{ $car->transmission?->label() }}</span>
                                        </div>
                                        @if ($car->trashed() || $car->photos_count === 0)
                                            <div class="car-cell-location">
                                            @if ($car->trashed())
                                                <x-ui.badge variant="danger" icon="fas fa-box-archive">Diarsipkan</x-ui.badge>
                                            @elseif ($car->photos_count === 0)
                                                <span class="car-photo-warning">
                                                    <i class="fas fa-triangle-exclamation"></i> Belum ada foto
                                                </span>
                                            @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td data-label="Harga">
                                <div class="car-price">Rp {{ number_format($car->price, 0, ',', '.') }}</div>
                                <span class="car-price-note">{{ $car->photos_count }} foto</span>
                            </td>
                            <td data-label="Status">
                                @if ($car->trashed())
                                    <x-ui.badge variant="danger">{{ $car->status->label() }}</x-ui.badge>
                                @else
                                    <form method="POST" action="{{ route('admin.cars.status', $car) }}" class="status-form">
                                        @csrf
                                        <select name="status"
                                                class="status-select {{ $statusClasses[$car->status->value] ?? '' }}"
                                                onchange="this.form.submit()"
                                                aria-label="Ubah status {{ $name }}">
                                            @foreach ($statuses as $statusOption)
                                                <option value="{{ $statusOption->value }}" @selected($car->status === $statusOption)>
                                                    {{ $statusOption->label() }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                @endif
                            </td>
                            <td class="text-center" data-label="Unggulan">
                                @if ($car->trashed())
                                    <span class="text-muted">—</span>
                                @else
                                    <form method="POST" action="{{ route('admin.cars.featured', $car) }}">
                                        @csrf
                                        <button type="submit"
                                                class="featured-pill {{ $car->is_featured ? 'is-featured' : '' }}"
                                                title="{{ $car->is_featured ? 'Hapus dari unggulan' : 'Tandai unggulan' }}">
                                            <i class="{{ $car->is_featured ? 'fas' : 'far' }} fa-star"></i>
                                            <span>{{ $car->is_featured ? 'Unggulan' : 'Biasa' }}</span>
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td data-label="Aksi">
                                <div class="row-actions">
                                    @if ($car->trashed())
                                        <form method="POST" action="{{ route('admin.cars.restore', $car->id) }}">
                                            @csrf
                                            <button type="submit" class="row-action row-action-success" title="Pulihkan mobil">
                                                <i class="fas fa-rotate-left"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.cars', $baseQuery + ['force' => $car->id]) }}"
                                           class="row-action row-action-danger" title="Hapus permanen">
                                            <i class="fas fa-trash-can"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.cars.edit', $car) }}"
                                           class="row-action" title="Edit mobil">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <a href="{{ route('admin.cars', $baseQuery + ['delete' => $car->id]) }}"
                                           class="row-action row-action-danger" title="Arsipkan mobil">
                                            <i class="fas fa-box-archive"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-ui.empty-state
                                    icon="fas fa-car"
                                    title="{{ $showArchived ? 'Arsip kosong' : 'Belum ada mobil' }}"
                                    description="{{ $showArchived ? 'Mobil yang diarsipkan akan muncul di sini.' : 'Tambahkan unit pertama untuk mulai mengelola stok.' }}"
                                    :action-label="$showArchived ? null : 'Tambah Mobil'"
                                    :action-href="$showArchived ? null : route('admin.cars.create')"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($cars->hasPages())
            <div class="inventory-pagination">
                {{ $cars->links() }}
            </div>
        @endif
    </x-layout.modern-card>

    @if ($deletingCar && ! $forceDeletingCar)
        <x-ui.confirm-modal
            :show="true"
            title="Arsipkan Mobil"
            :message="'Arsipkan '.trim(($deletingCar->carModel?->brand?->name ?? '').' '.($deletingCar->carModel?->name ?? '')).'? Unit dapat dipulihkan dari tab Arsip.'"
            confirm-text="Arsipkan"
            :action="route('admin.cars.destroy', $deletingCar)"
            method="DELETE"
            :cancel-url="$closeUrl"
            variant="danger"
            icon="fas fa-box-archive"
        />
    @endif

    @if ($forceDeletingCar)
        <x-ui.confirm-modal
            :show="true"
            title="Hapus Permanen"
            :message="'Hapus permanen '.trim(($forceDeletingCar->carModel?->brand?->name ?? '').' '.($forceDeletingCar->carModel?->name ?? '')).'? Data mobil, foto, dan relasinya akan dihapus dan tidak dapat dipulihkan.'"
            confirm-text="Hapus Permanen"
            :action="route('admin.cars.force-destroy', $forceDeletingCar->id)"
            method="DELETE"
            :cancel-url="$closeUrl"
            variant="danger"
            icon="fas fa-triangle-exclamation"
        />
    @endif
</x-layouts.app>
