<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceReport;
use App\Models\Asset;
use App\Models\User;
use App\Models\Notification;
use App\Http\Requests\MaintenanceReportRequest;
use Illuminate\Http\Request;

class MaintenanceReportController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $targetType = $request->input('target_type');
        $sortBy = in_array($request->input('sort_by'), ['report_code', 'status', 'created_at']) ? $request->input('sort_by') : 'created_at';
        $sortDir = strtolower($request->input('sort_dir')) === 'asc' ? 'asc' : 'desc';

        $query = MaintenanceReport::with(['user', 'asset', 'room']);

        $query->when($search, function ($q, $search) {
            $q->where('report_code', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhereHas('asset', function ($aq) use ($search) {
                    $aq->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('room', function ($rq) use ($search) {
                    $rq->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                });
        });

        $query->when($status, function ($q, $status) {
            $q->where('status', $status);
        });

        $query->when($targetType === 'asset', function ($q) {
            $q->whereNotNull('asset_id');
        });

        $query->when($targetType === 'room', function ($q) {
            $q->whereNotNull('room_id');
        });

        $reports = $query->orderBy($sortBy, $sortDir)->paginate(10)->withQueryString();

        return view('maintenance.index', compact('reports', 'search', 'status', 'targetType', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        $assets = Asset::all();
        $rooms = \App\Models\Room::all();
        $nextCode = 'MNT-' . date('Ymd') . '-' . str_pad((MaintenanceReport::max('id') + 1), 3, '0', STR_PAD_LEFT);

        return view('maintenance.create', compact('assets', 'rooms', 'nextCode'));
    }

    public function store(MaintenanceReportRequest $request)
    {
        $data = $request->validated();

        if (empty($data['report_code'])) {
            $data['report_code'] = 'MNT-' . date('Ymd') . '-' . str_pad((MaintenanceReport::max('id') + 1), 3, '0', STR_PAD_LEFT);
        }

        if (!$request->user()->isAdmin() || empty($data['user_id'])) {
            $data['user_id'] = $request->user()->id;
        }

        $data['status'] = 'Pending';

        $report = MaintenanceReport::create($data);

        $targetName = $report->asset->name ?? $report->room->name ?? 'Target Item';

        // Notify Admins and Staff
        $adminStaffIds = User::whereIn('role', ['Admin', 'Staff'])->pluck('id');
        foreach ($adminStaffIds as $recipientId) {
            Notification::create([
                'user_id' => $recipientId,
                'title' => 'New Maintenance Report',
                'message' => "{$request->user()->name} reported an issue ({$report->report_code}) for {$targetName}",
                'type' => 'maintenance_created',
                'link' => route('maintenance.index'),
            ]);
        }

        return redirect()->route('maintenance.index')->with('success', 'Maintenance report submitted successfully.');
    }

    public function edit(MaintenanceReport $maintenance)
    {
        if (!auth()->user()->isAdmin() && auth()->id() !== $maintenance->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $assets = Asset::all();
        $rooms = \App\Models\Room::all();
        return view('maintenance.edit', compact('maintenance', 'assets', 'rooms'));
    }

    public function update(MaintenanceReportRequest $request, MaintenanceReport $maintenance)
    {
        if (!auth()->user()->isAdmin() && auth()->id() !== $maintenance->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validated();
        $maintenance->update($data);

        return redirect()->route('maintenance.index')->with('success', 'Maintenance report updated successfully.');
    }

    public function destroy(MaintenanceReport $maintenance)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $maintenance->delete();

        return redirect()->route('maintenance.index')->with('success', 'Maintenance report deleted successfully.');
    }

    public function updateStatus(Request $request, MaintenanceReport $maintenance)
    {
        $user = $request->user();
        if (!$user->isAdmin() && !$user->isStaff()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:Pending,Completed',
        ]);

        $maintenance->update(['status' => $request->status]);

        $targetName = $maintenance->asset->name ?? $maintenance->room->name ?? 'Target Item';

        if ($request->status === 'Completed') {
            Notification::create([
                'user_id' => $maintenance->user_id,
                'title' => 'Maintenance Completed',
                'message' => "Your reported issue ({$maintenance->report_code}) for {$targetName} has been resolved & marked Completed.",
                'type' => 'maintenance_completed',
                'link' => route('maintenance.index'),
            ]);
        }

        return redirect()->route('maintenance.index')->with('success', 'Maintenance status updated to ' . $request->status . '.');
    }
}
