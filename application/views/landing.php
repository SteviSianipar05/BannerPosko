<?php
function convert_url($url) {
    if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&mute=1&loop=1&playlist=' . $m[1];
    }
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&mute=1&loop=1&playlist=' . $m[1];
    }
    return $url;
}

// Ambil semua setting dengan default value
$s = [];
if (!empty($settings)) {
    foreach ($settings as $k => $v) $s[$k] = $v;
}
$rt_font          = $s['rt_font']          ?? 'sans-serif';
$rt_size          = $s['rt_size']          ?? '24';
$rt_speed         = $s['rt_speed']         ?? '20';
$rt_color         = $s['rt_color']         ?? '#ffffff';
$rt_bg_type       = $s['rt_bg_type']       ?? 'transparent';
$rt_bg_color      = $s['rt_bg_color']      ?? '#000000';
$rt_bg_blur       = $s['rt_bg_blur']       ?? '0';

$dt_font          = $s['dt_font']          ?? 'monospace';
$dt_size          = $s['dt_size']          ?? '28';
$dt_jam_type      = $s['dt_jam_type']      ?? 'HH:MM:SS';
$dt_color         = $s['dt_color']         ?? '#ffffff';
$dt_bg_type       = $s['dt_bg_type']       ?? 'transparent';
$dt_bg_color      = $s['dt_bg_color']      ?? '#000000';
$dt_bg_blur       = $s['dt_bg_blur']       ?? '0';

$bar_bg_type      = $s['bar_bg_type']      ?? 'solid';
$bar_bg_color     = $s['bar_bg_color']     ?? '#000000';
$bar_bg_blur      = $s['bar_bg_blur']      ?? '8';

$slider_interval  = (int)($s['slider_interval'] ?? 5);

// Generate CSS background untuk bottom bar
function bg_css($type, $color, $blur) {
    $blur = (int)$blur;
    switch ($type) {
        case 'transparent': return 'background: transparent;';
        case 'blur':        return "background: rgba(0,0,0,0.5); backdrop-filter: blur({$blur}px); -webkit-backdrop-filter: blur({$blur}px);";
        case 'color':       return "background: {$color};";
        case 'gradient':    return "background: linear-gradient(to right, {$color}, transparent);";
        default:            return "background: rgba(0,0,0,0.88);";
    }
}

$bar_css = bg_css($bar_bg_type, $bar_bg_color, $bar_bg_blur);
$rt_bg_css = bg_css($rt_bg_type, $rt_bg_color, $rt_bg_blur);
$dt_bg_css = bg_css($dt_bg_type, $dt_bg_color, $dt_bg_blur);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BannerPosko</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background: #000;
            cursor: none;
        }

        .banner-container {
            width: 100vw;
            height: calc(100vh - 70px);
            position: relative;
            overflow: hidden;
        }

        .banner-slide {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0; left: 0;
            opacity: 0;
            transition: opacity 1s ease;
        }
        .banner-slide.active { opacity: 1; }
        .banner-slide img { width: 100%; height: 100%; object-fit: cover; }
        .banner-slide video { width: 100%; height: 100%; object-fit: cover; }
        .banner-slide iframe { width: 100%; height: 100%; border: none; }

        .no-banner {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            background: #0d0d0f; color: #333;
            font-family: monospace; font-size: 60px;
        }

        /* Bottom Bar */
        .bottom-bar {
            position: absolute;
            bottom: 0; left: 0;
            width: 100vw;
            height: 70px;
            <?= $bar_css ?>
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        /* Datetime Box */
        .datetime-box {
            flex-shrink: 0;
            width: 220px;
            padding: 0 16px;
            border-right: 2px solid rgba(255,255,255,0.15);
            <?= $dt_bg_css ?>
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .datetime-time {
            font-family: <?= htmlspecialchars($dt_font) ?>;
            font-size: <?= (int)$dt_size ?>px;
            font-weight: 700;
            color: <?= htmlspecialchars($dt_color) ?>;
            line-height: 1;
        }
        .datetime-date {
            font-family: <?= htmlspecialchars($dt_font) ?>;
            font-size: <?= max(10, (int)$dt_size - 14) ?>px;
            color: <?= htmlspecialchars($dt_color) ?>;
            opacity: 0.7;
            margin-top: 4px;
        }

        /* Running Text */
        .running-text-wrap {
            flex: 1;
            overflow: hidden;
            padding: 0 16px;
            height: 100%;
            display: flex;
            align-items: center;
            <?= $rt_bg_css ?>
        }
        .running-text {
            display: inline-block;
            white-space: nowrap;
            font-family: <?= htmlspecialchars($rt_font) ?>;
            font-size: <?= (int)$rt_size ?>px;
            color: <?= htmlspecialchars($rt_color) ?>;
            animation: marquee <?= (int)$rt_speed ?>s linear infinite;
        }
        @keyframes marquee {
            from { transform: translateX(100vw); }
            to   { transform: translateX(-100%); }
        }

        /* Fullscreen prompt overlay */
        #fs-prompt {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.92);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            cursor: pointer;
        }
        #fs-prompt h2 {
            color: #fff;
            font-family: sans-serif;
            font-size: 32px;
            margin-bottom: 16px;
        }
        #fs-prompt p {
            color: #aaa;
            font-family: sans-serif;
            font-size: 16px;
        }
        #fs-prompt .fs-icon { font-size: 64px; margin-bottom: 20px; }
    </style>
