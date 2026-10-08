<?php

declare(strict_types=1);

namespace App\Exceptions\Domain;

use Exception;

class PendaftaranAktifGandaException extends Exception
{
    protected $message = 'Peserta masih memiliki pengajuan pendaftaran magang yang aktif. Berdasarkan aturan BR-17, peserta hanya boleh memiliki 1 pendaftaran aktif pada satu waktu.';
}
