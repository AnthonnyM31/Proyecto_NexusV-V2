<!DOCTYPE html>
<html lang="es">
<head>
    <title>Certificado de {{ $course->title }}</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        .certificate-box { border: 10px solid #4a5568; padding: 50px; width: 80%; margin: 50px auto; }
        h1 { font-size: 2.5em; color: #4a5568; }
        p { font-size: 1.2em; margin-bottom: 30px; }
        .course-title { font-size: 1.8em; color: #38a169; font-weight: bold; }
    </style>
</head>
<body>
    <div class="certificate-box">
        <h1>CERTIFICADO DE FINALIZACIÓN</h1>
        <p>Se otorga a:</p>
        <h2>{{ $user->name }}</h2>
        <p>Por haber completado satisfactoriamente el curso:</p>
        <h3 class="course-title">{{ $course->title }}</h3>
        <p>En la plataforma NexusV-V2.</p>
        <p style="margin-top: 50px;">Fecha: {{ now()->format('d M Y') }}</p>
    </div>
</body>
</html>