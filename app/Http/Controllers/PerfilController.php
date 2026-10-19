<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    public function update(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.Auth::id(),
        ];

        if ($request->filled('current_password') || $request->filled('password')) {
            $rules = array_merge($rules, [
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'min:8', 'confirmed'],
            ]);
        }

        $data = $request->validate($rules);

        $user = Auth::user();
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => bcrypt($data['password']),
            ]);
        }

        return redirect()->route('profile.show')->with('status', 'Perfil actualizado.');
    }

    public function actualizarNotificaciones(Request $request)
    {
        Auth::user()->update([
            'notif_whatsapp_lista' => $request->boolean('notif_whatsapp_lista'),
            'notif_en_camino' => $request->boolean('notif_en_camino'),
            'notif_promos' => $request->boolean('notif_promos'),
        ]);

        return redirect()->route('profile.show')->with('status', 'Preferencias guardadas.');
    }
}
