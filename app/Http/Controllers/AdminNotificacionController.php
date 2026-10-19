<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminNotificacionController extends Controller
{
    public function index(): View
    {
        return view('admin.notificaciones.index', [
            'notificaciones' => auth()->user()->notifications()->latest()->paginate(20),
        ]);
    }

    public function marcarLeidas(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('status', 'Notificaciones marcadas como leídas.');
    }
}
