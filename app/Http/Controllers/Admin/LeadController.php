<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LeadRequest;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    /**
     * Display the lead list.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $type = (string) $request->query('type', '');

        $leads = Lead::query()
            ->with('car.carModel.brand')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($type !== '', fn ($query) => $query->where('type', $type))
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        $statusCounts = Lead::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.leads.index', [
            'leads' => $leads,
            'search' => $search,
            'status' => $status,
            'type' => $type,
            'statuses' => LeadStatus::cases(),
            'types' => LeadType::cases(),
            'stats' => [
                'total' => (int) $statusCounts->sum(),
                'new' => (int) ($statusCounts[LeadStatus::New->value] ?? 0),
                'contacted' => (int) ($statusCounts[LeadStatus::Contacted->value] ?? 0),
                'test_drive' => (int) ($statusCounts[LeadStatus::TestDrive->value] ?? 0),
                'deal' => (int) ($statusCounts[LeadStatus::Deal->value] ?? 0),
                'cancelled' => (int) ($statusCounts[LeadStatus::Cancelled->value] ?? 0),
            ],
            'editingLead' => $request->filled('edit')
                ? Lead::with('car.carModel.brand')->findOrFail($request->integer('edit'))
                : null,
        ]);
    }

    /**
     * Update a lead's status and notes.
     */
    public function update(LeadRequest $request, Lead $lead): RedirectResponse
    {
        $lead->update($request->validated());

        $query = array_filter([
            'q' => $request->input('return_q'),
            'status' => $request->input('return_status'),
            'type' => $request->input('return_type'),
            'page' => $request->input('return_page'),
        ], fn ($value) => $value !== null && $value !== '');

        return redirect()
            ->route('admin.leads', $query)
            ->with('success', 'Lead berhasil diperbarui.');
    }

}
