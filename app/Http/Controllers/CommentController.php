<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Reservation;
use App\Models\MaintenanceReport;
use App\Models\Notification;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
            'reservation_id' => 'nullable|exists:reservations,id',
            'maintenance_report_id' => 'nullable|exists:maintenance_reports,id',
        ]);

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'reservation_id' => $request->reservation_id,
            'maintenance_report_id' => $request->maintenance_report_id,
            'body' => $request->body,
        ]);

        // Dispatch notification to ticket/reservation owner if commenter is not the owner
        if ($request->reservation_id) {
            $reservation = Reservation::find($request->reservation_id);
            if ($reservation && $reservation->user_id !== $request->user()->id) {
                Notification::create([
                    'user_id' => $reservation->user_id,
                    'title' => 'New Comment on Reservation',
                    'message' => "{$request->user()->name} commented on your reservation {$reservation->reservation_code}: \"" . \Illuminate\Support\Str::limit($request->body, 50) . "\"",
                    'type' => 'info',
                    'link' => route('reservations.index'),
                ]);
            }
        } elseif ($request->maintenance_report_id) {
            $report = MaintenanceReport::find($request->maintenance_report_id);
            if ($report && $report->user_id !== $request->user()->id) {
                Notification::create([
                    'user_id' => $report->user_id,
                    'title' => 'New Comment on Maintenance Report',
                    'message' => "{$request->user()->name} commented on report {$report->report_code}: \"" . \Illuminate\Support\Str::limit($request->body, 50) . "\"",
                    'type' => 'info',
                    'link' => route('maintenance.index'),
                ]);
            }
        }

        return back()->with('success', 'Comment posted successfully.');
    }

    public function destroy(Request $request, Comment $comment)
    {
        if (!$request->user()->isAdmin() && $request->user()->id !== $comment->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}
