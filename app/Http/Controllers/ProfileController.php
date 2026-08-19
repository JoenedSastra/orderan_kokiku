<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

        try {
            if ($request->hasFile('avatar')) {
                // Hapus avatar lama jika ada dan bukan bawaan/default
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $file = $request->file('avatar');
                $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                
                // Simpan ke storage/app/public/avatars
                $path = $file->storeAs('avatars', $filename, 'public');
                
                // Update tabel users
                $user->avatar = $path;
                $user->save();

                return back()->with('success', 'Foto profil berhasil diperbarui.');
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengupload avatar: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui foto profil.');
        }

        return back();
    }
}
