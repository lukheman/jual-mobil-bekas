@php
    $carStats = $stats['cars'];
    $leadStats = $stats['leads'];
    $clickStats = $stats['clicks'];

    $totalCars = $carStats['total'];
    $totalLeads = $leadStats['total'];

    $statCards = [
        [
            'label' => 'Total Unit',
            'value' => $carStats['total'],
            'icon' => 'fas fa-car-side',
            'tone' => 'primary',
            'badge' => $carStats['featured'].' unggulan',
            'meta' => $carStats['available'].' tersedia · '.$carStats['reserved'].' dipesan',
            'href' => route('admin.cars'),
        ],
        [
            'label' => 'Lead Baru',
            'value' => $leadStats['new'],
            'icon' => 'fas fa-bell',
            'tone' => 'info',
            'badge' => number_format($totalLeads, 0, ',', '.').' total lead',
            'meta' => 'Belum ditindaklanjuti',
            'href' => route('admin.leads', ['status' => 'new']),
        ],
        [
            'label' => 'Deal',
            'value' => $leadStats['deal'],
            'icon' => 'fas fa-handshake',
            'tone' => 'success',
            'badge' => $leadStats['conversion'].'% konversi',
            'meta' => 'Dari seluruh lead masuk',
            'href' => route('admin.leads', ['status' => 'deal']),
        ],
        [
            'label' => 'Total Dilihat',
            'value' => $carStats['views'],
            'icon' => 'fas fa-eye',
            'tone' => 'warning',
            'badge' => number_format($clickStats['whatsapp'], 0, ',', '.').' WA',
            'meta' => number_format($clickStats['phone'], 0, ',', '.').' klik telepon',
            'href' => route('admin.cars'),
        ],
    ];

    $dayLabels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    $leadTrend = $leadTrend->map(function (array $day) use ($dayLabels): array {
        $day['label'] = $dayLabels[$day['date']->dayOfWeek];

        return $day;
    });
    $trendMax = max(1, (int) $leadTrend->max('total'));

    $deltaClass = $trendDelta > 0 ? 'is-up' : ($trendDelta < 0 ? 'is-down' : 'is-flat');
    $deltaIcon = $trendDelta > 0
        ? 'fas fa-arrow-trend-up'
        : ($trendDelta < 0 ? 'fas fa-arrow-trend-down' : 'fas fa-minus');

    $statusSegments = [
        ['label' => 'Tersedia', 'count' => $carStats['available'], 'color' => 'var(--bs-success)'],
        ['label' => 'Dipesan', 'count' => $carStats['reserved'], 'color' => 'var(--bs-warning)'],
        ['label' => 'Terjual', 'count' => $carStats['sold'], 'color' => 'var(--bs-danger)'],
    ];

    $donutGradient = 'conic-gradient(var(--bg-tertiary) 0deg 360deg)';

    if ($totalCars > 0) {
        $cursor = 0;
        $stops = [];

        foreach ($statusSegments as $segment) {
            if ($segment['count'] <= 0) {
                continue;
            }

            $degrees = $segment['count'] / $totalCars * 360;
            $stops[] = $segment['color'].' '.round($cursor, 2).'deg '.round($cursor + $degrees, 2).'deg';
            $cursor += $degrees;
        }

        if ($stops !== []) {
            $donutGradient = 'conic-gradient('.implode(', ', $stops).')';
        }
    }

    $pipelineColors = [
        'new' => 'var(--bs-info)',
        'contacted' => 'var(--bs-secondary)',
        'test_drive' => 'var(--bs-warning)',
        'deal' => 'var(--bs-success)',
        'cancelled' => 'var(--bs-danger)',
    ];

    $pipeline = collect($leadStatuses)->map(function ($status) use ($leadStats, $pipelineColors, $totalLeads): array {
        $count = (int) ($leadStats[$status->value] ?? 0);

        return [
            'label' => $status->label(),
            'count' => $count,
            'color' => $pipelineColors[$status->value] ?? 'var(--bs-primary)',
            'percentage' => $totalLeads > 0 ? (int) round($count / $totalLeads * 100) : 0,
        ];
    });

    $carLabel = static function ($car): string {
        if (! $car) {
            return 'Tanpa unit';
        }

        $label = trim(implode(' ', array_filter([
            $car->carModel?->brand?->name,
            $car->carModel?->name,
            $car->variant,
        ])));

        return $label !== '' ? $label : 'Unit #'.$car->id;
    };

    $initialsFor = static function (?string $name): string {
        $parts = array_slice(array_filter(preg_split('/\s+/', trim((string) $name)) ?: []), 0, 2);
        $letters = array_map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)), $parts);

        return implode('', $letters) ?: '?';
    };

    $timeAgo = static function ($date): string {
        if (! $date) {
            return '-';
        }

        $minutes = (int) $date->diffInMinutes(now());

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

    $attentionItems = array_values(array_filter([
        [
            'show' => $leadStats['new'] > 0,
            'count' => $leadStats['new'],
            'label' => 'Lead baru menunggu respons',
            'meta' => 'Tindak lanjuti lead terbaru',
            'icon' => 'fas fa-bell',
            'color' => 'var(--bs-info)',
            'soft' => 'rgba(79, 130, 144, 0.12)',
            'href' => route('admin.leads', ['status' => 'new']),
        ],
        [
            'show' => $leadStats['stale'] > 0,
            'count' => $leadStats['stale'],
            'label' => 'Lead belum direspons > 24 jam',
            'meta' => 'Sudah lewat satu hari',
            'icon' => 'fas fa-clock-rotate-left',
            'color' => 'var(--bs-warning)',
            'soft' => 'rgba(189, 138, 56, 0.14)',
            'href' => route('admin.leads', ['status' => 'new']),
        ],
        [
            'show' => $carStats['without_photo'] > 0,
            'count' => $carStats['without_photo'],
            'label' => 'Unit belum punya foto',
            'meta' => 'Lengkapi galeri mobil',
            'icon' => 'fas fa-image',
            'color' => 'var(--bs-danger)',
            'soft' => 'rgba(185, 88, 76, 0.12)',
            'href' => route('admin.cars'),
        ],
        [
            'show' => $carStats['archived'] > 0,
            'count' => $carStats['archived'],
            'label' => 'Unit diarsipkan',
            'meta' => 'Tinjau atau pulihkan unit',
            'icon' => 'fas fa-box-archive',
            'color' => 'var(--bs-secondary)',
            'soft' => 'rgba(113, 129, 122, 0.14)',
            'href' => route('admin.cars', ['arsip' => 1]),
        ],
    ], fn (array $item) => $item['show']));
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

        /* Kartu ringkasan */
        .dash-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .dash-stat {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            padding: 1.1rem 1.15rem 1.3rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border-light);
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            text-decoration: none;
            color: inherit;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .dash-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(23, 53, 43, 0.12);
            border-color: var(--border-color);
            color: inherit;
        }

        .dash-stat::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            background: var(--stat-color);
        }

        .dash-stat-primary { --stat-color: var(--bs-primary); --stat-soft: rgba(29, 91, 66, 0.1); }
        .dash-stat-info { --stat-color: var(--bs-info); --stat-soft: rgba(79, 130, 144, 0.12); }
        .dash-stat-success { --stat-color: var(--bs-success); --stat-soft: rgba(77, 138, 99, 0.12); }
        .dash-stat-warning { --stat-color: var(--bs-warning); --stat-soft: rgba(189, 138, 56, 0.14); }

        .dash-stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin-bottom: 0.35rem;
        }

        .dash-stat-icon {
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border-radius: 9px;
            background: var(--stat-soft);
            color: var(--stat-color);
            font-size: 0.95rem;
        }

        .dash-stat-pill {
            padding: 0.22rem 0.5rem;
            border-radius: 999px;
            background: var(--stat-soft);
            color: var(--stat-color);
            font-size: 0.64rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .dash-stat-value {
            color: var(--text-primary);
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .dash-stat-label {
            color: var(--text-primary);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .dash-stat-meta {
            color: var(--text-muted);
            font-size: 0.68rem;
        }

        /* Grid panel */
        .dash-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .dash-span-8 { grid-column: span 8; }
        .dash-span-7 { grid-column: span 7; }
        .dash-span-5 { grid-column: span 5; }
        .dash-span-4 { grid-column: span 4; }

        .dash-panel {
            padding: 0;
            overflow: hidden;
        }

        .dash-panel:hover {
            transform: none;
            box-shadow: var(--card-shadow);
        }

        .dash-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.05rem 1.2rem;
            border-bottom: 1px solid var(--border-light);
        }

        .dash-panel-title {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            min-width: 0;
        }

        .dash-panel-icon {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border-radius: 9px;
            background: rgba(29, 91, 66, 0.1);
            color: var(--bs-primary);
            font-size: 0.88rem;
        }

        .dash-panel-head h5 {
            margin: 0 0 0.1rem;
            color: var(--text-primary);
            font-size: 0.95rem;
            font-weight: 700;
        }

        .dash-panel-head p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.72rem;
        }

        .dash-panel-link {
            color: var(--bs-primary);
            font-size: 0.72rem;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
        }

        .dash-panel-link:hover {
            text-decoration: underline;
        }

        .dash-panel-body {
            padding: 1.15rem 1.2rem;
        }

        /* Grafik tren lead */
        .dash-chart-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.15rem;
        }

        .dash-chart-total strong {
            display: block;
            color: var(--text-primary);
            font-size: 1.7rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .dash-chart-total span {
            color: var(--text-muted);
            font-size: 0.7rem;
        }

        .dash-chart-compare {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.3rem;
        }

        .dash-chart-delta {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.55rem;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 700;
        }

        .dash-chart-delta.is-up {
            background: rgba(77, 138, 99, 0.14);
            color: var(--bs-success);
        }

        .dash-chart-delta.is-down {
            background: rgba(185, 88, 76, 0.12);
            color: var(--bs-danger);
        }

        .dash-chart-delta.is-flat {
            background: var(--bg-tertiary);
            color: var(--text-muted);
        }

        .dash-chart-compare small {
            color: var(--text-muted);
            font-size: 0.64rem;
        }

        .dash-chart {
            display: flex;
            align-items: flex-end;
            gap: 0.55rem;
            padding-top: 0.25rem;
        }

        .dash-chart-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.35rem;
            min-width: 0;
        }

        .dash-chart-value {
            color: var(--text-muted);
            font-size: 0.68rem;
            font-weight: 700;
        }

        .dash-chart-track {
            width: 100%;
            max-width: 44px;
            height: 128px;
            display: flex;
            align-items: flex-end;
            border-radius: 6px 6px 4px 4px;
            background: var(--bg-tertiary);
            overflow: hidden;
        }

        .dash-chart-bar {
            display: block;
            width: 100%;
            min-height: 3px;
            border-radius: 6px 6px 0 0;
            background: linear-gradient(180deg, #2f7a59, var(--bs-primary));
            transition: height 0.4s ease;
        }

        .dash-chart-bar.is-today {
            background: linear-gradient(180deg, var(--bs-success), #2f7a59);
        }

        .dash-chart-bar.is-zero {
            background: var(--border-color);
        }

        .dash-chart-col:hover .dash-chart-bar {
            filter: brightness(1.08);
        }

        .dash-chart-label {
            color: var(--text-muted);
            font-size: 0.68rem;
            font-weight: 600;
        }

        .dash-chart-label.is-today {
            color: var(--bs-primary);
        }

        /* Donut inventaris */
        .dash-donut-wrap {
            display: flex;
            justify-content: center;
            padding: 0.35rem 0 1.15rem;
        }

        .dash-donut {
            position: relative;
            width: 168px;
            height: 168px;
            border-radius: 50%;
            display: grid;
            place-items: center;
        }

        .dash-donut-center {
            width: 112px;
            height: 112px;
            border-radius: 50%;
            background: var(--bg-secondary);
            box-shadow: 0 0 0 1px var(--border-light);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .dash-donut-center strong {
            color: var(--text-primary);
            font-size: 1.55rem;
            font-weight: 700;
            line-height: 1;
        }

        .dash-donut-center span {
            margin-top: 0.2rem;
            color: var(--text-muted);
            font-size: 0.68rem;
        }

        .dash-legend {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .dash-legend-row {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            font-size: 0.76rem;
        }

        .dash-legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .dash-legend-label {
            flex: 1;
            color: var(--text-secondary);
        }

        .dash-legend-value {
            color: var(--text-primary);
            font-weight: 700;
        }

        .dash-legend-pct {
            min-width: 34px;
            color: var(--text-muted);
            font-size: 0.68rem;
            text-align: right;
        }

        /* Daftar lead & mobil */
        .dash-list {
            display: flex;
            flex-direction: column;
        }

        .dash-lead-row {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.65rem 0.35rem;
            border-bottom: 1px solid var(--border-light);
            border-radius: 6px;
            color: inherit;
            text-decoration: none;
            transition: background 0.15s ease;
        }

        .dash-lead-row:last-child {
            border-bottom: none;
        }

        .dash-lead-row:hover {
            background: var(--hover-bg);
            color: inherit;
        }

        .dash-avatar {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border-radius: 50%;
            background: var(--bg-tertiary);
            color: var(--bs-primary);
            font-size: 0.68rem;
            font-weight: 700;
        }

        .dash-lead-main {
            flex: 1;
            min-width: 0;
        }

        .dash-lead-main strong {
            display: block;
            color: var(--text-primary);
            font-size: 0.82rem;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dash-lead-main span {
            display: block;
            color: var(--text-muted);
            font-size: 0.68rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dash-lead-side {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.2rem;
            flex-shrink: 0;
        }

        .dash-lead-side small {
            color: var(--text-muted);
            font-size: 0.62rem;
        }

        .dash-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.5rem;
            border-radius: 999px;
            font-size: 0.62rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .dash-badge.is-new {
            background: rgba(79, 130, 144, 0.14);
            color: var(--bs-info);
        }

        .dash-badge.is-contacted {
            background: rgba(113, 129, 122, 0.14);
            color: var(--text-secondary);
        }

        .dash-badge.is-test_drive {
            background: rgba(189, 138, 56, 0.14);
            color: var(--bs-warning);
        }

        .dash-badge.is-deal {
            background: rgba(77, 138, 99, 0.14);
            color: var(--bs-success);
        }

        .dash-badge.is-cancelled {
            background: rgba(185, 88, 76, 0.12);
            color: var(--bs-danger);
        }

        .dash-car-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.35rem;
            border-bottom: 1px solid var(--border-light);
            border-radius: 6px;
            color: inherit;
            text-decoration: none;
            transition: background 0.15s ease;
        }

        .dash-car-row:last-child {
            border-bottom: none;
        }

        .dash-car-row:hover {
            background: var(--hover-bg);
            color: inherit;
        }

        .dash-car-thumb {
            width: 56px;
            height: 42px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border-radius: 7px;
            background: var(--bg-tertiary);
            color: var(--text-muted);
            overflow: hidden;
        }

        .dash-car-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dash-car-main {
            flex: 1;
            min-width: 0;
        }

        .dash-car-main strong {
            display: block;
            color: var(--text-primary);
            font-size: 0.8rem;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dash-car-main span {
            display: block;
            color: var(--text-muted);
            font-size: 0.66rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dash-car-side {
            flex-shrink: 0;
            text-align: right;
        }

        .dash-car-price {
            display: block;
            color: var(--bs-primary);
            font-size: 0.76rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .dash-car-meta {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            color: var(--text-muted);
            font-size: 0.64rem;
            white-space: nowrap;
        }

        /* Pipeline lead */
        .dash-pipeline {
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
        }

        .dash-pipeline-row {
            display: grid;
            grid-template-columns: 92px minmax(0, 1fr) 34px;
            align-items: center;
            gap: 0.6rem;
        }

        .dash-pipeline-label {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            color: var(--text-secondary);
            font-size: 0.72rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .dash-pipeline-label i {
            color: var(--pipeline-color);
            font-size: 0.5rem;
        }

        .dash-pipeline-track {
            height: 7px;
            border-radius: 999px;
            background: var(--bg-tertiary);
            overflow: hidden;
        }

        .dash-pipeline-track span {
            display: block;
            height: 100%;
            border-radius: 999px;
            background: var(--pipeline-color);
            transition: width 0.3s ease;
        }

        .dash-pipeline-count {
            color: var(--text-primary);
            font-size: 0.72rem;
            font-weight: 700;
            text-align: right;
        }

        /* Sebaran merek */
        .dash-brand-row {
            display: grid;
            grid-template-columns: 18px minmax(0, 1fr) auto;
            align-items: center;
            gap: 0.55rem;
            padding: 0.42rem 0;
        }

        .dash-brand-rank {
            color: var(--text-muted);
            font-size: 0.66rem;
            font-weight: 700;
        }

        .dash-brand-main {
            min-width: 0;
        }

        .dash-brand-main strong {
            display: block;
            color: var(--text-primary);
            font-size: 0.76rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dash-brand-track {
            height: 4px;
            margin-top: 0.28rem;
            border-radius: 999px;
            background: var(--bg-tertiary);
            overflow: hidden;
        }

        .dash-brand-track span {
            display: block;
            height: 100%;
            border-radius: 999px;
            background: var(--bs-primary);
        }

        .dash-brand-count {
            color: var(--text-secondary);
            font-size: 0.7rem;
            font-weight: 700;
            white-space: nowrap;
        }

        /* Perlu tindakan */
        .dash-attention {
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
        }

        .dash-attention-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.6rem 0.7rem;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            background: var(--bg-secondary);
            color: inherit;
            text-decoration: none;
            transition: background 0.15s ease, border-color 0.15s ease;
        }

        .dash-attention-item:hover {
            background: var(--hover-bg);
            border-color: var(--border-color);
            color: inherit;
        }

        .dash-attention-icon {
            width: 32px;
            height: 32px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border-radius: 8px;
            background: var(--attention-soft);
            color: var(--attention-color);
            font-size: 0.75rem;
        }

        .dash-attention-copy {
            flex: 1;
            min-width: 0;
        }

        .dash-attention-copy strong {
            display: block;
            color: var(--text-primary);
            font-size: 0.74rem;
            font-weight: 700;
        }

        .dash-attention-copy span {
            display: block;
            color: var(--text-muted);
            font-size: 0.65rem;
        }

        .dash-attention-count {
            color: var(--attention-color);
            font-size: 0.92rem;
            font-weight: 700;
        }

        .dash-attention-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.35rem;
            padding: 1.35rem 0.5rem;
            color: var(--text-muted);
            font-size: 0.74rem;
            text-align: center;
        }

        .dash-attention-empty i {
            color: var(--bs-success);
            font-size: 1.35rem;
        }

        /* Empty state kecil di panel */
        .dash-panel-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
            padding: 1.75rem 0.75rem;
            color: var(--text-muted);
            font-size: 0.74rem;
            text-align: center;
        }

        .dash-panel-empty i {
            color: var(--text-muted);
            font-size: 1.35rem;
            opacity: 0.6;
        }

        .dash-footnote {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 0.25rem;
            color: var(--text-muted);
            font-size: 0.68rem;
        }

        @media (max-width: 1199.98px) {
            .dash-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dash-span-8,
            .dash-span-7,
            .dash-span-5,
            .dash-span-4 {
                grid-column: span 6;
            }
        }

        @media (max-width: 767.98px) {
            .dash-stats {
                gap: 0.65rem;
            }

            .dash-stat {
                padding: 0.85rem 0.9rem 1.1rem;
            }

            .dash-stat-icon {
                width: 34px;
                height: 34px;
                font-size: 0.82rem;
            }

            .dash-stat-value {
                font-size: 1.25rem;
            }

            .dash-stat-pill {
                font-size: 0.58rem;
                padding: 0.18rem 0.4rem;
            }

            .dash-span-8,
            .dash-span-7,
            .dash-span-5,
            .dash-span-4 {
                grid-column: span 12;
            }

            .dash-panel-head {
                padding: 0.95rem 1rem;
            }

            .dash-panel-body {
                padding: 1rem;
            }

            .dash-chart {
                gap: 0.35rem;
            }

            .dash-chart-track {
                height: 108px;
            }

            .dash-chart-total strong {
                font-size: 1.4rem;
            }

            .dash-pipeline-row {
                grid-template-columns: 84px minmax(0, 1fr) 30px;
            }
        }

        @media (max-width: 575.98px) {
            .dash-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dash-chart-head {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .dash-chart-compare {
                align-items: flex-start;
            }

            .dash-car-thumb {
                width: 46px;
                height: 36px;
            }
        }
    </style>
@endpush

<x-layouts.app title="Dashboard - RodaTa Admin">
    <x-layout.page-header
        title="Dashboard Overview"
        :subtitle="($userName ? 'Selamat datang kembali, '.$userName.'. ' : '').'Ringkasan performa inventaris dan lead RodaTa.'"
    >
        <x-slot:actions>
            <div class="d-flex flex-wrap gap-2">
                <x-ui.button variant="secondary" icon="fas fa-arrow-up-right-from-square" :href="route('home')" target="_blank" rel="noopener">
                    Lihat Situs
                </x-ui.button>
                <x-ui.button variant="primary" icon="fas fa-plus" :href="route('admin.cars.create')">
                    Tambah Mobil
                </x-ui.button>
            </div>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Ringkasan utama --}}
    <div class="dash-stats">
        @foreach ($statCards as $card)
            <a href="{{ $card['href'] }}" class="dash-stat dash-stat-{{ $card['tone'] }}">
                <span class="dash-stat-top">
                    <span class="dash-stat-icon">
                        <i class="{{ $card['icon'] }}"></i>
                    </span>
                    <span class="dash-stat-pill">{{ $card['badge'] }}</span>
                </span>
                <span class="dash-stat-value">{{ number_format($card['value'], 0, ',', '.') }}</span>
                <span class="dash-stat-label">{{ $card['label'] }}</span>
                <span class="dash-stat-meta">{{ $card['meta'] }}</span>
            </a>
        @endforeach
    </div>

    {{-- Tren lead & status inventaris --}}
    <div class="dash-grid">
        <div class="dash-span-8">
            <x-layout.modern-card :padding="false" class="dash-panel">
                <div class="dash-panel-head">
                    <div class="dash-panel-title">
                        <span class="dash-panel-icon"><i class="fas fa-chart-column"></i></span>
                        <div>
                            <h5>Tren Lead</h5>
                            <p>Tujuh hari terakhir</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.leads') }}" class="dash-panel-link">
                        Lihat semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="dash-panel-body">
                    <div class="dash-chart-head">
                        <div class="dash-chart-total">
                            <strong>{{ number_format($trendTotal, 0, ',', '.') }}</strong>
                            <span>lead masuk dalam 7 hari</span>
                        </div>
                        <div class="dash-chart-compare">
                            <span class="dash-chart-delta {{ $deltaClass }}">
                                <i class="{{ $deltaIcon }}"></i>
                                {{ $trendDelta > 0 ? '+' : '' }}{{ $trendDelta }}%
                            </span>
                            <small>dibanding 7 hari sebelumnya</small>
                        </div>
                    </div>

                    <div class="dash-chart">
                        @foreach ($leadTrend as $day)
                            @php
                                $barHeight = $day['total'] > 0
                                    ? max(6, (int) round($day['total'] / $trendMax * 100))
                                    : 4;
                            @endphp
                            <div class="dash-chart-col"
                                 title="{{ $day['label'] }}, {{ $day['full_label'] }} — {{ $day['total'] }} lead">
                                <span class="dash-chart-value">{{ $day['total'] }}</span>
                                <div class="dash-chart-track">
                                    <span class="dash-chart-bar {{ $day['is_today'] ? 'is-today' : '' }} {{ $day['total'] > 0 ? '' : 'is-zero' }}"
                                          style="height: {{ $barHeight }}%;"></span>
                                </div>
                                <span class="dash-chart-label {{ $day['is_today'] ? 'is-today' : '' }}">
                                    {{ $day['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-layout.modern-card>
        </div>

        <div class="dash-span-4">
            <x-layout.modern-card :padding="false" class="dash-panel">
                <div class="dash-panel-head">
                    <div class="dash-panel-title">
                        <span class="dash-panel-icon"><i class="fas fa-chart-pie"></i></span>
                        <div>
                            <h5>Status Inventaris</h5>
                            <p>Komposisi unit saat ini</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.cars') }}" class="dash-panel-link">
                        Kelola
                    </a>
                </div>
                <div class="dash-panel-body">
                    <div class="dash-donut-wrap">
                        <div class="dash-donut" style="background: {{ $donutGradient }};">
                            <div class="dash-donut-center">
                                <strong>{{ number_format($totalCars, 0, ',', '.') }}</strong>
                                <span>unit mobil</span>
                            </div>
                        </div>
                    </div>

                    <div class="dash-legend">
                        @foreach ($statusSegments as $segment)
                            <div class="dash-legend-row">
                                <span class="dash-legend-dot" style="background: {{ $segment['color'] }};"></span>
                                <span class="dash-legend-label">{{ $segment['label'] }}</span>
                                <span class="dash-legend-value">{{ number_format($segment['count'], 0, ',', '.') }}</span>
                                <span class="dash-legend-pct">
                                    {{ $totalCars > 0 ? (int) round($segment['count'] / $totalCars * 100) : 0 }}%
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-layout.modern-card>
        </div>
    </div>

    {{-- Lead terbaru & mobil paling dilihat --}}
    <div class="dash-grid">
        <div class="dash-span-7">
            <x-layout.modern-card :padding="false" class="dash-panel">
                <div class="dash-panel-head">
                    <div class="dash-panel-title">
                        <span class="dash-panel-icon"><i class="fas fa-inbox"></i></span>
                        <div>
                            <h5>Lead Terbaru</h5>
                            <p>Permintaan terakhir dari calon pembeli</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.leads') }}" class="dash-panel-link">
                        Lihat semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="dash-panel-body">
                    @if ($recentLeads->isEmpty())
                        <div class="dash-panel-empty">
                            <i class="fas fa-inbox"></i>
                            <span>Belum ada lead masuk.</span>
                        </div>
                    @else
                        <div class="dash-list">
                            @foreach ($recentLeads as $lead)
                                <a href="{{ route('admin.leads', ['edit' => $lead->id]) }}" class="dash-lead-row">
                                    <span class="dash-avatar">{{ $initialsFor($lead->name) }}</span>
                                    <span class="dash-lead-main">
                                        <strong>{{ $lead->name }}</strong>
                                        <span>
                                            {{ $carLabel($lead->car) }}
                                            · {{ $lead->type->label() }}
                                        </span>
                                    </span>
                                    <span class="dash-lead-side">
                                        <span class="dash-badge is-{{ $lead->status->value }}">
                                            {{ $lead->status->label() }}
                                        </span>
                                        <small>{{ $timeAgo($lead->created_at) }}</small>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </x-layout.modern-card>
        </div>

        <div class="dash-span-5">
            <x-layout.modern-card :padding="false" class="dash-panel">
                <div class="dash-panel-head">
                    <div class="dash-panel-title">
                        <span class="dash-panel-icon"><i class="fas fa-fire"></i></span>
                        <div>
                            <h5>Mobil Paling Dilihat</h5>
                            <p>Unit dengan kunjungan terbanyak</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.cars') }}" class="dash-panel-link">
                        Kelola
                    </a>
                </div>
                <div class="dash-panel-body">
                    @if ($topCars->isEmpty())
                        <div class="dash-panel-empty">
                            <i class="fas fa-car-side"></i>
                            <span>Belum ada unit mobil.</span>
                        </div>
                    @else
                        <div class="dash-list">
                            @foreach ($topCars as $car)
                                <a href="{{ route('admin.cars.edit', $car) }}" class="dash-car-row">
                                    <span class="dash-car-thumb">
                                        @if ($car->imageUrl())
                                            <img src="{{ $car->imageUrl() }}" alt="{{ $carLabel($car) }}" loading="lazy">
                                        @else
                                            <i class="fas fa-car-side"></i>
                                        @endif
                                    </span>
                                    <span class="dash-car-main">
                                        <strong>{{ $carLabel($car) }}</strong>
                                        <span>{{ $car->year }}</span>
                                    </span>
                                    <span class="dash-car-side">
                                        <span class="dash-car-price">Rp {{ number_format($car->price, 0, ',', '.') }}</span>
                                        <span class="dash-car-meta">
                                            <i class="fas fa-eye"></i> {{ number_format($car->views_count, 0, ',', '.') }}
                                            · {{ $car->leads_count }} lead
                                        </span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </x-layout.modern-card>
        </div>
    </div>

    {{-- Pipeline, merek, tindakan --}}
    <div class="dash-grid">
        <div class="dash-span-4">
            <x-layout.modern-card :padding="false" class="dash-panel">
                <div class="dash-panel-head">
                    <div class="dash-panel-title">
                        <span class="dash-panel-icon"><i class="fas fa-diagram-project"></i></span>
                        <div>
                            <h5>Pipeline Lead</h5>
                            <p>Distribusi status lead</p>
                        </div>
                    </div>
                </div>
                <div class="dash-panel-body">
                    <div class="dash-pipeline">
                        @foreach ($pipeline as $stage)
                            <div class="dash-pipeline-row">
                                <span class="dash-pipeline-label" style="--pipeline-color: {{ $stage['color'] }};">
                                    <i class="fas fa-circle"></i>
                                    {{ $stage['label'] }}
                                </span>
                                <span class="dash-pipeline-track">
                                    <span style="--pipeline-color: {{ $stage['color'] }}; width: {{ $stage['percentage'] }}%;"></span>
                                </span>
                                <span class="dash-pipeline-count">{{ $stage['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-layout.modern-card>
        </div>

        <div class="dash-span-4">
            <x-layout.modern-card :padding="false" class="dash-panel">
                <div class="dash-panel-head">
                    <div class="dash-panel-title">
                        <span class="dash-panel-icon"><i class="fas fa-trademark"></i></span>
                        <div>
                            <h5>Sebaran Merek</h5>
                            <p>Merek dengan unit terbanyak</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.brands') }}" class="dash-panel-link">
                        Kelola
                    </a>
                </div>
                <div class="dash-panel-body">
                    @if ($topBrands->isEmpty())
                        <div class="dash-panel-empty">
                            <i class="fas fa-trademark"></i>
                            <span>Belum ada merek terdaftar.</span>
                        </div>
                    @else
                        @foreach ($topBrands as $brand)
                            <div class="dash-brand-row">
                                <span class="dash-brand-rank">{{ $loop->iteration }}</span>
                                <span class="dash-brand-main">
                                    <strong>{{ $brand->name }}</strong>
                                    <span class="dash-brand-track">
                                        <span style="width: {{ max(4, (int) round($brand->cars_count / $maxBrandCars * 100)) }}%;"></span>
                                    </span>
                                </span>
                                <span class="dash-brand-count">{{ $brand->cars_count }} unit</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </x-layout.modern-card>
        </div>

        <div class="dash-span-4">
            <x-layout.modern-card :padding="false" class="dash-panel">
                <div class="dash-panel-head">
                    <div class="dash-panel-title">
                        <span class="dash-panel-icon"><i class="fas fa-list-check"></i></span>
                        <div>
                            <h5>Perlu Tindakan</h5>
                            <p>Hal yang menunggu perhatian</p>
                        </div>
                    </div>
                </div>
                <div class="dash-panel-body">
                    @if ($attentionItems === [])
                        <div class="dash-attention-empty">
                            <i class="fas fa-circle-check"></i>
                            <span>Semua beres! Tidak ada tindakan mendesak.</span>
                        </div>
                    @else
                        <div class="dash-attention">
                            @foreach ($attentionItems as $item)
                                <a href="{{ $item['href'] }}" class="dash-attention-item"
                                   style="--attention-color: {{ $item['color'] }}; --attention-soft: {{ $item['soft'] }};">
                                    <span class="dash-attention-icon">
                                        <i class="{{ $item['icon'] }}"></i>
                                    </span>
                                    <span class="dash-attention-copy">
                                        <strong>{{ $item['label'] }}</strong>
                                        <span>{{ $item['meta'] }}</span>
                                    </span>
                                    <span class="dash-attention-count">{{ $item['count'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </x-layout.modern-card>
        </div>
    </div>

    <div class="dash-footnote">
        <span>
            <i class="fas fa-circle-info me-1"></i>
            Data dihitung langsung dari inventaris, lead, dan log klik.
        </span>
        <span>Diperbarui {{ now()->format('d/m/Y H:i') }}</span>
    </div>
</x-layouts.app>
