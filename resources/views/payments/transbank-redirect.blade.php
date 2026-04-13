<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redirigiendo a Transbank</title>
</head>
<body>
    <p>Redirigiendo a Transbank...</p>

    <form id="tbk-redirect-form" method="post" action="{{ $redirectUrl }}">
        <input type="hidden" name="token_ws" value="{{ $token }}">
    </form>

    <script>
        document.getElementById('tbk-redirect-form').submit();
    </script>
</body>
</html>
