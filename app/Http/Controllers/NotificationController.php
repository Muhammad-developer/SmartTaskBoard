<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->with(['actor', 'notifiable'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'notifications' => $notifications,
        ], 200);
    }

    public function markAsRead(Request $request)
    {
        $validated = $request->validate([
            'notification_id' => 'required|exists:notifications,id',
        ]);

        $notification = Notification::where('id', $validated['notification_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $notification->update([
            'read_at' => now(),
        ]);

        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification,
        ], 200);
    }

    public function deleteNotification(Request $request)
    {
        $validated = $request->validate([
            'notification_id' => 'required|exists:notifications,id',
        ]);

        $notification = Notification::where('id', $validated['notification_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted successfully',
        ], 200);
    }
}
