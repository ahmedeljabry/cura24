<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class NotificationController extends Controller
{
    public function myNotifications(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user || !in_array($user->user_type, [0, 1])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized. Only sellers or buyers can view these notifications.'
            ], 403);
        }

        $perPage = $request->get('per_page', 10); // default 10
        $page    = $request->get('page', 1);

        // Fetch unread notifications first
        $unread = DB::table('notifications')
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc');

        // Fetch read notifications
        $read = DB::table('notifications')
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNotNull('read_at')
            ->orderBy('created_at', 'desc');

        // Merge queries using union
        $notificationsQuery = $unread->unionAll($read);

        // Paginate combined query
        $notifications = DB::table(DB::raw("({$notificationsQuery->toSql()}) as n"))
            ->mergeBindings($notificationsQuery)
            ->paginate($perPage, ['*'], 'page', $page);

        // Mark unread as read *after serving*
        $unreadIds = $notifications->whereNull('read_at')->pluck('id');

        if ($unreadIds->count() > 0) {
            DB::table('notifications')
                ->whereIn('id', $unreadIds)
                ->update(['read_at' => now()]);
        }

        return response()->success([
            'notifications' => $notifications
        ]);
    }

    public function unreadCount(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user || !in_array($user->user_type, [0, 1])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized. Only sellers or buyers can view these notifications.'
            ], 403);
        }

        $count = DB::table('notifications')
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->success([
            'unread_count' => $count
        ]);
    }


}
