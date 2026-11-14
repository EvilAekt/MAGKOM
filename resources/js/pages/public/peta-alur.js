    // Dataset 4 Tahapan Magang Sesuai Gambar 2 Referensi MAGKOM
    const stepData = {
        1: {
            num: "01",
            title: "REGISTRASI",
            desc: "Calon peserta membuat akun, mengisi data pribadi, dan menunggu akun diverifikasi admin.",
            numColor: "#e11d48",
            btnColor: "#e11d48",
            nextText: "Lanjut ke Pos 2 âž”"
        },
        2: {
            num: "02",
            title: "ADMINISTRASI & SELEKSI",
            desc: "Pilih bidang, unggah berkas, lalu admin memverifikasi dan menyeleksi dalam 1â€“3 hari kerja.",
            numColor: "#0284c7",
            btnColor: "#0284c7",
            nextText: "Lanjut ke Pos 3 âž”"
        },
        3: {
            num: "03",
            title: "PELAKSANAAN MAGANG",
            desc: "Kerjakan tugas, isi logbook harian, dan unggah progres sesuai durasi magang.",
            numColor: "#0ea5e9",
            btnColor: "#0284c7",
            nextText: "Lanjut ke Pos 4 âž”"
        },
        4: {
            num: "04",
            title: "SELESAI MAGANG",
            desc: "Penyelesaian program magang, pengumpulan laporan akhir, dan penetapan status kelulusan resmi.",
            numColor: "#f59e0b",
            btnColor: "#16a34a",
            nextText: "Daftar Sekarang 🚀"
        }
    };

    // Web Audio Synthesizer (Friendly & Melodic Sound FX)
    let audioCtx = null;
    let isAudioEnabled = false;

    function initAudioContext() {
        if (!audioCtx) {
            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (AudioContextClass) audioCtx = new AudioContextClass();
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
    }

    let bgmInterval = null;
    let bgmStep = 0;
    let engineOsc = null;
    let engineHarmonicOsc = null;
    let engineGain = null;
    let engineFilter = null;
    let isEngineHumming = false;

    // Nada-nada ramah bernuansa ceria Lo-Fi Sleman
    const BGM_MELODY = [
        261.63, 329.63, 392.00, 523.25, 440.00, 392.00, 329.63, 293.66,
        261.63, 392.00, 440.00, 523.25, 659.25, 523.25, 440.00, 392.00
    ];
    const BGM_BASS = [
        130.81, 130.81, 130.81, 130.81, // C3
        110.00, 110.00, 110.00, 110.00, // A2
        87.31,  87.31,  87.31,  87.31,  // F2
        98.00,  98.00,  98.00,  98.00   // G2
    ];

    const MagkomSound = {
        playClick: function() {
            if (!isAudioEnabled || !audioCtx) return;
            try {
                const now = audioCtx.currentTime;
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(540, now);
                osc.frequency.exponentialRampToValueAtTime(320, now + 0.04);
                gain.gain.setValueAtTime(0.08, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.04);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start(now);
                osc.stop(now + 0.045);
            } catch(e) {}
        },
        playDing: function() {
            if (!isAudioEnabled || !audioCtx) return;
            try {
                // Arpeggio Triad Mayor Lembut & Ramah (C5 - E5 - G5 Chime)
                const now = audioCtx.currentTime;
                const notes = [523.25, 659.25, 783.99];
                notes.forEach((freq, i) => {
                    const start = now + (i * 0.055);
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, start);
                    gain.gain.setValueAtTime(0.11, start);
                    gain.gain.exponentialRampToValueAtTime(0.001, start + 0.38);
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.start(start);
                    osc.stop(start + 0.4);
                });
            } catch(e) {}
        },
        playCheckpointDing: function() {
            if (!isAudioEnabled || !audioCtx) return;
            try {
                const now = audioCtx.currentTime;
                // Dual-tone high notification chime manis saat tiba di halte checkpoint (G5 783.99Hz & C6 1046.50Hz)
                [783.99, 1046.50].forEach((freq, idx) => {
                    const start = now + (idx * 0.08);
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, start);
                    gain.gain.setValueAtTime(0.12, start);
                    gain.gain.exponentialRampToValueAtTime(0.0005, start + 0.35);
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.start(start);
                    osc.stop(start + 0.38);
                });
            } catch(e) {}
        },
        playCarHorn: function() {
            if (!isAudioEnabled || !audioCtx) return;
            try {
                const now = audioCtx.currentTime;
                // Dual-Tone Klakson Mobil Ringan, Halus & Merdu (F4 349.23Hz & A4 440.00Hz - Triangle Waves)
                [349.23, 440.00].forEach((freq) => {
                    const osc = audioCtx.createOscillator();
                    const filter = audioCtx.createBiquadFilter();
                    const gain = audioCtx.createGain();

                    // Menggunakan triangle wave agar tidak cempreng/kasar
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(freq, now);

                    // Lowpass filter yang lebih hangat dan lembut (950Hz)
                    filter.type = 'lowpass';
                    filter.frequency.setValueAtTime(950, now);

                    // Gain lebih ringan & nyaman di telinga (0.038)
                    gain.gain.setValueAtTime(0.0001, now);
                    gain.gain.linearRampToValueAtTime(0.038, now + 0.04);
                    gain.gain.setValueAtTime(0.038, now + 0.20);
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.35);

                    osc.connect(filter);
                    filter.connect(gain);
                    gain.connect(audioCtx.destination);

                    osc.start(now);
                    osc.stop(now + 0.36);
                });
            } catch(e) {}
        },
        startEngineHum: function() {
            if (!isAudioEnabled || !audioCtx || isEngineHumming) return;
            try {
                const now = audioCtx.currentTime;
                // Suara Mesin Halus & Ringan (Triangle 72Hz + Harmonic Sine 144Hz)
                engineOsc = audioCtx.createOscillator();
                engineHarmonicOsc = audioCtx.createOscillator();
                engineFilter = audioCtx.createBiquadFilter();
                engineGain = audioCtx.createGain();

                // Gelombang triangle memberikan getaran mesin yang empuk dan halus
                engineOsc.type = 'triangle';
                engineOsc.frequency.setValueAtTime(72, now);
                engineOsc.frequency.linearRampToValueAtTime(86, now + 0.25);

                engineHarmonicOsc.type = 'sine';
                engineHarmonicOsc.frequency.setValueAtTime(144, now);
                engineHarmonicOsc.frequency.linearRampToValueAtTime(172, now + 0.25);

                // Lowpass filter hangat 220Hz untuk memotong frekuensi tajam
                engineFilter.type = 'lowpass';
                engineFilter.frequency.setValueAtTime(220, now);

                // Gain lembut dan ringan (0.028)
                engineGain.gain.setValueAtTime(0.0001, now);
                engineGain.gain.linearRampToValueAtTime(0.028, now + 0.14);

                engineOsc.connect(engineFilter);
                engineHarmonicOsc.connect(engineFilter);
                engineFilter.connect(engineGain);
                engineGain.connect(audioCtx.destination);

                engineOsc.start(now);
                engineHarmonicOsc.start(now);
                isEngineHumming = true;
            } catch (e) {}
        },
        stopEngineHum: function() {
            if (!isEngineHumming || !engineGain || !audioCtx) return;
            try {
                const now = audioCtx.currentTime;
                engineGain.gain.cancelScheduledValues(now);
                engineGain.gain.setValueAtTime(engineGain.gain.value, now);
                engineGain.gain.exponentialRampToValueAtTime(0.0001, now + 0.16);
                setTimeout(() => {
                    if (engineOsc) {
                        try { engineOsc.stop(); engineOsc.disconnect(); } catch (err) {}
                        engineOsc = null;
                    }
                    if (engineHarmonicOsc) {
                        try { engineHarmonicOsc.stop(); engineHarmonicOsc.disconnect(); } catch (err) {}
                        engineHarmonicOsc = null;
                    }
                    if (engineFilter) {
                        try { engineFilter.disconnect(); } catch (err) {}
                        engineFilter = null;
                    }
                    if (engineGain) {
                        try { engineGain.disconnect(); } catch (err) {}
                        engineGain = null;
                    }
                    isEngineHumming = false;
                }, 170);
            } catch (e) {
                isEngineHumming = false;
            }
        },
        playWhoosh: function() {
            if (!isAudioEnabled || !audioCtx) return;
            try {
                const now = audioCtx.currentTime;
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(160, now);
                osc.frequency.exponentialRampToValueAtTime(280, now + 0.18);
                osc.frequency.exponentialRampToValueAtTime(130, now + 0.38);
                gain.gain.setValueAtTime(0.04, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.38);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start(now);
                osc.stop(now + 0.4);
            } catch(e) {}
        },
        playFanfare: function() {
            if (!isAudioEnabled || !audioCtx) return;
            try {
                const now = audioCtx.currentTime;
                const notes = [523.25, 659.25, 783.99, 1046.50];
                notes.forEach((freq, i) => {
                    const start = now + (i * 0.085);
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, start);
                    gain.gain.setValueAtTime(0.12, start);
                    gain.gain.exponentialRampToValueAtTime(0.001, start + 0.45);
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.start(start);
                    osc.stop(start + 0.48);
                });
            } catch(e) {}
        },
        startAmbient: function() {
            if (bgmInterval || !audioCtx) return;
            bgmStep = 0;
            bgmInterval = setInterval(() => {
                if (!isAudioEnabled || !audioCtx) return;
                try {
                    const now = audioCtx.currentTime;
                    // Melody Chime Note (Sine Wave Hangat)
                    const mFreq = BGM_MELODY[bgmStep % BGM_MELODY.length];
                    const mOsc = audioCtx.createOscillator();
                    const mGain = audioCtx.createGain();
                    mOsc.type = 'sine';
                    mOsc.frequency.setValueAtTime(mFreq, now);
                    mGain.gain.setValueAtTime(0.026, now);
                    mGain.gain.exponentialRampToValueAtTime(0.0005, now + 0.34);
                    mOsc.connect(mGain);
                    mGain.connect(audioCtx.destination);
                    mOsc.start(now);
                    mOsc.stop(now + 0.36);

                    // Bass Warm Triangle (Tiap 2 Beat)
                    if (bgmStep % 2 === 0) {
                        const bFreq = BGM_BASS[Math.floor((bgmStep / 2) % BGM_BASS.length)];
                        const bOsc = audioCtx.createOscillator();
                        const bGain = audioCtx.createGain();
                        bOsc.type = 'triangle';
                        bOsc.frequency.setValueAtTime(bFreq, now);
                        bGain.gain.setValueAtTime(0.032, now);
                        bGain.gain.exponentialRampToValueAtTime(0.0005, now + 0.52);
                        bOsc.connect(bGain);
                        bGain.connect(audioCtx.destination);
                        bOsc.start(now);
                        bOsc.stop(now + 0.56);
                    }

                    bgmStep++;
                } catch (e) {}
            }, 290);
        },
        stopAmbient: function() {
            if (bgmInterval) {
                clearInterval(bgmInterval);
                bgmInterval = null;
            }
        }
    };

    window.toggleModalAudio = function() {
        initAudioContext();
        isAudioEnabled = !isAudioEnabled;
        const btn = document.getElementById('btnModalAudioToggle');
        const iconOn = document.getElementById('audioIconOn');
        const iconOff = document.getElementById('audioIconOff');
        const label = document.getElementById('audioToggleLabel');

        if (isAudioEnabled) {
            if (iconOn) iconOn.style.display = 'inline-block';
            if (iconOff) iconOff.style.display = 'none';
            if (label) label.textContent = 'Suara: On â™«';
            if (btn) btn.classList.add('is-active');
            MagkomSound.playDing();
            MagkomSound.startAmbient();
        } else {
            if (iconOn) iconOn.style.display = 'none';
            if (iconOff) iconOff.style.display = 'inline-block';
            if (label) label.textContent = 'Suara: Off';
            if (btn) btn.classList.remove('is-active');
            MagkomSound.stopAmbient();
            MagkomSound.stopEngineHum();
        }
    };

    // State Aplikasi Game 2D Side-Scrolling
    let currentStep = 1;

    // Koordinat Dunia 2D & Lintasan Aspal Mendatar (2600 x 540)
    const ROAD_MIN_X = 140;
    const ROAD_MAX_X = 2360;
    const ROAD_CAR_Y = 390;
    const STEP_X_COORDS = { 1: 280, 2: 880, 3: 1480, 4: 2080 };

    let carX = 280;
    let carVelocity = 0;
    let wheelAngle = 0;
    let isCarAnimatingToTarget = false;
    let isDirectDraggingCar = false;
    let isAutoLooping = false;
    let carTargetAnim = null;

    // Fungsi Reset Otomatis Mobil Saat Mencapai Ujung Jalan (Auto Loop Cycle)
    function triggerAutoLoopReset() {
        if (isAutoLooping) return;
        isAutoLooping = true;

        if (carTargetAnim) {
            cancelAnimationFrame(carTargetAnim);
            isCarAnimatingToTarget = false;
        }

        MagkomSound.stopEngineHum();
        MagkomSound.playFanfare();

        const carGroup = document.getElementById('modalRoadCarGroup');
        if (carGroup) {
            carGroup.style.transition = 'opacity 0.35s ease';
            carGroup.style.opacity = '0';
        }

        setTimeout(() => {
            carX = STEP_X_COORDS[1] || 280;
            currentStep = 1;
            wheelAngle = 0;
            targetCamX = 0;
            curCam.x = 0;

            updateCarVisual(carX);
            updateCharacterScene(1);
            updateWebtoonBubbles(1);

            const svg = document.getElementById('modalRoadCanvas');
            if (svg) {
                svg.setAttribute('viewBox', `${curCam.x.toFixed(1)} ${curCam.y.toFixed(1)} ${curCam.w.toFixed(1)} ${curCam.h.toFixed(1)}`);
            }

            if (carGroup) {
                carGroup.style.opacity = '1';
                setTimeout(() => {
                    carGroup.style.transition = '';
                    isAutoLooping = false;
                }, 350);
            } else {
                isAutoLooping = false;
            }
        }, 400);
    }

    // Kamera 2D Side-Scrolling (Ukuran Layar 960 x 540, Kamera Bergeser 0 s/d 1440)
    const baseViewBox = { x: 0, y: 0, w: 960, h: 540 };
    let curCam = { x: 0, y: 0, w: 960, h: 540 };
    let targetCamX = 0;

    // Kontrol Input: Joystick Analog & Keyboard
    let joystickAxisX = 0;
    let isAnalogActive = false;
    const keysDown = { left: false, right: false };

    // Inisialisasi Game Engine saat Halaman Siap
    function init2DGameEngine() {
        const svg = document.getElementById('modalRoadCanvas');
        if (!svg) return;

        // Posisikan Halte Checkpoint di Pinggir Jalan (Trotoar: y = 310)
        Object.keys(STEP_X_COORDS).forEach(stepNum => {
            const cpGroup = document.getElementById('roadCp' + stepNum);
            if (cpGroup) {
                cpGroup.setAttribute('transform', `translate(${STEP_X_COORDS[stepNum]}, 310)`);
            }
        });

        // Set Posisi Awal Mobil & Karakter
        updateCarVisual(carX);
        updateCharacterScene(1);
        updateWebtoonBubbles(1);

        // Pasang Kontrol Input
        initAnalogJoystick();
        initKeyboardControls();
        initRoadClickAndDrag();

        // Mulai Game Loop 60FPS
        requestAnimationFrame(gameLoop);
    }

    // Tampilkan Webtoon Speech Bubble untuk Pos yang Aktif
    function updateWebtoonBubbles(stepNum) {
        [1, 2, 3, 4].forEach(s => {
            const bubble = document.getElementById('webtoonBubble' + s);
            if (bubble) {
                if (s === stepNum) {
                    bubble.classList.add('is-active');
                } else {
                    bubble.classList.remove('is-active');
                }
            }
        });
    }

    // Game Loop 60 FPS
    function gameLoop(timestamp) {
        let inputDirection = 0;
        if (isAnalogActive) {
            inputDirection = joystickAxisX;
        } else if (keysDown.left && !keysDown.right) {
            inputDirection = -1;
        } else if (keysDown.right && !keysDown.left) {
            inputDirection = 1;
        }

        if (inputDirection !== 0) {
            if (isCarAnimatingToTarget && carTargetAnim) {
                cancelAnimationFrame(carTargetAnim);
                isCarAnimatingToTarget = false;
            }

            MagkomSound.startEngineHum();

            const driveSpeed = 6.5;
            carVelocity = inputDirection * driveSpeed;

            carX += carVelocity;
            carX = Math.max(ROAD_MIN_X, Math.min(ROAD_MAX_X, carX));

            const wheelRadius = 14;
            wheelAngle += (carVelocity / wheelRadius) * (180 / Math.PI);

            updateCarVisual(carX);

            const nearest = getNearestStep(carX);
            if (nearest !== currentStep) {
                currentStep = nearest;
                updateCharacterScene(nearest);
                updateWebtoonBubbles(nearest);
                MagkomSound.playCheckpointDing();
            }
        } else {
            if (!isCarAnimatingToTarget && !isDirectDraggingCar) {
                MagkomSound.stopEngineHum();
            }
        }

        // Auto-loop reset jika mobil mencapai ujung pinggir jalan perkantoran timur
        if (carX >= 2320 && !isAutoLooping) {
            triggerAutoLoopReset();
        }

        // Camera Follow Mengikuti Mobil Secara Mulus
        targetCamX = Math.max(0, Math.min(1560, carX - 420));
        curCam.x += (targetCamX - curCam.x) * 0.1;

        const svg = document.getElementById('modalRoadCanvas');
        if (svg) {
            svg.setAttribute('viewBox', `${curCam.x.toFixed(1)} ${curCam.y.toFixed(1)} ${curCam.w.toFixed(1)} ${curCam.h.toFixed(1)}`);
        }

        requestAnimationFrame(gameLoop);
    }

    // Update Transform Mobil, Roda, dan Garis Progress Aspal
    function updateCarVisual(posX) {
        const carGroup = document.getElementById('modalRoadCarGroup');
        const wheelRear = document.getElementById('carWheelRear');
        const wheelFront = document.getElementById('carWheelFront');
        const roadProgress = document.getElementById('svgRoadProgress');

        if (carGroup) {
            carGroup.setAttribute('transform', `translate(${posX.toFixed(1)}, ${ROAD_CAR_Y})`);
        }

        if (wheelRear) {
            wheelRear.setAttribute('transform', `translate(-42, 21) rotate(${wheelAngle.toFixed(1)})`);
        }
        if (wheelFront) {
            wheelFront.setAttribute('transform', `translate(42, 21) rotate(${wheelAngle.toFixed(1)})`);
        }

        if (roadProgress) {
            roadProgress.setAttribute('x2', posX.toFixed(1));
        }
    }

    // Deteksi Checkpoint Berdasarkan Koordinat X Mobil
    function getNearestStep(posX) {
        let bestStep = 1;
        let minDiff = Infinity;
        Object.keys(STEP_X_COORDS).forEach(s => {
            const diff = Math.abs(STEP_X_COORDS[s] - posX);
            if (diff < minDiff) {
                minDiff = diff;
                bestStep = parseInt(s);
            }
        });
        return bestStep;
    }

    // =========================================================================
    // VIRTUAL ANALOG JOYSTICK ENGINE (SUPER RESPONSIVE: DRAG, TAP, & HOLD)
    // =========================================================================
    function initAnalogJoystick() {
        const base = document.getElementById('analogBase');
        const thumb = document.getElementById('analogThumb');
        const btnRight = document.getElementById('btnSteerRight');
        const btnLeft = document.getElementById('btnSteerLeft');
        if (!base || !thumb) return;

        const maxTravel = 38;
        let isThumbDragging = false;
        let dragPointerId = null;
        let thumbStartClientX = 0;

        function setJoystickDirection(normVal) {
            normVal = Math.max(-1, Math.min(1, normVal));
            joystickAxisX = normVal;
            isAnalogActive = (normVal !== 0);
            const dx = normVal * maxTravel;
            thumb.style.transition = isThumbDragging ? 'none' : 'transform 0.18s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            thumb.style.transform = `translateX(${dx.toFixed(1)}px)`;
            if (isAnalogActive) {
                base.classList.add('is-active');
            } else {
                base.classList.remove('is-active');
            }
        }

        // 1. Tombol Panah Kanan (Maju / Pos Berikutnya)
        if (btnRight) {
            let rightPressTime = 0;
            let rightHoldInterval = null;

            function onRightDown(e) {
                e.preventDefault();
                e.stopPropagation();
                initAudioContext();
                rightPressTime = Date.now();
                btnRight.classList.add('is-pressed');
                setJoystickDirection(1);

                // Pastikan mobil langsung melaju saat ditahan
                rightHoldInterval = setInterval(() => {
                    setJoystickDirection(1);
                }, 100);
            }

            function onRightUp(e) {
                if (!rightPressTime) return;
                e.preventDefault();
                e.stopPropagation();
                if (rightHoldInterval) {
                    clearInterval(rightHoldInterval);
                    rightHoldInterval = null;
                }
                btnRight.classList.remove('is-pressed');
                const duration = Date.now() - rightPressTime;
                rightPressTime = 0;

                setJoystickDirection(0);
                // Jika hanya tap/klik cepat (< 280ms), langsung meluncur ke Pos berikutnya
                if (duration < 280) {
                    window.navigateModalStep(1);
                }
            }

            btnRight.addEventListener('pointerdown', onRightDown);
            btnRight.addEventListener('pointerup', onRightUp);
            btnRight.addEventListener('pointercancel', onRightUp);
            btnRight.addEventListener('pointerleave', onRightUp);
        }

        // 2. Tombol Panah Kiri (Mundur / Pos Sebelumnya)
        if (btnLeft) {
            let leftPressTime = 0;
            let leftHoldInterval = null;

            function onLeftDown(e) {
                e.preventDefault();
                e.stopPropagation();
                initAudioContext();
                leftPressTime = Date.now();
                btnLeft.classList.add('is-pressed');
                setJoystickDirection(-1);

                leftHoldInterval = setInterval(() => {
                    setJoystickDirection(-1);
                }, 100);
            }

            function onLeftUp(e) {
                if (!leftPressTime) return;
                e.preventDefault();
                e.stopPropagation();
                if (leftHoldInterval) {
                    clearInterval(leftHoldInterval);
                    leftHoldInterval = null;
                }
                btnLeft.classList.remove('is-pressed');
                const duration = Date.now() - leftPressTime;
                leftPressTime = 0;

                setJoystickDirection(0);
                // Jika hanya tap/klik cepat (< 280ms), langsung meluncur ke Pos sebelumnya
                if (duration < 280) {
                    window.navigateModalStep(-1);
                }
            }

            btnLeft.addEventListener('pointerdown', onLeftDown);
            btnLeft.addEventListener('pointerup', onLeftUp);
            btnLeft.addEventListener('pointercancel', onLeftUp);
            btnLeft.addEventListener('pointerleave', onLeftUp);
        }

        // 3. Tuas Bundar Analog (Drag Kiri/Kanan dengan Pointer Capture)
        function onThumbDown(e) {
            e.preventDefault();
            e.stopPropagation();
            initAudioContext();
            isThumbDragging = true;
            dragPointerId = e.pointerId;
            try {
                thumb.setPointerCapture(e.pointerId);
            } catch (err) {}
            thumbStartClientX = e.clientX;
        }

        function onThumbMove(e) {
            if (!isThumbDragging) return;
            const dx = e.clientX - thumbStartClientX;
            const norm = Math.max(-1, Math.min(1, dx / maxTravel));
            setJoystickDirection(norm);
        }

        function onThumbUp(e) {
            if (!isThumbDragging) return;
            isThumbDragging = false;
            if (dragPointerId !== null) {
                try {
                    thumb.releasePointerCapture(dragPointerId);
                } catch (err) {}
                dragPointerId = null;
            }
            setJoystickDirection(0);
        }

        thumb.addEventListener('pointerdown', onThumbDown);
        thumb.addEventListener('pointermove', onThumbMove);
        thumb.addEventListener('pointerup', onThumbUp);
        thumb.addEventListener('pointercancel', onThumbUp);

        // 4. Klik langsung di area kapsul track base
        base.addEventListener('pointerdown', function(e) {
            if (e.target === thumb || e.target === btnLeft || e.target === btnRight || btnLeft.contains(e.target) || btnRight.contains(e.target)) return;
            const bRect = base.getBoundingClientRect();
            const centerX = bRect.left + (bRect.width / 2);
            if (e.clientX > centerX + 10) {
                window.navigateModalStep(1);
            } else if (e.clientX < centerX - 10) {
                window.navigateModalStep(-1);
            }
        });
    }

    // =========================================================================
    // FUNGSI KLAKSON MOBIL (TRIGGER DARI TOMBOL ATAS / KEYBOARD ARROW UP / W)
    // =========================================================================
    window.triggerCarHorn = function() {
        initAudioContext();
        MagkomSound.playCarHorn();
        const hornBtn = document.getElementById('btnSteerHorn');
        if (hornBtn) {
            hornBtn.classList.add('is-pressed');
            setTimeout(() => hornBtn.classList.remove('is-pressed'), 350);
        }
        const ripples = document.getElementById('carHornRipples');
        const headlight = document.getElementById('carHeadlightBeam');
        if (ripples) {
            ripples.classList.remove('active-horn');
            void ripples.offsetWidth;
            ripples.classList.add('active-horn');
        }
        if (headlight) {
            headlight.classList.add('horn-flash');
            setTimeout(() => headlight.classList.remove('horn-flash'), 380);
        }
    };

    // =========================================================================
    // KEYBOARD INPUT CONTROLS (PANAH â† / â†’, ATAS â†‘ UNTUK KLAKSON & TOMBOL A / D / W)
    // =========================================================================
    function initKeyboardControls() {
        const thumb = document.getElementById('analogThumb');
        window.addEventListener('keydown', function(e) {
            if (e.target && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA')) return;

            if (e.key === 'ArrowUp' || e.code === 'KeyW' || e.code === 'Space') {
                e.preventDefault();
                window.triggerCarHorn();
            } else if (e.key === 'ArrowRight' || e.code === 'KeyD') {
                e.preventDefault();
                initAudioContext();
                keysDown.right = true;
                if (thumb && !isAnalogActive) {
                    thumb.style.transition = 'transform 0.1s ease-out';
                    thumb.style.transform = 'translateX(24px)';
                }
            } else if (e.key === 'ArrowLeft' || e.code === 'KeyA') {
                e.preventDefault();
                initAudioContext();
                keysDown.left = true;
                if (thumb && !isAnalogActive) {
                    thumb.style.transition = 'transform 0.1s ease-out';
                    thumb.style.transform = 'translateX(-24px)';
                }
            } else if (['1', '2', '3', '4'].includes(e.key)) {
                window.selectModalStep(parseInt(e.key));
            }
        });

        window.addEventListener('keyup', function(e) {
            if (e.key === 'ArrowRight' || e.code === 'KeyD') {
                keysDown.right = false;
                if (thumb && !isAnalogActive && !keysDown.left) {
                    thumb.style.transition = 'transform 0.18s ease-out';
                    thumb.style.transform = 'translateX(0px)';
                }
            } else if (e.key === 'ArrowLeft' || e.code === 'KeyA') {
                keysDown.left = false;
                if (thumb && !isAnalogActive && !keysDown.right) {
                    thumb.style.transition = 'transform 0.18s ease-out';
                    thumb.style.transform = 'translateX(0px)';
                }
            }
        });
    }

    // =========================================================================
    // KLIK PADA ASPAL & DRAGGING LANGSUNG MOBIL
    // =========================================================================
    function initRoadClickAndDrag() {
        const svg = document.getElementById('modalRoadCanvas');
        const roadBase = document.getElementById('svgRoadBase');
        const carGroup = document.getElementById('modalRoadCarGroup');
        if (!svg || !roadBase || !carGroup) return;

        function getSvgCoordinates(evt) {
            const pt = svg.createSVGPoint();
            const clientX = evt.touches && evt.touches.length > 0 ? evt.touches[0].clientX : evt.clientX;
            const clientY = evt.touches && evt.touches.length > 0 ? evt.touches[0].clientY : evt.clientY;
            pt.x = clientX;
            pt.y = clientY;
            const ctm = svg.getScreenCTM();
            if (ctm) {
                return pt.matrixTransform(ctm.inverse());
            }
            return { x: clientX, y: clientY };
        }

        roadBase.addEventListener('click', function(e) {
            initAudioContext();
            const pt = getSvgCoordinates(e);
            driveCarToX(pt.x);
        });

        isDirectDraggingCar = false;
        function onCarDragStart(e) {
            e.stopPropagation();
            isDirectDraggingCar = true;
            carGroup.classList.add('is-dragging');
            initAudioContext();
            MagkomSound.playClick();
            MagkomSound.startEngineHum();
        }

        function onCarDragMove(e) {
            if (!isDirectDraggingCar) return;
            const pt = getSvgCoordinates(e);
            const dx = pt.x - carX;
            carX = Math.max(ROAD_MIN_X, Math.min(ROAD_MAX_X, pt.x));
            wheelAngle += (dx / 14) * (180 / Math.PI);
            updateCarVisual(carX);

            const nearest = getNearestStep(carX);
            if (nearest !== currentStep) {
                currentStep = nearest;
                updateCharacterScene(nearest);
                updateWebtoonBubbles(nearest);
                MagkomSound.playCheckpointDing();
            }
        }

        function onCarDragEnd() {
            if (!isDirectDraggingCar) return;
            isDirectDraggingCar = false;
            carGroup.classList.remove('is-dragging');
            MagkomSound.stopEngineHum();
            const nearest = getNearestStep(carX);
            window.selectModalStep(nearest);
        }

        carGroup.addEventListener('mousedown', onCarDragStart);
        window.addEventListener('mousemove', onCarDragMove);
        window.addEventListener('mouseup', onCarDragEnd);

        carGroup.addEventListener('touchstart', onCarDragStart, { passive: true });
        window.addEventListener('touchmove', onCarDragMove, { passive: true });
        window.addEventListener('touchend', onCarDragEnd);
    }

    // Melajukan Mobil Secara Halus ke Koordinat Target
    function driveCarToX(targetX, onComplete) {
        if (isCarAnimatingToTarget && carTargetAnim) {
            cancelAnimationFrame(carTargetAnim);
        }

        targetX = Math.max(ROAD_MIN_X, Math.min(ROAD_MAX_X, targetX));
        const startX = carX;
        const dist = targetX - startX;
        if (Math.abs(dist) < 2) {
            if (typeof onComplete === 'function') onComplete();
            return;
        }

        isCarAnimatingToTarget = true;
        const startTime = performance.now();
        const duration = Math.min(850, Math.max(400, Math.abs(dist) * 0.7));

        MagkomSound.playWhoosh();
        MagkomSound.startEngineHum();

        function stepTween(now) {
            const elapsed = now - startTime;
            const p = Math.min(1, elapsed / duration);
            const ease = p < 0.5 ? 4 * p * p * p : 1 - Math.pow(-2 * p + 2, 3) / 2;

            const prevX = carX;
            carX = startX + (dist * ease);
            const deltaX = carX - prevX;
            wheelAngle += (deltaX / 14) * (180 / Math.PI);

            updateCarVisual(carX);

            if (p < 1 && isCarAnimatingToTarget) {
                carTargetAnim = requestAnimationFrame(stepTween);
            } else {
                isCarAnimatingToTarget = false;
                carX = targetX;
                updateCarVisual(carX);
                MagkomSound.stopEngineHum();

                const activeStep = getNearestStep(carX);
                updateWebtoonBubbles(activeStep);
                MagkomSound.playCheckpointDing();
                if (activeStep === 4) {
                    MagkomSound.playFanfare();
                }
                if (typeof onComplete === 'function') onComplete();
            }
        }

        carTargetAnim = requestAnimationFrame(stepTween);
    }

    function animateCarToStep(targetStep) {
        const targetX = STEP_X_COORDS[targetStep] || 280;
        // Tutup speech bubble selama perjalanan mobil agar animasi pergerakan bersih dan dinamis
        updateWebtoonBubbles(0);

        driveCarToX(targetX, () => {
            currentStep = targetStep;
            updateCharacterScene(targetStep);
            updateWebtoonBubbles(targetStep);
        });
    }

    function updateCharacterScene(stepNum) {
        [1, 2, 3, 4].forEach(s => {
            const charScene = document.getElementById('charScene' + s);
            if (charScene) {
                charScene.style.display = (s === stepNum) ? 'block' : 'none';
            }
        });
    }

    window.selectModalStep = function(stepNum) {
        stepNum = parseInt(stepNum, 10);
        if (isNaN(stepNum) || stepNum < 1 || stepNum > 4) return;
        initAudioContext();

        const targetX = STEP_X_COORDS[stepNum] || 280;
        // Jika mobil sudah berada di pos tersebut (selisih < 10px), langsung pastikan bubble dan karakter aktif
        if (Math.abs(carX - targetX) < 10) {
            currentStep = stepNum;
            updateCharacterScene(stepNum);
            updateWebtoonBubbles(stepNum);
            return;
        }

        animateCarToStep(stepNum);
    };

    window.navigateModalStep = function(direction) {
        const nextStep = currentStep + direction;
        if (nextStep >= 1 && nextStep <= 4) {
            window.selectModalStep(nextStep);
        } else if (nextStep > 4) {
            triggerAutoLoopReset();
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init2DGameEngine);
    } else {
        init2DGameEngine();
    }