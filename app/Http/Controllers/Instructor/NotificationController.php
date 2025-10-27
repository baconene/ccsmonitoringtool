<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Get unread notifications count
     */
    public function getUnreadCount()
    {
        $count = InstructorNotification::forInstructor(auth()->id())
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get all notifications
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        \Log::info('Fetching notifications for user', ['user_id' => $userId]);
        
        $notifications = InstructorNotification::forInstructor($userId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        \Log::info('Notifications found', [
            'user_id' => $userId,
            'count' => $notifications->count(),
            'total' => $notifications->total()
        ]);

        // Force JSON response, bypassing Inertia
        return response()->json($notifications)
            ->header('Content-Type', 'application/json');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = InstructorNotification::forInstructor(auth()->id())
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead()
    {
        InstructorNotification::forInstructor(auth()->id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = InstructorNotification::forInstructor(auth()->id())
            ->findOrFail($id);

        $notification->delete();

        return response()->json(['success' => true]);
    }
}
