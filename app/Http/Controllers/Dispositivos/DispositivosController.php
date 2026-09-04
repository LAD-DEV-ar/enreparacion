<?php

namespace App\Http\Controllers\Dispositivos;

use App\Http\Controllers\Controller;
use App\Models\Dispositivo;
use Illuminate\Http\Request;

class DispositivosController extends Controller
{
    public function update(Request $request, Dispositivo $dispositivo)
    {
        $user = auth()->user();

        // Validamos que el dispositivo pertenezca a un cliente del negocio del usuario autenticado
        if ($dispositivo->cliente?->negocios_id !== $user->negocios_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para modificar este dispositivo.',
            ], 403);
        }

        $validated = $request->validate([
            'marca_y_modelo' => ['required', 'string', 'max:255'],
            'imei_o_serie' => ['nullable', 'string', 'max:255'],
        ], [
            'marca_y_modelo.required' => 'La marca y modelo del dispositivo son obligatorios.',
            'marca_y_modelo.max' => 'La marca y modelo no pueden exceder 255 caracteres.',
            'imei_o_serie.max' => 'El IMEI o número de serie no puede exceder 255 caracteres.',
        ]);

        $dispositivo->marca_y_modelo = $validated['marca_y_modelo'];
        $dispositivo->imei_o_serie = $validated['imei_o_serie'] ?? null;
        $dispositivo->save();

        return response()->json([
            'success' => true,
            'message' => 'Datos del dispositivo actualizados correctamente.',
            'dispositivo' => [
                'id' => $dispositivo->id,
                'marca_y_modelo' => $dispositivo->marca_y_modelo,
                'imei_o_serie' => $dispositivo->imei_o_serie ?? 'No especificado',
            ],
        ]);
    }
}
