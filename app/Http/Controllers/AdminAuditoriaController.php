<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class AdminAuditoriaController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = [
            'desde' => $request->query('desde'),
            'hasta' => $request->query('hasta'),
            'q' => $request->query('q'),
        ];

        $actividades = Activity::with(['causer', 'subject.pago', 'subject.user'])
            ->where('log_name', 'pedido')
            ->when($filtros['desde'], fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($filtros['hasta'], fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->when($filtros['q'], fn ($q, $texto) => $q->where('description', 'like', "%{$texto}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.auditoria.index', [
            'actividades' => $actividades,
            'filtros' => $filtros,
            'admin' => auth()->user(),
        ]);
    }
}
