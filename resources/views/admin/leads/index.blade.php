@php
    $baseQuery = array_filter([
        'q' => $search,
        'status' => $status,
        'type' => $type,
        'page' => request('page'),
    ], fn ($value) => $value !== null && $value !== '');

    $closeUrl = route('admin.leads', $baseQuery);

    $statusLabel = collect($statuses)->firstWhere('value', $status)?->label();
    $typeLabel = collect($types)->firstWhere('value', $type)?->label();
    $hasFilters = $search !== '' || $status !== '' || $type !== '';

    $percentage = fn (int $value): int => $stats['total'] > 0
        ? (int) round($value / $stats['total'] * 100)
        : 0;

    $statCards = [
        [
            'key' => 'total',
            'label' => 'Total Lead',
            'meta' => 'Seluruh lead masuk',
            'icon' => 'fas fa-inbox',
            'tone' => 'primary',
            'value' => $stats['total'],
            'percentage' => $stats['total'] > 0 ? 100 : 0,
            'status' => null,
        ],
        [
            'key' => 'new',
            'label' => 'Baru',
            'meta' => 'Belum ditindaklanjuti',
            'icon' => 'fas fa-bell',
            'tone' => 'info',
            'value' => $stats['new'],
            'percentage' => $percentage($stats['new']),
            'status' => 'new',
        ],
        [
            'key' => 'deal',
            'label' => 'Deal',
            'meta' => 'Berhasil closing',
            'icon' => 'fas fa-handshake',
            'tone' => 'success',
            'value' => $stats['deal'],
            'percentage' => $percentage($stats['deal']),
            'status' => 'deal',
        ],
        [
            'key' => 'cancelled',
            'label' => 'Batal',
            'meta' => 'Tidak dilanjutkan',
            'icon' => 'fas fa-ban',
            'tone' => 'danger',
            'value' => $stats['cancelled'],
            'percentage' => $percentage($stats['cancelled']),
            'status' => 'cancelled',
        ],
    ];

    $statusTabs = collect([['value' => '', 'label' => 'Semua', 'count' => $stats['total']]])
        ->concat(collect($statuses)->map(fn ($statusOption) => [
            'value' => $statusOption->value,
            'label' => $statusOption->label(),
            'count' => $stats[$statusOption->value] ?? 0,
        ]));

    $carLabel = static function ($car): ?string {
        if (! $car) {
            return null;
        }

        return trim(($car->carModel?->brand?->name ?? '').' '.($car->carModel?->name ?? '').' '.($car->variant ?? ''));
    };

    $whatsappNumber = static function (?string $phone): ?string {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    };

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

        /* Ringkasan lead */
        .lead-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .lead-stat {
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

        .lead-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(23, 53, 43, 0.12);
            border-color: var(--border-color);
            color: inherit;
        }

        .lead-stat.is-active {
            border-color: var(--stat-color);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--stat-color) 14%, transparent);
        }

        .lead-stat-primary { --stat-color: var(--bs-primary); --stat-soft: rgba(29, 91, 66, 0.1); }
        .lead-stat-info { --stat-color: var(--bs-info); --stat-soft: rgba(79, 130, 144, 0.12); }
        .lead-stat-success { --stat-color: var(--bs-success); --stat-soft: rgba(77, 138, 99, 0.12); }
        .lead-stat-danger { --stat-color: var(--bs-danger); --stat-soft: rgba(185, 88, 76, 0.12); }

        .lead-stat-icon {
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

        .lead-stat-copy {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
            min-width: 0;
        }

        .lead-stat-copy strong {
            color: var(--text-primary);
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .lead-stat-copy span {
            color: var(--text-muted);
            font-size: 0.76rem;
            font-weight: 500;
            margin-top: 0.2rem;
        }

        .lead-stat-copy small {
            color: var(--text-muted);
            font-size: 0.65rem;
            margin-top: 0.12rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .lead-stat-progress {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            background: var(--bg-tertiary);
        }

        .lead-stat-progress span {
            display: block;
            height: 100%;
            background: var(--stat-color);
            transition: width 0.3s ease;
        }

        /* Tab status */
        .lead-tabbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .lead-tabs {
            display: flex;
            gap: 0.25rem;
            padding: 0.3rem;
            background: var(--bg-tertiary);
            border-radius: 8px;
            overflow-x: auto;
            max-width: 100%;
            scrollbar-width: none;
        }

        .lead-tabs::-webkit-scrollbar {
            display: none;
        }

        .lead-tab {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            flex-shrink: 0;
            padding: 0.5rem 0.8rem;
            border-radius: 6px;
            color: var(--text-secondary);
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .lead-tab:hover {
            color: var(--bs-primary);
        }

        .lead-tab.is-active {
            background: var(--bg-secondary);
            color: var(--text-primary);
            box-shadow: 0 1px 3px rgba(23, 53, 43, 0.12);
        }

        .lead-tab span {
            display: inline-grid;
            place-items: center;
            min-width: 22px;
            height: 20px;
            padding: 0 0.35rem;
            border-radius: 999px;
            background: var(--bg-secondary);
            color: var(--text-muted);
            font-size: 0.68rem;
        }

        .lead-tab.is-active span {
            background: var(--bs-primary);
            color: #fff;
        }

        .lead-tab-note {
            color: var(--text-muted);
            font-size: 0.74rem;
            white-space: nowrap;
        }

        .lead-tab-note i {
            margin-right: 0.3rem;
            color: var(--bs-info);
        }

        .lead-tab-note.is-clear i {
            color: var(--bs-success);
        }

        /* Filter */
        .lead-filter-card {
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .lead-filter-card:hover,
        .lead-panel:hover {
            transform: none;
        }

        .lead-filter {
            display: grid;
            grid-template-columns: minmax(240px, 1fr) 190px auto auto;
            gap: 0.65rem;
            align-items: center;
        }

        .lead-search {
            position: relative;
            display: flex;
            align-items: center;
        }

        .lead-search > i {
            position: absolute;
            left: 0.9rem;
            color: var(--text-muted);
            font-size: 0.85rem;
            pointer-events: none;
        }

        .lead-search input {
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

        .lead-search input:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(29, 91, 66, 0.1);
        }

        .lead-search input::placeholder {
            color: var(--text-muted);
        }

        .lead-search-clear {
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

        .lead-search-clear:hover {
            background: var(--hover-bg);
            color: var(--bs-danger);
        }

        .lead-select {
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

        .lead-select:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(29, 91, 66, 0.1);
        }

        .lead-filter-submit {
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

        .lead-filter-submit:hover {
            background: #164934;
            transform: translateY(-1px);
        }

        .lead-filter-reset {
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

        .lead-filter-reset:hover {
            border-color: var(--bs-danger);
            color: var(--bs-danger);
        }

        .lead-chips {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.45rem;
            margin-top: 0.85rem;
            padding-top: 0.85rem;
            border-top: 1px solid var(--border-light);
        }

        .lead-chips-label {
            color: var(--text-muted);
            font-size: 0.74rem;
            font-weight: 600;
        }

        .lead-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.55rem;
            border-radius: 999px;
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            font-size: 0.74rem;
        }

        .lead-chip > i {
            color: var(--bs-primary);
            font-size: 0.65rem;
        }

        .lead-chip a {
            display: grid;
            place-items: center;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            color: var(--text-muted);
        }

        .lead-chip a:hover {
            background: var(--bg-secondary);
            color: var(--bs-danger);
        }

        .lead-chips-clear {
            margin-left: 0.25rem;
            color: var(--bs-danger);
            font-size: 0.74rem;
            font-weight: 600;
            text-decoration: none;
        }

        .lead-chips-clear:hover {
            text-decoration: underline;
        }

        /* Panel daftar */
        .lead-panel {
            padding: 0;
            overflow: hidden;
        }

        .lead-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.15rem 1.35rem;
            border-bottom: 1px solid var(--border-light);
        }

        .lead-panel-head h5 {
            margin: 0 0 0.15rem;
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 700;
        }

        .lead-panel-head p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.76rem;
        }

        .lead-panel-meta {
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }

        .lead-priority {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            background: rgba(79, 130, 144, 0.12);
            color: var(--bs-info);
            font-size: 0.72rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .lead-sort {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            font-size: 0.72rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .lead-table {
            margin: 0;
            min-width: 980px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .lead-table thead th {
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

        .lead-table tbody td {
            padding: 0.95rem 1.1rem;
            border: none;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .lead-table tbody tr:last-child td {
            border-bottom: none;
        }

        .lead-table tbody tr:hover td {
            background: var(--hover-bg);
        }

        .lead-table tbody tr.is-new td:first-child {
            box-shadow: inset 3px 0 0 var(--bs-info);
        }

        .lead-cell {
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
            min-width: 235px;
        }

        .lead-avatar {
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            border: 1px solid var(--border-light);
            border-radius: 50%;
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .lead-avatar-new { background: rgba(79, 130, 144, 0.14); color: var(--bs-info); }
        .lead-avatar-contacted { background: rgba(29, 91, 66, 0.12); color: var(--bs-primary); }
        .lead-avatar-test-drive { background: rgba(189, 138, 56, 0.16); color: var(--bs-warning); }
        .lead-avatar-deal { background: rgba(77, 138, 99, 0.16); color: var(--bs-success); }
        .lead-avatar-cancelled { background: rgba(185, 88, 76, 0.13); color: var(--bs-danger); }

        .lead-name {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.4rem;
            color: var(--text-primary);
            font-size: 0.88rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .lead-contact {
            display: flex;
            flex-direction: column;
            gap: 0.12rem;
            margin-top: 0.28rem;
            font-size: 0.71rem;
        }

        .lead-contact a {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            color: var(--text-muted);
            text-decoration: none;
            line-height: 1.35;
            word-break: break-word;
        }

        .lead-contact a:hover {
            color: var(--bs-primary);
        }

        .lead-contact i {
            width: 11px;
            font-size: 0.62rem;
            flex-shrink: 0;
        }

        .lead-car {
            min-width: 0;
        }

        .lead-car-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--text-primary);
            font-size: 0.82rem;
            font-weight: 600;
            line-height: 1.35;
            text-decoration: none;
        }

        .lead-car-link i {
            color: var(--bs-primary);
            font-size: 0.72rem;
            flex-shrink: 0;
        }

        .lead-car-link:hover {
            color: var(--bs-primary);
        }

        .lead-car-meta {
            display: block;
            margin-top: 0.18rem;
            color: var(--text-muted);
            font-size: 0.7rem;
        }

        .lead-general {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            color: var(--text-muted);
            font-size: 0.78rem;
        }

        .lead-type,
        .lead-status {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.28rem 0.58rem;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .lead-type.is-inquiry {
            background: var(--bg-tertiary);
            color: var(--text-secondary);
        }

        .lead-type.is-test-drive {
            background: rgba(189, 138, 56, 0.14);
            color: var(--bs-warning);
        }

        .lead-status.is-new {
            background: rgba(79, 130, 144, 0.13);
            color: var(--bs-info);
        }

        .lead-status.is-contacted {
            background: rgba(29, 91, 66, 0.12);
            color: var(--bs-primary);
        }

        .lead-status.is-test-drive {
            background: rgba(189, 138, 56, 0.16);
            color: var(--bs-warning);
        }

        .lead-status.is-deal {
            background: rgba(77, 138, 99, 0.16);
            color: var(--bs-success);
        }

        .lead-status.is-cancelled {
            background: rgba(185, 88, 76, 0.13);
            color: var(--bs-danger);
        }

        .lead-status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0;
        }

        .lead-when {
            min-width: 0;
        }

        .lead-date {
            display: block;
            color: var(--text-secondary);
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .lead-date-exact {
            display: block;
            margin-top: 0.08rem;
            color: var(--text-muted);
            font-size: 0.68rem;
            white-space: nowrap;
        }

        .lead-schedule {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            margin-top: 0.32rem;
            color: var(--bs-warning);
            font-size: 0.68rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .lead-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.35rem;
        }

        .lead-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            height: 32px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: var(--bg-secondary);
            color: var(--text-secondary);
            font-size: 0.7rem;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .lead-action-wa {
            width: 32px;
            color: var(--bs-success);
        }

        .lead-action-wa:hover {
            border-color: var(--bs-success);
            background: rgba(77, 138, 99, 0.08);
            color: var(--bs-success);
        }

        .lead-action-detail {
            padding: 0 0.65rem;
        }

        .lead-action-detail:hover {
            border-color: var(--bs-primary);
            background: var(--hover-bg);
            color: var(--bs-primary);
        }

        .lead-pagination {
            display: flex;
            justify-content: flex-end;
            padding: 1rem 1.35rem;
            border-top: 1px solid var(--border-light);
            background: var(--bg-secondary);
        }

        /* Modal detail */
        .lead-modal .modal-content-custom {
            max-width: 660px;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
        }

        .lead-modal-hero {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            padding: 1rem;
            border: 1px solid var(--border-light);
            border-radius: 10px;
            background: var(--bg-tertiary);
        }

        .lead-modal-avatar {
            display: grid;
            place-items: center;
            width: 54px;
            height: 54px;
            flex-shrink: 0;
            border-radius: 50%;
            background: var(--bg-secondary);
            color: var(--bs-primary);
            font-size: 0.95rem;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(23, 53, 43, 0.08);
        }

        .lead-modal-hero h6 {
            margin: 0;
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 700;
        }

        .lead-modal-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
            margin-top: 0.45rem;
        }

        .lead-quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.9rem;
        }

        .lead-quick-action {
            flex: 1 1 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.55rem 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 7px;
            background: var(--bg-secondary);
            color: var(--text-secondary);
            font-size: 0.74rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .lead-quick-action:hover {
            border-color: var(--bs-primary);
            background: var(--hover-bg);
            color: var(--bs-primary);
        }

        .lead-quick-action.is-wa:hover {
            border-color: var(--bs-success);
            background: rgba(77, 138, 99, 0.08);
            color: var(--bs-success);
        }

        .lead-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.65rem;
            margin-top: 1rem;
        }

        .lead-info-item {
            padding: 0.65rem 0.75rem;
            border: 1px solid var(--border-light);
            border-radius: 7px;
            background: var(--bg-secondary);
            min-width: 0;
        }

        .lead-info-item span {
            display: block;
            color: var(--text-muted);
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .lead-info-item strong,
        .lead-info-item a {
            display: block;
            margin-top: 0.18rem;
            color: var(--text-primary);
            font-size: 0.78rem;
            font-weight: 600;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }

        .lead-info-item a {
            color: var(--bs-primary);
            text-decoration: none;
        }

        .lead-info-item a:hover {
            text-decoration: underline;
        }

        .lead-message {
            margin-top: 1rem;
            padding: 0.85rem 1rem;
            border-left: 3px solid var(--bs-primary);
            border-radius: 0 8px 8px 0;
            background: var(--bg-tertiary);
        }

        .lead-message-label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--text-muted);
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .lead-message-label i {
            color: var(--bs-primary);
        }

        .lead-message p {
            margin: 0.4rem 0 0;
            color: var(--text-primary);
            font-size: 0.8rem;
            line-height: 1.55;
            white-space: pre-line;
        }

        .lead-followup {
            margin-top: 1.25rem;
            padding-top: 1.1rem;
            border-top: 1px solid var(--border-light);
        }

        .lead-followup-head {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            margin-bottom: 0.9rem;
            color: var(--text-primary);
            font-size: 0.8rem;
            font-weight: 700;
        }

        .lead-followup-head i {
            color: var(--bs-primary);
        }

        @media (max-width: 1199.98px) {
            .lead-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .lead-filter {
                grid-template-columns: minmax(200px, 1fr) 1fr auto auto;
            }
        }

        @media (max-width: 991.98px) {
            .lead-panel-meta .lead-sort {
                display: none;
            }
        }

        @media (max-width: 767.98px) {
            .lead-stats {
                gap: 0.65rem;
            }

            .lead-stat {
                padding: 0.85rem 0.9rem 1.1rem;
            }

            .lead-stat-icon {
                width: 36px;
                height: 36px;
                font-size: 0.85rem;
            }

            .lead-stat-copy strong {
                font-size: 1.15rem;
            }

            .lead-stat-copy span {
                font-size: 0.68rem;
            }

            .lead-stat-copy small {
                font-size: 0.62rem;
                white-space: normal;
                overflow: visible;
            }

            .lead-tabbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .lead-tabs {
                flex-wrap: wrap;
                width: 100%;
            }

            .lead-tab-note {
                white-space: normal;
                font-size: 0.7rem;
            }

            .lead-filter {
                grid-template-columns: 1fr 1fr;
            }

            .lead-search {
                grid-column: 1 / -1;
            }

            .lead-filter-submit {
                grid-column: 1;
            }

            .lead-filter-reset {
                grid-column: 2;
                width: 100%;
            }

            .lead-panel-head {
                padding: 1rem;
            }

            .lead-priority {
                display: none;
            }

            /* Tabel menjadi kartu di layar kecil */
            .lead-table {
                min-width: 0;
            }

            .lead-table thead {
                display: none;
            }

            .lead-table,
            .lead-table tbody,
            .lead-table tr,
            .lead-table td {
                display: block;
                width: 100%;
            }

            .lead-table tbody tr {
                padding: 1rem;
                border-bottom: 1px solid var(--border-light);
            }

            .lead-table tbody tr:last-child {
                border-bottom: none;
            }

            .lead-table tbody td {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 0.35rem 0;
                border: none;
                background: transparent;
            }

            .lead-table tbody tr:hover td {
                background: transparent;
            }

            .lead-table tbody tr.is-new td:first-child {
                box-shadow: none;
            }

            .lead-table tbody td:first-child {
                display: block;
                padding: 0 0 0.7rem;
            }

            .lead-table tbody td:not(:first-child)::before {
                content: attr(data-label);
                color: var(--text-muted);
                font-size: 0.68rem;
                font-weight: 700;
                letter-spacing: 0.05em;
                text-transform: uppercase;
            }

            .lead-table tbody td[data-label="Aksi"] {
                padding-top: 0.75rem;
            }

            .lead-cell {
                min-width: 0;
            }

            .lead-car,
            .lead-when {
                text-align: right;
            }

            .lead-date-exact {
                display: none;
            }

            .lead-actions {
                flex-wrap: wrap;
            }

            .lead-pagination {
                justify-content: center;
                padding: 0.9rem;
            }

            .lead-info-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .lead-filter {
                grid-template-columns: minmax(0, 1fr) 42px;
            }

            .lead-search,
            .lead-select {
                grid-column: 1 / -1;
            }

            .lead-filter-submit {
                grid-column: 1;
            }

            .lead-filter-reset {
                grid-column: 2;
                width: 42px;
            }

            .lead-modal .modal-content-custom {
                padding: 1.1rem;
                max-height: calc(100vh - 1rem);
            }
        }
    </style>
@endpush

<x-layouts.app title="Lead - RodaTa Admin">
    <x-layout.page-header title="Lead" subtitle="Kelola calon pembeli, status, dan tindak lanjutnya">
        <x-slot:actions>
            <x-ui.button variant="secondary" icon="fas fa-file-csv"
                         :href="route('admin.leads.export', array_filter(['q' => $search, 'status' => $status, 'type' => $type]))">
                Ekspor CSV
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Ringkasan lead --}}
    <div class="lead-stats">
        @foreach ($statCards as $card)
            <a href="{{ route('admin.leads', array_filter(['q' => $search, 'type' => $type, 'status' => $card['status']])) }}"
               class="lead-stat lead-stat-{{ $card['tone'] }} {{ $status === ($card['status'] ?? '') ? 'is-active' : '' }}">
                <span class="lead-stat-icon">
                    <i class="{{ $card['icon'] }}"></i>
                </span>
                <span class="lead-stat-copy">
                    <strong>{{ number_format($card['value'], 0, ',', '.') }}</strong>
                    <span>{{ $card['label'] }}</span>
                    <small>{{ $card['meta'] }}</small>
                </span>
                <span class="lead-stat-progress" aria-hidden="true">
                    <span style="width: {{ $card['percentage'] }}%;"></span>
                </span>
            </a>
        @endforeach
    </div>

    {{-- Tab status --}}
    <div class="lead-tabbar">
        <nav class="lead-tabs" aria-label="Filter status lead">
            @foreach ($statusTabs as $tab)
                <a href="{{ route('admin.leads', array_filter(['q' => $search, 'type' => $type, 'status' => $tab['value']])) }}"
                   class="lead-tab {{ $status === $tab['value'] ? 'is-active' : '' }}">
                    {{ $tab['label'] }}
                    <span>{{ number_format($tab['count'], 0, ',', '.') }}</span>
                </a>
            @endforeach
        </nav>
        <span class="lead-tab-note {{ $stats['new'] === 0 ? 'is-clear' : '' }}">
            @if ($stats['new'] > 0)
                <i class="fas fa-bolt"></i>
                {{ number_format($stats['new'], 0, ',', '.') }} lead baru menunggu tindak lanjut.
            @else
                <i class="fas fa-circle-check"></i>
                Semua lead baru sudah ditindaklanjuti.
            @endif
        </span>
    </div>

    {{-- Filter --}}
    <x-layout.modern-card class="lead-filter-card">
        <form method="GET" action="{{ route('admin.leads') }}" class="lead-filter">
            @if ($status !== '')
                <input type="hidden" name="status" value="{{ $status }}">
            @endif

            <div class="lead-search">
                <i class="fas fa-search"></i>
                <input type="search" id="q" name="q" value="{{ $search }}"
                       placeholder="Cari nama, nomor HP, atau email..." aria-label="Cari lead">
                @if ($search !== '')
                    <a href="{{ route('admin.leads', array_filter(['status' => $status, 'type' => $type])) }}"
                       class="lead-search-clear" aria-label="Hapus pencarian">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>

            <select id="type" name="type" class="lead-select" aria-label="Filter jenis lead">
                <option value="">Semua jenis</option>
                @foreach ($types as $typeOption)
                    <option value="{{ $typeOption->value }}" @selected($type === $typeOption->value)>
                        {{ $typeOption->label() }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="lead-filter-submit">
                <i class="fas fa-filter"></i>
                Tampilkan
            </button>

            <a href="{{ route('admin.leads') }}" class="lead-filter-reset" title="Reset filter" aria-label="Reset filter">
                <i class="fas fa-rotate-left"></i>
            </a>
        </form>

        @if ($hasFilters)
            <div class="lead-chips">
                <span class="lead-chips-label">Filter aktif:</span>

                @if ($search !== '')
                    <span class="lead-chip">
                        <i class="fas fa-search"></i>
                        “{{ $search }}”
                        <a href="{{ route('admin.leads', array_filter(['status' => $status, 'type' => $type])) }}"
                           aria-label="Hapus filter pencarian">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if ($statusLabel)
                    <span class="lead-chip">
                        <i class="fas fa-circle"></i>
                        {{ $statusLabel }}
                        <a href="{{ route('admin.leads', array_filter(['q' => $search, 'type' => $type])) }}"
                           aria-label="Hapus filter status">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if ($typeLabel)
                    <span class="lead-chip">
                        <i class="fas fa-tag"></i>
                        {{ $typeLabel }}
                        <a href="{{ route('admin.leads', array_filter(['q' => $search, 'status' => $status])) }}"
                           aria-label="Hapus filter jenis">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                <a href="{{ route('admin.leads') }}" class="lead-chips-clear">Bersihkan semua</a>
            </div>
        @endif
    </x-layout.modern-card>

    {{-- Daftar lead --}}
    <x-layout.modern-card :padding="false" class="lead-panel">
        <div class="lead-panel-head">
            <div>
                <h5>Daftar Lead</h5>
                <p>
                    @if ($leads->total() > 0)
                        Menampilkan {{ $leads->firstItem() }}–{{ $leads->lastItem() }} dari
                        {{ number_format($leads->total(), 0, ',', '.') }} lead
                    @elseif ($hasFilters)
                        Tidak ada lead yang cocok dengan filter
                    @else
                        Belum ada lead masuk
                    @endif
                </p>
            </div>
            <div class="lead-panel-meta">
                @if ($stats['new'] > 0)
                    <span class="lead-priority">
                        <i class="fas fa-bolt"></i>
                        {{ number_format($stats['new'], 0, ',', '.') }} baru
                    </span>
                @endif
                <span class="lead-sort">
                    <i class="fas fa-arrow-down-wide-short"></i>
                    Terbaru
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table lead-table">
                <thead>
                    <tr>
                        <th>Lead</th>
                        <th>Mobil Diminati</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Masuk</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leads as $lead)
                        @php
                            $leadCarName = $carLabel($lead->car);
                            $waPhone = $whatsappNumber($lead->phone);
                            $initials = $initialsFor($lead->name);
                            $statusClass = str_replace('_', '-', $lead->status->value);
                            $typeClass = str_replace('_', '-', $lead->type->value);
                            $isNew = $lead->status->value === 'new';
                            $created = $lead->created_at;
                            $createdLabel = $created
                                ? ($created->isToday()
                                    ? 'Hari ini, '.$created->format('H:i')
                                    : ($created->isYesterday()
                                        ? 'Kemarin, '.$created->format('H:i')
                                        : $created->format('d M Y H:i')))
                                : '—';
                        @endphp
                        <tr class="{{ $isNew ? 'is-new' : '' }}">
                            <td data-label="Lead">
                                <div class="lead-cell">
                                    <span class="lead-avatar lead-avatar-{{ $statusClass }}">{{ $initials }}</span>
                                    <div>
                                        <div class="lead-name">
                                            {{ $lead->name }}
                                        </div>
                                        <div class="lead-contact">
                                            <a href="tel:{{ $lead->phone }}">
                                                <i class="fas fa-phone"></i>
                                                {{ $lead->phone }}
                                            </a>
                                            @if ($lead->email)
                                                <a href="mailto:{{ $lead->email }}">
                                                    <i class="fas fa-envelope"></i>
                                                    {{ $lead->email }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Mobil">
                                @if ($leadCarName)
                                    <div class="lead-car">
                                        <a href="{{ route('admin.cars.edit', $lead->car) }}" class="lead-car-link">
                                            <i class="fas fa-car-side"></i>
                                            {{ $leadCarName }}
                                        </a>
                                        <span class="lead-car-meta">
                                            {{ $lead->car->year }} · {{ $lead->car->city }}
                                        </span>
                                    </div>
                                @else
                                    <span class="lead-general">
                                        <i class="fas fa-comments"></i>
                                        Pertanyaan umum
                                    </span>
                                @endif
                            </td>
                            <td data-label="Jenis">
                                <span class="lead-type is-{{ $typeClass }}">
                                    <i class="{{ $lead->type->value === 'test_drive' ? 'fas fa-car-side' : 'fas fa-comment-dots' }}"></i>
                                    {{ $lead->type->label() }}
                                </span>
                            </td>
                            <td data-label="Status">
                                <span class="lead-status is-{{ $statusClass }}">
                                    <span class="lead-status-dot"></span>
                                    {{ $lead->status->label() }}
                                </span>
                            </td>
                            <td data-label="Masuk">
                                <div class="lead-when">
                                    <span class="lead-date" title="{{ $lead->created_at?->format('d M Y H:i') }}">
                                        {{ $createdLabel }}
                                    </span>
                                    @if ($lead->created_at)
                                        <span class="lead-date-exact">{{ $lead->created_at->format('d M Y H:i') }}</span>
                                    @endif
                                    @if ($lead->preferred_schedule)
                                        <span class="lead-schedule">
                                            <i class="fas fa-calendar-check"></i>
                                            {{ $lead->preferred_schedule->format('d M Y H:i') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td data-label="Aksi">
                                <div class="lead-actions">
                                    @if ($waPhone)
                                        <a href="https://wa.me/{{ $waPhone }}" target="_blank" rel="noopener"
                                           class="lead-action lead-action-wa" title="Chat WhatsApp {{ $lead->name }}"
                                           aria-label="Chat WhatsApp {{ $lead->name }}">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.leads', $baseQuery + ['edit' => $lead->id]) }}"
                                       class="lead-action lead-action-detail" title="Tindak lanjut lead">
                                        <i class="fas fa-arrow-right"></i>
                                        <span>Tindak Lanjut</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-ui.empty-state
                                    icon="fas fa-inbox"
                                    :title="$hasFilters ? 'Tidak ada lead yang cocok' : 'Belum ada lead'"
                                    :description="$hasFilters
                                        ? 'Coba ubah kata kunci atau bersihkan filter untuk melihat lead lainnya.'
                                        : 'Lead dari formulir minat dan test drive akan tampil di sini.'"
                                    :action-label="$hasFilters ? 'Bersihkan Filter' : null"
                                    :action-href="route('admin.leads')"
                                    action-icon="fas fa-rotate-left"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($leads->hasPages())
            <div class="lead-pagination">
                {{ $leads->links() }}
            </div>
        @endif
    </x-layout.modern-card>

    @if ($editingLead)
        @php
            $editingCarName = $carLabel($editingLead->car);
            $editingWaPhone = $whatsappNumber($editingLead->phone);
            $editingInitials = $initialsFor($editingLead->name);
            $editingStatusClass = str_replace('_', '-', $editingLead->status->value);
            $editingTypeClass = str_replace('_', '-', $editingLead->type->value);
        @endphp

        <x-layout.modal-form
            title="Detail Lead"
            :action="route('admin.leads.update', $editingLead)"
            method="PUT"
            :close-url="$closeUrl"
            submit-label="Simpan Perubahan"
            close-label="Batal"
            class="lead-modal"
        >
            <input type="hidden" name="return_q" value="{{ $search }}">
            <input type="hidden" name="return_status" value="{{ $status }}">
            <input type="hidden" name="return_type" value="{{ $type }}">
            <input type="hidden" name="return_page" value="{{ request('page') }}">

            <div class="lead-modal-hero">
                <span class="lead-modal-avatar">{{ $editingInitials }}</span>
                <div>
                    <h6>{{ $editingLead->name }}</h6>
                    <div class="lead-modal-tags">
                        <span class="lead-status is-{{ $editingStatusClass }}">
                            <span class="lead-status-dot"></span>
                            {{ $editingLead->status->label() }}
                        </span>
                        <span class="lead-type is-{{ $editingTypeClass }}">
                            <i class="{{ $editingLead->type->value === 'test_drive' ? 'fas fa-car-side' : 'fas fa-comment-dots' }}"></i>
                            {{ $editingLead->type->label() }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="lead-quick-actions">
                @if ($editingWaPhone)
                    <a href="https://wa.me/{{ $editingWaPhone }}" target="_blank" rel="noopener"
                       class="lead-quick-action is-wa">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                @endif
                <a href="tel:{{ $editingLead->phone }}" class="lead-quick-action">
                    <i class="fas fa-phone"></i> Telepon
                </a>
                @if ($editingLead->email)
                    <a href="mailto:{{ $editingLead->email }}" class="lead-quick-action">
                        <i class="fas fa-envelope"></i> Email
                    </a>
                @endif
            </div>

            <div class="lead-info-grid">
                <div class="lead-info-item">
                    <span>Mobil diminati</span>
                    @if ($editingLead->car)
                        <a href="{{ route('admin.cars.edit', $editingLead->car) }}">
                            {{ $editingCarName ?: 'Lihat mobil' }}
                        </a>
                    @else
                        <strong>Pertanyaan umum</strong>
                    @endif
                </div>
                <div class="lead-info-item">
                    <span>Waktu masuk</span>
                    <strong>{{ $editingLead->created_at?->format('d M Y H:i') ?? '—' }}</strong>
                </div>
                @if ($editingLead->preferred_schedule)
                    <div class="lead-info-item">
                        <span>Jadwal test drive</span>
                        <strong>{{ $editingLead->preferred_schedule->format('d M Y H:i') }}</strong>
                    </div>
                @endif
                @if ($editingLead->consent_at)
                    <div class="lead-info-item">
                        <span>Persetujuan data</span>
                        <strong>{{ $editingLead->consent_at->format('d M Y H:i') }}</strong>
                    </div>
                @endif
            </div>

            @if ($editingLead->message)
                <div class="lead-message">
                    <span class="lead-message-label">
                        <i class="fas fa-quote-left"></i>
                        Pesan dari calon pembeli
                    </span>
                    <p>{{ $editingLead->message }}</p>
                </div>
            @endif

            <div class="lead-followup">
                <div class="lead-followup-head">
                    <i class="fas fa-arrow-right"></i>
                    Tindak Lanjut
                </div>

                <x-form.select
                    name="status"
                    label="Status"
                    :options="collect($statuses)->mapWithKeys(fn ($item) => [$item->value => $item->label()])->all()"
                    :selected="old('status', $editingLead->status->value)"
                    required
                    :error="$errors->first('status')"
                />

                <x-form.textarea
                    name="notes"
                    label="Catatan Internal"
                    :value="old('notes', $editingLead->notes)"
                    placeholder="Hasil follow up, kesepakatan, atau catatan lain..."
                    :rows="4"
                    maxlength="5000"
                    hint="Catatan hanya terlihat oleh admin."
                    :error="$errors->first('notes')"
                />
            </div>
        </x-layout.modal-form>
    @endif
</x-layouts.app>
