@if(str_contains(strtolower(trim($cuerpoHtml)), '<html'))
{!! $cuerpoHtml !!}
@else
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #4F46E5; padding: 24px 32px; }
        .header span { color: #ffffff; font-size: 20px; font-weight: 600; }
        .body { padding: 32px; color: #374151; line-height: 1.7; font-size: 15px; }
        .footer { background: #F9FAFB; padding: 20px 32px; text-align: center; font-size: 12px; color: #9CA3AF; border-top: 1px solid #E5E7EB; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <span>{{ config('app.name') }}</span>
        </div>
        <div class="body">
            {!! $cuerpoHtml !!}
        </div>
        <div class="footer">
            Este correo fue enviado por {{ config('app.name') }}. Si crees que lo recibiste por error, por favor ignóralo.
        </div>
    </div>
</body>
</html>
@endif
