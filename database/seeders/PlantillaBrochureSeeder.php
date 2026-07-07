<?php

namespace Database\Seeders;

use App\Models\PlantillaCorreo;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlantillaBrochureSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('email', 'admin@berzoti.com')->value('id');

        PlantillaCorreo::updateOrCreate(
            ['nombre' => 'Propuesta Panetones Corporativos - Brochure'],
            [
                'asunto'      => 'Propuesta especial: Panetones personalizados para {{razon_social}}',
                'cuerpo_html' => $this->html(),
                'activo'      => true,
                'created_by'  => $adminId,
            ]
        );
    }

    private function html(): string
    {
        return '<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#f5f5f0;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f5f0;padding:30px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

          <tr>
            <td style="background-color:#7a1c1c;padding:28px 40px;text-align:center;">
              <p style="margin:0;font-size:13px;color:#f0c060;letter-spacing:2px;text-transform:uppercase;">Propuesta Comercial</p>
              <h1 style="margin:6px 0 0;font-size:26px;color:#ffffff;font-weight:700;">Panetones Corporativos</h1>
              <p style="margin:4px 0 0;font-size:13px;color:#f0c060;">Personalizados con tu marca</p>
            </td>
          </tr>

          <tr>
            <td style="padding:36px 40px 0;">
              <p style="margin:0;font-size:15px;color:#333333;">Estimado/a <strong>{{contacto_principal}}</strong>,</p>
            </td>
          </tr>

          <tr>
            <td style="padding:20px 40px 28px;">
              <p style="margin:0 0 16px;font-size:15px;color:#444444;line-height:1.7;">
                Sabemos que tus campañas de marketing buscan siempre <strong>sorprender y dejar huella</strong>. Por eso queremos presentarte una propuesta deliciosa y efectiva:
              </p>
              <div style="background-color:#fdf6ec;border-left:4px solid #c8922a;padding:16px 20px;margin:0 0 20px;border-radius:0 6px 6px 0;">
                <p style="margin:0;font-size:16px;color:#7a1c1c;font-weight:700;">Panetones personalizados con la marca de <em>{{razon_social}}</em></p>
              </div>
              <p style="margin:0 0 16px;font-size:15px;color:#444444;line-height:1.7;">
                Nos encantaría conversar contigo sobre cómo adaptar esta idea a tu próxima campaña y ofrecerte opciones que se ajusten a tus necesidades y presupuesto.
              </p>
              <p style="margin:0;font-size:15px;color:#444444;line-height:1.7;">
                Quedamos atentos a coordinar una reunión o enviarte más detalles.
              </p>
            </td>
          </tr>

          <tr>
            <td style="padding:0 40px 36px;text-align:center;">
              <p style="margin:0 0 12px;font-size:12px;color:#999999;text-transform:uppercase;letter-spacing:1px;">Nuestro catálogo de productos</p>
              <img src="https://www.proyectosup.com/images/brochure-berzotti.jpg"
                   alt="Catálogo Berzotti - Panetones Corporativos Personalizados"
                   width="520"
                   style="max-width:100%;border-radius:6px;box-shadow:0 2px 10px rgba(0,0,0,0.12);">
            </td>
          </tr>

          <tr>
            <td style="padding:0 40px 36px;text-align:center;">
              <a href="https://wa.me/51985599394"
                 style="display:inline-block;background-color:#7a1c1c;color:#ffffff;font-size:15px;font-weight:700;padding:14px 36px;border-radius:6px;text-decoration:none;">
                Coordinar reunión →
              </a>
            </td>
          </tr>

          <tr>
            <td style="background-color:#f9f9f7;border-top:1px solid #eeeeee;padding:24px 40px;text-align:center;">
              <p style="margin:0 0 4px;font-size:13px;color:#888888;">
                <strong style="color:#7a1c1c;">Berzotti</strong> &nbsp;|&nbsp; 985 599 394 / 908 801 180
              </p>
              <p style="margin:0;font-size:12px;color:#aaaaaa;">
                www.berzotti.com.pe &nbsp;|&nbsp; @berzottioficial &nbsp;|&nbsp; @panetonesberzotti
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>';
    }
}