</head>
<body>

<!-- Fullscreen prompt — klik untuk masuk fullscreen -->
<div id="fs-prompt" onclick="enterFullscreen()">
    <div class="fs-icon">⛶</div>
    <h2>Klik untuk Memulai</h2>
    <p>Layar akan otomatis fullscreen</p>
</div>

<div class="banner-container" id="bannerContainer">
    <?php if (empty($banners)): ?>
        <div class="no-banner">Belum ada banner aktif</div>
    <?php else: ?>
        <?php foreach ($banners as $i => $b): ?>
        <div class="banner-slide <?= $i === 0 ? 'active' : '' ?>" data-tipe="<?= $b->tipe ?>">
            <?php if ($b->tipe === 'video'): ?>
                <video <?= $i === 0 ? 'autoplay' : '' ?> muted playsinline>
                    <source src="<?= base_url('uploads/' . $b->gambar) ?>" type="video/mp4">
                </video>
            <?php elseif ($b->tipe === 'url'): ?>
                <iframe src="<?= convert_url($b->url) ?>"
                    allowfullscreen
                    allow="autoplay; fullscreen"
                    sandbox="allow-same-origin allow-scripts allow-popups allow-forms">
                </iframe>
            <?php else: ?>
                <img src="<?= base_url('uploads/' . $b->gambar) ?>">
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="bottom-bar">
    <div class="datetime-box">
        <div class="datetime-time" id="clock">00:00:00</div>
        <div class="datetime-date" id="date">-</div>
    </div>
    <div class="running-text-wrap">
        <span class="running-text">
            <?= isset($running_text) ? htmlspecialchars($running_text) : 'Selamat Datang di BannerPosko' ?>
        </span>
    </div>
</div>

<script>
    const SLIDER_INTERVAL = <?= $slider_interval * 1000 ?>;
    const JAM_TYPE = '<?= $dt_jam_type ?>';

    // ── Fullscreen ───────────────────────────────────────
    function enterFullscreen() {
        const el = document.documentElement;
        const req = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
        if (req) req.call(el);
        document.getElementById('fs-prompt').style.display = 'none';
        document.body.requestPointerLock && document.body.requestPointerLock();
    }

    // Auto fullscreen jika sudah pernah dibuka (tidak ada interaksi pertama kali)
    document.addEventListener('DOMContentLoaded', function() {
        // Coba auto fullscreen tanpa klik (berhasil di beberapa browser)
        setTimeout(function() {
            const el = document.documentElement;
            const req = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
            if (req) {
                req.call(el).then(function() {
                    document.getElementById('fs-prompt').style.display = 'none';
                }).catch(function() {
                    // Gagal auto — tampilkan prompt klik
                    document.getElementById('fs-prompt').style.display = 'flex';
                });
            }
        }, 300);
    });

    // Kalau fullscreen keluar, sembunyikan prompt
    document.addEventListener('fullscreenchange', function() {
        if (!document.fullscreenElement) {
            document.getElementById('fs-prompt').style.display = 'flex';
        } else {
            document.getElementById('fs-prompt').style.display = 'none';
        }
    });

    // ── Clock ─────────────────────────────────────────────
    function updateClock() {
        const now = new Date();
        const h   = String(now.getHours()).padStart(2,'0');
        const m   = String(now.getMinutes()).padStart(2,'0');
        const s   = String(now.getSeconds()).padStart(2,'0');

        let timeStr;
        if (JAM_TYPE === 'HH:MM') {
            timeStr = h + ':' + m;
        } else if (JAM_TYPE === '12H') {
            const h12 = now.getHours() % 12 || 12;
            const ampm = now.getHours() >= 12 ? 'PM' : 'AM';
            timeStr = String(h12).padStart(2,'0') + ':' + m + ':' + s + ' ' + ampm;
        } else {
            timeStr = h + ':' + m + ':' + s;
        }
        document.getElementById('clock').textContent = timeStr;

        const days   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
        document.getElementById('date').textContent =
            days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ── Slideshow ─────────────────────────────────────────
    const slides = document.querySelectorAll('.banner-slide');

    if (slides.length === 1) {
        const video = slides[0].querySelector('video');
        if (video) { video.loop = true; video.play(); }
    } else if (slides.length > 1) {
        let current = 0;

        function showSlide(index) {
            slides.forEach(function(s) {
                s.classList.remove('active');
                const v = s.querySelector('video');
                if (v) { v.pause(); v.currentTime = 0; }
            });
            slides[index].classList.add('active');
            const video = slides[index].querySelector('video');
            if (video) {
                video.loop = false;
                video.play();
                video.onended = function() {
                    current = (current + 1) % slides.length;
                    showSlide(current);
                };
            } else {
                setTimeout(function() {
                    current = (current + 1) % slides.length;
                    showSlide(current);
                }, SLIDER_INTERVAL);
            }
        }
        showSlide(0);
    }
</script>
</body>
</html>