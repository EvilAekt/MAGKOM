<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Permintaan Reset Password - MAGKOM</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F1F5F9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #1E293B;">
  <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #F1F5F9; padding: 40px 16px;">
    <tr>
      <td align="center">
        <!-- Main Email Container / Card -->
        <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="max-width: 580px; background-color: #FFFFFF; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -2px rgba(0, 0, 0, 0.05); border: 1px solid #E2E8F0;">
          
          <!-- Header Banner -->
          <tr>
            <td style="background-color: #003566; padding: 32px 36px 28px; text-align: center; border-bottom: 4px solid #FFC300;">
              <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center">
                    <p style="margin: 0 0 6px; font-size: 13px; font-weight: 800; letter-spacing: 0.15em; text-transform: uppercase; color: #FFC300;">
                      DINAS KOMUNIKASI DAN INFORMATIKA
                    </p>
                    <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #FFFFFF; letter-spacing: -0.01em;">
                      MAGKOM
                    </h1>
                    <p style="margin: 4px 0 0; font-size: 13px; color: #94A3B8;">
                      Sistem Manajemen Pendaftaran &amp; Monitoring Magang
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Email Body -->
          <tr>
            <td style="padding: 36px 36px 28px;">
              <!-- Title -->
              <h2 style="margin: 0 0 16px; font-size: 20px; font-weight: 700; color: #003566;">
                Permintaan Reset Password
              </h2>

              <!-- Greeting -->
              <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6; color: #334155;">
                Halo, <strong>{{ $user->nama ?? 'Mahasiswa Magang' }}</strong>!
              </p>

              <!-- Description -->
              <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6; color: #334155;">
                Kami menerima permintaan untuk mengatur ulang password akun Sistem Manajemen Magang Diskominfo (MAGKOM) Anda.
              </p>
              <p style="margin: 0 0 28px; font-size: 15px; line-height: 1.6; color: #334155;">
                Silakan klik tombol di bawah ini untuk membuat password baru:
              </p>

              <!-- Main Call-to-Action Button -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto 28px;">
                <tr>
                  <td align="center" style="border-radius: 8px; background-color: #003566; border-bottom: 3px solid #FFC300;">
                    <a href="{{ $url }}" target="_blank" style="display: inline-block; padding: 14px 36px; font-size: 15px; font-weight: 700; color: #FFFFFF; text-decoration: none; border-radius: 8px; letter-spacing: 0.05em; text-transform: uppercase;">
                      RESET PASSWORD
                    </a>
                  </td>
                </tr>
              </table>

              <!-- Expiration Notice Box -->
              <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #F8FAFC; border-radius: 10px; border-left: 4px solid #003566; padding: 14px 18px; margin-bottom: 24px;">
                <tr>
                  <td>
                    <p style="margin: 0; font-size: 13.5px; line-height: 1.5; color: #475569;">
                      ⏱️ <strong>Keterangan:</strong> Tautan reset password ini berlaku selama <strong>{{ $expire }} menit</strong>. Demi keamanan akun, tautan hanya dapat digunakan satu kali.
                    </p>
                  </td>
                </tr>
              </table>

              <!-- Warning -->
              <p style="margin: 0 0 24px; font-size: 13.5px; line-height: 1.5; color: #64748B;">
                ⚠️ <strong>Peringatan Keamanan:</strong> Jika Anda tidak merasa meminta pengaturan ulang password, abaikan email ini. Password akun Anda tidak akan berubah dan akun Anda tetap aman.
              </p>

              <!-- Divider -->
              <hr style="border: none; border-top: 1px solid #E2E8F0; margin: 24px 0;">

              <!-- Fallback Plain URL -->
              <p style="margin: 0 0 8px; font-size: 12px; color: #94A3B8; line-height: 1.5;">
                Jika tombol di atas tidak berfungsi, salin dan tempelkan alamat tautan berikut langsung ke bilah alamat browser Anda:
              </p>
              <p style="margin: 0; font-size: 12px; line-height: 1.5; word-break: break-all;">
                <a href="{{ $url }}" style="color: #003566; text-decoration: underline;">{{ $url }}</a>
              </p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background-color: #F8FAFC; padding: 24px 36px; text-align: center; border-top: 1px solid #E2E8F0;">
              <p style="margin: 0 0 4px; font-size: 13px; font-weight: 700; color: #475569;">
                Sistem Manajemen Magang Diskominfo (MAGKOM)
              </p>
              <p style="margin: 0 0 8px; font-size: 12px; color: #94A3B8;">
                Dinas Komunikasi dan Informatika Kabupaten Sleman
              </p>
              <p style="margin: 0; font-size: 11.5px; color: #94A3B8; font-style: italic;">
                Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>

