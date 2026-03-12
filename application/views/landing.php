<?php
/**
 * landing.php — BannerPosko Display View
 * Fitur: Auto fullscreen, mirror reflection, running text & datetime settings lengkap
 */

function convert_url($url) {
    if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m))
        return 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&mute=1&loop=1&playlist=' . $m[1];
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $m))
        return 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&mute=1&loop=1&playlist=' . $m[1];
    return $url;
}

$s = [];
if (!empty($settings)) foreach ($settings as $k => $v) $s[$k] = $v;

// Running Text settings
$rt_font      = $s['rt_font']      ?? 'sans-serif';
$rt_size      = (int)($s['rt_size']      ?? 24);
$rt_speed     = (int)($s['rt_speed']     ?? 20);
$rt_color     = $s['rt_color']     ?? '#ffffff';
$rt_bg_type   = $s['rt_bg_type']   ?? 'transparent';
$rt_bg_color  = $s['rt_bg_color']  ?? '#000000';
$rt_bg_blur   = (int)($s['rt_bg_blur']   ?? 0);

// Datetime settings
$dt_font      = $s['dt_font']      ?? 'monospace';
$dt_size      = (int)($s['dt_size']      ?? 28);
$dt_jam_type  = $s['dt_jam_type']  ?? 'HH:MM:SS';
$dt_color     = $s['dt_color']     ?? '#ffffff';
$dt_bg_type   = $s['dt_bg_type']   ?? 'transparent';
$dt_bg_color  = $s['dt_bg_color']  ?? '#000000';
$dt_bg_blur   = (int)($s['dt_bg_blur']   ?? 0);

// Bottom bar settings
$bar_bg_type  = $s['bar_bg_type']  ?? 'solid';
$bar_bg_color = $s['bar_bg_color'] ?? '#000000';
$bar_bg_blur  = (int)($s['bar_bg_blur']  ?? 8);

// Slider
$slider_interval = (int)($s['slider_interval'] ?? 5);

// Mirror reflection on/off
$mirror_enable = ($s['mirror_enable'] ?? '0') === '1';
$mirror_height = (int)($s['mirror_height'] ?? 18); // % tinggi refleksi

// Generate CSS background
function bg_css($type, $color, $blur) {
    $blur = (int)$blur;
    switch ($type) {
        case 'transparent': return 'background: transparent;';
        case 'blur':        return "background: rgba(0,0,0,0.45); backdrop-filter: blur({$blur}px); -webkit-backdrop-filter: blur({$blur}px);";
        case 'color':
        case 'solid':       return "background: {$color};";
        case 'gradient':    return "background: linear-gradient(to top, {$color}dd 0%, transparent 100%);";
        default:            return "background: rgba(0,0,0,0.88);";
    }
}

$bar_css   = bg_css($bar_bg_type,  $bar_bg_color,  $bar_bg_blur);
$rt_bg_css = bg_css($rt_bg_type,   $rt_bg_color,   $rt_bg_blur);
$dt_bg_css = bg_css($dt_bg_type,   $dt_bg_color,   $dt_bg_blur);

