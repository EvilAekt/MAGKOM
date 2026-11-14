## 📋 Ringkasan Perubahan (Summary)
<!-- Jelaskan secara ringkas maksud dan tujuan dari Pull Request ini -->

## 🔗 Terkait Issue (Related Issue)
<!-- Tautkan issue yang diselesaikan, contoh: Closes #12, Fixes #45, Ref #89 -->
Closes #

## 🏷️ Tipe Perubahan (Type of Change)
<!-- Centang tipe yang sesuai -->
- [ ] 🐛 Bug fix (perbaikan masalah non-breaking)
- [ ] ✨ Feature (penambahan fitur baru)
- [ ] 🔒 Security hardening (perbaikan keamanan atau mitigasi celah)
- [ ] 🔨 Maintenance / Refactoring (pembersihan kode, pembaruan dependency)
- [ ] 📚 Documentation (penambahan atau revisi dokumen panduan/spesifikasi)

## 🛠️ Rincian Perubahan (Changes Made)
<!-- Rincikan file utama yang diubah beserta dampaknya -->
- 

## 🧪 Pengujian yang Dijalankan (Testing Performed)
<!-- Sebutkan pengujian lokal yang telah diverifikasi sebelum membuat PR -->
- [ ] `composer validate --strict` (Lolos tanpa warning/error)
- [ ] `composer audit` (Dependency audit diperiksa)
- [ ] `composer check-platform-reqs` (Kompatibilitas PHP 8.3 terverifikasi)
- [ ] `./vendor/bin/pint --test` (Formatting standar PSR-12/Pint lolos)
- [ ] `./vendor/bin/phpstan analyse --memory-limit=1G` (Larastan Level 2 lolos)
- [ ] `php artisan test` (Seluruh test suite in-tree lolos)
- [ ] `npm ci && npm run build` (Aset frontend terkompilasi bersih tanpa error)

## 🛡️ Dampak Keamanan (Security Impact)
<!-- Analisis dampak perubahan terhadap keamanan sistem -->
- **RBAC & Authorization**: Apakah ada rute atau controller baru yang memerlukan pengecekan policy/middleware?
- **Validasi Input & Sanitasi**: Apakah seluruh input telah divalidasi melalui Form Request terpisah?
- **Data Protection**: Apakah ada data sensitif yang diekspos di log, session, atau UI?

## 🗄️ Dampak Basis Data (Database Impact)
- [ ] Tidak ada migrasi basis data
- [ ] Terdapat penambahan tabel / kolom baru (non-destructive)
- [ ] Memerlukan seeder baru

## ✅ Checklist Sebelum Merge
- [ ] Kode mematuhi standar arsitektur: Route → Middleware/Policy → Form Request → Controller → Service → Model
- [ ] Tidak ada CSS atau JavaScript baru yang disisipkan di dalam file Blade (seluruh styling di `resources/css/` dan JS di `resources/js/`)
- [ ] Tidak ada API key, token, kredensial, atau rahasia yang ter-commit
- [ ] Tidak ada modifikasi destructive pada migration database
- [ ] Seluruh CI checks pada GitHub Actions berstatus hijau (Passed)

