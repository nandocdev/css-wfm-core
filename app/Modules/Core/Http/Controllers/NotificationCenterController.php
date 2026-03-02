<?php

declare(strict_types=1);

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class NotificationCenterController extends Controller {
    public function index(Request $request): View {
        $user = $request->user();

        abort_if($user === null, 403);

        $notifications = $user->notifications()
            ->latest()
            ->paginate(15);

        $unreadCount = $user->unreadNotifications()->count();

        return view('core::notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function markAsRead(Request $request, string $notification): RedirectResponse {
        $user = $request->user();

        abort_if($user === null, 403);

        $targetNotification = $user->notifications()->where('id', $notification)->first();

        if ($targetNotification === null) {
            return back()->withErrors(['notification' => 'La notificación no existe o no pertenece al usuario autenticado.']);
        }

        $targetNotification->markAsRead();

        return back()->with('status', 'Notificación marcada como leída.');
    }

    public function markAllAsRead(Request $request): RedirectResponse {
        $user = $request->user();

        abort_if($user === null, 403);

        $user->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('status', 'Todas las notificaciones fueron marcadas como leídas.');
    }
}
