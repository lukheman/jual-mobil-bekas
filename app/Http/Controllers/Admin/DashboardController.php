<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CarStatus;
use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Models\ClickLog;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function __invoke(Request $request): View
    {
        $carStatusCounts = Car::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalCars = (int) $carStatusCounts->sum();

        $leadStatusCounts = Lead::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalLeads = (int) $leadStatusCounts->sum();
        $dealLeads = (int) ($leadStatusCounts[LeadStatus::Deal->value] ?? 0);

        $clickCounts = ClickLog::query()
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $trendStart = now()->subDays(6)->startOfDay();
        $leadTimestamps = Lead::query()
            ->where('created_at', '>=', $trendStart)
            ->get(['created_at'])
            ->countBy(fn (Lead $lead) => $lead->created_at->toDateString());

        $leadTrend = collect(range(0, 6))->map(function (int $offset) use ($trendStart, $leadTimestamps): array {
            $date = $trendStart->copy()->addDays($offset);

            return [
                'date' => $date,
                'label' => $date->translatedFormat('D'),
                'full_label' => $date->translatedFormat('d M'),
                'total' => (int) ($leadTimestamps[$date->toDateString()] ?? 0),
                'is_today' => $date->isToday(),
            ];
        });

        $trendTotal = (int) $leadTrend->sum('total');
        $previousWeekTotal = Lead::query()
            ->whereBetween('created_at', [now()->subDays(13)->startOfDay(), now()->subDays(7)->endOfDay()])
            ->count();

        $trendDelta = $previousWeekTotal > 0
            ? (int) round(($trendTotal - $previousWeekTotal) / $previousWeekTotal * 100)
            : ($trendTotal > 0 ? 100 : 0);

        $topBrands = Brand::query()
            ->withCount('cars')
            ->orderByDesc('cars_count')
            ->orderBy('name')
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'userName' => $request->user()?->name,
            'stats' => [
                'cars' => [
                    'total' => $totalCars,
                    'available' => (int) ($carStatusCounts[CarStatus::Available->value] ?? 0),
                    'reserved' => (int) ($carStatusCounts[CarStatus::Reserved->value] ?? 0),
                    'sold' => (int) ($carStatusCounts[CarStatus::Sold->value] ?? 0),
                    'featured' => Car::query()->where('is_featured', true)->count(),
                    'archived' => Car::onlyTrashed()->count(),
                    'without_photo' => Car::query()->doesntHave('photos')->count(),
                    'views' => (int) Car::query()->sum('views_count'),
                ],
                'leads' => [
                    'total' => $totalLeads,
                    'new' => (int) ($leadStatusCounts[LeadStatus::New->value] ?? 0),
                    'contacted' => (int) ($leadStatusCounts[LeadStatus::Contacted->value] ?? 0),
                    'test_drive' => (int) ($leadStatusCounts[LeadStatus::TestDrive->value] ?? 0),
                    'deal' => $dealLeads,
                    'cancelled' => (int) ($leadStatusCounts[LeadStatus::Cancelled->value] ?? 0),
                    'stale' => Lead::query()
                        ->where('status', LeadStatus::New)
                        ->where('created_at', '<', now()->subDay())
                        ->count(),
                    'conversion' => $totalLeads > 0 ? (int) round($dealLeads / $totalLeads * 100) : 0,
                ],
                'clicks' => [
                    'whatsapp' => (int) ($clickCounts['whatsapp'] ?? 0),
                    'phone' => (int) ($clickCounts['phone'] ?? 0),
                ],
            ],
            'leadTrend' => $leadTrend,
            'leadStatuses' => LeadStatus::cases(),
            'trendTotal' => $trendTotal,
            'trendDelta' => $trendDelta,
            'recentLeads' => Lead::query()
                ->with('car.carModel.brand')
                ->latest()
                ->take(6)
                ->get(),
            'topCars' => Car::query()
                ->with(['carModel.brand', 'primaryPhoto'])
                ->withCount('leads')
                ->orderByDesc('views_count')
                ->take(5)
                ->get(),
            'topBrands' => $topBrands,
            'maxBrandCars' => max(1, (int) $topBrands->max('cars_count')),
        ]);
    }
}
