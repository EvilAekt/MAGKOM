/**
 * MAGKOM Landing Interactive Engine
 * Parity with NAUFALFEBRIAN62/magkomlandingpage
 */

(function () {
    'use strict';

    // =========================================================================
    // 0. SCROLL POSITION RESET ON PAGE LOAD / REFRESH
    // =========================================================================
    // Force browser to disable automatic scroll restoration so refresh always starts at top
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    // Instantly reset scroll position to top
    try {
        window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
    } catch (e) {
        window.scrollTo(0, 0);
    }

    // If loaded with a hash in the URL on refresh, clean hash so browser doesn't jump
    if (window.location.hash) {
        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, '', window.location.pathname + window.location.search);
        }
    }

    // Ensure scroll position is at (0, 0) right before unload
    window.addEventListener('beforeunload', function () {
        try {
            window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
        } catch (e) {
            window.scrollTo(0, 0);
        }
    });

    // =========================================================================
    // 1. DATA STATE & FALLBACK
    // =========================================================================
    const defaultData = {
        bulanList: ['Nov', 'Des', 'Jan'],
        bulanPanjang: {
            Nov: 'November 2026',
            Des: 'Desember 2026',
            Jan: 'Januari 2027',
        },
        bidangList: [
            {
                id: 'aptika',
                nama: 'Aplikasi Informatika',
                ikon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>',
                jurusan: ['RPL', 'Informatika', 'Sistem Informasi'],
                kegiatan: 'Mengembangkan dan merawat aplikasi layanan publik Pemkab Sleman.',
            },
            {
                id: 'statistik',
                nama: 'Statistik',
                ikon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 7V4H6l6 8-6 8h12v-3"></path></svg>',
                jurusan: ['Statistika', 'Matematika', 'Informatika'],
                kegiatan: 'Mengolah data sektoral dan menyusun visualisasi Satu Data Sleman.',
            },
            {
                id: 'sandi',
                nama: 'Persandian',
                ikon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>',
                jurusan: ['TKJ', 'Teknik Komputer', 'Keamanan Siber'],
                kegiatan: 'Membantu pengamanan jaringan dan sertifikat elektronik.',
            },
            {
                id: 'sekretariat',
                nama: 'Sekretariat',
                ikon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
                jurusan: ['OTKP', 'Administrasi', 'Manajemen'],
                kegiatan: 'Mendukung administrasi persuratan dan arsip dinas.',
            },
        ],
        kuota: {
            smk: {
                Nov: { aptika: { sisa: 4, total: 5 }, statistik: { sisa: 1, total: 4 }, sandi: { sisa: 3, total: 3 }, sekretariat: { sisa: 0, total: 3 } },
                Des: { aptika: { sisa: 5, total: 5 }, statistik: { sisa: 2, total: 4 }, sandi: { sisa: 1, total: 3 }, sekretariat: { sisa: 2, total: 3 } },
                Jan: { aptika: { sisa: 2, total: 5 }, statistik: { sisa: 4, total: 4 }, sandi: { sisa: 3, total: 3 }, sekretariat: { sisa: 3, total: 3 } },
            },
            mahasiswa: {
                Nov: { aptika: { sisa: 3, total: 6 }, statistik: { sisa: 5, total: 6 }, sandi: { sisa: 0, total: 3 }, sekretariat: { sisa: 2, total: 3 } },
                Des: { aptika: { sisa: 1, total: 6 }, statistik: { sisa: 3, total: 6 }, sandi: { sisa: 2, total: 3 }, sekretariat: { sisa: 3, total: 3 } },
                Jan: { aptika: { sisa: 6, total: 6 }, statistik: { sisa: 6, total: 6 }, sandi: { sisa: 3, total: 3 }, sekretariat: { sisa: 1, total: 3 } },
            },
        },
        tahapan: [
            { judul: 'Registrasi', isi: 'Calon peserta membuat akun, mengisi data pribadi, dan menunggu akun diverifikasi admin.' },
            { judul: 'Administrasi & Seleksi', isi: 'Pilih bidang, unggah berkas, lalu admin memverifikasi dan menyeleksi dalam 1–3 hari kerja.' },
            { judul: 'Pelaksanaan Magang', isi: 'Kerjakan tugas, isi logbook harian, dan unggah progres sesuai durasi magang.' },
            { judul: 'Kelulusan', isi: 'Unduh Lembar Nilai Magang dan sertifikat dalam bentuk PDF dari portal.' },
        ],
        faqCategories: [
            {
                id: 'syarat',
                nama: 'Syarat dan Tata Cara Mendaftar',
                items: [
                    {
                        q: 'Bagaimana alur dan tata cara mendaftar magang di Diskominfo Sleman?',
                        a: 'Pendaftaran dilakukan secara mandiri atau kelompok melalui portal MAGKOM: (1) Buat akun portal, (2) Pilih formasi bidang dan bulan pelaksanaan yang masih tersedia kuotanya, (3) Unggah berkas persyaratan (Surat Pengantar Institusi, CV, Proposal, dan Transkrip/Rapor), (4) Tunggu verifikasi admin.'
                    },
                    {
                        q: 'Apakah siswa SMK dan mahasiswa perguruan tinggi memiliki syarat yang berbeda?',
                        a: 'Secara umum sama, namun untuk siswa SMK surat permohonan ditandatangani oleh Kepala Sekolah / Waka Hubin dan dilengkapi izin orang tua, sedangkan mahasiswa melampirkan surat pengantar resmi dari Fakultas/Jurusan.'
                    },
                    {
                        q: 'Apakah bisa mendaftar lebih dari satu bidang atau bulan magang?',
                        a: 'Tidak. Setiap peserta hanya dapat memilih satu bidang formasi dalam satu periode magang agar alokasi kuota adil bagi seluruh pendaftar.'
                    }
                ]
            },
            {
                id: 'akun',
                nama: 'Akun',
                items: [
                    {
                        q: 'Bagaimana jika tidak menerima email verifikasi akun?',
                        a: 'Silakan periksa folder Spam atau Promosi pada email Anda. Jika dalam 15 menit belum masuk, Anda dapat meminta kirim ulang link verifikasi melalui halaman login.'
                    },
                    {
                        q: 'Apakah akun portal bisa digunakan untuk mendaftar periode berikutnya?',
                        a: 'Akun Anda berlaku permanen di portal MAGKOM. Anda dapat menggunakannya kembali untuk melihat riwayat sertifikat atau mendaftar periode berikutnya setelah magang sebelumnya selesai.'
                    }
                ]
            },
            {
                id: 'kuis',
                nama: 'Kuis',
                items: [
                    {
                        q: 'Apakah ada kuis atau tes kompetensi saat proses seleksi?',
                        a: 'Beberapa bidang teknis seperti Aplikasi Informatika (Aptika) dan Persandian dapat menyertakan tes kompetensi dasar atau kuis teknis singkat untuk memastikan kecocokan dengan proyek yang akan dikerjakan.'
                    },
                    {
                        q: 'Berapa lama proses verifikasi berkas oleh admin?',
                        a: 'Proses verifikasi dan kurasi berkas administrasi membutuhkan waktu 1 hingga 3 hari kerja setelah pengajuan dikirimkan.'
                    }
                ]
            },
            {
                id: 'benefit',
                nama: 'Benefit Magang',
                items: [
                    {
                        q: 'Apa saja fasilitas dan benefit yang didapatkan peserta magang?',
                        a: 'Peserta mendapatkan bimbingan intensif dari Mentor ASN profesional, akses modul proyek nyata di pemerintahan, sertifikat resmi Diskominfo ber-QR code sah, serta penilaian kompetensi standar industri.'
                    },
                    {
                        q: 'Apakah program magang di Diskominfo Sleman berbayar atau digaji?',
                        a: 'Program magang Diskominfo Sleman 100% GRATIS (tidak dipungut biaya apa pun) dan bersifat non-komersial tanpa kompensasi uang saku (unpaid government internship).'
                    }
                ]
            },
            {
                id: 'kewajiban',
                nama: 'Kewajiban Peserta Magang',
                items: [
                    {
                        q: 'Apa saja kewajiban dari peserta magang?',
                        a: 'Peserta wajib: (1) Mematuhi jam kerja ASN Diskominfo (Senin–Kamis 07.30–15.30 WIB, Jumat 07.30–14.30 WIB), (2) Mengenakan seragam sekolah/almamater rapi dan bersepatu, (3) Mengerjakan penugasan proyek tepat waktu, (4) Mengisi logbook mingguan secara disiplin, dan (5) Menjaga etika serta kerahasiaan data kedinasan.'
                    },
                    {
                        q: 'Apakah magang bisa dilaksanakan secara daring / WFH?',
                        a: 'Tidak. Seluruh pelaksanaan magang di Diskominfo Sleman dilaksanakan secara luring (Work From Office) di lingkungan kantor Pemkab Sleman untuk memastikan pembimbingan maksimal.'
                    }
                ]
            }
        ]
    };

    // Gabungkan data dari window.MAGKOM_DATA jika disediakan oleh backend Laravel
    const AppData = Object.assign({}, defaultData, window.MAGKOM_DATA || {});

    // Helper status kuota
    function getStatusKuota(sisa, total) {
        if (sisa <= 0) return { key: 'penuh', label: 'Penuh', barClass: 'bar-penuh', badgeClass: 'status-penuh' };
        if (total > 0 && sisa / total <= 0.34) return { key: 'hampir', label: 'Tinggal sedikit', barClass: 'bar-hampir', badgeClass: 'status-hampir' };
        return { key: 'tersedia', label: 'Masih banyak', barClass: 'bar-tersedia', badgeClass: 'status-tersedia' };
    }

    // =========================================================================
    // 2. KUOTA EXPLORER CONTROLLER (Sinkron Kartu Database MagKom)
    // =========================================================================
    function initKuotaExplorer() {
        let currentJenjang = 'smk';
        let currentRegMode = 'mandiri'; // 'mandiri' | 'kelompok'
        let currentMemberCount = 1;

        const jenjangPills = document.querySelectorAll('[data-jenjang-pill]');
        const bulanPills = document.querySelectorAll('[data-bulan-pill]');
        const modePills = document.querySelectorAll('[data-mode-pill]');
        const teamSizeWrap = document.getElementById('kuotaTeamSizeWrapper');
        const memberPills = document.querySelectorAll('#kuotaMemberPills [data-count]');

        const gaugeCircle = document.getElementById('kuotaGaugeProgress');
        const gaugeNumber = document.getElementById('kuotaGaugeNumber');
        const gaugeDesc = document.getElementById('kuotaGaugeDesc');
        const carouselTrack = document.getElementById('kuotaCarouselTrack');
        const btnPrev = document.getElementById('btnCarouselPrev');
        const btnNext = document.getElementById('btnCarouselNext');

        if (!carouselTrack) return;

        // Ambil bulan pertama yang aktif dari pill
        let currentBulanId = '';
        let currentBulanName = '';
        const initialActiveBulan = document.querySelector('[data-bulan-pill].active') || document.querySelector('[data-bulan-pill]');
        if (initialActiveBulan) {
            currentBulanId = initialActiveBulan.getAttribute('data-bulan-pill');
            currentBulanName = initialActiveBulan.getAttribute('data-bulan-name') || initialActiveBulan.textContent.trim();
        }

        // Perbarui CTA link dan status kuota pada kartu secara dinamis
        function updateCardActionButtons() {
            const allCards = carouselTrack.querySelectorAll('.kuota-card');
            allCards.forEach(function (card) {
                const sisa = parseInt(card.getAttribute('data-sisa'), 10) || 0;
                const total = parseInt(card.getAttribute('data-total'), 10) || 0;
                const isAvailable = card.getAttribute('data-is-available') === '1';
                const bulanId = card.getAttribute('data-bulan-id');
                const bidangId = card.getAttribute('data-bidang-id') || '';
                const jurusanId = card.getAttribute('data-jurusan-id') || '';
                const jenjang = card.getAttribute('data-jenjang') || currentJenjang;
                const statusPill = card.querySelector('.kuota-status-pill');
                let btnLink = card.querySelector('.btn-card-cta');

                if (!btnLink) return;

                const baseUrl = btnLink.getAttribute('data-base-url') || '/register';
                function buildRegisterUrl(tipe, jumlah) {
                    let url = baseUrl + '?bulan=' + bulanId;
                    if (bidangId) url += '&bidang=' + bidangId;
                    if (jenjang) url += '&jenjang=' + (jenjang === 'smk' ? 'smk' : 's1');
                    if (jurusanId) url += '&jurusan=' + jurusanId;
                    url += '&tipe=' + tipe + '&jumlah=' + jumlah;
                    return url;
                }

                function makeLinkActive(href, text) {
                    if (btnLink.tagName.toLowerCase() !== 'a') {
                        const a = document.createElement('a');
                        a.setAttribute('data-base-url', baseUrl);
                        btnLink.parentNode.replaceChild(a, btnLink);
                        btnLink = a;
                    }
                    btnLink.className = 'btn-card-action btn-primary-action btn-card-cta';
                    btnLink.removeAttribute('disabled');
                    btnLink.setAttribute('href', href);
                    btnLink.textContent = text;
                }

                function makeButtonDisabled(text) {
                    if (btnLink.tagName.toLowerCase() !== 'button') {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.setAttribute('data-base-url', baseUrl);
                        btnLink.parentNode.replaceChild(btn, btnLink);
                        btnLink = btn;
                    }
                    btnLink.className = 'btn-card-action btn-disabled btn-card-cta';
                    btnLink.removeAttribute('href');
                    btnLink.setAttribute('disabled', 'true');
                    btnLink.textContent = text;
                }

                if (currentRegMode === 'mandiri') {
                    // Mode Mandiri (1 Orang)
                    if (isAvailable && sisa > 0) {
                        makeLinkActive(buildRegisterUrl('mandiri', 1), 'Daftar Sekarang ➔');
                        if (statusPill) {
                            statusPill.className = (total > 0 && sisa / total <= 0.34) ? 'kuota-status-pill status-hampir' : 'kuota-status-pill status-tersedia';
                            statusPill.textContent = (total > 0 && sisa / total <= 0.34) ? 'Tinggal sedikit' : 'Tersedia';
                        }
                    } else if (isAvailable && sisa === 0) {
                        makeButtonDisabled('Kuota Penuh');
                        if (statusPill) {
                            statusPill.className = 'kuota-status-pill status-penuh';
                            statusPill.textContent = 'Penuh';
                        }
                    } else {
                        makeButtonDisabled('Pendaftaran Ditutup');
                        if (statusPill) {
                            statusPill.className = 'kuota-status-pill status-penuh';
                            statusPill.textContent = 'Tutup';
                        }
                    }
                } else {
                    // Mode Kelompok (2 - 4 Orang)
                    if (isAvailable && sisa >= currentMemberCount) {
                        // Kuota cukup untuk tim
                        makeLinkActive(buildRegisterUrl('kelompok', currentMemberCount), 'Daftar Kelompok (' + currentMemberCount + ' Orang) ➔');
                        if (statusPill) {
                            statusPill.className = 'kuota-status-pill status-tersedia';
                            statusPill.textContent = 'Tersedia (' + sisa + ' Kursi)';
                        }
                    } else if (isAvailable && sisa > 0 && sisa < currentMemberCount) {
                        // Kuota kurang untuk seluruh rombongan
                        makeButtonDisabled('Sisa Kuota Tidak Cukup (' + sisa + ' Kursi)');
                        if (statusPill) {
                            statusPill.className = 'kuota-status-pill status-kurang';
                            statusPill.textContent = 'Sisa ' + sisa + ' Kursi · Tim ' + currentMemberCount;
                        }
                    } else if (isAvailable && sisa === 0) {
                        makeButtonDisabled('Kuota Penuh');
                        if (statusPill) {
                            statusPill.className = 'kuota-status-pill status-penuh';
                            statusPill.textContent = 'Penuh';
                        }
                    } else {
                        makeButtonDisabled('Pendaftaran Ditutup');
                        if (statusPill) {
                            statusPill.className = 'kuota-status-pill status-penuh';
                            statusPill.textContent = 'Tutup';
                        }
                    }
                }
            });
        }

        // Kalkulasi Gauge Ring Meter dari data kartu aktif yang terlihat
        function refreshGaugeFromVisibleCards() {
            const allCards = carouselTrack.querySelectorAll('.kuota-card');
            let totalSisa = 0;
            let totalKursi = 0;
            let visibleCount = 0;

            allCards.forEach(function (card) {
                const cardJenjang = card.getAttribute('data-jenjang');
                const cardBulanId = card.getAttribute('data-bulan-id');
                const sisa = parseInt(card.getAttribute('data-sisa'), 10) || 0;
                const total = parseInt(card.getAttribute('data-total'), 10) || 0;

                const isMatch = (cardJenjang === currentJenjang) && (!currentBulanId || cardBulanId === currentBulanId);

                if (isMatch) {
                    totalSisa += sisa;
                    totalKursi += total;
                    visibleCount++;
                }
            });

            // Update Circle Gauge
            const C = 2 * Math.PI * 52; // ~326.72
            if (gaugeCircle) {
                const ratio = totalKursi > 0 ? (totalSisa / totalKursi) : 0;
                const offset = C * (1 - ratio);
                gaugeCircle.style.strokeDasharray = C;
                gaugeCircle.style.strokeDashoffset = offset;
            }
            if (gaugeNumber) {
                gaugeNumber.textContent = totalSisa;
            }
            if (gaugeDesc) {
                const jenjangLabel = currentJenjang === 'smk' ? 'Siswa SMK' : 'Mahasiswa (D3/S1)';
                const modeLabel = currentRegMode === 'mandiri' ? 'Mandiri (1 orang)' : 'Kelompok (' + currentMemberCount + ' orang)';
                if (visibleCount === 0) {
                    gaugeDesc.innerHTML = 'Belum ada jurusan yang dibuka untuk <strong>' + jenjangLabel + '</strong> pada periode <strong>' + (currentBulanName || '') + '</strong>.';
                } else {
                    gaugeDesc.innerHTML = 'Ada <strong>' + totalSisa + ' kursi kosong</strong> dari ' + totalKursi + ' total kursi untuk <strong>' + jenjangLabel + '</strong> yang mulai <strong>' + (currentBulanName || '') + '</strong> · <em>Jalur: ' + modeLabel + '</em>.';
                }
            }
        }

        // Filter tampilan kartu dan kalkulasi Gauge dari data riil
        function updateKuotaView() {
            const allCards = carouselTrack.querySelectorAll('.kuota-card');

            allCards.forEach(function (card) {
                const cardJenjang = card.getAttribute('data-jenjang');
                const cardBulanId = card.getAttribute('data-bulan-id');
                const isMatch = (cardJenjang === currentJenjang) && (!currentBulanId || cardBulanId === currentBulanId);

                if (isMatch) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });

            refreshGaugeFromVisibleCards();
            updateCardActionButtons();

            carouselTrack.scrollLeft = 0;
            checkCarouselButtons();
        }

        // Listener Klik Jurusan Pills pada Kartu Kuota (Delegasi Event)
        carouselTrack.addEventListener('click', function (e) {
            const pill = e.target.closest('.jurusan-pill-btn');
            if (!pill) return;
            e.preventDefault();

            const card = pill.closest('.kuota-card');
            if (!card) return;

            // Nonaktifkan pill lain di kartu yang sama
            const cardPills = card.querySelectorAll('.jurusan-pill-btn');
            cardPills.forEach(function (p) {
                p.classList.remove('active');
                p.setAttribute('aria-checked', 'false');
            });

            // Aktifkan pill yang diklik
            pill.classList.add('active');
            pill.setAttribute('aria-checked', 'true');

            // Ambil data atribut dari pill yang dipilih
            const jId = pill.getAttribute('data-jurusan-id') || '';
            const sisa = parseInt(pill.getAttribute('data-sisa'), 10) || 0;
            const total = parseInt(pill.getAttribute('data-total'), 10) || 0;
            const persen = parseInt(pill.getAttribute('data-persen'), 10) || 0;
            const badgeLabel = pill.getAttribute('data-badge-label') || (sisa > 0 ? 'Tersedia' : 'Penuh');
            const badgeClass = pill.getAttribute('data-badge-class') || (sisa > 0 ? 'status-tersedia' : 'status-penuh');
            const barClass = pill.getAttribute('data-bar-class') || (sisa > 0 ? 'bar-tersedia' : 'bar-penuh');

            // Update data atribut kartu
            card.setAttribute('data-jurusan-id', jId);
            card.setAttribute('data-sisa', sisa);
            card.setAttribute('data-total', total);
            if (sisa === 0) {
                card.classList.add('is-penuh');
            } else {
                card.classList.remove('is-penuh');
            }

            // Update elemen visual angka, progress bar, dan status pill di dalam kartu
            const bigNum = card.querySelector('.kuota-big-number');
            if (bigNum) bigNum.textContent = sisa;

            const totalLabel = card.querySelector('.kuota-stat-total');
            if (totalLabel) totalLabel.textContent = 'dari ' + total + ' kursi';

            const barFill = card.querySelector('.kuota-progress-bar-fill');
            if (barFill) {
                barFill.style.width = persen + '%';
                barFill.className = 'kuota-progress-bar-fill ' + barClass;
            }

            const statusPill = card.querySelector('.kuota-status-pill');
            if (statusPill) {
                statusPill.className = 'kuota-status-pill ' + badgeClass;
                statusPill.textContent = badgeLabel;
            }

            // Perbarui tombol CTA kartu
            updateCardActionButtons();

            // Perbarui total gauge ring meter
            refreshGaugeFromVisibleCards();
        });

        // Listener Jenjang Pills (1. Kamu siapa?)
        jenjangPills.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const j = btn.getAttribute('data-jenjang-pill');
                if (j === currentJenjang) return;
                currentJenjang = j;
                jenjangPills.forEach(function (b) {
                    b.classList.toggle('active', b.getAttribute('data-jenjang-pill') === currentJenjang);
                });
                updateKuotaView();
            });
        });

        // Listener Bulan Pills (2. Mau mulai kapan?)
        bulanPills.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const bId = btn.getAttribute('data-bulan-pill');
                if (bId === currentBulanId) return;
                currentBulanId = bId;
                currentBulanName = btn.getAttribute('data-bulan-name') || btn.textContent.trim();
                bulanPills.forEach(function (b) {
                    b.classList.toggle('active', b.getAttribute('data-bulan-pill') === currentBulanId);
                });
                updateKuotaView();
            });
        });

        // Listener Mode Pills (3. Magang sama siapa?)
        modePills.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const mode = btn.getAttribute('data-mode-pill');
                if (mode === currentRegMode) return;
                currentRegMode = mode;
                modePills.forEach(function (b) {
                    b.classList.toggle('active', b.getAttribute('data-mode-pill') === currentRegMode);
                });

                if (currentRegMode === 'kelompok') {
                    if (currentMemberCount < 2) currentMemberCount = 2;
                    if (teamSizeWrap) teamSizeWrap.style.display = 'flex';
                    memberPills.forEach(function (mb) {
                        mb.classList.toggle('active', parseInt(mb.getAttribute('data-count'), 10) === currentMemberCount);
                    });
                } else {
                    currentMemberCount = 1;
                    if (teamSizeWrap) teamSizeWrap.style.display = 'none';
                }
                updateKuotaView();
            });
        });

        // Listener Anggota Tim Pills (2, 3, 4 orang)
        memberPills.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const count = parseInt(btn.getAttribute('data-count'), 10) || 2;
                if (count === currentMemberCount) return;
                currentMemberCount = count;
                memberPills.forEach(function (b) {
                    b.classList.toggle('active', parseInt(b.getAttribute('data-count'), 10) === currentMemberCount);
                });
                updateKuotaView();
            });
        });

        // Carousel Scroll & Buttons
        function checkCarouselButtons() {
            if (!carouselTrack) return;
            const scrollLeft = carouselTrack.scrollLeft;
            const maxScroll = carouselTrack.scrollWidth - carouselTrack.clientWidth;
            if (btnPrev) btnPrev.disabled = scrollLeft <= 4;
            if (btnNext) btnNext.disabled = scrollLeft >= maxScroll - 4;
        }

        carouselTrack.addEventListener('scroll', checkCarouselButtons, { passive: true });

        if (btnPrev) {
            btnPrev.addEventListener('click', function () {
                const card = carouselTrack.querySelector('.kuota-card:not([style*="display: none"])');
                const step = card ? card.offsetWidth + 18 : 300;
                carouselTrack.scrollBy({ left: -step, behavior: 'smooth' });
            });
        }

        if (btnNext) {
            btnNext.addEventListener('click', function () {
                const card = carouselTrack.querySelector('.kuota-card:not([style*="display: none"])');
                const step = card ? card.offsetWidth + 18 : 300;
                carouselTrack.scrollBy({ left: step, behavior: 'smooth' });
            });
        }

        // Inisialisasi awal
        updateKuotaView();
    }

    // =========================================================================
    // 3. TAHAPANROAD INTERACTIVE ENGINE (Scroll-Driven Car on SVG Spline)
    // =========================================================================
    function initTahapanRoad() {
        const wrap = document.getElementById('tahapanRoadWrapper');
        const roadPath = document.getElementById('roadSvgPath');
        const roadGradPath = document.getElementById('roadGradProgressPath');
        const carGroup = document.getElementById('roadCarGroup');
        const checkpointGroups = document.querySelectorAll('[data-checkpoint-index]');
        const milestoneCards = document.querySelectorAll('[data-milestone-index]');

        if (!wrap || !roadPath || !carGroup) return;

        let totalLength = roadPath.getTotalLength ? roadPath.getTotalLength() : 1000;
        const H = 210;
        const cpY = function (i) { return i * H + 105; };

        // Binary search untuk mencari posisi length kurva di setiap checkpoint
        const cpLengths = [0, 1, 2, 3].map(function (i) {
            let lo = 0;
            let hi = totalLength;
            const targetY = cpY(i);
            for (let k = 0; k < 32; k++) {
                const mid = (lo + hi) / 2;
                const pt = roadPath.getPointAtLength(mid);
                if (pt.y < targetY) {
                    lo = mid;
                } else {
                    hi = mid;
                }
            }
            return lo;
        });

        let rafId = null;

        function updateRoadAnimation() {
            if (rafId) cancelAnimationFrame(rafId);
            rafId = requestAnimationFrame(function () {
                const rect = wrap.getBoundingClientRect();
                const header = document.querySelector('.landing-header');
                const headerH = header ? header.offsetHeight : 68;
                const startOffset = headerH + 20;

                // Hitung progres scroll sejak bagian atas seksi tiba di posisi baca
                // Saat user tiba di seksi (rect.top >= startOffset), prog = 0 (mobil tepat di titik START)
                const scrolled = startOffset - rect.top;
                const travelRange = Math.max(400, rect.height - 240);

                let prog = scrolled / travelRange;
                prog = Math.max(0, Math.min(1, prog));

                const currentLen = prog * totalLength;

                // Hitung koordinat dan rotasi mobil
                const pt = roadPath.getPointAtLength(currentLen);
                const ptAhead = roadPath.getPointAtLength(Math.min(totalLength, currentLen + 2));
                const ptBehind = roadPath.getPointAtLength(Math.max(0, currentLen - 2));
                const angle = (Math.atan2(ptAhead.y - ptBehind.y, ptAhead.x - ptBehind.x) * 180) / Math.PI;

                carGroup.setAttribute('transform', 'translate(' + pt.x + ' ' + pt.y + ') rotate(' + angle + ')');

                // Update stroke dash offset pada gradient jalanan
                if (roadGradPath) {
                    roadGradPath.style.strokeDasharray = totalLength;
                    roadGradPath.style.strokeDashoffset = totalLength - currentLen;
                }

                // Update checkpoint nodes & milestone cards
                for (let i = 0; i < 4; i++) {
                    const isPassed = currentLen >= (cpLengths[i] - 6);

                    // Checkpoint SVG Node
                    const node = document.querySelector('[data-checkpoint-index="' + i + '"]');
                    if (node) {
                        const halo = node.querySelector('.checkpoint-halo');
                        const dot = node.querySelector('.checkpoint-dot');
                        if (halo) halo.setAttribute('opacity', isPassed ? '0.28' : '0');
                        if (dot) {
                            dot.setAttribute('fill', isPassed ? dot.getAttribute('data-step-color') : 'var(--background)');
                        }
                    }

                    // Milestone card
                    const card = document.querySelector('[data-milestone-index="' + i + '"]');
                    if (card) {
                        if (isPassed) {
                            card.classList.add('active');
                            card.classList.remove('inactive');
                        } else {
                            card.classList.remove('active');
                            card.classList.add('inactive');
                        }
                    }
                }
            });
        }

        // Click on checkpoint or milestone card: smooth scroll to that checkpoint
        function scrollToStep(index) {
            const rect = wrap.getBoundingClientRect();
            const targetLength = cpLengths[index] || 0;
            const progressTarget = targetLength / totalLength;
            const currentScrollY = window.scrollY || window.pageYOffset;
            const header = document.querySelector('.landing-header');
            const headerH = header ? header.offsetHeight : 68;
            const startOffset = headerH + 20;
            const travelRange = Math.max(400, rect.height - 240);

            const destinationY = currentScrollY + rect.top - startOffset + (progressTarget * travelRange);
            window.scrollTo({ top: Math.max(0, destinationY), behavior: 'smooth' });
        }

        // Click on START node: scroll back to start of road
        const startNode = wrap.querySelector('[aria-label="Start Node"]');
        if (startNode) {
            startNode.style.cursor = 'pointer';
            startNode.addEventListener('click', function () {
                const rect = wrap.getBoundingClientRect();
                const currentScrollY = window.scrollY || window.pageYOffset;
                const header = document.querySelector('.landing-header');
                const headerH = header ? header.offsetHeight : 68;
                const startOffset = headerH + 20;
                const destinationY = currentScrollY + rect.top - startOffset;
                window.scrollTo({ top: Math.max(0, destinationY), behavior: 'smooth' });
            });
        }

        checkpointGroups.forEach(function (cp) {
            cp.addEventListener('click', function () {
                const idx = parseInt(cp.getAttribute('data-checkpoint-index'), 10);
                scrollToStep(idx);
            });
        });

        milestoneCards.forEach(function (card) {
            card.addEventListener('click', function () {
                const idx = parseInt(card.getAttribute('data-milestone-index'), 10);
                scrollToStep(idx);
            });
        });

        window.addEventListener('scroll', updateRoadAnimation, { passive: true });
        window.addEventListener('resize', function () {
            totalLength = roadPath.getTotalLength();
            updateRoadAnimation();
        });

        updateRoadAnimation();
    }

    // =========================================================================
    // 4. FAQ CATEGORIZED ACCORDION CONTROLLER (Parity with User Reference)
    // =========================================================================
    function initFaqAccordion() {
        const stackContainer = document.getElementById('faqCategoryAccordion');
        if (!stackContainer) return;
        if (stackContainer.getAttribute('data-faq-bound') === 'true') return;
        stackContainer.setAttribute('data-faq-bound', 'true');

        // Ambil seluruh item kategori yang ada
        const categoryItems = stackContainer.querySelectorAll('.faq-category-item');

        categoryItems.forEach(function (catItem) {
            const headerBtn = catItem.querySelector('.faq-category-header-btn');
            const drawer = catItem.querySelector('.faq-questions-drawer');
            const subQaItems = catItem.querySelectorAll('.faq-sub-qa-item');

            if (headerBtn && drawer) {
                headerBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const isCurrentlyExpanded = catItem.classList.contains('is-expanded');

                    // Tutup kategori lain agar accordion rapi sesuai referensi
                    categoryItems.forEach(function (otherItem) {
                        if (otherItem !== catItem) {
                            otherItem.classList.remove('is-expanded');
                            const otherBtn = otherItem.querySelector('.faq-category-header-btn');
                            const otherDrawer = otherItem.querySelector('.faq-questions-drawer');
                            if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
                            if (otherDrawer) otherDrawer.style.display = 'none';
                        }
                    });

                    catItem.classList.toggle('is-expanded', !isCurrentlyExpanded);
                    headerBtn.setAttribute('aria-expanded', !isCurrentlyExpanded ? 'true' : 'false');
                    drawer.style.display = !isCurrentlyExpanded ? 'flex' : 'none';
                });
            }

            // Binding untuk sub-pertanyaan emas (Gold) di dalam kategori
            subQaItems.forEach(function (qaItem) {
                const qBtn = qaItem.querySelector('.faq-sub-question-btn');
                const aPane = qaItem.querySelector('.faq-sub-answer-pane');

                if (qBtn && aPane) {
                    qBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const isCurrentlyOpen = qaItem.classList.contains('is-open');

                        // Tutup pertanyaan lain di dalam kategori yang sama
                        subQaItems.forEach(function (otherQa) {
                            if (otherQa !== qaItem) {
                                otherQa.classList.remove('is-open');
                                const otherBtn = otherQa.querySelector('.faq-sub-question-btn');
                                const otherPane = otherQa.querySelector('.faq-sub-answer-pane');
                                if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
                                if (otherPane) otherPane.style.display = 'none';
                            }
                        });

                        qaItem.classList.toggle('is-open', !isCurrentlyOpen);
                        qBtn.setAttribute('aria-expanded', !isCurrentlyOpen ? 'true' : 'false');
                        aPane.style.display = !isCurrentlyOpen ? 'block' : 'none';
                    });
                }
            });
        });
    }

    // =========================================================================
    // 5. HERO SLIDER CONTROLLER (Dokumentasi Magang Kominfo - No Hover)
    // =========================================================================
    function initHeroSlider() {
        const track = document.getElementById('heroSliderTrack');
        const badge = document.getElementById('heroSliderBadge');
        const btnPrev = document.getElementById('btnHeroSidePrev') || document.getElementById('btnHeroPrev');
        const btnNext = document.getElementById('btnHeroSideNext') || document.getElementById('btnHeroNext');
        const dotBtns = document.querySelectorAll('#heroSliderDots .hero-dot-btn');

        if (!track) return;

        const slides = track.querySelectorAll('.hero-slide-item');
        const totalSlides = slides.length;
        if (totalSlides <= 1) return;

        // Sinkronisasi tinggi presisi Slide Foto agar SAMA PERSIS dengan Slide Info Awal
        const slide1Box = track.querySelector('.hero-slide-item:first-child .hero-box');
        const imgSlide = document.getElementById('heroFullImageSlide');

        function syncHeroHeight() {
            if (!slide1Box || !imgSlide) return;
            imgSlide.style.height = '';
            imgSlide.style.minHeight = '';
            imgSlide.style.maxHeight = '';
            const h = slide1Box.offsetHeight;
            if (h > 0) {
                imgSlide.style.height = h + 'px';
                imgSlide.style.minHeight = h + 'px';
                imgSlide.style.maxHeight = h + 'px';
            }
        }

        syncHeroHeight();
        window.addEventListener('resize', syncHeroHeight);
        window.addEventListener('load', syncHeroHeight);
        const heroImg = document.getElementById('heroFullImg');
        if (heroImg) {
            heroImg.addEventListener('load', syncHeroHeight);
        }

        let currentIndex = 0;
        let autoSlideTimer = null;

        function goToSlide(index) {
            if (index < 0) {
                currentIndex = totalSlides - 1;
            } else if (index >= totalSlides) {
                currentIndex = 0;
            } else {
                currentIndex = index;
            }

            // Move track
            track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';

            // Update badge text
            if (badge) {
                badge.textContent = (currentIndex + 1) + ' / ' + totalSlides;
            }

            // Update dot indicators
            dotBtns.forEach(function (btn, idx) {
                if (idx === currentIndex) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        // Button Controls
        if (btnPrev) {
            btnPrev.addEventListener('click', function () {
                stopAutoSlide();
                goToSlide(currentIndex - 1);
                startAutoSlide();
            });
        }

        if (btnNext) {
            btnNext.addEventListener('click', function () {
                stopAutoSlide();
                goToSlide(currentIndex + 1);
                startAutoSlide();
            });
        }

        // Dot Buttons
        dotBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const targetIdx = parseInt(btn.getAttribute('data-slide-index'), 10);
                if (!isNaN(targetIdx)) {
                    stopAutoSlide();
                    goToSlide(targetIdx);
                    startAutoSlide();
                }
            });
        });

        // Auto slide every 7 seconds
        function startAutoSlide() {
            stopAutoSlide();
            autoSlideTimer = setInterval(function () {
                goToSlide(currentIndex + 1);
            }, 7000);
        }

        function stopAutoSlide() {
            if (autoSlideTimer) {
                clearInterval(autoSlideTimer);
                autoSlideTimer = null;
            }
        }

        // Start
        goToSlide(0);
        startAutoSlide();
    }

    // =========================================================================
    // 6. HEADER SMOOTH SCROLL NAVIGATION & SECTION LANDING OFFSET
    // =========================================================================
    function initHeaderScrollNav() {
        const navLinks = document.querySelectorAll('.landing-nav-link[href^="#"], .landing-header a[href^="#"], a.btn-header-register[href^="#"]');
        navLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                const targetId = link.getAttribute('href');
                if (!targetId || targetId === '#' || !targetId.startsWith('#')) return;

                let targetEl = document.querySelector(targetId);
                // Jika menuju #peta-alur atau #tahapan, arahkan tepat ke kontainer awal seksi tahapan
                if (targetId === '#peta-alur' || targetId === '#tahapan') {
                    const sectionTahapan = document.getElementById('tahapan');
                    if (sectionTahapan) {
                        targetEl = sectionTahapan;
                    }
                }

                if (targetEl) {
                    e.preventDefault();
                    const header = document.querySelector('.landing-header');
                    const headerHeight = header ? header.offsetHeight : 68;
                    // Jarak bernapas proporsional 20px di bawah navbar sticky
                    const targetTop = targetEl.getBoundingClientRect().top + window.scrollY - (headerHeight + 20);
                    window.scrollTo({
                        top: Math.max(0, targetTop),
                        behavior: 'smooth'
                    });

                    if (window.history && window.history.pushState) {
                        window.history.pushState(null, '', targetId);
                    }
                }
            });
        });
    }

    // =========================================================================
    // 0. SITE PRELOADER & SPLASH SCREEN DISMISSAL
    // =========================================================================
    function initSitePreloader() {
        const preloader = document.getElementById('sitePreloader');
        if (!preloader) return;

        // Cek preferensi aksesibilitas minim gerak
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            preloader.classList.add('is-loaded');
            preloader.style.display = 'none';
            return;
        }

        const startTime = Date.now();
        let dismissed = false;

        function dismissPreloader() {
            if (dismissed) return;
            dismissed = true;

            try {
                window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
            } catch (e) {
                window.scrollTo(0, 0);
            }

            const elapsed = Date.now() - startTime;
            // Durasi minimal estetika 650ms agar animasi tidak berkedip
            const delay = Math.max(0, 650 - elapsed);

            setTimeout(function () {
                preloader.classList.add('is-loaded');
                setTimeout(function () {
                    preloader.style.display = 'none';
                }, 520);
            }, delay);
        }

        // Tutup saat window selesai memuat seluruh asset
        if (document.readyState === 'complete') {
            dismissPreloader();
        } else {
            window.addEventListener('load', dismissPreloader, { once: true });
        }

        // Safety fallback timeout: maksimal 2.2 detik agar user tidak pernah terblokir
        setTimeout(dismissPreloader, 2200);
    }

    // =========================================================================
    // 7. HEADER DYNAMIC SCROLL STATE (SHRINK & FROSTED GLASS ELEVATION)
    // =========================================================================
    function initHeaderScrollState() {
        const header = document.querySelector('.landing-header');
        if (!header) return;

        let ticking = false;
        function updateHeader() {
            const isScrolled = (window.scrollY || window.pageYOffset) > 20;
            header.classList.toggle('is-scrolled', isScrolled);
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(updateHeader);
                ticking = true;
            }
        }, { passive: true });

        updateHeader();
    }

    // =========================================================================
    // 8. SCROLL REVEAL ENTRANCE ANIMATION (FADE-IN UP ON FIRST VIEWPORT ENTRY)
    // =========================================================================
    function initScrollReveal() {
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        const selectorList = [
            '#penempatan .section-heading-lg',
            '#penempatan .section-desc-sub',
            '#penempatan .kuota-questions-grid',
            '#penempatan .kuota-highlight-banner',
            '#penempatan .kuota-carousel-wrapper',
            '.tahapan-sticky-left',
            '#tahapanRoadWrap',
            '.faq-visual-col',
            '.faq-content-col'
        ];

        const elements = document.querySelectorAll(selectorList.join(', '));
        if (!elements.length) return;

        elements.forEach(function (el) {
            el.classList.add('scroll-reveal');
            if (el.classList.contains('section-desc-sub')) {
                el.classList.add('delay-1');
            } else if (el.classList.contains('kuota-questions-grid')) {
                el.classList.add('delay-2');
            }
        });

        if (!('IntersectionObserver' in window)) {
            elements.forEach(function (el) {
                el.classList.add('is-revealed');
            });
            return;
        }

        const revealObserver = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            root: null,
            rootMargin: '0px 0px -40px 0px',
            threshold: 0.08
        });

        elements.forEach(function (el) {
            const rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom > 0) {
                el.classList.add('is-revealed');
            } else {
                revealObserver.observe(el);
            }
        });
    }

    // =========================================================================
    // BOOTSTRAP ON DOM READY
    // =========================================================================
    function applyDynamicTheme() {
        const el = document.documentElement;
        if (!el) return;

        const ds = el.dataset;
        if (ds.themePrimary) el.style.setProperty('--navy', ds.themePrimary);
        if (ds.themeNavyDark) el.style.setProperty('--navy-dark', ds.themeNavyDark);
        if (ds.themeAccent) el.style.setProperty('--accent', ds.themeAccent);
        if (ds.themeGold) el.style.setProperty('--gold', ds.themeGold);
        if (ds.themeBg) el.style.setProperty('--background', ds.themeBg);
        if (ds.themeFontDisplay) el.style.setProperty('--font-display', `'${ds.themeFontDisplay}', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`);
        if (ds.themeFontBody) el.style.setProperty('--font-sans', `'${ds.themeFontBody}', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`);
        if (ds.themeRadius) el.style.setProperty('--border-radius-card', ds.themeRadius);
        if (ds.themeBtnStyle) {
            const btnRadius = ds.themeBtnStyle === 'pill' ? '9999px' : (ds.themeBtnStyle === 'sharp' ? '0px' : '8px');
            el.style.setProperty('--radius-button', btnRadius);
        }
    }

    // Apply immediate execution to prevent flash
    applyDynamicTheme();

    document.addEventListener('DOMContentLoaded', function () {
        try {
            window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
        } catch (e) {
            window.scrollTo(0, 0);
        }
        applyDynamicTheme();
        initSitePreloader();
        initHeroSlider();
        initKuotaExplorer();
        initTahapanRoad();
        initFaqAccordion();
        initHeaderScrollNav();
        initHeaderScrollState();
        initScrollReveal();
    });

    window.addEventListener('load', function () {
        try {
            window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
        } catch (e) {
            window.scrollTo(0, 0);
        }
    });

})();
