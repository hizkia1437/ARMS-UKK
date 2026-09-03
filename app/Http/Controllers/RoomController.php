<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Http\Requests\RoomRequest;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $sortBy = in_array($request->input('sort_by'), ['name', 'room_code', 'capacity', 'status', 'created_at']) ? $request->input('sort_by') : 'created_at';
        $sortDir = strtolower($request->input('sort_dir')) === 'asc' ? 'asc' : 'desc';

        $query = Room::query();

        $query->when($search, function ($q, $search) {
            $q->where('room_code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('capacity', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%");
        });

        $query->when($status, function ($q, $status) {
            $q->where('status', $status);
        });

        $rooms = $query->orderBy($sortBy, $sortDir)->paginate(10)->withQueryString();

        return view('rooms.index', compact('rooms', 'search', 'status', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        $nextCode = 'RM-' . str_pad((Room::max('id') + 1), 3, '0', STR_PAD_LEFT);
        return view('rooms.create', compact('nextCode'));
    }

    public function store(RoomRequest $request)
    {
        Room::create($request->validated());

        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
    }

    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(RoomRequest $request, Room $room)
    {
        $room->update($request->validated());

        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }
}