$bar_height     = 70;
$reflect_height = $mirror_enable ? (int)($bar_height * $mirror_height / 100) + 60 : 0;
$canvas_height  = "calc(100vh - {$bar_height}px" . ($mirror_enable ? " - {$reflect_height}px" : "") . ")";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BannerPosko</title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background: #000;
            cursor: none;
            user-select: none;
        }

        /* ── BANNER AREA ── */
        .banner-container {
            width: 100vw;
            height: <?= $canvas_height ?>;
            position: relative;
            overflow: hidden;
            background: #000;
        }

        .banner-slide {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0; left: 0;
            opacity: 0;
            transition: opacity 1s ease;
            pointer-events: none;
        }
        .banner-slide.active { opacity: 1; }
        .banner-slide img   { width: 100%; height: 100%; object-fit: cover; display: block; }
        .banner-slide video  { width: 100%; height: 100%; object-fit: cover; display: block; }
        .banner-slide iframe { width: 100%; height: 100%; border: none; display: block; }

        .no-banner {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            background: #0d0d0f; color: #222;
            font-family: monospace; font-size: 48px; letter-spacing: 4px;
        }

        /* ── MIRROR REFLECTION ── */
        <?php if ($mirror_enable): ?>
        .mirror-strip {
            width: 100vw;
            height: <?= $reflect_height ?>px;
            position: relative;
            overflow: hidden;
            background: #000;
        }
        .mirror-inner {
            width: 100vw;
            height: <?= $canvas_height ?>;
            position: absolute;
            bottom: 0; left: 0;
            transform: scaleY(-1);
            transform-origin: bottom center;
            pointer-events: none;
        }
        .mirror-inner .banner-slide { opacity: 0; }
        .mirror-inner .banner-slide.active { opacity: 1; }
        .mirror-inner img   { width: 100%; height: 100%; object-fit: cover; display: block; }
        .mirror-inner video { width: 100%; height: 100%; object-fit: cover; display: block; }
        .mirror-inner iframe { width: 100%; height: 100%; border: none; display: block; }
        /* Water ripple gradient overlay */
        .mirror-fade {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom,
                rgba(0,0,0,0.0) 0%,
                rgba(0,0,0,0.4) 40%,
                rgba(0,0,0,0.85) 75%,
                rgba(0,0,0,1) 100%
            );
            pointer-events: none;
            z-index: 2;
        }
        <?php endif; ?>

        /* ── BOTTOM BAR ── */
        .bottom-bar {
            position: relative;
            width: 100vw;
            height: <?= $bar_height ?>px;
            <?= $bar_css ?>
            display: flex;
            align-items: stretch;
            overflow: hidden;
            flex-shrink: 0;
        }

        /* ── DATETIME BOX ── */
        .datetime-box {
            flex-shrink: 0;
            min-width: 200px;
            padding: 0 18px;
            border-right: 1px solid rgba(255,255,255,0.12);
            <?= $dt_bg_css ?>
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 3px;
        }
        .datetime-time {
            font-family: <?= htmlspecialchars($dt_font) ?>;
            font-size: <?= $dt_size ?>px;
            font-weight: 700;
            color: <?= htmlspecialchars($dt_color) ?>;
            line-height: 1;
            letter-spacing: 1px;
        }
        .datetime-date {
            font-family: <?= htmlspecialchars($dt_font) ?>;
            font-size: <?= max(9, $dt_size - 14) ?>px;
            color: <?= htmlspecialchars($dt_color) ?>;
            opacity: 0.65;
        }

        /* ── RUNNING TEXT ── */
        .running-text-wrap {
            flex: 1;
            overflow: hidden;
            height: 100%;
            display: flex;
            align-items: center;
            <?= $rt_bg_css ?>
        }
        .running-text {
            display: inline-block;
            white-space: nowrap;
            padding: 0 40px;
            font-family: <?= htmlspecialchars($rt_font) ?>;
            font-size: <?= $rt_size ?>px;
            color: <?= htmlspecialchars($rt_color) ?>;
            animation: marquee-scroll <?= $rt_speed ?>s linear infinite;
            will-change: transform;
        }
        @keyframes marquee-scroll {
            from { transform: translateX(100vw); }
            to   { transform: translateX(-100%); }
        }

        /* ── FULLSCREEN PROMPT ── */
        #fs-prompt {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.93);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            cursor: pointer;
            transition: opacity 0.4s;
        }
        #fs-prompt.hidden {
            opacity: 0;
            pointer-events: none;
        }
        #fs-prompt .fs-logo {
            font-size: 64px;
            margin-bottom: 24px;
            animation: pulse 2s ease infinite;
        }
        #fs-prompt h2 {
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }
        #fs-prompt p {
            color: #666;
            font-family: sans-serif;
            font-size: 14px;
        }
        #fs-prompt .fs-btn {
            margin-top: 28px;
            padding: 14px 36px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            font-family: sans-serif;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: background 0.2s, transform 0.1s;
        }
        #fs-prompt .fs-btn:hover { background: #1d4ed8; transform: scale(1.03); }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50%       { transform: scale(1.1); }
        }
    </style>
</head>
<body>

<!-- ══ FULLSCREEN PROMPT ══ -->
<div id="fs-prompt">
    <div class="fs-logo">⛶</div>
    <h2>Klik untuk Memulai</h2>
    <p>Layar akan otomatis fullscreen & semua notifikasi disembunyikan</p>
    <button class="fs-btn" onclick="enterFullscreen()">▶ Mulai Tampilan</button>
</div>

