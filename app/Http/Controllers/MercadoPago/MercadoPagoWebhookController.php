<?php

namespace App\Http\Controllers\MercadoPago;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessMercadoPagoNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoPagoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $topic = $request->input('type', $request->query('type', $request->input('topic')));
        $resourceId = $request->input('data.id', $request->query('data.id', $request->input('id')));

        if (! $topic || ! $resourceId) {
            return response()->json(['error' => 'Payload inválido.'], 400);
        }

        if (! $this->hasValidSignature($request)) {
            Log::warning('Webhook de Mercado Pago con firma inválida.', [
                'topic' => $topic,
                'resource' => $resourceId,
            ]);
        }

        ProcessMercadoPagoNotification::dispatch((string) $topic, (string) $resourceId);

        return response()->json(['received' => true]);
    }

    private function hasValidSignature(Request $request): bool
    {
        $signature = $request->header('x-signature');
        $secret = config('mercadopago.webhook_secret');

        if (! $signature || ! $secret) {
            return true;
        }

        $parts = collect(explode(',', $signature))
            ->mapWithKeys(fn (string $part): array => [
                str($part)->before('=')->toString() => str($part)->after('=')->toString(),
            ])
            ->all();

        $ts = $parts['ts'] ?? null;
        $v1 = $parts['v1'] ?? null;
        $dataId = $request->input('data.id', $request->query('data.id'));

        if (! $ts || ! $v1 || ! $dataId) {
            return false;
        }

        $manifest = 'id:'.$dataId.';request-id:'.$request->header('x-request-id').';ts:'.$ts.';';

        return hash_equals(hash_hmac('sha256', $manifest, $secret), $v1);
    }
}
