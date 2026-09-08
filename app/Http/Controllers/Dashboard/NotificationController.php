<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = UserNotification::where('user_id', $user->id)->latest()->paginate(15)->withQueryString();
        $unreadCount = UserNotification::where('user_id', $user->id)->whereNull('read_at')->count();

        return view('dashboard.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(Request $request, int $id)
    {
        $user = $request->user();
        $notification = UserNotification::where('user_id', $user->id)->findOrFail($id);

        if (is_null($notification->read_at)) {
            $notification->read_at = now();
            $notification->save();
        }

        if ($notification->action_url) {
            return redirect()->to($notification->action_url);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead(Request $request)
    {
        $user = $request->user();
        UserNotification::where('user_id', $user->id)->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
