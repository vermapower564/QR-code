<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->paginate(15);
        $unreadCount = $user->unreadNotifications()->count();

        return view('dashboard.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(Request $request, string $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->findOrFail($id);

        $notification->markAsRead();

        if (isset($notification->data['action_url'])) {
            return redirect()->to($notification->data['action_url']);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}