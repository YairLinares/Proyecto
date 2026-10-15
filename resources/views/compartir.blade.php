<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Compartir | Delicias Dulces</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 32px 16px; background: #fff4f7; color: #23141b; font-family: Arial, sans-serif; text-align: center; }
        main { max-width: 850px; margin: auto; padding: 40px 24px; background: white; border-radius: 20px; }
        h1 { margin: 0 0 16px; font-size: clamp(36px, 7vw, 68px); }
        .subtitle { margin: 0 0 24px; font-size: clamp(20px, 4vw, 30px); }
        #qr { width: fit-content; max-width: 100%; padding: 32px; margin: auto; background: white; }
        #qr img, #qr canvas { max-width: 100%; height: auto; }
        .url { display: block; margin: 24px 0; color: inherit; font-size: clamp(16px, 3vw, 24px); overflow-wrap: anywhere; }
        .actions { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
        button, .button { border: 0; border-radius: 8px; background: #87284c; color: white; padding: 14px 20px; font: inherit; cursor: pointer; text-decoration: none; }
        .note { color: #65565c; line-height: 1.5; }
        @media print {
            body { background: white; padding: 0; color: black; }
            main { padding: 20px 0; }
            .actions, .note { display: none; }
            a { text-decoration: none; }
        }
    </style>
</head>
<body>
    <main>
        <h1>Delicias Dulces</h1>
        <p class="subtitle">Escanear para abrir el sistema</p>
        <div id="qr" role="img" aria-label="Código QR para abrir Delicias Dulces"></div>
        <noscript>Activa JavaScript para ver el código QR.</noscript>
        <a class="url" href="{{ config('app.url') }}">{{ config('app.url') }}</a>
        <div class="actions">
            <button type="button" onclick="window.print()">Imprimir / Guardar PDF</button>
            <a class="button" href="{{ config('app.url') }}">Abrir sistema</a>
        </div>
        <p class="note">Comparte este enlace o imprime el cartel. El acceso requiere que el servidor y el túnel estén encendidos.</p>
    </main>
    <script src="{{ asset('js/vendor/qrcode.min.js') }}"></script>
    <script>
        new QRCode(document.getElementById('qr'), {
            text: {{ Illuminate\Support\Js::from(config('app.url')) }},
            width: 512,
            height: 512,
            correctLevel: QRCode.CorrectLevel.M
        });
    </script>
</body>
</html>
