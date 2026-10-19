<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRegistroController extends Controller
{
    public function index(Request $request): View
    {
        $registros = Pedido::with(['user', 'direccion', 'lineas', 'pago'])
            ->where('archivado', true)
            ->when($request->query('desde'), fn ($q, $d) => $q->whereDate('archivado_en', '>=', $d))
            ->when($request->query('hasta'), fn ($q, $d) => $q->whereDate('archivado_en', '<=', $d))
            ->when($request->query('q'), function ($q, $texto) {
                $q->where(function ($sub) use ($texto) {
                    $sub->where('codigo', 'like', "%{$texto}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$texto}%")
                            ->orWhere('email', 'like', "%{$texto}%"));
                });
            })
            ->orderByDesc('archivado_en')
            ->paginate(10)
            ->withQueryString();

        return view('admin.registros.index', [
            'registros' => $registros,
            'filtros' => [
                'desde' => $request->query('desde'),
                'hasta' => $request->query('hasta'),
                'q' => $request->query('q'),
            ],
        ]);
    }
}