<!-- ══ WRAPPER UTAMA ══ -->
<div id="mainWrapper" style="display:flex;flex-direction:column;width:100vw;height:100vh;">

    <!-- Banner Slides -->
    <div class="banner-container" id="bannerContainer">
        <?php if (empty($banners)): ?>
            <div class="no-banner">BANNER POSKO</div>
        <?php else: ?>
            <?php foreach ($banners as $i => $b): ?>
            <div class="banner-slide <?= $i === 0 ? 'active' : '' ?>"
                 data-tipe="<?= $b->tipe ?>"
                 data-index="<?= $i ?>">
                <?php if ($b->tipe === 'video'): ?>
                    <video <?= $i === 0 ? 'autoplay' : '' ?> muted playsinline preload="metadata">
                        <source src="<?= base_url('uploads/' . $b->gambar) ?>" type="video/mp4">
                    </video>
                <?php elseif ($b->tipe === 'url'): ?>
                    <iframe src="<?= convert_url($b->url) ?>"
                        allowfullscreen allow="autoplay; fullscreen"
                        sandbox="allow-same-origin allow-scripts allow-popups allow-forms">
                    </iframe>
                <?php else: ?>
                    <img src="<?= base_url('uploads/' . $b->gambar) ?>" alt="">
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($mirror_enable && !empty($banners)): ?>
    <!-- ══ MIRROR REFLECTION STRIP ══ -->
    <div class="mirror-strip" id="mirrorStrip">
        <div class="mirror-inner" id="mirrorInner">
            <?php foreach ($banners as $i => $b): ?>
            <div class="banner-slide <?= $i === 0 ? 'active' : '' ?>"
                 data-mirror="<?= $i ?>">
                <?php if ($b->tipe === 'video'): ?>
                    <video <?= $i === 0 ? 'autoplay' : '' ?> muted playsinline>
                        <source src="<?= base_url('uploads/' . $b->gambar) ?>" type="video/mp4">
                    </video>
                <?php elseif ($b->tipe === 'url'): ?>
                    <iframe src="<?= convert_url($b->url) ?>"
                        allowfullscreen allow="autoplay; fullscreen"
                        sandbox="allow-same-origin allow-scripts allow-popups allow-forms">
                    </iframe>
                <?php else: ?>
                    <img src="<?= base_url('uploads/' . $b->gambar) ?>" alt="">
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="mirror-fade"></div>
    </div>
    <?php endif; ?>

    <!-- ══ BOTTOM BAR ══ -->
    <div class="bottom-bar">
        <div class="datetime-box">
            <div class="datetime-time" id="clock">--:--</div>
            <div class="datetime-date" id="dateStr">-</div>
        </div>
        <div class="running-text-wrap">
            <span class="running-text" id="rtText">
                <?= isset($running_text) ? htmlspecialchars($running_text) : 'Selamat Datang di BannerPosko' ?>
            </span>
        </div>
    </div>

</div><!-- /mainWrapper -->

