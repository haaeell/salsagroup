<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('account.setting', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama_depan'   => 'required|string|max:100',
            'nama_belakang' => 'nullable|string|max:100',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'no_telepon'   => 'nullable|string',
            'alamat'       => 'nullable|string',
            'password'     => 'nullable|confirmed|min:6',
        ]);

        $user->update([
            'nama_depan'   => $request->nama_depan,
            'nama_belakang' => $request->nama_belakang,
            'email'        => $request->email,
            'no_telepon'   => $request->no_telepon,
            'alamat'       => $request->alamat,
            'password'     => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
