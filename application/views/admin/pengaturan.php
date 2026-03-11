<!-- pengaturan.php -->


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - BannerPosko</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
    <style>
        /* ── TABS ── */
        .tabs { display: flex; gap: 4px; margin-bottom: 24px; background: #fff; padding: 6px; border-radius: 12px; box-shadow: var(--shadow); width: fit-content; }
        .tab-btn { padding: 9px 22px; border-radius: 8px; border: none; background: none; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500; color: var(--text-muted); cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 7px; }
        .tab-btn:hover { background: #f3f4f6; color: var(--text); }
        .tab-btn.active { background: var(--blue); color: #fff; }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }

        /* ── SETTING CARD ── */
        .setting-card { background: #fff; border-radius: 12px; box-shadow: var(--shadow); overflow: hidden; max-width: 560px; }
        .setting-header { padding: 20px 24px; border-bottom: 1px solid var(--border); }
        .setting-title { font-size: 15px; font-weight: 700; margin-bottom: 3px; }
        .setting-desc { font-size: 12px; color: var(--text-muted); }
        .setting-body { padding: 24px; }

        /* ── FOTO PROFIL ── */
        .foto-wrap {
            display: flex; align-items: center; gap: 20px;
            padding: 20px; background: #f9fafb;
            border-radius: 12px; margin-bottom: 20px;
            border: 1px solid var(--border);
        }
        .foto-ava {
            width: 80px; height: 80px; border-radius: 50%;
            object-fit: cover; flex-shrink: 0;
            border: 3px solid #e5e7eb;
        }
        .foto-ava-init {
            width: 80px; height: 80px; border-radius: 50%;
            background: var(--blue); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; font-weight: 700; flex-shrink: 0;
            border: 3px solid #e5e7eb;
        }
        .foto-actions { display: flex; flex-direction: column; gap: 8px; }
        .foto-info-name { font-size: 15px; font-weight: 700; margin-bottom: 2px; }
        .foto-info-role { font-size: 12px; color: var(--text-muted); }
        .btn-foto-upload {
            padding: 7px 14px; background: var(--blue); color: #fff;
            border: none; border-radius: 7px; font-size: 12px; font-weight: 600;
            font-family: 'DM Sans', sans-serif; cursor: pointer;
            transition: background 0.15s; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-foto-upload:hover { background: var(--blue-dark); }
        .btn-foto-hapus {
            padding: 7px 14px; background: #fef2f2; color: var(--red);
            border: 1px solid #fecaca; border-radius: 7px; font-size: 12px; font-weight: 600;
            font-family: 'DM Sans', sans-serif; cursor: pointer;
            transition: background 0.15s; display: inline-flex; align-items: center; gap: 6px;
            text-decoration: none;
        }
        .btn-foto-hapus:hover { background: #fee2e2; }
        .foto-hint { font-size: 11px; color: var(--text-muted); }

        /* ── PASSWORD STRENGTH ── */
        .strength-bar { height: 3px; border-radius: 2px; background: #e5e7eb; margin-top: 6px; overflow: hidden; }
        .strength-fill { height: 100%; border-radius: 2px; transition: width 0.3s, background 0.3s; width: 0; }
        .strength-label { font-size: 11px; margin-top: 4px; }

        .alert { padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: var(--green); }
        .alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: var(--red); }

        .pw-wrap { position: relative; }
        .pw-toggle { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 15px; color: var(--text-muted); }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"></div>
        <span class="brand-name">BannerPosko</span>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Menu</div>
        <a class="nav-link" href="<?= base_url('Admin') ?>"><span class="icon"></span> Dashboard</a>

        <div class="nav-label" style="margin-top:10px;">Landing Page</div>
        <div class="nav-accordion-header" onclick="toggleAccordion()">
            <div class="left"><span class="icon"></span> Update Landing Page</div>
            <span class="accordion-arrow" id="accordionArrow">›</span>
        </div>
        <div class="nav-submenu" id="landingSubmenu">
            <a class="nav-link" href="<?= base_url('Admin') ?>"><span class="icon"></span> Update Media</a>
            <a class="nav-link" href="<?= base_url('Admin') ?>"><span class="icon"></span> Update Penjadwalan</a>
            <a class="nav-link" href="<?= base_url('Admin') ?>"><span class="icon"></span> Update Running Text</a>
        </div>

        <?php if ($this->session->userdata('role') === 'superadmin'): ?>
        <div class="nav-label" style="margin-top:10px;">Manajemen</div>
        <a class="nav-link" href="<?= base_url('Admin/kelola_admin') ?>"><span class="icon"></span> Kelola Admin</a>
        <?php endif; ?>
        <div class="nav-label" style="margin-top:10px;">Lainnya</div>
        <a class="nav-link active" href="<?= base_url('Admin/pengaturan') ?>"><span class="icon"></span> Pengaturan</a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-box">
            <?php if (!empty($user->foto_profil)): ?>
                <img src="<?= base_url('uploads/profil/' . $user->foto_profil) ?>"
                     style="width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0;">
            <?php else: ?>
                <div class="user-ava"><?= strtoupper(substr($this->session->userdata('username'), 0, 1)) ?></div>
            <?php endif; ?>
            <div>
                <div class="user-name"><?= $user->nama_tampilan ?: $this->session->userdata('username') ?></div>
                <div class="user-role">Administrator</div>
                <?php if ($this->session->userdata('role') === 'superadmin'): ?>
                    <div class="user-role-chip">Superadmin</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- MAIN -->
<div class="main" id="main">
    <div class="navbar">
        <button class="hamburger" onclick="toggleSidebar()">☰</button>
        <div class="navbar-breadcrumb">BannerPosko › <span>Pengaturan</span></div>
        <div class="navbar-right">
            <a href="<?= base_url('Auth/logout') ?>" class="navbar-logout">Logout</a>
            <div class="navbar-ava">
                <?php if (!empty($user->foto_profil)): ?>
                    <img src="<?= base_url('uploads/profil/' . $user->foto_profil) ?>"
                         style="width:34px;height:34px;border-radius:50%;object-fit:cover;">
                <?php else: ?>
                    <?= strtoupper(substr($this->session->userdata('username'), 0, 1)) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="page-title">Pengaturan</div>
        <div class="page-sub">Kelola akun dan profil kamu</div>

        <?php
        $active_tab  = $this->session->flashdata('success_tab') ?: $this->session->flashdata('error_tab') ?: 'password';
        $msg_success = $this->session->flashdata('success');
        $msg_error   = $this->session->flashdata('error');
        ?>

        <!-- TABS -->
        <div class="tabs">
            <button class="tab-btn <?= $active_tab === 'password' ? 'active' : '' ?>" onclick="switchTab('password', this)">Ganti Password</button>
            <button class="tab-btn <?= $active_tab === 'profil'   ? 'active' : '' ?>" onclick="switchTab('profil', this)">Profil</button>
        </div>

        <!-- ══ TAB: GANTI PASSWORD ══ -->
        <div class="tab-panel <?= $active_tab === 'password' ? 'active' : '' ?>" id="tab-password">
            <?php if ($active_tab === 'password' && $msg_success): ?>
                <div class="alert alert-success"><?= $msg_success ?></div>
            <?php elseif ($active_tab === 'password' && $msg_error): ?>
                <div class="alert alert-error"><?= $msg_error ?></div>
            <?php endif; ?>

            <div class="setting-card">
                <div class="setting-header">
                    <div class="setting-title"> Ganti Password</div>
                    <div class="setting-desc">Ubah password login akun kamu</div>
                </div>
                <div class="setting-body">
                    <form action="<?= base_url('Admin/simpan_password') ?>" method="POST" autocomplete="off">
                        <div class="form-group">
                            <label class="form-label">Password Lama</label>
                            <div class="pw-wrap">
                                <input type="password" name="password_lama" id="pwLama" class="form-input" placeholder="Masukkan password lama" required>
                                <button type="button" class="pw-toggle" onclick="togglePw('pwLama', this)">👁</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password Baru</label>
                            <div class="pw-wrap">
                                <input type="password" name="password_baru" id="pwBaru" class="form-input" placeholder="Minimal 6 karakter" required oninput="checkStrength(this.value)">
                                <button type="button" class="pw-toggle" onclick="togglePw('pwBaru', this)">👁</button>
                            </div>
                            <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                            <div class="strength-label" id="strengthLabel"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <div class="pw-wrap">
                                <input type="password" name="password_konfirm" id="pwKonfirm" class="form-input" placeholder="Ulangi password baru" required>
                                <button type="button" class="pw-toggle" onclick="togglePw('pwKonfirm', this)">👁</button>
                            </div>
                        </div>
                        <button type="submit" class="btn-submit">Simpan Password</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ══ TAB: PROFIL ══ -->
        <div class="tab-panel <?= $active_tab === 'profil' ? 'active' : '' ?>" id="tab-profil">
            <?php if ($active_tab === 'profil' && $msg_success): ?>
                <div class="alert alert-success">✓ <?= $msg_success ?></div>
            <?php elseif ($active_tab === 'profil' && $msg_error): ?>
                <div class="alert alert-error">✗ <?= $msg_error ?></div>
            <?php endif; ?>

            <div class="setting-card">
                <div class="setting-header">
                    <div class="setting-title">Profil</div>
                    <div class="setting-desc">Ubah foto dan nama tampilan akun kamu</div>
                </div>
                <div class="setting-body">
                    <form action="<?= base_url('Admin/simpan_profil') ?>" method="POST" enctype="multipart/form-data">

                        <!-- Foto Profil -->
                        <div class="foto-wrap">
                            <?php if (!empty($user->foto_profil)): ?>
                                <img src="<?= base_url('uploads/profil/' . $user->foto_profil) ?>"
                                     class="foto-ava" id="fotoPreview">
                            <?php else: ?>
                                <div class="foto-ava-init" id="fotoInisial">
                                    <?= strtoupper(substr($this->session->userdata('username'), 0, 1)) ?>
                                </div>
                                <img src="" class="foto-ava" id="fotoPreview" style="display:none;">
                            <?php endif; ?>

                            <div>
                                <div class="foto-info-name"><?= $user->nama_tampilan ?: $this->session->userdata('username') ?></div>
                                <div class="foto-info-role" style="margin-bottom:10px;">
                                    <?= $this->session->userdata('role') === 'superadmin' ? 'Superadmin' : '● Admin' ?>
                                </div>
                                <div class="foto-actions">
                                    <button type="button" class="btn-foto-upload" onclick="document.getElementById('inputFoto').click()">
                                        Ganti Foto
                                    </button>
                                    <?php if (!empty($user->foto_profil)): ?>
                                    <a href="<?= base_url('Admin/hapus_foto_profil') ?>"
                                       class="btn-foto-hapus"
                                       onclick="return confirm('Hapus foto profil?')">
                                        Hapus Foto
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <div class="foto-hint" style="margin-top:8px;">JPG, PNG, WEBP — Maks 2MB, 500×500px</div>
                            </div>
                        </div>

                        <input type="file" id="inputFoto" name="foto_profil" accept="image/*"
                               style="display:none" onchange="previewFoto(this)">

                        <!-- Nama Tampilan -->
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-input"
                                   value="<?= $this->session->userdata('username') ?>"
                                   disabled style="opacity:0.5; cursor:not-allowed;">
                            <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">Username tidak dapat diubah</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nama Tampilan</label>
                            <input type="text" name="nama_tampilan" class="form-input"
                                   value="<?= htmlspecialchars($user->nama_tampilan ?? '') ?>"
                                   placeholder="Nama yang ditampilkan di sistem">
                            <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">Kosongkan untuk menggunakan username</div>
                        </div>

                        <button type="submit" class="btn-submit">Simpan Profil</button>
                    </form>
                </div>
            </div>
        </div>

    </div><!-- /content -->
</div><!-- /main -->

<script>
    const BASE_URL = '<?= base_url() ?>';
</script>
<script src="<?= base_url('assets/js/admin.js') ?>"></script>
<script>
    function switchTab(name, btn) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        btn.classList.add('active');
        history.replaceState(null, '', '#' + name);
    }

    window.addEventListener('load', function() {
        const hash  = location.hash.replace('#', '');
        const valid = ['password', 'profil'];
        if (valid.includes(hash)) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.getElementById('tab-' + hash).classList.add('active');
            document.querySelectorAll('.tab-btn')[valid.indexOf(hash)].classList.add('active');
        }
    });

    function togglePw(id, btn) {
        const inp = document.getElementById(id);
        if (inp.type === 'password') { inp.type = 'text'; btn.textContent = '🙈'; }
        else { inp.type = 'password'; btn.textContent = '👁'; }
    }

    function checkStrength(val) {
        const fill  = document.getElementById('strengthFill');
        const label = document.getElementById('strengthLabel');
        let score = 0;
        if (val.length >= 6)  score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^a-zA-Z0-9]/.test(val)) score++;
        const map = [
            { w:'0%',   bg:'#e5e7eb', txt:'' },
            { w:'25%',  bg:'#ef4444', txt:'Lemah' },
            { w:'50%',  bg:'#f97316', txt:'Cukup' },
            { w:'75%',  bg:'#eab308', txt:'Bagus' },
            { w:'90%',  bg:'#22c55e', txt:'Kuat' },
            { w:'100%', bg:'#16a34a', txt:'Sangat Kuat' },
        ];
        const m = map[Math.min(score, 5)];
        fill.style.width = m.w; fill.style.background = m.bg;
        label.textContent = m.txt; label.style.color = m.bg;
    }

    // Preview foto sebelum upload
    function previewFoto(input) {
        if (!input.files[0]) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview  = document.getElementById('fotoPreview');
            const inisial  = document.getElementById('fotoInisial');
            preview.src    = e.target.result;
            preview.style.display = '';
            if (inisial) inisial.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
</script>
</body>
</html>