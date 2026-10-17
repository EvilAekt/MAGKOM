# Aturan Keselamatan & Batasan Tooling (tooling-safety.md)

Aturan ini mengikat agen AI saat menggunakan CLI pengujian, alat pentest (Strix), dan MCP servers integrasi eksternal (Cloudflare, Sentry, Datadog, Composio, TestSprite).

---

## 1. Batasan Target Penetration Testing (Strix & Security Tools)

1. **Target Legalitas Terbatas**:
   - Pengujian keamanan otomatis menggunakan Strix atau tool sejenis **HANYA BOLEH MENYASAR APLIKASI MILIK PENGGUNA SENDIRI** pada lingkungan `localhost` atau URL staging privat yang diverifikasi (`127.0.0.1:8000`, `localhost:3000`, dll.).
2. **Larangan Domain Eksternal & Production**:
   - **DILARANG KERAS** menjalankan security scanning, fuzzing, atau eksploitasi terhadap domain pihak ketiga, API eksternal, atau domain production tanpa persetujuan tertulis eksplisit dari pengguna.
3. **Mode Pengujian**:
   - Gunakan selalu mode cepat (*quick mode*) untuk evaluasi rutin harian. Jangan memicu *deep autonomous brute-force* tanpa izin.

---

## 2. Pencegahan Biaya Tak Terduga (*Cost & Token Guardrails*)

1. **Panggilan Model Berbayar**:
   - Jangan menjalankan eksekusi pengujian otomatis yang mengonsumsi kuota token berbayar dalam jumlah besar tanpa memberitahukan pengguna terlebih dahulu.
2. **Graphify Semantic Extraction**:
   - Ekstraksi graph codebase harian wajib memprioritaskan mode `--code-only` (AST parser lokal, zero token cost). Beri tahu pengguna jika ada proses analisis dokumen/gambar yang memerlukan API key eksternal.

---

## 3. Batasan Operasi MCP Cloud & Monitoring (Read-Only Policy)

1. **Cloudflare MCP**:
   - Gunakan hanya untuk referensi dokumentasi dan observabilitas baca (*read-only*).
   - **DILARANG** melakukan modifikasi DNS records, routing rules, WAF policies, atau penghapusan zone.
2. **Sentry & Datadog MCP**:
   - Query bersifat telemetri murni (*read-only inspection*): memeriksa riwayat error terbaru, latency, dan trace stack.
   - **DILARANG** me-resolve, me-mute, atau menghapus issue/alert secara otomatis tanpa instruksi langsung pengguna.
3. **Composio & Gateway MCP**:
   - Beroperasi dengan prinsip hak akses minimum (*least-privilege*). Jangan mengeksekusi integrasi mutasi (kirim email massal, penghapusan repositori, penulisan data eksternal) tanpa konfirmasi.

---

## 4. Kerahasiaan Kredensial

- JANGAN PERNAH menampilkan atau meminta pengguna menempelkan API key, personal access token, atau password ke dalam jendela chat.
- Seluruh token harus dikonfigurasi melalui variabel lingkungan privat lokal atau berkas `mcp_config.json` di luar tracking git.
