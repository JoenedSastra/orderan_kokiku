<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Tandai 1 notifikasi sebagai dibaca, lalu redirect ke url terkait.
     */
    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        return $url ? redirect($url) : back();
    }

    /**
     * Tandai semua notifikasi sebagai dibaca.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
    /**
     * Hapus notifikasi
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus');
    }
    /**
     * Hapus semua notifikasi yang sudah dibaca
     */
    public function destroyAllRead(Request $request): RedirectResponse
    {
        $request->user()->notifications()->whereNotNull('read_at')->delete();

        return back()->with('success', 'Semua notifikasi yang sudah dibaca berhasil dihapus');
    }
}
