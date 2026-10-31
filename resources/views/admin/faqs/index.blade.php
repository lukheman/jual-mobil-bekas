@php
    $sortQuery = $sort !== 'order_asc' ? $sort : null;

    $baseQuery = array_filter([
        'q' => $search,
        'status' => $status,
        'sort' => $sortQuery,
        'page' => request('page'),
    ], fn ($value) => $value !== null && $value !== '');

    $closeUrl = route('admin.faqs', $baseQuery);

    $returnFields = array_filter([
        'return_q' => $search,
        'return_status' => $status,
        'return_sort' => $sortQuery,
        'return_page' => request('page'),
    ], fn ($value) => $value !== null && $value !== '');

    $statusLabels = [
        'published' => 'Terbit',
        'draft' => 'Draf',
    ];

    $statusLabel = $statusLabels[$status] ?? null;
    $hasFilters = $search !== '' || $status !== '';

    $percentage = fn (int $value, int $total): int => $total > 0
        ? (int) round($value / $total * 100)
        : 0;

    $statCards = [
        [
            'key' => 'total',
            'label' => 'Total FAQ',
            'meta' => 'Seluruh pertanyaan',
            'icon' => 'fas fa-circle-question',
            'tone' => 'primary',
            'value' => $stats['total'],
            'percentage' => $stats['total'] > 0 ? 100 : 0,
            'status' => '',
            'href' => route('admin.faqs', array_filter(['q' => $search, 'sort' => $sortQuery])),
        ],
        [
            'key' => 'published',
            'label' => 'Terbit',
            'meta' => 'Tampil di situs',
            'icon' => 'fas fa-circle-check',
            'tone' => 'success',
            'value' => $stats['published'],
            'percentage' => $percentage($stats['published'], $stats['total']),
            'status' => 'published',
            'href' => route('admin.faqs', array_filter(['q' => $search, 'sort' => $sortQuery, 'status' => 'published'])),
        ],
        [
            'key' => 'draft',
            'label' => 'Draf',
            'meta' => 'Belum ditampilkan',
            'icon' => 'fas fa-pen-to-square',
            'tone' => 'warning',
            'value' => $stats['draft'],
            'percentage' => $percentage($stats['draft'], $stats['total']),
            'status' => 'draft',
            'href' => route('admin.faqs', array_filter(['q' => $search, 'sort' => $sortQuery, 'status' => 'draft'])),
        ],
        [
            'key' => 'recent',
            'label' => 'Diperbarui',
            'meta' => '7 hari terakhir',
            'icon' => 'fas fa-clock-rotate-left',
            'tone' => 'info',
            'value' => $stats['recent'],
            'percentage' => $percentage($stats['recent'], $stats['total']),
            'status' => 'recent',
            'href' => route('admin.faqs', array_filter(['q' => $search, 'status' => $status, 'sort' => 'newest'])),
        ],
    ];

    $updatedLabel = static function (?\Illuminate\Support\Carbon $date): string {
        if (! $date) {
            return '—';
        }

        $minutes = max(0, (int) $date->diffInMinutes(now()));

        if ($minutes < 1) {
            return 'Baru saja';
        }

        if ($minutes < 60) {
            return $minutes.' menit lalu';
        }

        $hours = intdiv($minutes, 60);

        if ($hours < 24) {
            return $hours.' jam lalu';
        }

        $days = intdiv($hours, 24);

        if ($days < 7) {
            return $days.' hari lalu';
        }

        return $date->format('d/m/Y');
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

        /* Ringkasan FAQ */
        .faq-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .faq-stat {
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

        .faq-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(23, 53, 43, 0.12);
            border-color: var(--border-color);
            color: inherit;
        }

        .faq-stat.is-active {
            border-color: var(--stat-color);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--stat-color) 14%, transparent);
        }

        .faq-stat-primary { --stat-color: var(--bs-primary); --stat-soft: rgba(29, 91, 66, 0.1); }
        .faq-stat-success { --stat-color: var(--bs-success); --stat-soft: rgba(77, 138, 99, 0.12); }
        .faq-stat-warning { --stat-color: var(--bs-warning); --stat-soft: rgba(189, 138, 56, 0.14); }
        .faq-stat-info { --stat-color: var(--bs-info); --stat-soft: rgba(79, 130, 144, 0.12); }

        .faq-stat-icon {
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

        .faq-stat-copy {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
            min-width: 0;
        }

        .faq-stat-copy strong {
            color: var(--text-primary);
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .faq-stat-copy span {
            color: var(--text-muted);
            font-size: 0.76rem;
            font-weight: 500;
            margin-top: 0.2rem;
        }

        .faq-stat-copy small {
            color: var(--text-muted);
            font-size: 0.65rem;
            margin-top: 0.12rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .faq-stat-progress {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            background: var(--bg-tertiary);
        }

        .faq-stat-progress span {
            display: block;
            height: 100%;
            background: var(--stat-color);
            transition: width 0.3s ease;
        }

        /* Filter */
        .faq-filter-card {
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .faq-filter-card:hover,
        .faq-panel:hover {
            transform: none;
        }

        .faq-filter {
            display: grid;
            grid-template-columns: minmax(240px, 1fr) 180px 190px auto auto;
            gap: 0.65rem;
            align-items: center;
        }

        .faq-search {
            position: relative;
            display: flex;
            align-items: center;
        }

        .faq-search > i {
            position: absolute;
            left: 0.9rem;
            color: var(--text-muted);
            font-size: 0.85rem;
            pointer-events: none;
        }

        .faq-search input {
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

        .faq-search input:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(29, 91, 66, 0.1);
        }

        .faq-search input::placeholder {
            color: var(--text-muted);
        }

        .faq-search-clear {
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

        .faq-search-clear:hover {
            background: var(--hover-bg);
            color: var(--bs-danger);
        }

        .faq-select {
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

        .faq-select:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(29, 91, 66, 0.1);
        }

        .faq-filter-submit {
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

        .faq-filter-submit:hover {
            background: #164934;
            transform: translateY(-1px);
        }

        .faq-filter-reset {
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

        .faq-filter-reset:hover {
            border-color: var(--bs-danger);
            color: var(--bs-danger);
        }

        .faq-chips {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.45rem;
            margin-top: 0.85rem;
            padding-top: 0.85rem;
            border-top: 1px solid var(--border-light);
        }

        .faq-chips-label {
            color: var(--text-muted);
            font-size: 0.74rem;
            font-weight: 600;
        }

        .faq-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.55rem;
            border-radius: 999px;
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            font-size: 0.74rem;
        }

        .faq-chip > i {
            color: var(--bs-primary);
            font-size: 0.65rem;
        }

        .faq-chip a {
            display: grid;
            place-items: center;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            color: var(--text-muted);
        }

        .faq-chip a:hover {
            background: var(--bg-secondary);
            color: var(--bs-danger);
        }

        .faq-chips-clear {
            margin-left: 0.25rem;
            color: var(--bs-danger);
            font-size: 0.74rem;
            font-weight: 600;
            text-decoration: none;
        }

        .faq-chips-clear:hover {
            text-decoration: underline;
        }

        /* Panel daftar */
        .faq-panel {
            padding: 0;
            overflow: hidden;
        }

        .faq-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.15rem 1.35rem;
            border-bottom: 1px solid var(--border-light);
        }

        .faq-panel-head h5 {
            margin: 0 0 0.15rem;
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 700;
        }

        .faq-panel-head p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.76rem;
        }

        .faq-sort {
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

        .faq-table {
            margin: 0;
            min-width: 860px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .faq-table thead th {
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

        .faq-table tbody td {
            padding: 0.95rem 1.1rem;
            border: none;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .faq-table tbody tr:last-child td {
            border-bottom: none;
        }

        .faq-table tbody tr:hover td {
            background: var(--hover-bg);
        }

        .faq-table tbody tr.is-draft td:first-child {
            box-shadow: inset 3px 0 0 var(--bs-warning);
        }

        .faq-order {
            display: inline-grid;
            place-items: center;
            min-width: 34px;
            height: 34px;
            padding: 0 0.45rem;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            font-size: 0.78rem;
            font-weight: 700;
        }

        .faq-cell-copy {
            min-width: 240px;
            max-width: 640px;
        }

        .faq-question {
            color: var(--text-primary);
            font-size: 0.9rem;
            font-weight: 700;
            line-height: 1.35;
        }

        .faq-answer {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-top: 0.2rem;
            color: var(--text-muted);
            font-size: 0.78rem;
            line-height: 1.5;
        }

        .faq-health {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.28rem 0.58rem;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .faq-health.is-published {
            background: rgba(77, 138, 99, 0.14);
            color: var(--bs-success);
        }

        .faq-health.is-draft {
            background: rgba(189, 138, 56, 0.14);
            color: var(--bs-warning);
        }

        .faq-updated {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
            white-space: nowrap;
        }

        .faq-updated strong {
            color: var(--text-secondary);
            font-size: 0.76rem;
            font-weight: 600;
        }

        .faq-updated span {
            color: var(--text-muted);
            font-size: 0.68rem;
        }

        .faq-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.35rem;
        }

        .faq-toggle-form {
            display: inline-flex;
            margin: 0;
        }

        .faq-row-action {
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

        .faq-row-action:hover {
            border-color: var(--bs-primary);
            background: var(--hover-bg);
            color: var(--bs-primary);
        }

        .faq-row-action.is-danger:hover {
            border-color: var(--bs-danger);
            background: rgba(185, 88, 76, 0.06);
            color: var(--bs-danger);
        }

        .faq-row-action.is-live {
            border-color: rgba(77, 138, 99, 0.35);
            color: var(--bs-success);
        }

        .faq-row-action.is-live:hover {
            border-color: var(--bs-warning);
            background: rgba(189, 138, 56, 0.08);
            color: var(--bs-warning);
        }

        .faq-pagination {
            display: flex;
            justify-content: flex-end;
            padding: 1rem 1.35rem;
            border-top: 1px solid var(--border-light);
            background: var(--bg-secondary);
        }

        .faq-panel-note {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.7rem 1.35rem;
            border-top: 1px solid var(--border-light);
            background: var(--bg-secondary);
            color: var(--text-muted);
            font-size: 0.72rem;
        }

        .faq-panel-note i {
            color: var(--bs-info);
        }

        /* Modal FAQ */
        .faq-modal .modal-content-custom {
            max-width: 600px;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
        }

        .faq-modal-intro {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.9rem 1rem;
            margin-bottom: 1.15rem;
            border: 1px solid var(--border-light);
            border-radius: 9px;
            background: var(--bg-tertiary);
        }

        .faq-modal-intro-icon {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border-radius: 9px;
            background: rgba(29, 91, 66, 0.1);
            color: var(--bs-primary);
        }

        .faq-modal-intro strong {
            display: block;
            color: var(--text-primary);
            font-size: 0.82rem;
            font-weight: 700;
        }

        .faq-modal-intro span {
            display: block;
            margin-top: 0.15rem;
            color: var(--text-muted);
            font-size: 0.72rem;
            line-height: 1.5;
        }

        .faq-modal-meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .faq-modal-meta div {
            padding: 0.6rem 0.7rem;
            border: 1px solid var(--border-light);
            border-radius: 7px;
            background: var(--bg-secondary);
            min-width: 0;
        }

        .faq-modal-meta span {
            display: block;
            color: var(--text-muted);
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .faq-modal-meta strong {
            display: block;
            margin-top: 0.15rem;
            color: var(--text-primary);
            font-size: 0.8rem;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .faq-form-row {
            display: grid;
            grid-template-columns: 140px minmax(0, 1fr);
            gap: 0.9rem;
            align-items: start;
            margin-bottom: 0.25rem;
        }

        .faq-toggle-field {
            padding: 0.55rem 0.75rem;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            background: var(--bg-secondary);
        }

        .faq-toggle-field small {
            display: block;
            margin-top: 0.35rem;
            color: var(--text-muted);
            font-size: 0.7rem;
        }

        @media (max-width: 1199.98px) {
            .faq-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .faq-filter {
                grid-template-columns: minmax(200px, 1fr) 1fr 1fr auto auto;
            }
        }

        @media (max-width: 991.98px) {
            .faq-filter {
                grid-template-columns: 1fr 1fr;
            }

            .faq-search {
                grid-column: 1 / -1;
            }

            .faq-filter-submit {
                grid-column: 1;
            }

            .faq-filter-reset {
                grid-column: 2;
                width: 100%;
            }
        }

        @media (max-width: 767.98px) {
            .faq-stats {
                gap: 0.65rem;
            }

            .faq-stat {
                padding: 0.85rem 0.9rem 1.1rem;
            }

            .faq-stat-icon {
                width: 36px;
                height: 36px;
                font-size: 0.85rem;
            }

            .faq-stat-copy strong {
                font-size: 1.15rem;
            }

            .faq-stat-copy span {
                font-size: 0.68rem;
            }

            .faq-stat-copy small {
                font-size: 0.62rem;
                white-space: normal;
                overflow: visible;
            }

            .faq-panel-head {
                padding: 1rem;
            }

            /* Tabel menjadi kartu di layar kecil */
            .faq-table {
                min-width: 0;
            }

            .faq-table thead {
                display: none;
            }

            .faq-table,
            .faq-table tbody,
            .faq-table tr,
            .faq-table td {
                display: block;
                width: 100%;
            }

            .faq-table tbody tr {
                padding: 1rem;
                border-bottom: 1px solid var(--border-light);
            }

            .faq-table tbody tr:last-child {
                border-bottom: none;
            }

            .faq-table tbody td {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 0.35rem 0;
                border: none;
                background: transparent;
            }

            .faq-table tbody tr:hover td {
                background: transparent;
            }

            .faq-table tbody tr.is-draft td:first-child {
                box-shadow: none;
            }

            .faq-table tbody td:first-child {
                display: block;
                padding: 0 0 0.6rem;
            }

            .faq-table tbody td:not(:first-child)::before {
                content: attr(data-label);
                color: var(--text-muted);
                font-size: 0.68rem;
                font-weight: 700;
                letter-spacing: 0.05em;
                text-transform: uppercase;
            }

            .faq-table tbody td[colspan] {
                display: block;
            }

            .faq-table tbody td[colspan]::before {
                content: none;
            }

            .faq-table tbody td[data-label="Aksi"] {
                padding-top: 0.75rem;
            }

            .faq-cell-copy {
                min-width: 0;
                max-width: none;
            }

            .faq-actions {
                flex-wrap: wrap;
            }

            .faq-pagination {
                justify-content: center;
                padding: 0.9rem;
            }

            .faq-form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .faq-filter {
                grid-template-columns: minmax(0, 1fr) 42px;
            }

            .faq-search,
            .faq-select {
                grid-column: 1 / -1;
            }

            .faq-filter-submit {
                grid-column: 1;
            }

            .faq-filter-reset {
                grid-column: 2;
                width: 42px;
            }

            .faq-modal .modal-content-custom {
                padding: 1.1rem;
                max-height: calc(100vh - 1rem);
            }

            .faq-modal-meta {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
@endpush

<x-layouts.app title="FAQ - RodaTa Admin">
    <x-layout.page-header title="FAQ" subtitle="Kelola pertanyaan yang sering diajukan pembeli">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" :href="route('admin.faqs', $baseQuery + ['create' => 1])">
                Tambah FAQ
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Ringkasan FAQ --}}
    <div class="faq-stats">
        @foreach ($statCards as $card)
            <a href="{{ $card['href'] }}"
               class="faq-stat faq-stat-{{ $card['tone'] }} {{ $status === $card['status'] ? 'is-active' : '' }}">
                <span class="faq-stat-icon">
                    <i class="{{ $card['icon'] }}"></i>
                </span>
                <span class="faq-stat-copy">
                    <strong>{{ number_format($card['value'], 0, ',', '.') }}</strong>
                    <span>{{ $card['label'] }}</span>
                    <small>{{ $card['meta'] }}</small>
                </span>
                <span class="faq-stat-progress" aria-hidden="true">
                    <span style="width: {{ $card['percentage'] }}%;"></span>
                </span>
            </a>
        @endforeach
    </div>

    {{-- Filter --}}
    <x-layout.modern-card class="faq-filter-card">
        <form method="GET" action="{{ route('admin.faqs') }}" class="faq-filter">
            <div class="faq-search">
                <i class="fas fa-search"></i>
                <input type="search" id="q" name="q" value="{{ $search }}"
                       placeholder="Cari pertanyaan atau jawaban..." aria-label="Cari FAQ">
                @if ($search !== '')
                    <a href="{{ route('admin.faqs', array_filter(['status' => $status, 'sort' => $sortQuery])) }}"
                       class="faq-search-clear" aria-label="Hapus pencarian">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>

            <select id="status" name="status" class="faq-select" aria-label="Filter status FAQ">
                <option value="">Semua status</option>
                <option value="published" @selected($status === 'published')>Terbit</option>
                <option value="draft" @selected($status === 'draft')>Draf</option>
            </select>

            <select id="sort" name="sort" class="faq-select" aria-label="Urutkan FAQ">
                @foreach ($sortOptions as $sortValue => $sortLabel)
                    <option value="{{ $sortValue }}" @selected($sort === $sortValue)>
                        {{ $sortLabel }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="faq-filter-submit">
                <i class="fas fa-filter"></i>
                Tampilkan
            </button>

            <a href="{{ route('admin.faqs') }}" class="faq-filter-reset" title="Reset filter" aria-label="Reset filter">
                <i class="fas fa-rotate-left"></i>
            </a>
        </form>

        @if ($hasFilters)
            <div class="faq-chips">
                <span class="faq-chips-label">Filter aktif:</span>

                @if ($search !== '')
                    <span class="faq-chip">
                        <i class="fas fa-search"></i>
                        “{{ $search }}”
                        <a href="{{ route('admin.faqs', array_filter(['status' => $status, 'sort' => $sortQuery])) }}"
                           aria-label="Hapus filter pencarian">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if ($statusLabel)
                    <span class="faq-chip">
                        <i class="fas fa-circle-half-stroke"></i>
                        {{ $statusLabel }}
                        <a href="{{ route('admin.faqs', array_filter(['q' => $search, 'sort' => $sortQuery])) }}"
                           aria-label="Hapus filter status">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                <a href="{{ route('admin.faqs') }}" class="faq-chips-clear">Bersihkan semua</a>
            </div>
        @endif
    </x-layout.modern-card>

    {{-- Daftar FAQ --}}
    <x-layout.modern-card :padding="false" class="faq-panel">
        <div class="faq-panel-head">
            <div>
                <h5>Daftar FAQ</h5>
                <p>
                    @if ($faqs->total() > 0)
                        Menampilkan {{ $faqs->firstItem() }}–{{ $faqs->lastItem() }} dari
                        {{ number_format($faqs->total(), 0, ',', '.') }} pertanyaan
                    @elseif ($hasFilters)
                        Tidak ada FAQ yang cocok dengan filter
                    @else
                        Belum ada FAQ terdaftar
                    @endif
                </p>
            </div>
            <span class="faq-sort">
                <i class="fas fa-arrow-down-wide-short"></i>
                {{ $sortOptions[$sort] }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table faq-table">
                <thead>
                    <tr>
                        <th>Pertanyaan</th>
                        <th style="width: 90px;">Urutan</th>
                        <th>Status</th>
                        <th>Diperbarui</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($faqs as $faq)
                        <tr class="{{ $faq->is_published ? '' : 'is-draft' }}">
                            <td data-label="Pertanyaan">
                                <div class="faq-cell-copy">
                                    <div class="faq-question">{{ $faq->question }}</div>
                                    <div class="faq-answer" title="{{ $faq->answer }}">
                                        {{ \Illuminate\Support\Str::limit($faq->answer, 180) }}
                                    </div>
                                </div>
                            </td>
                            <td data-label="Urutan">
                                <span class="faq-order" title="Urutan tampil {{ $faq->sort_order }}">
                                    {{ $faq->sort_order }}
                                </span>
                            </td>
                            <td data-label="Status">
                                @if ($faq->is_published)
                                    <span class="faq-health is-published">
                                        <i class="fas fa-circle-check"></i>
                                        Terbit
                                    </span>
                                @else
                                    <span class="faq-health is-draft">
                                        <i class="fas fa-pen"></i>
                                        Draf
                                    </span>
                                @endif
                            </td>
                            <td data-label="Diperbarui">
                                <div class="faq-updated"
                                     title="{{ $faq->updated_at?->format('d/m/Y H:i') }}">
                                    <strong>{{ $updatedLabel($faq->updated_at) }}</strong>
                                    <span>Dibuat {{ $faq->created_at?->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td data-label="Aksi">
                                <div class="faq-actions">
                                    <form method="POST" action="{{ route('admin.faqs.toggle', $faq) }}" class="faq-toggle-form">
                                        @csrf
                                        @foreach ($returnFields as $field => $value)
                                            <input type="hidden" name="{{ $field }}" value="{{ $value }}">
                                        @endforeach
                                        <button type="submit"
                                                class="faq-row-action {{ $faq->is_published ? 'is-live' : '' }}"
                                                title="{{ $faq->is_published ? 'Jadikan draf' : 'Terbitkan FAQ' }}"
                                                aria-label="{{ $faq->is_published ? 'Jadikan draf' : 'Terbitkan FAQ' }}">
                                            <i class="fas {{ $faq->is_published ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                        </button>
                                    </form>

                                    <a href="{{ route('admin.faqs', $baseQuery + ['edit' => $faq->id]) }}"
                                       class="faq-row-action" title="Edit FAQ"
                                       aria-label="Edit FAQ">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <a href="{{ route('admin.faqs', $baseQuery + ['delete' => $faq->id]) }}"
                                       class="faq-row-action is-danger" title="Hapus FAQ"
                                       aria-label="Hapus FAQ">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-ui.empty-state
                                    icon="fas fa-circle-question"
                                    :title="$hasFilters ? 'Tidak ada FAQ yang cocok' : 'Belum ada FAQ'"
                                    :description="$hasFilters
                                        ? 'Coba ubah kata kunci atau bersihkan filter untuk melihat FAQ lainnya.'
                                        : 'Tambahkan pertanyaan untuk membantu pembeli memahami layanan showroom.'"
                                    :action-label="$hasFilters ? 'Bersihkan Filter' : 'Tambah FAQ'"
                                    :action-href="$hasFilters ? route('admin.faqs') : route('admin.faqs', ['create' => 1])"
                                    :action-icon="$hasFilters ? 'fas fa-rotate-left' : 'fas fa-plus'"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($stats['draft'] > 0)
            <div class="faq-panel-note">
                <i class="fas fa-circle-info"></i>
                FAQ berstatus draf tidak ditampilkan di halaman publik. Gunakan ikon mata untuk menerbitkan.
            </div>
        @endif

        @if ($faqs->hasPages())
            <div class="faq-pagination">
                {{ $faqs->links() }}
            </div>
        @endif
    </x-layout.modern-card>

    @if ($showCreateModal && ! $editingFaq && ! $deletingFaq)
        <x-layout.modal-form
            title="Tambah FAQ"
            :action="route('admin.faqs.store')"
            :close-url="$closeUrl"
            submit-label="Simpan FAQ"
            close-label="Batal"
            class="faq-modal"
        >
            @foreach ($returnFields as $field => $value)
                <input type="hidden" name="{{ $field }}" value="{{ $value }}">
            @endforeach

            <div class="faq-modal-intro">
                <span class="faq-modal-intro-icon">
                    <i class="fas fa-circle-question"></i>
                </span>
                <div>
                    <strong>Tambah pertanyaan baru</strong>
                    <span>FAQ yang diterbitkan akan tampil pada bagian pertanyaan umum di halaman publik.</span>
                </div>
            </div>

            <x-form.input
                name="question"
                label="Pertanyaan"
                :value="old('question')"
                placeholder="Apakah bisa test drive?"
                maxlength="255"
                required
                :error="$errors->first('question')"
            />

            <x-form.textarea
                name="answer"
                label="Jawaban"
                :value="old('answer')"
                placeholder="Tulis jawaban yang jelas dan ringkas..."
                :rows="5"
                maxlength="5000"
                hint="Maksimal 5.000 karakter. Jelaskan langkah atau syaratnya secara singkat."
                required
                :error="$errors->first('answer')"
            />

            <div class="faq-form-row">
                <x-form.input
                    type="number"
                    name="sort_order"
                    label="Urutan"
                    :value="old('sort_order', $nextSortOrder)"
                    min="0"
                    max="9999"
                    hint="Angka kecil tampil lebih dulu."
                    :error="$errors->first('sort_order')"
                />

                <div>
                    <label class="form-label">Status tayang</label>
                    <div class="faq-toggle-field">
                        <x-form.toggle
                            name="is_published"
                            label="Terbitkan sekarang"
                            :checked="old('is_published', true)"
                        />
                        <small>Matikan untuk menyimpan sebagai draf terlebih dahulu.</small>
                    </div>
                </div>
            </div>
        </x-layout.modal-form>
    @endif

    @if ($editingFaq)
        <x-layout.modal-form
            title="Edit FAQ"
            :action="route('admin.faqs.update', $editingFaq)"
            method="PUT"
            :close-url="$closeUrl"
            submit-label="Simpan Perubahan"
            close-label="Batal"
            class="faq-modal"
        >
            @foreach ($returnFields as $field => $value)
                <input type="hidden" name="{{ $field }}" value="{{ $value }}">
            @endforeach

            <div class="faq-modal-intro">
                <span class="faq-modal-intro-icon">
                    <i class="fas fa-pen"></i>
                </span>
                <div>
                    <strong>Perbarui isi FAQ</strong>
                    <span>Perubahan langsung tampil di halaman publik jika FAQ berstatus terbit.</span>
                </div>
            </div>

            <div class="faq-modal-meta">
                <div>
                    <span>Status</span>
                    <strong>{{ $editingFaq->is_published ? 'Terbit' : 'Draf' }}</strong>
                </div>
                <div>
                    <span>Urutan</span>
                    <strong>{{ $editingFaq->sort_order }}</strong>
                </div>
                <div>
                    <span>Diperbarui</span>
                    <strong>{{ $updatedLabel($editingFaq->updated_at) }}</strong>
                </div>
            </div>

            <x-form.input
                name="question"
                label="Pertanyaan"
                :value="old('question', $editingFaq->question)"
                placeholder="Apakah bisa test drive?"
                maxlength="255"
                required
                :error="$errors->first('question')"
            />

            <x-form.textarea
                name="answer"
                label="Jawaban"
                :value="old('answer', $editingFaq->answer)"
                placeholder="Tulis jawaban yang jelas dan ringkas..."
                :rows="5"
                maxlength="5000"
                hint="Maksimal 5.000 karakter."
                required
                :error="$errors->first('answer')"
            />

            <div class="faq-form-row">
                <x-form.input
                    type="number"
                    name="sort_order"
                    label="Urutan"
                    :value="old('sort_order', $editingFaq->sort_order)"
                    min="0"
                    max="9999"
                    hint="Angka kecil tampil lebih dulu."
                    :error="$errors->first('sort_order')"
                />

                <div>
                    <label class="form-label">Status tayang</label>
                    <div class="faq-toggle-field">
                        <x-form.toggle
                            name="is_published"
                            label="Terbitkan di situs"
                            :checked="old('is_published', $editingFaq->is_published)"
                        />
                        <small>Matikan untuk menyembunyikan FAQ tanpa menghapusnya.</small>
                    </div>
                </div>
            </div>
        </x-layout.modal-form>
    @endif

    @if ($deletingFaq && ! $editingFaq)
        <x-ui.confirm-modal
            :show="true"
            title="Hapus FAQ"
            :message="'Hapus FAQ \''.\Illuminate\Support\Str::limit($deletingFaq->question, 90).'\'? Tindakan ini tidak dapat dibatalkan.'"
            confirm-text="Hapus FAQ"
            cancel-text="Batal"
            :action="route('admin.faqs.destroy', $deletingFaq)"
            method="DELETE"
            :cancel-url="$closeUrl"
            variant="danger"
            icon="fas fa-trash-can"
            :form-data="$returnFields"
        />
    @endif
</x-layouts.app>
