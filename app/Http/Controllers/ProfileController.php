<?php

namespace App\Http\Controllers;

use App\Services\ImgBBService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $user = auth()->user();
        $user->name = $request->name;

        if ($request->hasFile('avatar')) {
            $imgbb = new ImgBBService();
            $url = $imgbb->upload($request->file('avatar'));

            if ($url) {
                // Uploaded to ImgBB successfully
                $user->avatar = $url;
            } else {
                // Fallback: store locally (works on localhost, not on Vercel)
                if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $path = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $path;
            }
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}

