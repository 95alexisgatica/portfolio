<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function storeNickname(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nickname' => ['required', 'string', 'min:2', 'max:24'],
        ]);

        $user = $request->user();
        $user->update(['nickname' => trim($data['nickname'])]);

        if ($user->categories()->doesntExist()) {
            $user->seedDefaultCategories();
        }

        return redirect()->route('expenses.index');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nickname' => ['required', 'string', 'min:2', 'max:24'],
        ]);

        $request->user()->update(['nickname' => trim($data['nickname'])]);

        return redirect()->route('expenses.index');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ]);

        $user = $request->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->update([
            'avatar_path' => $request->file('avatar')->store('avatars', 'public'),
        ]);

        return redirect()->route('expenses.index');
    }

    public function destroyAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }

        return redirect()->route('expenses.index');
    }
}
