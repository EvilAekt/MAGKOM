<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ModulDinamis;
use App\Models\ModulDinamisData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicModulController extends Controller
{
    /**
     * Menampilkan katalog publik read-only (Ekuivalen Basefw PUBLIC_list).
     */
    public function index(Request $request, string $slug): View
    {
        $modul = ModulDinamis::with('fields')
            ->where('slug', $slug)
            ->where('status_aktif', true)
            ->where('is_public_read', true)
            ->firstOrFail();

        $search = trim((string) $request->input('q'));
        $searchableCols = $modul->fields->where('dapat_dicari', true)->pluck('nama_kolom');

        if ($modul->tipe_sumber === 'physical_table' && !empty($modul->nama_tabel_fisik)) {
            $table = $modul->nama_tabel_fisik;
            $pk = $modul->primary_key ?: 'id';
            $query = DB::table($table);

            if ($search !== '' && $searchableCols->isNotEmpty()) {
                $query->where(function ($sub) use ($searchableCols, $search) {
                    foreach ($searchableCols as $col) {
                        $sub->orWhere($col, 'LIKE', "%{$search}%");
                    }
                });
            }

            $records = $query->orderBy($pk, 'desc')->paginate(20)->withQueryString();
        } else {
            $query = ModulDinamisData::where('modul_id', $modul->id)->orderBy('id', 'desc');

            if ($search !== '' && $searchableCols->isNotEmpty()) {
                $query->where(function ($sub) use ($searchableCols, $search) {
                    foreach ($searchableCols as $col) {
                        $sub->orWhere("data->{$col}", 'LIKE', "%{$search}%");
                    }
                });
            }

            $records = $query->paginate(20)->withQueryString();
        }

        return view('public.modul.index', compact('modul', 'records'));
    }

    /**
     * Menampilkan detail satu baris publik read-only (Ekuivalen Basefw PUBLIC_detail).
     */
    public function show(string $slug, string|int $id): View
    {
        $modul = ModulDinamis::with('fields')
            ->where('slug', $slug)
            ->where('status_aktif', true)
            ->where('is_public_read', true)
            ->firstOrFail();

        if ($modul->tipe_sumber === 'physical_table' && !empty($modul->nama_tabel_fisik)) {
            $table = $modul->nama_tabel_fisik;
            $pk = $modul->primary_key ?: 'id';
            $record = DB::table($table)->where($pk, $id)->first();
            if (!$record) {
                abort(404, 'Data tidak ditemukan.');
            }
            $data = (array) $record;
        } else {
            $row = ModulDinamisData::where('modul_id', $modul->id)->findOrFail($id);
            $data = $row->data;
            $data['id'] = $row->id;
            $data['created_at'] = $row->created_at?->format('d/m/Y H:i') ?? '-';
        }

        return view('public.modul.show', compact('modul', 'data', 'id'));
    }
}
