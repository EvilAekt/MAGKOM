<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Master Definisi Modul Kustom
        Schema::create('modul_dinamis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_modul', 100);
            $table->string('slug', 100)->unique();
            $table->text('deskripsi')->nullable();
            $table->string('ikon', 50)->default('box'); // Nama ikon Lucide / SVG
            $table->json('target_roles'); // Array JSON: ["superadmin", "admin", "pembimbing"]
            $table->json('izin_aksi')->nullable(); // Granular: {"create":["admin"],"delete":["superadmin"]}
            $table->boolean('status_aktif')->default(true)->index();
            $table->unsignedInteger('urutan')->default(0);
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Skema Field / Kolom Modul Dinamis
        Schema::create('modul_dinamis_field', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('modul_dinamis')->cascadeOnDelete();
            $table->string('nama_kolom', 60); // snake_case unik per modul
            $table->string('label', 100);
            $table->string('tipe_data', 30); // text, number, textarea, select, multiselect, date, datetime, file, boolean, relation
            $table->json('opsi_pilihan')->nullable(); // Opsi array jika tipe select/multiselect
            $table->string('relasi_tabel', 60)->nullable(); // Tabel tujuan jika tipe relation
            $table->string('relasi_kolom_tampil', 60)->nullable(); // Kolom label tujuan
            $table->boolean('wajib')->default(false);
            $table->boolean('tampil_di_tabel')->default(true);
            $table->boolean('dapat_dicari')->default(true);
            $table->boolean('dapat_difilter')->default(false);
            $table->string('placeholder', 150)->nullable();
            $table->string('bantuan_teks', 255)->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->unique(['modul_id', 'nama_kolom']);
            $table->index(['modul_id', 'urutan']);
        });

        // 3. Penyimpanan Data Baris Modul (EAV/JSON Hybrid dengan Soft Deletes)
        Schema::create('modul_dinamis_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('modul_dinamis')->cascadeOnDelete();
            $table->json('data'); // Key-Value pair tersanitasi
            $table->json('files_metadata')->nullable(); // Info berkas yang diunggah
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['modul_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modul_dinamis_data');
        Schema::dropIfExists('modul_dinamis_field');
        Schema::dropIfExists('modul_dinamis');
    }
};
