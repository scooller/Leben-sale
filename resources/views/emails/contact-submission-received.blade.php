<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo contacto</title>
</head>
<body>
    <h2>Nuevo contacto recibido</h2>

    <p><strong>Sitio:</strong> {{ $siteName }}</p>
    <p><strong>ID:</strong> {{ $submission->id }}</p>
    <p><strong>Fecha:</strong> {{ optional($submission->submitted_at)->format('d/m/Y H:i') }}</p>

    @if($submission->name)
        <p><strong>Nombre:</strong> {{ $submission->name }}</p>
    @endif

    @if($submission->email)
        <p><strong>Email:</strong> {{ $submission->email }}</p>
    @endif

    @if($submission->phone)
        <p><strong>Teléfono:</strong> {{ $submission->phone }}</p>
    @endif

    @if($submission->rut)
        <p><strong>RUT:</strong> {{ $submission->rut }}</p>
    @endif

    <hr>

    <h3>Campos enviados</h3>
    @foreach(($submission->fields ?? []) as $key => $value)
        <p><strong>{{ $key }}:</strong> {{ is_scalar($value) ? $value : json_encode($value) }}</p>
    @endforeach
</body>
</html>
