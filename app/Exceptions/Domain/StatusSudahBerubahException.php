<?php

declare(strict_types=1);

namespace App\Exceptions\Domain;

use Exception;

class StatusSudahBerubahException extends Exception
{
    protected $message = 'Status pendaftaran telah berubah atau sedang diproses oleh verifikator lain.';
}
