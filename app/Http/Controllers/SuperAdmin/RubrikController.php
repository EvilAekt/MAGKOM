<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;

class RubrikController extends Controller
{
    /**
     * Modul pengelolaan rubrik nilai oleh Super Admin telah dinonaktifkan (Task C).
     * Histori penilaian dan kriteria sistem tetap dipertahankan pada database untuk penilaian peserta.
     */
    public function index(): never
    {
        abort(404, 'Modul pengelolaan rubrik nilai telah dinonaktifkan.');
    }
}
