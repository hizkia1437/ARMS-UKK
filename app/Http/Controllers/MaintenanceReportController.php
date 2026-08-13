<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceReport;
use App\Models\Asset;
use App\Http\Requests\MaintenanceReportRequest;
use Illuminate\Http\Request;

class MaintenanceReportController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = MaintenanceReport::with(['user', 'asset', 'room']);

        $reports = $query->when($search, function ($q, $search) {
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
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('maintenance.index', compact('reports', 'search'));
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

        MaintenanceReport::create($data);

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

        return redirect()->route('maintenance.index')->with('success', 'Maintenance status updated to ' . $request->status . '.');
    }
}