<script>
    /* ═══════════════════════════════════════════════════
       CONFIG
    ═══════════════════════════════════════════════════ */
    const SLIDER_MS  = <?= $slider_interval * 1000 ?>;
    const JAM_TYPE   = '<?= addslashes($dt_jam_type) ?>';
    const MIRROR_ON  = <?= $mirror_enable ? 'true' : 'false' ?>;

    /* ═══════════════════════════════════════════════════
       FULLSCREEN + HIDE BROWSER UI
    ═══════════════════════════════════════════════════ */
    const prompt = document.getElementById('fs-prompt');

    function enterFullscreen() {
        const el  = document.documentElement;
        const req = el.requestFullscreen
                 || el.webkitRequestFullscreen
                 || el.mozRequestFullScreen
                 || el.msRequestFullscreen;
        if (req) {
            req.call(el).then(() => {
                hidePrompt();
                // Kunci pointer agar notif translate/cursor hilang
                try { document.body.requestPointerLock(); } catch(e) {}
                // Keyboard lock supaya shortcut browser (F11, ESC dll) diredam
                if (navigator.keyboard && navigator.keyboard.lock) {
                    navigator.keyboard.lock(['Escape','F11']).catch(()=>{});
                }
            }).catch(() => {
                hidePrompt(); // Tetap hide prompt meski gagal fullscreen
            });
        } else {
            hidePrompt();
        }
    }

    function hidePrompt() {
        prompt.classList.add('hidden');
        setTimeout(() => { prompt.style.display = 'none'; }, 400);
    }

    // Coba auto-fullscreen saat halaman pertama kali load
    document.addEventListener('DOMContentLoaded', () => {
        // Dismiss notifikasi browser otomatis dengan setTimeout pendek
        setTimeout(() => {
            const el  = document.documentElement;
            const req = el.requestFullscreen
                     || el.webkitRequestFullscreen
                     || el.mozRequestFullScreen
                     || el.msRequestFullscreen;
            if (req) {
                req.call(el).then(() => {
                    hidePrompt();
                    try { document.body.requestPointerLock(); } catch(e) {}
                    if (navigator.keyboard && navigator.keyboard.lock)
                        navigator.keyboard.lock(['Escape','F11']).catch(()=>{});
                }).catch(() => {
                    // Browser butuh gesture — tampilkan prompt
                    prompt.style.display = 'flex';
                });
            }
        }, 500);
    });

    // Kalau fullscreen keluar (ESC), tampilkan prompt lagi
    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement) {
            prompt.style.display = 'flex';
            setTimeout(() => prompt.classList.remove('hidden'), 10);
        }
    });

    // Klik mana saja (selain prompt) masuk fullscreen lagi
    document.addEventListener('click', (e) => {
        if (!document.fullscreenElement && !prompt.contains(e.target)) {
            enterFullscreen();
        }
    });

    /* ═══════════════════════════════════════════════════
       CLOCK
    ═══════════════════════════════════════════════════ */
    const days   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];

    function updateClock() {
        const now = new Date();
        const h   = String(now.getHours()).padStart(2,'0');
        const m   = String(now.getMinutes()).padStart(2,'0');
        const s   = String(now.getSeconds()).padStart(2,'0');

        let timeStr;
        switch (JAM_TYPE) {
            case 'HH:MM':
                timeStr = h + ':' + m; break;
            case '12H': {
                const h12   = now.getHours() % 12 || 12;
                const ampm  = now.getHours() >= 12 ? 'PM' : 'AM';
                timeStr = String(h12).padStart(2,'0') + ':' + m + ':' + s + ' ' + ampm;
                break;
            }
            default:
                timeStr = h + ':' + m + ':' + s;
        }
        document.getElementById('clock').textContent   = timeStr;
        document.getElementById('dateStr').textContent =
            days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
    }
    setInterval(updateClock, 1000);
    updateClock();

    /* ═══════════════════════════════════════════════════
       SLIDESHOW
    ═══════════════════════════════════════════════════ */
    const slides       = Array.from(document.querySelectorAll('#bannerContainer .banner-slide'));
    const mirrorSlides = MIRROR_ON
        ? Array.from(document.querySelectorAll('#mirrorInner .banner-slide'))
        : [];

    let current     = 0;
    let sliderTimer = null;

    function activateSlide(index) {
        // Hide semua
        slides.forEach((s, i) => {
            s.classList.remove('active');
            const v = s.querySelector('video');
            if (v) { v.pause(); v.currentTime = 0; }
        });
        mirrorSlides.forEach(s => {
            s.classList.remove('active');
            const v = s.querySelector('video');
            if (v) { v.pause(); v.currentTime = 0; }
        });

        // Aktifkan slide index
        slides[index].classList.add('active');
        if (mirrorSlides[index]) mirrorSlides[index].classList.add('active');

        // Handle video / image
        const video = slides[index].querySelector('video');
        if (video) {
            video.loop   = (slides.length === 1);
            video.onended = null;
            video.play().catch(()=>{});
            if (slides.length > 1) {
                video.onended = () => nextSlide();
            }
            // Mirror video sync
            if (mirrorSlides[index]) {
                const mv = mirrorSlides[index].querySelector('video');
                if (mv) { mv.currentTime = 0; mv.play().catch(()=>{}); }
            }
        } else {
            // Image / iframe — auto advance
            if (slides.length > 1) {
                clearTimeout(sliderTimer);
                sliderTimer = setTimeout(nextSlide, SLIDER_MS);
            }
        }
    }

    function nextSlide() {
        current = (current + 1) % slides.length;
        activateSlide(current);
    }

    if (slides.length > 0) activateSlide(0);
</script>
</body>
</html>