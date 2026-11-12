@props([
    'variant' => 'login',
])

@php
    $isRegister = ($variant === 'register');
    $sectionStyle = $isRegister
        ? 'flex: 1 1 480px; min-height: 560px; max-height: 800px; position: relative; overflow: hidden; box-sizing: border-box; padding: 32px; display: flex; flex-direction: column; justify-content: space-between; background: linear-gradient(155deg, #3F51B5 0%, #01347C 100%); color: #FFFFFF; width: 583px'
        : 'flex: 1 1 480px; min-height: 560px; position: relative; overflow: hidden; box-sizing: border-box; padding: 32px; display: flex; flex-direction: column; justify-content: space-between; background: linear-gradient(155deg, #3F51B5 0%, #01347C 100%); color: #FFFFFF; width: 583px';
@endphp

<section style="{{ $sectionStyle }}">

  <svg aria-hidden="true" viewBox="0 0 560 860" preserveAspectRatio="xMidYMid slice" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0">
    <circle class="ring ring-a rot" cx="280" cy="370" r="112"></circle>
    <circle class="ring ring-b rot rot-r" cx="280" cy="370" r="215"></circle>

    <g class="sweep">
      <path class="sw1" d="M280 370L132 194A230 230 0 0 1 280 140Z"></path>
      <path class="sw2" d="M280 370L217 149A230 230 0 0 1 280 140Z"></path>
      <path class="sw3" d="M280 370L280 140"></path>
    </g>

    <g>
      <path class="lk" d="M280 370L390 370"></path>
      <path class="lk" d="M280 370L335 465"></path>
      <path class="lk" d="M280 370L225 465"></path>
      <path class="lk" d="M280 370L170 370"></path>
      <path class="lk" d="M280 370L225 275"></path>
      <path class="lk" d="M280 370L335 275"></path>
      <path class="lk" d="M390 370L335 465"></path>
      <path class="lk" d="M335 465L225 465"></path>
      <path class="lk" d="M225 465L170 370"></path>
      <path class="lk" d="M170 370L225 275"></path>
      <path class="lk" d="M225 275L335 275"></path>
      <path class="lk" d="M335 275L390 370"></path>
      <path class="lk" d="M390 370L505 330"></path>
      <path class="lk" d="M390 370L490 470"></path>
      <path class="lk" d="M335 465L490 470"></path>
      <path class="lk" d="M335 465L395 575"></path>
      <path class="lk" d="M225 465L170 585"></path>
      <path class="lk" d="M170 370L65 480"></path>
      <path class="lk" d="M170 370L50 330"></path>
      <path class="lk" d="M225 275L110 190"></path>
      <path class="lk" d="M225 275L290 150"></path>
      <path class="lk" d="M335 275L290 150"></path>
      <path class="lk" d="M335 275L460 200"></path>
      <path class="lk" d="M390 370L460 200"></path>
      <path class="lk" d="M505 330L490 470"></path>
      <path class="lk" d="M490 470L395 575"></path>
      <path class="lk" d="M395 575L170 585"></path>
      <path class="lk" d="M170 585L65 480"></path>
      <path class="lk" d="M65 480L50 330"></path>
      <path class="lk" d="M50 330L110 190"></path>
      <path class="lk" d="M110 190L290 150"></path>
      <path class="lk" d="M290 150L460 200"></path>
      <path class="lk" d="M460 200L505 330"></path>
      <path class="lk" d="M50 330L30 200"></path>
      <path class="lk" d="M460 200L535 120"></path>
      <path class="lk" d="M395 575L525 590"></path>
    </g>

    <g>
      <path class="pk pk-y" d="M280 370L390 370" style="animation-delay: 0s"></path>
      <path class="pk pk-y" d="M280 370L335 465" style="animation-delay: -1.1s"></path>
      <path class="pk pk-y" d="M280 370L225 465" style="animation-delay: -2s"></path>
      <path class="pk pk-y" d="M280 370L170 370" style="animation-delay: -0.6s"></path>
      <path class="pk pk-y" d="M280 370L225 275" style="animation-delay: -1.7s"></path>
      <path class="pk pk-y" d="M280 370L335 275" style="animation-delay: -2.4s"></path>

      <path class="pk pk-w" d="M505 330L390 370" style="animation-delay: -0.3s"></path>
      <path class="pk pk-w" d="M490 470L335 465" style="animation-delay: -1.4s"></path>
      <path class="pk pk-w" d="M395 575L335 465" style="animation-delay: -2.6s"></path>
      <path class="pk pk-w" d="M170 585L225 465" style="animation-delay: -0.9s"></path>
      <path class="pk pk-w" d="M65 480L170 370" style="animation-delay: -2.1s"></path>
      <path class="pk pk-w" d="M110 190L225 275" style="animation-delay: -1.8s"></path>
      <path class="pk pk-w" d="M290 150L335 275" style="animation-delay: -0.5s"></path>
      <path class="pk pk-w" d="M460 200L335 275" style="animation-delay: -2.9s"></path>

      <path class="pk pk-s" d="M460 200L505 330" style="animation-delay: -1s"></path>
      <path class="pk pk-s" d="M490 470L395 575" style="animation-delay: -3s"></path>
      <path class="pk pk-s" d="M50 330L110 190" style="animation-delay: -2s"></path>
      <path class="pk pk-s" d="M290 150L110 190" style="animation-delay: -4s"></path>
      <path class="pk pk-s" d="M395 575L170 585" style="animation-delay: -0.5s"></path>
    </g>

    <g>
      <circle class="nd tw" cx="505" cy="330" r="3.6" style="animation-delay: -0.4s"></circle>
      <circle class="nd tw" cx="490" cy="470" r="3.6" style="animation-delay: -1.6s"></circle>
      <circle class="nd tw" cx="395" cy="575" r="3.6" style="animation-delay: -2.8s"></circle>
      <circle class="nd tw" cx="170" cy="585" r="3.6" style="animation-delay: -0.9s"></circle>
      <circle class="nd tw" cx="65" cy="480" r="3.6" style="animation-delay: -2.2s"></circle>
      <circle class="nd tw" cx="50" cy="330" r="3.6" style="animation-delay: -3.3s"></circle>
      <circle class="nd tw" cx="110" cy="190" r="3.6" style="animation-delay: -1.2s"></circle>
      <circle class="nd tw" cx="290" cy="150" r="3.6" style="animation-delay: -2.5s"></circle>
      <circle class="nd tw" cx="460" cy="200" r="3.6" style="animation-delay: -0.2s"></circle>
      <circle class="nd" cx="30" cy="200" r="2.2" style="fill: #9DB9E6; opacity: 0.6"></circle>
      <circle class="nd" cx="535" cy="120" r="2.2" style="fill: #9DB9E6; opacity: 0.6"></circle>
      <circle class="nd" cx="525" cy="590" r="2.2" style="fill: #9DB9E6; opacity: 0.6"></circle>

      <circle class="hl" cx="390" cy="370" r="6" style="animation-delay: -0.5s"></circle>
      <circle class="hl" cx="335" cy="465" r="6" style="animation-delay: -1.1s"></circle>
      <circle class="hl" cx="225" cy="465" r="6" style="animation-delay: -1.7s"></circle>
      <circle class="hl" cx="170" cy="370" r="6" style="animation-delay: -2.3s"></circle>
      <circle class="hl" cx="225" cy="275" r="6" style="animation-delay: -2.9s"></circle>
      <circle class="hl" cx="335" cy="275" r="6" style="animation-delay: -3.3s"></circle>
      <circle class="nd" cx="390" cy="370" r="5.5"></circle>
      <circle class="nd" cx="335" cy="465" r="5.5"></circle>
      <circle class="nd" cx="225" cy="465" r="5.5"></circle>
      <circle class="nd" cx="170" cy="370" r="5.5"></circle>
      <circle class="nd" cx="225" cy="275" r="5.5"></circle>
      <circle class="nd" cx="335" cy="275" r="5.5"></circle>
    </g>

    <g>
      <text class="lbl" x="408" y="374">Tugas</text>
      <text class="lbl" x="343" y="487">Logbook</text>
      <text class="lbl" x="217" y="487" text-anchor="end">Kuota</text>
      <text class="lbl" x="152" y="374" text-anchor="end">Peserta</text>
      <text class="lbl" x="217" y="262" text-anchor="end">Pembimbing</text>
      <text class="lbl" x="343" y="262">Admin</text>
    </g>

    <g>
      <circle class="rp" cx="280" cy="370" r="14" style="animation-delay: 0s"></circle>
      <circle class="rp" cx="280" cy="370" r="14" style="animation-delay: -1.4s"></circle>
      <circle class="rp" cx="280" cy="370" r="14" style="animation-delay: -2.8s"></circle>
      <circle cx="280" cy="370" r="22" style="fill: none; stroke: rgba(255,195,0,.4); stroke-width: 1"></circle>
      <circle cx="280" cy="370" r="13" style="fill: #FFC300"></circle>
      <circle cx="280" cy="370" r="5" style="fill: #003566"></circle>
    </g>
  </svg>

  <div style="position: relative; z-index: 1; display: flex; align-items: center; gap: 14px">
    <img src="{{ asset('images/logo-sleman.png') }}" alt="Lambang Kabupaten Sleman" style="height: 52px; width: auto; display: block; border-radius: 4px; flex-shrink: 0">
    <div style="display: flex; flex-direction: column; gap: 4px">
      <span style="font-size: 20px; font-weight: 800; letter-spacing: 0.08em; line-height: 1">MAGKOM</span>
      <span style="font-size: 11px; font-weight: 700; letter-spacing: 0.06em; color: #FFC300; line-height: 1.2">DINAS KOMINFO KAB. SLEMAN</span>
      <span class="sr-only" style="display: none">MAGANG KOMINFO SLEMAN - Magang Kominfo Sleman</span>
    </div>
  </div>

  <div style="position: relative; z-index: 1; width: 100%; box-sizing: border-box; padding: 0 8px 24px; display: flex; flex-direction: column; gap: 22px; align-items: center">
    <div style="width: 100%; max-width: 440px; overflow: hidden">
      <div class="slides">
        <div style="width: 25%; flex-shrink: 0; box-sizing: border-box; padding: 0 12px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 12px">
          <h2 style="margin: 0; font-size: 30px; line-height: 1.2; font-weight: 800; letter-spacing: -0.01em">Kelola magang lebih terarah</h2>
          <p style="margin: 0; max-width: 380px; font-size: 15px; line-height: 1.6; color: rgba(255,255,255,.85)">Daftar, kerjakan tugas, dan kirim logbook dalam satu platform terpadu Dinas Kominfo Sleman.</p>
        </div>
        <div style="width: 25%; flex-shrink: 0; box-sizing: border-box; padding: 0 12px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 12px">
          <h2 style="margin: 0; font-size: 30px; line-height: 1.2; font-weight: 800; letter-spacing: -0.01em">Logbook mingguan lebih rapi</h2>
          <p style="margin: 0; max-width: 380px; font-size: 15px; line-height: 1.6; color: rgba(255,255,255,.85)">Catat kegiatan setiap minggu, kirim ke pembimbing, dan lihat statusnya langsung dari satu tempat.</p>
        </div>
        <div style="width: 25%; flex-shrink: 0; box-sizing: border-box; padding: 0 12px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 12px">
          <h2 style="margin: 0; font-size: 30px; line-height: 1.2; font-weight: 800; letter-spacing: -0.01em">Daftar magang lebih mudah</h2>
          <p style="margin: 0; max-width: 380px; font-size: 15px; line-height: 1.6; color: rgba(255,255,255,.85)">Cek kuota, ajukan pendaftaran, dan ikuti prosesnya dari satu platform.</p>
        </div>
        <div aria-hidden="true" style="width: 25%; flex-shrink: 0; box-sizing: border-box; padding: 0 12px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 12px">
          <h2 style="margin: 0; font-size: 30px; line-height: 1.2; font-weight: 800; letter-spacing: -0.01em">Kelola magang lebih terarah</h2>
          <p style="margin: 0; max-width: 380px; font-size: 15px; line-height: 1.6; color: rgba(255,255,255,.85)">Daftar, kerjakan tugas, dan kirim logbook dalam satu platform terpadu Dinas Kominfo Sleman.</p>
        </div>
      </div>
    </div>
    <div aria-hidden="true" style="display: flex; align-items: center; gap: 8px">
      <span class="bar"><span class="fill f1"></span></span>
      <span class="bar"><span class="fill f2"></span></span>
      <span class="bar"><span class="fill f3"></span></span>
    </div>
  </div>
</section>

