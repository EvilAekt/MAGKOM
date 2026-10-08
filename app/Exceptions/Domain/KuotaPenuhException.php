<?php

declare(strict_types=1);

namespace App\Exceptions\Domain;

use Exception;

class KuotaPenuhException extends Exception
{
    protected $message = 'Alokasi kuota penerimaan magang telah penuh.';
}
