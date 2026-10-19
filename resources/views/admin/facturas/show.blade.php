<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Factura {{ $factura }} · Espuma</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif; margin: 0; background: #f4f4f5; color: #1B2540; }
        .page { max-width: 720px; margin: 24px auto; background: #fff; padding: 40px; border: 1px solid #e5e7eb; }
        .head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #1B2540; padding-bottom: 16px; }
        .brand { font-size: 28px; font-weight: 900; letter-spacing: -0.5px; }
        .brand span { color: #F5D25F; }
        .muted { color: #6B7280; font-size: 13px; }
        .factura { text-align: right; }
        .factura .num { font-size: 22px; font-weight: 900; }
        .badge { display: inline-block; background: #1B2540; color: #F5D25F; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 24px 0; }
        .box { border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; }
        .box h4 { margin: 0 0 6px; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: #6B7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { text-align: left; background: #1B2540; color: #F5D25F; font-size: 11px; text-transform: uppercase; padding: 10px; }
        td { padding: 10px; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .num, th.num, td.num { text-align: right; }
        .totals { margin-top: 16px; margin-left: auto; width: 280px; }
        .totals .row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
        .totals .row.total { border-top: 2px solid #1B2540; margin-top: 6px; padding-top: 10px; font-size: 20px; font-weight: 900; }
        .foot { margin-top: 32px; text-align: center; color: #6B7280; font-size: 12px; }
        .actions { max-width: 720px; margin: 16px auto; display: flex; gap: 8px; }
        .btn { border: 2px solid #1B2540; background: #1B2540; color: #F5D25F; padding: 10px 18px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px; }
        .btn.secondary { background: #fff; color: #1B2540; }
        @media print {
            body { background: #fff; }
            .page { border: none; margin: 0; padding: 0; max-width: 100%; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button class="btn" onclick="window.print()">🖨️ Imprimir factura</button>
        <button class="btn secondary" onclick="window.close()">Cerrar</button>
    </div>

    <div class="page">
        <div class="head">
            <div>
                <div class="brand">Espu<span>ma</span></div>
                <p class="muted">Lavandería a domicilio</p>
                <p class="muted">WhatsApp: {{ config('services.whatsapp.number') }}</p>
            </div>
            <div class="factura">
                <span class="badge">FACTURA</span>
                <div class="num">{{ $factura }}</div>
                <p class="muted">Fecha: {{ $pedido->created_at->translatedFormat('j M Y') }}</p>
            </div>
        </div>

        <div class="grid">
            <div class="box">
                <h4>Cliente</h4>
                <p><strong>{{ $pedido->user?->name ?? '—' }}</strong></p>
                <p class="muted">{{ $pedido->user?->email ?? '—' }}</p>
                <p class="muted">WhatsApp: {{ $pedido->user?->whatsapp ?? '—' }}</p>
            </div>
            <div class="box">
                <h4>Pedido</h4>
                <p><strong>#{{ $pedido->codigo }}</strong></p>
                <p class="muted">Estado: {{ $pedido->estado_texto }}</p>
                <p class="muted">Entrega: {{ $pedido->entrega_estimada?->translatedFormat('j M Y') ?? '—' }}</p>
                <p class="muted">Dirección: {{ $pedido->direccion?->linea ?? '—' }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th class="num">Cant.</th>
                    <th class="num">P. unitario</th>
                    <th class="num">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pedido->lineas as $linea)
                    <tr>
                        <td>{{ $linea->nombre }}</td>
                        <td class="num">{{ $linea->cantidad }}</td>
                        <td class="num">${{ number_format($linea->precio_unitario, 2) }}</td>
                        <td class="num">${{ number_format($linea->subtotal, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="muted">Sin servicios registrados.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="totals">
            @if ($pedido->express)
                <div class="row"><span>Servicio exprés (+50%)</span><span>Incluido</span></div>
            @endif
            <div class="row total"><span>Total</span><span>${{ number_format($pedido->total, 2) }}</span></div>
        </div>

        <div class="grid">
            <div class="box">
                <h4>Método de pago</h4>
                @if ($pedido->pago)
                    <p><strong>{{ $pedido->pago->metodo_texto }}</strong></p>
                    <p class="muted">Estado: {{ $pedido->pago->estado_texto }}</p>
                    <p class="muted">Monto: ${{ number_format($pedido->pago->monto, 2) }}</p>
                @else
                    <p class="muted">Sin pago registrado.</p>
                @endif
            </div>
            <div class="box">
                <h4>Detalles</h4>
                <p class="muted">Creado: {{ $pedido->created_at->translatedFormat('j M Y, H:i') }}</p>
                <p class="muted">Recogido: {{ $pedido->recogido_en?->translatedFormat('j M Y, H:i') ?? '—' }}</p>
            </div>
        </div>

        <div class="foot">
            Gracias por tu preferencia · Este documento es un comprobante de compra generado por Espuma.
        </div>
    </div>
</body>
</html>
