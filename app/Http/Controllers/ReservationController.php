<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\Models\Notification;
use App\Http\Requests\ReservationRequest;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $roomId = $request->input('room_id');
        $sortBy = in_array($request->input('sort_by'), ['reservation_date', 'start_time', 'reservation_code', 'status', 'created_at']) ? $request->input('sort_by') : 'reservation_date';
        $sortDir = strtolower($request->input('sort_dir')) === 'asc' ? 'asc' : 'desc';

        $user = $request->user();

        $query = Reservation::with(['room', 'user', 'comments.user']);

        if ($user->isUser()) {
            $query->where('user_id', $user->id);
        }

        $query->when($search, function ($q, $search) {
            $q->where('reservation_code', 'like', "%{$search}%")
                ->orWhere('purpose', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('room', function ($rq) use ($search) {
                    $rq->where('name', 'like', "%{$search}%");
                });
        });

        $query->when($status, function ($q, $status) {
            $q->where('status', $status);
        });

        $query->when($roomId, function ($q, $roomId) {
            $q->where('room_id', $roomId);
        });

        $reservations = $query->orderBy($sortBy, $sortDir)->paginate(10)->withQueryString();
        $rooms = Room::all();

        return view('reservations.index', compact('reservations', 'search', 'status', 'roomId', 'sortBy', 'sortDir', 'rooms'));
    }

    public function create()
    {
        $rooms = Room::where('status', '!=', 'Under Maintenance')->get();
        $nextCode = 'RSV-' . date('Ymd') . '-' . str_pad((Reservation::max('id') + 1), 3, '0', STR_PAD_LEFT);
        
        return view('reservations.create', compact('rooms', 'nextCode'));
    }

    public function store(ReservationRequest $request)
    {
        $data = $request->validated();

        if (empty($data['reservation_code'])) {
            $data['reservation_code'] = 'RSV-' . date('Ymd') . '-' . str_pad((Reservation::max('id') + 1), 3, '0', STR_PAD_LEFT);
        }

        if (!$request->user()->isAdmin() || empty($data['user_id'])) {
            $data['user_id'] = $request->user()->id;
        }

        // Check if there is an already APPROVED reservation for the same room, date & overlapping time
        $alreadyApproved = Reservation::where('room_id', $data['room_id'])
            ->where('reservation_date', $data['reservation_date'])
            ->where('status', 'Approved')
            ->where(function ($query) use ($data) {
                $query->where('start_time', '<', $data['end_time'])
                      ->where('end_time', '>', $data['start_time']);
            })->exists();

        if ($alreadyApproved) {
            return back()->withInput()->with('error', 'This room is already approved for another reservation during the selected time slot. Please select a different time or room.');
        }

        $data['status'] = 'Pending';

        $reservation = Reservation::create($data);

        // Notify Admins and Staff
        $adminStaffIds = User::whereIn('role', ['Admin', 'Staff'])->pluck('id');
        foreach ($adminStaffIds as $recipientId) {
            Notification::create([
                'user_id' => $recipientId,
                'title' => 'New Reservation Submitted',
                'message' => "{$request->user()->name} submitted a reservation ({$reservation->reservation_code}) for " . ($reservation->room->name ?? 'Room') . " on " . date('d M Y', strtotime($reservation->reservation_date)),
                'type' => 'reservation_created',
                'link' => route('reservations.index'),
            ]);
        }

        return redirect()->route('reservations.index')->with('success', 'Reservation submitted successfully.');
    }

    public function edit(Reservation $reservation)
    {
        if (!auth()->user()->isAdmin() && auth()->id() !== $reservation->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $rooms = Room::all();
        return view('reservations.edit', compact('reservation', 'rooms'));
    }

    public function update(ReservationRequest $request, Reservation $reservation)
    {
        if (!auth()->user()->isAdmin() && auth()->id() !== $reservation->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validated();

        // Check if there is another APPROVED reservation for the same room, date & overlapping time
        $alreadyApproved = Reservation::where('id', '!=', $reservation->id)
            ->where('room_id', $data['room_id'])
            ->where('reservation_date', $data['reservation_date'])
            ->where('status', 'Approved')
            ->where(function ($query) use ($data) {
                $query->where('start_time', '<', $data['end_time'])
                      ->where('end_time', '>', $data['start_time']);
            })->exists();

        if ($alreadyApproved) {
            return back()->withInput()->with('error', 'This room is already approved for another reservation during the selected time slot.');
        }

        $reservation->update($data);

        return redirect()->route('reservations.index')->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation)
    {
        if (!auth()->user()->isAdmin() && auth()->id() !== $reservation->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $reservation->delete();

        return redirect()->route('reservations.index')->with('success', 'Reservation deleted successfully.');
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only admin can change reservation status.');
        }

        $request->validate([
            'status' => 'required|in:Approved,Rejected',
        ]);

        $newStatus = $request->status;
        $reservation->update(['status' => $newStatus]);

        // Notify reservation owner
        Notification::create([
            'user_id' => $reservation->user_id,
            'title' => "Reservation {$newStatus}",
            'message' => "Your reservation {$reservation->reservation_code} for " . ($reservation->room->name ?? 'Room') . " has been {$newStatus}.",
            'type' => $newStatus === 'Approved' ? 'reservation_approved' : 'reservation_rejected',
            'link' => route('reservations.index'),
        ]);

        $autoRejectedCount = 0;

        // If approved, automatically reject any pending conflicting reservations for the same room, date & overlapping time slot
        if ($newStatus === 'Approved') {
            $conflictingReservations = Reservation::where('id', '!=', $reservation->id)
                ->where('room_id', $reservation->room_id)
                ->where('reservation_date', $reservation->reservation_date)
                ->where('status', 'Pending')
                ->where(function ($query) use ($reservation) {
                    $query->where('start_time', '<', $reservation->end_time)
                          ->where('end_time', '>', $reservation->start_time);
                })->get();

            $autoRejectedCount = $conflictingReservations->count();
            foreach ($conflictingReservations as $conflict) {
                $conflict->update(['status' => 'Rejected']);
                Notification::create([
                    'user_id' => $conflict->user_id,
                    'title' => 'Reservation Auto-Rejected',
                    'message' => "Your reservation {$conflict->reservation_code} for " . ($conflict->room->name ?? 'Room') . " was automatically rejected due to a schedule conflict.",
                    'type' => 'reservation_rejected',
                    'link' => route('reservations.index'),
                ]);
            }
        }

        $message = "Reservation {$reservation->reservation_code} status updated to {$newStatus}.";
        if ($autoRejectedCount > 0) {
            $message .= " {$autoRejectedCount} conflicting pending reservation(s) for the same room & time slot were automatically rejected.";
        }

        return redirect()->route('reservations.index')->with('success', $message);
    }
}
