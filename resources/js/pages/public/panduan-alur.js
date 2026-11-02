    // Data Node Dictionary for Dynamic Inspector
    const nodeData = {
        1: {
            phase: 'Fase 1: Pendaftaran & Kuota',
            phaseColor: '#0751FF',
            phaseBg: '#E6EEFF',
            title: 'Pilih Formasi & Cek Kuota Real-Time',
            desc: 'Calon peserta memantau sisa kuota kursi magang pada kalender bulanan secara langsung. Pendaftaran hanya dibuka untuk periode yang berstatus "Buka".',
            actor: 'Calon Peserta Magang',
            sla: 'Instan (Real-Time)',
            checklist: [
                'Menentukan Jalur SMK (PKL) atau Mahasiswa (D3/S1)',
                'Memilih mode Mandiri (1 orang) atau Kelompok (2â€“5 orang)',
                'Memastikan periode magang berstatus "Buka"'
            ],
            callout: '<strong>Aturan Bisnis Kunci (BR-01):</strong> Calon peserta hanya dapat mendaftar pada 1 bidang dalam 1 periode bulan yang sedang aktif.',
            ctaText: 'Lihat Kuota Tersedia Sekarang',
            ctaUrl: '{{ route("landing") }}#kuota'
        },
        2: {
            phase: 'Fase 1: Pendaftaran & Kuota',
            phaseColor: '#0751FF',
            phaseBg: '#E6EEFF',
            title: 'Registrasi Akun & Pengisian Biodata',
            desc: 'Mendaftarkan akun menggunakan alamat email aktif, verifikasi email, serta melengkapi biodata instansi pendidikan dan nomor WhatsApp aktif untuk notifikasi sistem.',
            actor: 'Calon Peserta / Ketua Kelompok',
            sla: '5â€“10 Menit',
            checklist: [
                'Alamat email aktif yang dapat diverifikasi',
                'Nomor WhatsApp aktif untuk koordinasi darurat',
                'Data NISN (SMK) atau NIM (Mahasiswa) yang valid'
            ],
            callout: '<strong>Jalur Kelompok (BR-02):</strong> Pendaftaran kelompok didaftarkan oleh 1 orang Ketua Kelompok yang menginput data seluruh anggota perwakilan.',
            ctaText: 'Mulai Buat Akun Peserta',
            ctaUrl: '{{ route("login") }}'
        },
        3: {
            phase: 'Fase 1: Pendaftaran & Kuota',
            phaseColor: '#0751FF',
            phaseBg: '#E6EEFF',
            title: 'Unggah Dokumen Persyaratan Wajib (PDF)',
            desc: 'Mengunggah seluruh kelengkapan berkas administratif dalam format PDF dengan ukuran maksimal 2MB per file sebelum batas waktu penutupan pendaftaran.',
            actor: 'Calon Peserta Magang',
            sla: 'Sebelum Batas Periode',
            checklist: [
                'Surat Pengantar Resmi Sekolah/Kampus (stempel basah/digital)',
                'Curriculum Vitae (CV) terbaru memuat keahlian & proyek',
                'Transkrip Nilai Kumulatif atau Rapor Semester Terakhir',
                'Proposal Magang (Opsional, sangat disarankan untuk kelompok)'
            ],
            callout: '<strong>Validitas Dokumen:</strong> Pastikan berkas terbaca jelas dan tidak berbayang agar proses seleksi berkas berjalan lancar tanpa status perbaikan.',
            ctaText: 'Unduh Panduan Lengkap PDF',
            ctaUrl: '{{ route("panduan.pdf") }}'
        },
        4: {
            phase: 'Fase 2: Seleksi & Penempatan',
            phaseColor: '#16A34A',
            phaseBg: '#E8F7EE',
            title: 'Verifikasi Berkas oleh Tim Admin Diskominfo',
            desc: 'Tim Administrasi Diskominfo menelaah keabsahan surat pengantar sekolah/kampus, keselarasan jurusan dengan bidang yang dituju, dan ketersediaan kuota.',
            actor: 'Tim Admin Diskominfo Sleman',
            sla: '1 sampai 3 Hari Kerja',
            checklist: [
                'Pengecekan stempel resmi dan legalitas surat pengantar',
                'Pencocokan kompetensi jurusan dengan kualifikasi bidang',
                'Alokasi ketersediaan ruangan kerja dan pembimbing lapangan'
            ],
            callout: '<strong>SLA Verifikasi:</strong> Hasil telaah diumumkan secara transparan melalui dashboard peserta maksimal dalam 3 hari kerja setelah berkas diajukan.',
            ctaText: 'Pantau Status Seleksi',
            ctaUrl: '{{ route("login") }}'
        },
        'diterima': {
            phase: 'Fase 2: Seleksi & Penempatan',
            phaseColor: '#166534',
            phaseBg: '#DCFCE7',
            title: 'Hasil Seleksi: Status DITERIMA RESMI',
            desc: 'Selamat! Berkas dan kuota Anda terkonfirmasi lolos. Sistem secara otomatis menerbitkan Surat Keterangan Diterima resmi lengkap dengan tanda tangan digital & QR Code.',
            actor: 'Peserta Magang Terpilih',
            sla: 'Konfirmasi Maks. 3 Hari',
            checklist: [
                'Mengunduh Surat Keterangan Diterima (PDF)',
                'Melakukan konfirmasi kehadiran melalui portal peserta',
                'Mempersiapkan seragam almamater dan perlengkapan dinas'
            ],
            callout: '<strong>Kewajiban Konfirmasi (BR-05):</strong> Peserta yang tidak melakukan konfirmasi kehadiran berisiko digantikan oleh antrean Waiting List.',
            ctaText: 'Masuk ke Dashboard Peserta',
            ctaUrl: '{{ route("login") }}'
        },
        'waiting': {
            phase: 'Fase 2: Seleksi & Penempatan',
            phaseColor: '#1E40AF',
            phaseBg: '#EFF6FF',
            title: 'Hasil Seleksi: WAITING LIST (Daftar Tunggu)',
            desc: 'Kuota formasi telah terisi penuh oleh pemohon yang mendaftar lebih awal. Anda masuk ke dalam antrean prioritas berbasis First-Come First-Served (FIFO).',
            actor: 'Sistem Antrean Otomatis',
            sla: 'Otomatis Promosi',
            checklist: [
                'Memantau pergerakan antrean berkala pada dashboard',
                'Otomatis naik menjadi Diterima jika kuota dibuka kembali',
                'Dapat memilih periode berikutnya jika kuota periode ini tertutup'
            ],
            callout: '<strong>Keadilan Alokasi (BR-03):</strong> Antrean bersifat otomatis tanpa intervensi manual guna menjaga transparansi penuh.',
            ctaText: 'Cek Posisi Antrean Saya',
            ctaUrl: '{{ route("login") }}'
        },
        'perbaikan': {
            phase: 'Fase 2: Seleksi & Penempatan',
            phaseColor: '#991B1B',
            phaseBg: '#FEF2F2',
            title: 'Hasil Seleksi: PERLU PERBAIKAN BERKAS',
            desc: 'Terdapat dokumen yang buram, tidak terbaca, atau keliru lampiran. Anda diberikan kesempatan waktu 3x24 jam untuk mengunggah ulang dokumen yang benar.',
            actor: 'Calon Peserta Magang',
            sla: '3x24 Jam Kalender',
            checklist: [
                'Membaca catatan revisi spesifik dari verifikator admin',
                'Mengunggah kembali dokumen PDF yang telah diperbaiki',
                'Menekan tombol kirim ulang perbaikan di dashboard'
            ],
            callout: '<strong>Batas Waktu:</strong> Apabila dalam 3x24 jam perbaikan tidak dikirimkan, permohonan otomatis berstatus tidak memenuhi syarat.',
            ctaText: 'Unggah Ulang Perbaikan',
            ctaUrl: '{{ route("login") }}'
        },
        5: {
            phase: 'Fase 3: Pelaksanaan & Logbook',
            phaseColor: '#2563EB',
            phaseBg: '#EFF6FF',
            title: 'Orientasi Dinas & Penetapan Mentor Lapangan',
            desc: 'Hari pertama kegiatan magang diawali dengan orientasi pengenalan lingkungan kantor Diskominfo, penugasan ruangan, serta penetapan Pembimbing Lapangan.',
            actor: 'Pembimbing Lapangan & Peserta',
            sla: 'Hari Pertama Magang',
            checklist: [
                'Pengenalan visi misi dan tata kelola Diskominfo',
                'Pemberian kartu identitas tamu / badge peserta magang',
                'Penyusunan rencana kerja proyek bersama Pembimbing'
            ],
            callout: '<strong>Tata Tertib:</strong> Wajib mematuhi jam kerja 07.30 s/d 16.00 WIB dan mengenakan seragam almamater atau pakaian formal rapi.',
            ctaText: 'Lihat Panduan Kedinasan',
            ctaUrl: '{{ route("landing") }}'
        },
        6: {
            phase: 'Fase 3: Pelaksanaan & Logbook',
            phaseColor: '#D97706',
            phaseBg: '#FEF3C7',
            title: 'Pengisian Logbook Harian (Batas Mundur H-1)',
            desc: 'Peserta wajib mencatat ringkasan aktivitas dan bukti progres kerja setiap hari kerja melalui portal peserta. Pembimbing akan meninjau dan menyetujui logbook berkala.',
            actor: 'Peserta Magang & Mentor',
            sla: 'Harian (Maks. H-1)',
            checklist: [
                'Uraian aktivitas harian spesifik dan terukur',
                'Melampirkan bukti output (link repositori, dokumen, atau foto)',
                'Mendapatkan persetujuan (Approve) mingguan dari Pembimbing'
            ],
            callout: '<strong>Aturan Disiplin (BR-09):</strong> Logbook hanya dapat diisi maksimal H-1 (kemarin). Sistem secara otomatis mengunci tanggal lampau guna menjamin keaslian kegiatan.',
            ctaText: 'Buka Logbook Harian',
            ctaUrl: '{{ route("login") }}'
        },
        7: {
            phase: 'Fase 4: Evaluasi & Kelulusan',
            phaseColor: '#D97706',
            phaseBg: '#FEF3C7',
            title: 'Penilaian Rubrik 100 Poin & Sertifikat Digital',
            desc: 'Penutupan magang, pengesahan laporan akhir, evaluasi rubrik objektif 100 poin oleh Pembimbing, dan penerbitan Lembar Nilai Magang resmi & Sertifikat Digital QR Code.',
            actor: 'Pembimbing & Pimpinan Dinas',
            sla: 'Masa Akses 30 Hari',
            checklist: [
                'Pengunggahan Laporan Akhir Magang yang telah disahkan',
                'Penilaian 5 aspek: Kehadiran, Kualitas Kerja, Teknis, Inisiatif, Etika',
                'Unduh Lembar Nilai Resmi (PDF) dan Sertifikat Kelulusan Digital'
            ],
            callout: '<strong>Masa Tenggang 30 Hari (BR-10):</strong> Akun peserta tetap aktif selama 30 hari kalender setelah masa magang berakhir untuk mengunduh seluruh dokumen kelulusan.',
            ctaText: 'Lihat Contoh Sertifikat QR',
            ctaUrl: '{{ route("landing") }}'
        }
    };

    let currentSimJenjang = 'semua';
    let currentSimTipe = 'mandiri';
    let currentActiveNodeId = 1;
    let isAutoPlaying = false;
    let autoPlayTimer = null;

    const tourSequence = [1, 2, 3, 4, 'diterima', 5, 6, 7];
    const nodeStepMapping = {
        1: { stepNum: 1, label: 'Langkah 1 dari 7: Cek Kuota' },
        2: { stepNum: 2, label: 'Langkah 2 dari 7: Registrasi Akun' },
        3: { stepNum: 3, label: 'Langkah 3 dari 7: Unggah Berkas' },
        4: { stepNum: 4, label: 'Langkah 4 dari 7: Verifikasi Admin' },
        'diterima': { stepNum: 4, label: 'Keputusan: Diterima Resmi' },
        'waiting': { stepNum: 4, label: 'Keputusan: Waiting List' },
        'perbaikan': { stepNum: 4, label: 'Keputusan: Perlu Perbaikan (Loopback)' },
        5: { stepNum: 5, label: 'Langkah 5 dari 7: Orientasi & Mentor' },
        6: { stepNum: 6, label: 'Langkah 6 dari 7: Logbook Harian' },
        7: { stepNum: 7, label: 'Langkah 7 dari 7: Nilai & Sertifikat' }
    };

    function setSimJenjang(val) {
        currentSimJenjang = val;
        updateSimPills();
        updateSimulatorState();
    }

    function setSimTipe(val) {
        currentSimTipe = val;
        updateSimPills();
        updateSimulatorState();
    }

    function updateSimPills() {
        const jenjangBtns = document.querySelectorAll('[aria-label="Filter Jenjang"] .pill-btn');
        jenjangBtns.forEach(btn => {
            const text = btn.innerText.toLowerCase();
            if (currentSimJenjang === 'semua' && text.includes('semua')) btn.classList.add('active');
            else if (currentSimJenjang === 'smk' && text.includes('smk')) btn.classList.add('active');
            else if (currentSimJenjang === 'mahasiswa' && text.includes('mahasiswa')) btn.classList.add('active');
            else btn.classList.remove('active');
        });

        const tipeBtns = document.querySelectorAll('[aria-label="Filter Tipe Permohonan"] .pill-btn');
        tipeBtns.forEach(btn => {
            const text = btn.innerText.toLowerCase();
            if (currentSimTipe === 'mandiri' && text.includes('mandiri')) btn.classList.add('active');
            else if (currentSimTipe === 'kelompok' && text.includes('kelompok')) btn.classList.add('active');
            else btn.classList.remove('active');
        });
    }

    function updateSimulatorState() {
        const bannerText = document.getElementById('simulatorBannerText');
        let text = '';

        if (currentSimJenjang === 'smk' && currentSimTipe === 'mandiri') {
            text = '<strong>Simulasi Siswa SMK (Mandiri):</strong> Pendaftaran perseorangan untuk Prakerin/PKL SMK. Berkas wajib: Surat Pengantar Sekolah berstempel resmi, CV siswa, dan Rapor semester terakhir (PDF maks. 2MB).';
        } else if (currentSimJenjang === 'smk' && currentSimTipe === 'kelompok') {
            text = '<strong>Simulasi Siswa SMK (Kelompok 2â€“5 Orang):</strong> Pendaftaran PKL beregu dari SMK yang sama. Ketua kelompok menginput data seluruh anggota + melampirkan Surat Pengantar kolektif dan Proposal Magang Tim.';
        } else if (currentSimJenjang === 'mahasiswa' && currentSimTipe === 'mandiri') {
            text = '<strong>Simulasi Mahasiswa D3/S1 (Mandiri):</strong> Magang riset/industri perseorangan. Berkas wajib: Surat Pengantar Fakultas/Jurusan, CV memuat portofolio proyek/keahlian, dan Transkrip Kumulatif resmi.';
        } else if (currentSimJenjang === 'mahasiswa' && currentSimTipe === 'kelompok') {
            text = '<strong>Simulasi Mahasiswa D3/S1 (Kelompok 2â€“5 Orang):</strong> Magang proyek tim atau riset kelompok. Wajib melampirkan Proposal Proyek Magang yang memuat pembagian peran anggota tim.';
        } else {
            text = '<strong>Peta Alur Penuh (Semua Jenjang):</strong> Menampilkan visualisasi siklus lengkap pendaftaran mandiri untuk seluruh formasi SMK dan Perguruan Tinggi di Diskominfo.';
        }

        bannerText.innerHTML = text;

        // Visual feedback on Node 3
        const node3 = document.getElementById('node-3');
        if (node3) {
            node3.classList.add('highlighted');
            setTimeout(() => node3.classList.remove('highlighted'), 1200);
        }
    }

    function selectDagNode(nodeId) {
        currentActiveNodeId = nodeId;

        // Deselect previous
        document.querySelectorAll('.svg-node-group, .dag-node-card, .dag-branch-pill').forEach(el => el.classList.remove('active'));

        // Highlight selected
        const selectedEl = document.getElementById(`node-${nodeId}`);
        if (selectedEl) {
            selectedEl.classList.add('active');
        }

        // Update step counter & stepper progress
        const mapping = nodeStepMapping[nodeId] || { stepNum: 1, label: 'Langkah 1 dari 7' };
        const counterEl = document.getElementById('dagStepCounter');
        if (counterEl) {
            counterEl.innerText = mapping.label;
        }

        const progressBar = document.getElementById('dagProgressBar');
        if (progressBar) {
            let pct = Math.round((mapping.stepNum / 7) * 100);
            if (nodeId === 'perbaikan') pct = 42; // Loopback indication
            progressBar.style.width = `${pct}%`;
        }

        // Update Stepper dots
        document.querySelectorAll('.stepper-node-dot').forEach(dot => {
            const dotStep = parseInt(dot.getAttribute('data-step'), 10);
            dot.classList.remove('active', 'completed');
            if (dotStep < mapping.stepNum) {
                dot.classList.add('completed');
            } else if (dotStep === mapping.stepNum) {
                dot.classList.add('active');
            }
        });

        // Update SVG Flow Pulses & Highlights
        const pulse12 = document.getElementById('edge-1-2-pulse');
        const pulse23 = document.getElementById('edge-2-3-pulse');
        const pulse34 = document.getElementById('edge-3-4-pulse');
        const pulse4Diterima = document.getElementById('edge-4-diterima-pulse');

        if (pulse12) pulse12.classList.toggle('active', mapping.stepNum >= 2);
        if (pulse23) pulse23.classList.toggle('active', mapping.stepNum >= 3);
        if (pulse34) pulse34.classList.toggle('active', mapping.stepNum >= 4);
        if (pulse4Diterima) pulse4Diterima.classList.toggle('active', (nodeId === 'diterima' || mapping.stepNum >= 5));

        // Update Loopback Edge & Target Highlight
        const loopbackRibbon = document.getElementById('loopbackRibbon');
        const loopbackTargetAnchor = document.getElementById('loopbackTargetAnchor');
        const loopbackEdge = document.getElementById('edge-loopback-flow');
        const loopbackBadge = document.getElementById('badgeLoopback');
        const node3 = document.getElementById('node-3');

        const isPerbaikan = (nodeId === 'perbaikan');
        if (loopbackRibbon) loopbackRibbon.classList.toggle('active', isPerbaikan);
        if (loopbackEdge) loopbackEdge.classList.toggle('active', isPerbaikan);
        if (loopbackBadge) loopbackBadge.classList.toggle('active', isPerbaikan);
        if (loopbackTargetAnchor) loopbackTargetAnchor.style.transform = isPerbaikan ? 'translateX(-50%) scale(1.6)' : 'translateX(-50%) scale(1)';

        if (node3) {
            node3.classList.toggle('loopback-target-active', isPerbaikan);
        }

        // Update Start & Finish Terminator Nodes
        const finishTerminator = document.getElementById('flow-node-finish');
        const edge7Finish = document.getElementById('edge-7-finish');

        if (finishTerminator) finishTerminator.classList.toggle('active', mapping.stepNum === 7);
        if (edge7Finish) edge7Finish.classList.toggle('active', mapping.stepNum >= 7);

        // Update Inspector Card
        const data = nodeData[nodeId];
        if (!data) return;

        const badge = document.getElementById('inspectorPhaseBadge');
        if (badge) {
            badge.innerText = data.phase;
            badge.style.color = data.phaseColor;
            badge.style.backgroundColor = data.phaseBg;
        }

        const titleEl = document.getElementById('inspectorTitle');
        if (titleEl) titleEl.innerText = data.title;

        const descEl = document.getElementById('inspectorDesc');
        if (descEl) descEl.innerText = data.desc;

        const actorEl = document.getElementById('inspectorActor');
        if (actorEl) actorEl.innerText = data.actor;

        const slaEl = document.getElementById('inspectorSLA');
        if (slaEl) slaEl.innerText = data.sla;

        const calloutEl = document.getElementById('inspectorCallout');
        if (calloutEl) calloutEl.innerHTML = data.callout;

        const ctaBtn = document.getElementById('inspectorCTA');
        if (ctaBtn) {
            const spanText = ctaBtn.querySelector('span');
            if (spanText) spanText.innerText = data.ctaText;
            ctaBtn.href = data.ctaUrl;
        }

        // Render checklist
        const listContainer = document.getElementById('inspectorChecklist');
        if (listContainer) {
            listContainer.innerHTML = '';
            data.checklist.forEach(item => {
                const li = document.createElement('li');
                li.className = 'inspector-check-item';
                li.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>${item}</span>
                `;
                listContainer.appendChild(li);
            });
        }

        // Smooth URL sync
        if (window.history && window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.set('step', nodeId);
            window.history.replaceState({}, '', url);
        }
    }

    // Zero-Scroll Auto-Fit & Responsive Zoom Engine
    let userZoomMultiplier = 1.0;
    const BASE_CANVAS_WIDTH = 1040;
    const BASE_CANVAS_HEIGHT = 500;

    function autoFitFlowchartCanvas() {
        const viewport = document.getElementById('flowchartViewport');
        const plane = document.getElementById('flowchartCanvasPlane');
        const stage = document.getElementById('flowchartStage');
        if (!viewport || !plane) return;

        // Ambil lebar kontainer viewport aktual (tanpa scrollbar)
        const availableWidth = viewport.clientWidth;
        if (availableWidth <= 0) return;

        // Auto scale proporsional terhadap base width 1040px
        const autoFitScale = Math.min(1.0, Math.max(0.40, availableWidth / BASE_CANVAS_WIDTH));
        const effectiveScale = parseFloat((autoFitScale * userZoomMultiplier).toFixed(3));

        plane.style.transform = `scale(${effectiveScale})`;
        plane.style.transformOrigin = 'top left';

        if (stage) {
            stage.style.height = `${Math.round(BASE_CANVAS_HEIGHT * effectiveScale)}px`;
            stage.style.width = '100%';
        }

        const zoomLabel = document.getElementById('zoomPercentLabel');
        if (zoomLabel) {
            const isAuto = (userZoomMultiplier === 1.0);
            zoomLabel.innerText = isAuto ? `${Math.round(effectiveScale * 100)}% (Auto)` : `${Math.round(effectiveScale * 100)}%`;
        }
    }

    function zoomCanvas(delta) {
        userZoomMultiplier = Math.min(1.6, Math.max(0.6, parseFloat((userZoomMultiplier + delta).toFixed(2))));
        autoFitFlowchartCanvas();
    }

    function resetCanvasZoom() {
        userZoomMultiplier = 1.0;
        autoFitFlowchartCanvas();
    }

    // Auto-Tour Player Functions
    function toggleAutoPlayTour() {
        if (isAutoPlaying) {
            stopAutoPlayTour();
        } else {
            startAutoPlayTour();
        }
    }

    function startAutoPlayTour() {
        isAutoPlaying = true;
        const playBtn = document.getElementById('btnPlayTour');
        if (playBtn) {
            playBtn.innerHTML = `
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                <span>Jeda Simulasi</span>
            `;
            playBtn.classList.remove('primary');
            playBtn.style.background = '#D97706';
            playBtn.style.borderColor = '#D97706';
            playBtn.style.color = '#ffffff';
        }
        runTourLoop();
    }

    function runTourLoop() {
        if (!isAutoPlaying) return;
        let currentIndex = tourSequence.indexOf(currentActiveNodeId);
        let nextIndex = (currentIndex + 1) % tourSequence.length;
        selectDagNode(tourSequence[nextIndex]);

        autoPlayTimer = setTimeout(() => {
            runTourLoop();
        }, 2800);
    }

    function stopAutoPlayTour() {
        isAutoPlaying = false;
        clearTimeout(autoPlayTimer);
        const playBtn = document.getElementById('btnPlayTour');
        if (playBtn) {
            playBtn.innerHTML = `
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                <span>Mulai Simulasi Alur</span>
            `;
            playBtn.classList.add('primary');
            playBtn.style.background = '';
            playBtn.style.borderColor = '';
            playBtn.style.color = '';
        }
    }

    function nextDagStep() {
        stopAutoPlayTour();
        let currentIndex = tourSequence.indexOf(currentActiveNodeId);
        let nextIndex = (currentIndex + 1) % tourSequence.length;
        selectDagNode(tourSequence[nextIndex]);
    }

    function prevDagStep() {
        stopAutoPlayTour();
        let currentIndex = tourSequence.indexOf(currentActiveNodeId);
        let prevIndex = (currentIndex - 1 + tourSequence.length) % tourSequence.length;
        selectDagNode(tourSequence[prevIndex]);
    }

    function resetDagTour() {
        stopAutoPlayTour();
        selectDagNode(1);
    }

    function switchMainView(mode) {
        const btnGraph = document.getElementById('btn-view-graph');
        const btnRoadmap = document.getElementById('btn-view-roadmap');
        const viewGraph = document.getElementById('viewGraphArea');
        const viewRoadmap = document.getElementById('viewRoadmapArea');

        if (mode === 'graph') {
            btnGraph.classList.add('active');
            btnRoadmap.classList.remove('active');
            viewGraph.style.display = 'grid';
            viewRoadmap.classList.remove('active');
            requestAnimationFrame(() => autoFitFlowchartCanvas());
        } else {
            btnRoadmap.classList.add('active');
            btnGraph.classList.remove('active');
            viewGraph.style.display = 'none';
            viewRoadmap.classList.add('active');
        }
    }

    function toggleFaq(btn) {
        const item = btn.closest('.faq-item');
        item.classList.toggle('open');
    }

    // Window & Viewport Resize Observers for Zero-Scroll
    window.addEventListener('resize', autoFitFlowchartCanvas);
    window.addEventListener('load', autoFitFlowchartCanvas);

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        // Panggil initial auto-fit layout
        autoFitFlowchartCanvas();

        const urlParams = new URLSearchParams(window.location.search);
        const stepParam = urlParams.get('step') || urlParams.get('fase');
        if (stepParam && nodeData[stepParam]) {
            selectDagNode(stepParam);
        } else {
            selectDagNode(1);
        }

        // Add keyboard Enter/Space support for milestone cards
        document.querySelectorAll('.journey-node-card, .decision-branch-card, .stepper-node-dot, .flow-terminator-pill').forEach(el => {
            el.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    el.click();
                }
            });
        });

        // Global Arrow key & Zoom navigation
        document.addEventListener('keydown', (e) => {
            if (['input', 'textarea', 'select'].includes(document.activeElement.tagName.toLowerCase())) {
                return;
            }
            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                e.preventDefault();
                nextDagStep();
            } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                e.preventDefault();
                prevDagStep();
            } else if (e.key === '+' || e.key === '=') {
                e.preventDefault();
                zoomCanvas(0.15);
            } else if (e.key === '-' || e.key === '_') {
                e.preventDefault();
                zoomCanvas(-0.15);
            } else if (e.key === '0') {
                e.preventDefault();
                resetCanvasZoom();
            }
        });
    });

    // Expose functions to window (Backward compatibility with selectNode)
    window.selectNode = selectDagNode;
    window.selectDagNode = selectDagNode;
    window.toggleAutoPlayTour = toggleAutoPlayTour;
    window.nextDagStep = nextDagStep;
    window.prevDagStep = prevDagStep;
    window.resetDagTour = resetDagTour;
    window.zoomCanvas = zoomCanvas;
    window.resetCanvasZoom = resetCanvasZoom;
    window.autoFitFlowchartCanvas = autoFitFlowchartCanvas;
    window.setSimJenjang = setSimJenjang;
    window.setSimTipe = setSimTipe;
    window.switchMainView = switchMainView;
    window.toggleFaq = toggleFaq;