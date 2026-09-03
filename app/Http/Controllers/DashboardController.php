<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\MaintenanceReport;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        
        $totalAssets = Asset::count();
        $totalRooms = Room::count();
        $pendingReservations = Reservation::where('status', 'Pending')->count();
        $pendingMaintenances = MaintenanceReport::where('status', 'Pending')->count();

        if ($user->isUser()) {
            $recentReservations = Reservation::with(['room', 'user'])
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
        } else {
            $recentReservations = Reservation::with(['room', 'user'])
                ->latest()
                ->take(5)
                ->get();
        }

        return view('dashboard', compact(
            'totalAssets',
            'totalRooms',
            'pendingReservations',
            'pendingMaintenances',
            'recentReservations'
        ));
    }
}
