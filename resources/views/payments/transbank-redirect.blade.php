<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redirigiendo a Transbank</title>
</head>
<body>
    <p>Redirigiendo a Transbank...</p>

    <!-- Debug: Mostrar valores en console -->
    <script>
        console.log('Transbank Redirect Debug:', {
            token: '{{ $token }}',
            token_is_empty: {{ $token === '' ? 'true' : 'false' }},
            redirect_url: '{{ $redirectUrl }}',
            redirect_url_is_empty: {{ $redirectUrl === '' ? 'true' : 'false' }},
        });
    </script>

    <form id="tbk-redirect-form" method="post" action="{{ $redirectUrl }}">
        <input type="hidden" name="token_ws" value="{{ $token }}">
    </form>

    <script>
        const form = document.getElementById('tbk-redirect-form');
        const token = form.querySelector('[name="token_ws"]').value;

        console.log('Form Details:', {
            action: form.action,
            method: form.method,
            token_input_value: token,
            token_is_empty: token === '',
        });

        if (token === '') {
            console.error('ERROR: Token is empty! Form will not submit correctly.');
        }

        form.submit();
    </script>
</body>
</html>
