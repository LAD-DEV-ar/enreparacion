<?php

namespace App\Http\Controllers\Planes;

use App\Exceptions\MercadoPagoException;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\MercadoPago\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PlanesController extends Controller
{
    public function index()
    {
        $planes = Plan::all()->all();

        return view('home.planes', [
            'planes' => $planes,
        ]);
    }

    public function store(Request $request, SubscriptionService $subscriptionService)
    {
        $validated = $request->validate([
            'plan' => ['required', 'exists:planes,id'],
        ]);

        $user = auth()->user();
        $negocio = $user->negocio;

        if (! $negocio) {
            return response()->json([
                'error' => 'No tienes un negocio asociado a tu cuenta.',
            ], 403);
        }

        $plan = Plan::findOrFail((int) $validated['plan']);

        try {
            $suscripcion = $subscriptionService->createPendingPreapproval(
                plan: $plan,
                negocio: $negocio,
                payerEmail: $user->email,
            );
        } catch (MercadoPagoException $e) {
            Log::error('Mercado Pago: fallo al crear preapproval', [
                'message' => $e->getMessage(),
                'status' => $e->getStatusCode(),
                'response' => $e->getResponse()?->getContent(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'No pudimos iniciar el pago. Intentá nuevamente en unos minutos.',
                ], 422);
            }

            return redirect()->route('planes.index')
                ->with('error', 'No pudimos iniciar el pago. Intentá nuevamente en unos minutos.');
        }

        $initPoint = $suscripcion->metadatos['init_point'] ?? null;

        if (! $initPoint) {
            return response()->json([
                'error' => 'Mercado Pago no devolvió una URL de pago.',
            ], 422);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => $initPoint,
            ]);
        }

        return redirect()->away($initPoint);
    }

    public function retorno(Request $request)
    {
        $negocio = auth()->user()->negocio;

        if (! $negocio) {
            return redirect()->route('negocios');
        }

        $preapprovalId = $request->query('preapproval_id');

        if (! $preapprovalId) {
            $preapprovalId = $negocio->suscripcion?->mp_preapproval_id;
        }

        if ($preapprovalId) {
            try {
                $suscripcion = app(SubscriptionService::class)->syncFromPreapproval((string) $preapprovalId);
            } catch (MercadoPagoException $e) {
                $suscripcion = null;
            }

            if ($suscripcion?->estado) {
                return redirect()->route('dashboard.index')
                    ->with('success', '¡Suscripción activada correctamente!');
            }
        }

        return redirect()->route('planes.index')
            ->with('error', 'No pudimos confirmar el pago. Si ya pagaste, aguardá unos minutos e intentá nuevamente.');
    }
}
