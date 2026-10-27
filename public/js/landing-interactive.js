/**
 * MAGKOM Landing Interactive Engine
 * Parity with NAUFALFEBRIAN62/magkomlandingpage
 */

(function () {
    'use strict';

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
        faqList: [
            { q: 'Apakah saya bisa mendaftar lebih dari satu bidang?', a: 'Tidak. Setiap peserta hanya dapat memilih satu bidang dalam satu periode magang.' },
            { q: 'Bagaimana cara saya mendaftarkan diri?', a: 'Klik tombol Daftar, buat akun, lengkapi data diri, lalu pilih bidang dan unggah berkas yang diminta.' },
            { q: 'Apa saja bidang yang tersedia di MAGKOM?', a: 'Aplikasi Informatika, Statistik, Persandian, dan Sekretariat. Lihat sisa kuotanya di bagian Kuota per Bidang.' },
            { q: 'Berapa lama durasi magang?', a: 'Durasi magang mengikuti surat pengantar dari sekolah atau kampus, umumnya 1 sampai 3 bulan.' },
            { q: 'Mengapa saya tidak mendapatkan email verifikasi?', a: 'Periksa folder spam atau promosi. Jika tetap tidak ada, hubungi admin melalui kontak di bagian bawah halaman.' },
            { q: 'Apakah magang ini dibayar?', a: 'Magang di Diskominfo Sleman tidak berbayar dan tidak memungut biaya apa pun.' },
            { q: 'Apakah magang bisa dilakukan dari rumah (WFH)?', a: 'Tidak. Magang dilaksanakan secara luring di kantor Diskominfo Sleman.' },
            { q: 'Kapan sertifikat magang bisa diunduh?', a: 'Setelah magang selesai dan nilai diinput pembimbing, sertifikat dapat diunduh dari portal MAGKOM.' },
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
                const isWaiting = card.getAttribute('data-is-waiting') === '1';
                const isAvailable = card.getAttribute('data-is-available') === '1';
                const bulanId = card.getAttribute('data-bulan-id');
                const jurusanId = card.getAttribute('data-jurusan-id');
                const statusPill = card.querySelector('.kuota-status-pill');
                const btnLink = card.querySelector('.btn-card-cta');

                if (!btnLink) return;

                const baseUrl = btnLink.getAttribute('data-base-url') || '/register';

                if (currentRegMode === 'mandiri') {
                    // Mode Mandiri (1 Orang)
                    const targetUrl = baseUrl + '?bulan=' + bulanId + '&jurusan=' + jurusanId + '&tipe=mandiri&jumlah=1';
                    btnLink.setAttribute('href', targetUrl);

                    if (isAvailable && !isWaiting && sisa > 0) {
                        btnLink.className = 'btn-card-action btn-primary-action btn-card-cta';
                        btnLink.textContent = 'Daftar Sekarang ➔';
                        if (statusPill) {
                            statusPill.className = (total > 0 && sisa / total <= 0.34) ? 'kuota-status-pill status-hampir' : 'kuota-status-pill status-tersedia';
                            statusPill.textContent = (total > 0 && sisa / total <= 0.34) ? 'Tinggal sedikit' : 'Tersedia';
                        }
                    } else if (isWaiting || (isAvailable && sisa === 0)) {
                        btnLink.className = 'btn-card-action btn-waitlist-action btn-card-cta';
                        btnLink.textContent = 'Daftar Waiting List ➔';
                        if (statusPill) {
                            statusPill.className = 'kuota-status-pill status-penuh';
                            statusPill.textContent = 'Waiting List';
                        }
                    }
                } else {
                    // Mode Kelompok (2 - 4 Orang)
                    const targetUrl = baseUrl + '?bulan=' + bulanId + '&jurusan=' + jurusanId + '&tipe=kelompok&jumlah=' + currentMemberCount;
                    btnLink.setAttribute('href', targetUrl);

                    if (isAvailable && !isWaiting && sisa >= currentMemberCount) {
                        // Kuota cukup untuk tim
                        btnLink.className = 'btn-card-action btn-primary-action btn-card-cta';
                        btnLink.textContent = 'Daftar Kelompok (' + currentMemberCount + ' Orang) ➔';
                        if (statusPill) {
                            statusPill.className = 'kuota-status-pill status-tersedia';
                            statusPill.textContent = 'Tersedia (' + sisa + ' Kursi)';
                        }
                    } else if (isAvailable && !isWaiting && sisa > 0 && sisa < currentMemberCount) {
                        // Kuota kurang untuk seluruh rombongan
                        btnLink.className = 'btn-card-action btn-waitlist-action btn-card-cta';
                        btnLink.textContent = 'Daftar Waiting List (' + currentMemberCount + ' Orang) ➔';
                        if (statusPill) {
                            statusPill.className = 'kuota-status-pill status-kurang';
                            statusPill.textContent = 'Sisa ' + sisa + ' Kursi · Tim ' + currentMemberCount;
                        }
                    } else {
                        // Kuota habis (0) atau waiting list
                        btnLink.className = 'btn-card-action btn-waitlist-action btn-card-cta';
                        btnLink.textContent = 'Daftar Waiting List (' + currentMemberCount + ' Orang) ➔';
                        if (statusPill) {
                            statusPill.className = 'kuota-status-pill status-penuh';
                            statusPill.textContent = 'Waiting List';
                        }
                    }
                }
            });
        }

        // Filter tampilan kartu dan kalkulasi Gauge dari data riil
        function updateKuotaView() {
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
                    card.style.display = 'flex';
                    totalSisa += sisa;
                    totalKursi += total;
                    visibleCount++;
                } else {
                    card.style.display = 'none';
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

            // Sinkronkan tombol pendaftaran dan kuota tim
            updateCardActionButtons();

            carouselTrack.scrollLeft = 0;
            checkCarouselButtons();
        }

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
                const viewH = window.innerHeight;
                
                // Hitung progres scroll (0 sampai 1) saat elemen melintas layar
                const targetPoint = viewH * 0.58;
                let prog = (targetPoint - rect.top) / rect.height;
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
            const destinationY = currentScrollY + rect.top + (progressTarget * rect.height) - (window.innerHeight * 0.58);
            window.scrollTo({ top: destinationY, behavior: 'smooth' });
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
    // 4. FAQ ACCORDION CONTROLLER
    // =========================================================================
    function initFaqAccordion() {
        const container = document.getElementById('faqAccordionContainer');
        const btnToggleAll = document.getElementById('btnFaqToggleAll');
        if (!container) return;

        let isAllExpanded = false;
        const initialCount = 5;

        function renderFaqItems() {
            container.innerHTML = '';
            const items = isAllExpanded ? AppData.faqList : AppData.faqList.slice(0, initialCount);

            items.forEach(function (faq, idx) {
                const card = document.createElement('div');
                card.className = 'faq-card';

                const trigger = document.createElement('button');
                trigger.type = 'button';
                trigger.className = 'faq-trigger-btn';
                trigger.setAttribute('aria-expanded', 'false');
                trigger.innerHTML = `
                    <span>${faq.q}</span>
                    <svg class="faq-icon-toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" class="icon-vert-line"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                `;

                const answer = document.createElement('div');
                answer.className = 'faq-answer-pane';
                answer.style.display = 'none';
                answer.textContent = faq.a;

                trigger.addEventListener('click', function () {
                    const expanded = trigger.getAttribute('aria-expanded') === 'true';
                    trigger.setAttribute('aria-expanded', !expanded);
                    answer.style.display = expanded ? 'none' : 'block';
                    const vertLine = trigger.querySelector('.icon-vert-line');
                    if (vertLine) {
                        vertLine.style.display = expanded ? 'block' : 'none';
                    }
                });

                card.appendChild(trigger);
                card.appendChild(answer);
                container.appendChild(card);
            });

            if (btnToggleAll) {
                btnToggleAll.innerHTML = isAllExpanded 
                    ? 'Tampilkan Lebih Sedikit <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"></polyline></svg>'
                    : 'Lihat Semua Pertanyaan <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>';
            }
        }

        if (btnToggleAll) {
            btnToggleAll.addEventListener('click', function () {
                isAllExpanded = !isAllExpanded;
                renderFaqItems();
            });
        }

        renderFaqItems();
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
    // BOOTSTRAP ON DOM READY
    // =========================================================================
    document.addEventListener('DOMContentLoaded', function () {
        initHeroSlider();
        initKuotaExplorer();
        initTahapanRoad();
        initFaqAccordion();
    });

})();
