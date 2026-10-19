<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DireccionController extends Controller
{
    public function index()
    {
        return view('direcciones.index', [
            'direcciones' => Auth::user()->direcciones()->orderByDesc('predeterminada')->get(),
        ]);
    }

    public function create()
    {
        return view('direcciones.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($data['predeterminada'] ?? false) {
            $request->user()->direcciones()->update(['predeterminada' => false]);
        }

        $request->user()->direcciones()->create($data);

        return redirect()->route('direcciones.index');
    }

    public function edit(Direccion $direccion)
    {
        abort_unless($direccion->user_id === Auth::id(), 403);

        return view('direcciones.edit', ['direccion' => $direccion]);
    }

    public function update(Request $request, Direccion $direccion)
    {
        abort_unless($direccion->user_id === $request->user()->id, 403);

        $data = $this->validated($request);

        if ($data['predeterminada'] ?? false) {
            $request->user()->direcciones()->where('id', '!=', $direccion->id)->update(['predeterminada' => false]);
        }

        $direccion->update($data);

        return redirect()->route('direcciones.index');
    }

    public function destroy(Direccion $direccion)
    {
        abort_unless($direccion->user_id === Auth::id(), 403);

        $direccion->delete();

        return redirect()->route('direcciones.index');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'etiqueta' => 'required|string|max:255',
            'linea' => 'required|string|max:255',
            'referencia' => 'nullable|string|max:255',
            'predeterminada' => 'boolean',
        ]) + ['predeterminada' => $request->boolean('predeterminada')];
    }
}
