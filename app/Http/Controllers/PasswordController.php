<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function edit()
    {
        return view('auth.password');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ], [
            'current_password.current_password' =>
                'Kata sandi lama tidak sesuai.',
            'password.confirmed' =>
                'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' =>
                'Kata sandi baru minimal 8 karakter.',
        ]);

        $user = $request->user();

        $user->password = $validated['password'];
        $user->save();

        return redirect()
            ->route('password.edit')
            ->with('success', 'Kata sandi berhasil diubah.');
    }
}