<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - BannerPosko</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body>

<?php $this->load->view('admin/sidebar'); ?>

<div class="main" id="main">
    <div class="navbar">
        <button class="hamburger" onclick="toggleSidebar()">☰</button>
        <div class="navbar-breadcrumb">BannerPosko > <span>Dashboard</span></div>
        <div class="navbar-right">
            <a href="<?= base_url('Auth/logout') ?>" class="navbar-logout"> Logout</a>
            <?php $foto_nav = $this->session->userdata('foto_profil'); ?>
            <?php if ($foto_nav): ?>
                <img src="<?= base_url('uploads/profil/' . $foto_nav) ?>" style="width:34px;height:34px;border-radius:50%;object-fit:cover;">
            <?php else: ?>
                <div class="navbar-ava"><?= strtoupper(substr($this->session->userdata('username'), 0, 1)) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="content">
        <div class="page-title">Dashboard</div>
        <div class="page-sub">Selamat datang kembali, <?= $this->session->userdata('username') ?></div>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success">✓ <?= $this->session->flashdata('success') ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-error">✗ <?= $this->session->flashdata('error') ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card green">
                <div class="stat-label">Banner Aktif</div>
                <div class="stat-value"><?= $banner_aktif ?></div>
                <div class="stat-desc">Banner sedang ditampilkan</div>
            </div>
            <div class="stat-card red">
                <div class="stat-label">Banner Nonaktif</div>
                <div class="stat-value"><?= $banner_nonaktif ?></div>
                <div class="stat-desc">Banner tidak ditampilkan</div>
            </div>
            <div class="stat-card blue">
                <div class="stat-label">Terakhir Diupdate</div>
                <div class="stat-value"><?= $terakhir_update ? date('d M Y', strtotime($terakhir_update)) : '—' ?></div>
                <div class="stat-desc"><?= $terakhir_update ? date('H:i', strtotime($terakhir_update)) . ' WIB' : 'Belum ada perubahan' ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Daftar Banner</div>
                    <div class="card-sub">Semua banner yang tersimpan</div>
                </div>
                <button class="btn-add" onclick="openModal('modalUpload')">+ Tambah Banner</button>
            </div>

            <?php if (empty($banners)): ?>
                <div class="empty-state">
                    <div class="empty-icon"></div>
                    <div>Belum ada banner. Klik "Tambah Banner" untuk mulai.</div>
                </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Status</th>
                        <th>Jadwal Mulai</th>
                        <th>Jadwal Selesai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($banners as $i => $b): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <?php if ($b->tipe === 'video'): ?>
                                <video src="<?= base_url('uploads/' . $b->gambar) ?>" style="height:44px;border-radius:6px;object-fit:cover;" muted></video>
                            <?php elseif ($b->tipe === 'url'): ?>
                                <img src="<?= $b->url ?>" style="height:44px;border-radius:6px;object-fit:cover;">
                            <?php else: ?>
                                <img src="<?= base_url('uploads/' . $b->gambar) ?>" style="height:44px;border-radius:6px;object-fit:cover;">
                            <?php endif; ?>
                        </td>
                        <td><span class="badge <?= $b->status ?>">● <?= $b->status === 'aktif' ? 'Aktif' : 'Nonaktif' ?></span></td>
                        <td><?= $b->jadwal_mulai ? date('d M Y H:i', strtotime($b->jadwal_mulai)) : '—' ?></td>
                        <td><?= $b->jadwal_selesai ? date('d M Y H:i', strtotime($b->jadwal_selesai)) : '—' ?></td>
                        <td>
                            <div class="action-btns">
                                <button class="btn-tbl" onclick="openJadwal(<?= $b->id ?>, '<?= $b->jadwal_mulai ?>', '<?= $b->jadwal_selesai ?>')">Jadwal</button>
                                <?php if ($b->status == 'aktif'): ?>
                                    <a href="<?= base_url('preview/' . $b->id) ?>" target="_blank" class="btn-tbl">Lihat</a>
                                <?php else: ?>
                                    <button class="btn-tbl" onclick="alert('Aktifkan banner ini terlebih dahulu!')">Lihat</button>
                                <?php endif; ?>
                                <a href="<?= base_url('Admin/hapus/' . $b->id) ?>" class="btn-tbl danger" onclick="return confirm('Hapus banner ini?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     Modal Tambah Banner
═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modalUpload">
    <div class="modal">
        <div class="modal-head">
            <h3>Tambah Banner</h3>
            <button class="modal-close" onclick="closeModal('modalUpload')">✕</button>
        </div>
        <form action="<?= base_url('Admin/upload_gambar') ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Tipe Konten</label>
                <div style="display:flex;gap:8px;">
                    <label style="flex:1;cursor:pointer;">
                        <input type="radio" name="tipe" value="gambar" checked onchange="switchTipe('gambar')" style="display:none;">
                        <div class="tipe-btn active" id="tipe-gambar">Gambar</div>
                    </label>
                    <label style="flex:1;cursor:pointer;">
                        <input type="radio" name="tipe" value="url" onchange="switchTipe('url')" style="display:none;">
                        <div class="tipe-btn" id="tipe-url">URL</div>
                    </label>
                    <label style="flex:1;cursor:pointer;">
                        <input type="radio" name="tipe" value="video" onchange="switchTipe('video')" style="display:none;">
                        <div class="tipe-btn" id="tipe-video">Video</div>
                    </label>
                </div>
            </div>
            <div class="form-group" id="input-gambar">
                <label class="form-label">Pilih Gambar</label>
                <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                    <div class="upload-icon"></div>
                    <div class="upload-text">Klik untuk pilih gambar<br><span>JPG, PNG, GIF — Maks 10MB</span></div>
                    <div id="fileName" style="margin-top:8px;font-size:12px;color:var(--blue);"></div>
                </div>
                <input type="file" id="fileInput" name="gambar" accept="image/*" style="display:none" onchange="showFileName(this)">
            </div>
            <div class="form-group" id="input-url" style="display:none;">
                <label class="form-label">URL Gambar / YouTube</label>
                <input type="text" name="url_input" class="form-input" placeholder="https://example.com/gambar.jpg atau URL YouTube">
            </div>
            <div class="form-group" id="input-video" style="display:none;">
                <label class="form-label">Pilih Video</label>
                <div class="upload-area" onclick="document.getElementById('videoInput').click()">
                    <div class="upload-icon"></div>
                    <div class="upload-text">Klik untuk pilih video<br><span>MP4, WEBM — Maks 100MB</span></div>
                    <div id="videoName" style="margin-top:8px;font-size:12px;color:var(--blue);"></div>
                </div>
                <input type="file" id="videoInput" name="video_file" accept="video/*" style="display:none" onchange="showVideoName(this)">
            </div>
            <div class="form-group">
                <label class="form-label">Jadwal Mulai (opsional)</label>
                <input type="datetime-local" name="jadwal_mulai" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Jadwal Selesai (opsional)</label>
                <input type="datetime-local" name="jadwal_selesai" class="form-input">
            </div>
            <button type="submit" class="btn-submit">Simpan Banner</button>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     Modal Update Media Banner
═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modalUpdateGambar">
    <div class="modal">
        <div class="modal-head">
            <h3>Update Media Banner</h3>
            <button class="modal-close" onclick="closeModal('modalUpdateGambar')">✕</button>
        </div>
        <?php if (empty($banners)): ?>
            <p style="color:var(--text-muted);font-size:13px;">Belum ada banner.</p>
        <?php else: ?>
        <form id="formUpdateGambar" action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">1. Pilih Banner</label>
                <select class="form-select" name="banner_id" id="selectBannerGambar" onchange="previewBannerLama(this)" required>
                    <option value="">— Pilih Banner —</option>
                    <?php foreach ($banners as $b): ?>
                        <option value="<?= $b->id ?>"
                            data-tipe="<?= $b->tipe ?>"
                            data-src="<?= $b->tipe === 'url' ? $b->url : base_url('uploads/' . $b->gambar) ?>">
                            Banner #<?= $b->id ?> — <?= ucfirst($b->tipe) ?> — <?= $b->status === 'aktif' ? 'Aktif' : 'Nonaktif' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="previewLama" style="display:none;margin-bottom:16px;background:#f9fafb;border-radius:8px;padding:12px;text-align:center;">
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:8px;">Media saat ini:</div>
                <img id="previewLamaImg" src="" style="max-height:80px;border-radius:6px;display:none;">
                <video id="previewLamaVideo" src="" style="max-height:80px;border-radius:6px;display:none;" muted></video>
                <div id="previewLamaUrl" style="font-size:12px;color:var(--blue);display:none;word-break:break-all;"></div>
            </div>
            <input type="hidden" name="tipe" id="hiddenTipe" value="">
            <div class="form-group" id="uinput-gambar" style="display:none;">
                <label class="form-label">2. Upload Gambar Baru</label>
                <div class="upload-area" onclick="document.getElementById('fileInputUpdate').click()">
                    <div class="upload-icon"></div>
                    <div class="upload-text">Klik untuk pilih gambar<br><span>JPG, PNG, GIF — Maks 10MB</span></div>
                    <div id="fileNameUpdate" style="margin-top:8px;font-size:12px;color:var(--blue);"></div>
                </div>
                <input type="file" id="fileInputUpdate" name="gambar" accept="image/*" style="display:none"
                    onchange="document.getElementById('fileNameUpdate').textContent = '📁 ' + this.files[0].name">
            </div>
            <div class="form-group" id="uinput-url" style="display:none;">
                <label class="form-label">2. URL Gambar Baru</label>
                <input type="text" name="url_input" class="form-input" placeholder="https://example.com/gambar.jpg">
            </div>
            <div class="form-group" id="uinput-video" style="display:none;">
                <label class="form-label">2. Upload Video Baru</label>
                <div class="upload-area" onclick="document.getElementById('videoInputUpdate').click()">
                    <div class="upload-icon"></div>
                    <div class="upload-text">Klik untuk pilih video<br><span>MP4, WEBM — Maks 100MB</span></div>
                    <div id="videoNameUpdate" style="margin-top:8px;font-size:12px;color:var(--blue);"></div>
                </div>
                <input type="file" id="videoInputUpdate" name="video_file" accept="video/*" style="display:none"
                    onchange="document.getElementById('videoNameUpdate').textContent = '🎬 ' + this.files[0].name">
            </div>
            <button type="submit" class="btn-submit" id="btnSimpanMedia" style="display:none;">Simpan Media Baru</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     Modal Update Penjadwalan
═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modalJadwalBaru">
    <div class="modal">
        <div class="modal-head">
            <h3>Update Penjadwalan</h3>
            <button class="modal-close" onclick="closeModal('modalJadwalBaru')">✕</button>
        </div>
        <?php if (empty($banners)): ?>
            <p style="color:var(--text-muted);font-size:13px;">Belum ada banner. Upload gambar terlebih dahulu.</p>
        <?php else: ?>
        <form id="formJadwalBaru" action="" method="POST">
            <div class="form-group">
                <label class="form-label">Pilih Banner</label>
                <select class="form-select" onchange="pilihBanner(this)">
                    <option value="">— Pilih Banner —</option>
                    <?php foreach ($banners as $b): ?>
                        <option value="<?= $b->id ?>" data-mulai="<?= $b->jadwal_mulai ?>" data-selesai="<?= $b->jadwal_selesai ?>">
                            Banner #<?= $b->id ?> (<?= $b->status ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Jadwal Mulai</label>
                <input type="datetime-local" name="jadwal_mulai" id="jadwalMulai2" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Jadwal Selesai</label>
                <input type="datetime-local" name="jadwal_selesai" id="jadwalSelesai2" class="form-input">
            </div>
            <button type="submit" class="btn-submit">Simpan Penjadwalan</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     Modal Edit Jadwal (dari tabel)
═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modalJadwal">
    <div class="modal">
        <div class="modal-head">
            <h3>Edit Jadwal Banner</h3>
            <button class="modal-close" onclick="closeModal('modalJadwal')">✕</button>
        </div>
        <form id="formJadwal" action="" method="POST">
            <div class="form-group">
                <label class="form-label">Jadwal Mulai</label>
                <input type="datetime-local" name="jadwal_mulai" id="jadwalMulai" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Jadwal Selesai</label>
                <input type="datetime-local" name="jadwal_selesai" id="jadwalSelesai" class="form-input">
            </div>
            <button type="submit" class="btn-submit">Simpan Perubahan</button>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     Modal Running Text
═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modalRunningText">
    <div class="modal">
        <div class="modal-head">
            <h3>Update Running Text</h3>
            <button class="modal-close" onclick="closeModal('modalRunningText')">✕</button>
        </div>
        <form action="<?= base_url('Admin/simpan_running_text') ?>" method="POST">
            <div class="form-group">
                <label class="form-label">Teks Berjalan</label>
                <textarea name="running_text" class="form-input" rows="4" style="resize:vertical;"><?= isset($running_text) ? $running_text : '' ?></textarea>
                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">Gunakan " | " sebagai pemisah antar teks</div>
            </div>
            <button type="submit" class="btn-submit">Simpan Running Text</button>
        </form>
    </div>
</div>


<!-- ═══════════════════════════════════════════
     Modal Display Settings — LENGKAP
═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modalDisplaySettings">
    <div class="modal" style="max-width:660px; max-height:92vh; overflow-y:auto;">
        <div class="modal-head" style="position:sticky;top:0;background:#fff;z-index:10;">
            <h3>⚙️ Display Settings</h3>
            <button class="modal-close" onclick="closeModal('modalDisplaySettings')">✕</button>
        </div>

        <form action="<?= base_url('Admin/simpan_display_settings') ?>" method="POST">

            <!-- ── TABS ── -->
            <div style="display:flex;gap:4px;margin-bottom:20px;background:#f3f4f6;padding:5px;border-radius:10px;flex-wrap:wrap;">
                <button type="button" class="ds-tab" id="tab-btn-rt"
                    onclick="switchDsTab('rt',this)"
                    style="flex:1;min-width:80px;padding:8px 4px;border:none;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;background:#1e3a5f;color:#fff;letter-spacing:.3px;">
                    📢 Running Text
                </button>
                <button type="button" class="ds-tab" id="tab-btn-dt"
                    onclick="switchDsTab('dt',this)"
                    style="flex:1;min-width:80px;padding:8px 4px;border:none;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;background:none;color:#666;letter-spacing:.3px;">
                    🕐 Jam & Tanggal
                </button>
                <button type="button" class="ds-tab" id="tab-btn-bar"
                    onclick="switchDsTab('bar',this)"
                    style="flex:1;min-width:80px;padding:8px 4px;border:none;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;background:none;color:#666;letter-spacing:.3px;">
                    📊 Bottom Bar
                </button>
                <button type="button" class="ds-tab" id="tab-btn-slide"
                    onclick="switchDsTab('slide',this)"
                    style="flex:1;min-width:80px;padding:8px 4px;border:none;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;background:none;color:#666;letter-spacing:.3px;">
                    🖼️ Slider
                </button>
                <button type="button" class="ds-tab" id="tab-btn-mirror"
                    onclick="switchDsTab('mirror',this)"
                    style="flex:1;min-width:80px;padding:8px 4px;border:none;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;background:none;color:#666;letter-spacing:.3px;">
                    🌊 Mirror
                </button>
            </div>

            <?php $s = $settings ?? []; ?>

            <!-- ══════════════════════════════════
                 TAB: RUNNING TEXT
            ══════════════════════════════════ -->
            <div class="ds-panel" id="ds-rt" style="display:block;">

                <div style="background:#f0f7ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:12px;color:#1e40af;">
                    📢 Mengatur tampilan teks berjalan di bagian bawah layar.
                </div>

                <div class="form-group">
                    <label class="form-label">Font Running Text</label>
                    <select name="rt_font" class="form-select" onchange="updatePreviewRT()">
                        <?php $rt_font = $s['rt_font'] ?? 'sans-serif'; ?>
                        <option value="sans-serif"                     <?= $rt_font==="sans-serif"?"selected":"" ?>>Sans Serif (Default)</option>
                        <option value="serif"                          <?= $rt_font==="serif"?"selected":"" ?>>Serif</option>
                        <option value="monospace"                      <?= $rt_font==="monospace"?"selected":"" ?>>Monospace</option>
                        <option value="Arial, sans-serif"              <?= $rt_font==="Arial, sans-serif"?"selected":"" ?>>Arial</option>
                        <option value="'Times New Roman', serif"       <?= $rt_font==="'Times New Roman', serif"?"selected":"" ?>>Times New Roman</option>
                        <option value="'Courier New', monospace"       <?= $rt_font==="'Courier New', monospace"?"selected":"" ?>>Courier New</option>
                        <option value="Georgia, serif"                 <?= $rt_font==="Georgia, serif"?"selected":"" ?>>Georgia</option>
                        <option value="Impact, sans-serif"             <?= $rt_font==="Impact, sans-serif"?"selected":"" ?>>Impact</option>
                        <option value="'Trebuchet MS', sans-serif"     <?= $rt_font==="'Trebuchet MS', sans-serif"?"selected":"" ?>>Trebuchet MS</option>
                        <option value="'Arial Black', sans-serif"      <?= $rt_font==="'Arial Black', sans-serif"?"selected":"" ?>>Arial Black</option>
                        <option value="Verdana, sans-serif"            <?= $rt_font==="Verdana, sans-serif"?"selected":"" ?>>Verdana</option>
                    </select>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Ukuran Teks (px)</label>
                        <input type="number" name="rt_size" id="rt_size_inp" class="form-input"
                            value="<?= $s['rt_size'] ?? 24 ?>" min="10" max="120"
                            oninput="updatePreviewRT()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kecepatan (detik)</label>
                        <input type="number" name="rt_speed" class="form-input"
                            value="<?= $s['rt_speed'] ?? 20 ?>" min="3" max="300">
                        <div style="font-size:10px;color:var(--text-muted);margin-top:2px;">Kecil = lebih cepat</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Warna Teks</label>
                        <div style="display:flex;gap:6px;align-items:center;">
                            <input type="color" name="rt_color" id="rt_color_pick"
                                value="<?= $s['rt_color'] ?? '#ffffff' ?>"
                                style="width:40px;height:36px;border:none;border-radius:6px;cursor:pointer;flex-shrink:0;"
                                oninput="syncHex('rt_color')">
                            <input type="text" id="rt_color_hex" class="form-input"
                                value="<?= $s['rt_color'] ?? '#ffffff' ?>"
                                style="font-size:12px;"
                                oninput="syncPicker('rt_color')">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Background Running Text</label>
                    <select name="rt_bg_type" class="form-select" onchange="toggleBgOpts('rt',this.value)">
                        <?php $rt_bg = $s['rt_bg_type'] ?? 'transparent'; ?>
                        <option value="transparent" <?= $rt_bg==="transparent"?"selected":"" ?>>Transparan</option>
                        <option value="solid"       <?= $rt_bg==="solid"?"selected":"" ?>>Warna Solid</option>
                        <option value="blur"        <?= $rt_bg==="blur"?"selected":"" ?>>Blur / Frosted Glass</option>
                        <option value="gradient"    <?= $rt_bg==="gradient"?"selected":"" ?>>Gradient</option>
                    </select>
                </div>
                <div id="rt_bg_opts"
                    style="display:<?= in_array($s['rt_bg_type'] ?? '', ['solid','blur','gradient']) ? 'grid' : 'none' ?>;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Warna BG</label>
                        <input type="color" name="rt_bg_color" value="<?= $s['rt_bg_color'] ?? '#000000' ?>"
                            style="width:100%;height:36px;border:none;border-radius:6px;cursor:pointer;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Intensitas Blur (px)</label>
                        <input type="number" name="rt_bg_blur" class="form-input"
                            value="<?= $s['rt_bg_blur'] ?? 8 ?>" min="0" max="40">
                    </div>
                </div>

                <!-- Preview Running Text -->
                <div style="margin-top:12px;">
                    <label class="form-label">Preview</label>
                    <div id="rt-preview-box"
                        style="height:42px;border-radius:8px;overflow:hidden;background:#111;display:flex;align-items:center;position:relative;">
                        <span id="rt-preview-text"
                            style="white-space:nowrap;font-size:<?= $s['rt_size'] ?? 24 ?>px;color:<?= $s['rt_color'] ?? '#fff' ?>;font-family:<?= $s['rt_font'] ?? 'sans-serif' ?>;padding:0 16px;">
                            <?= isset($running_text) ? htmlspecialchars($running_text) : 'Teks Berjalan Preview...' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════
                 TAB: JAM & TANGGAL
            ══════════════════════════════════ -->
            <div class="ds-panel" id="ds-dt" style="display:none;">

                <div style="background:#f0f7ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:12px;color:#1e40af;">
                    🕐 Mengatur kotak jam dan tanggal di sisi kiri bottom bar.
                </div>

                <div class="form-group">
                    <label class="form-label">Format Jam</label>
                    <select name="dt_jam_type" class="form-select">
                        <?php $dt_jam = $s['dt_jam_type'] ?? 'HH:MM:SS'; ?>
                        <option value="HH:MM:SS" <?= $dt_jam==="HH:MM:SS"?"selected":"" ?>>HH:MM:SS — 24 jam dengan detik</option>
                        <option value="HH:MM"    <?= $dt_jam==="HH:MM"?"selected":"" ?>>HH:MM — 24 jam tanpa detik</option>
                        <option value="12H"      <?= $dt_jam==="12H"?"selected":"" ?>>hh:mm:ss AM/PM — 12 jam</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Font Jam & Tanggal</label>
                    <select name="dt_font" class="form-select">
                        <?php $dt_font = $s['dt_font'] ?? 'monospace'; ?>
                        <option value="monospace"                 <?= $dt_font==="monospace"?"selected":"" ?>>Monospace (Default)</option>
                        <option value="'Courier New', monospace"  <?= $dt_font==="'Courier New', monospace"?"selected":"" ?>>Courier New</option>
                        <option value="sans-serif"                <?= $dt_font==="sans-serif"?"selected":"" ?>>Sans Serif</option>
                        <option value="serif"                     <?= $dt_font==="serif"?"selected":"" ?>>Serif</option>
                        <option value="Arial, sans-serif"         <?= $dt_font==="Arial, sans-serif"?"selected":"" ?>>Arial</option>
                        <option value="Impact, sans-serif"        <?= $dt_font==="Impact, sans-serif"?"selected":"" ?>>Impact</option>
                        <option value="Verdana, sans-serif"       <?= $dt_font==="Verdana, sans-serif"?"selected":"" ?>>Verdana</option>
                        <option value="'Arial Black', sans-serif" <?= $dt_font==="'Arial Black', sans-serif"?"selected":"" ?>>Arial Black</option>
                    </select>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Ukuran Jam (px)</label>
                        <input type="number" name="dt_size" class="form-input"
                            value="<?= $s['dt_size'] ?? 28 ?>" min="12" max="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Warna Teks</label>
                        <div style="display:flex;gap:6px;align-items:center;">
                            <input type="color" name="dt_color" id="dt_color_pick"
                                value="<?= $s['dt_color'] ?? '#ffffff' ?>"
                                style="width:40px;height:36px;border:none;border-radius:6px;cursor:pointer;flex-shrink:0;"
                                oninput="syncHex('dt_color')">
                            <input type="text" id="dt_color_hex" class="form-input"
                                value="<?= $s['dt_color'] ?? '#ffffff' ?>"
                                style="font-size:12px;"
                                oninput="syncPicker('dt_color')">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Background Kotak Jam</label>
                    <select name="dt_bg_type" class="form-select" onchange="toggleBgOpts('dt',this.value)">
                        <?php $dt_bg = $s['dt_bg_type'] ?? 'transparent'; ?>
                        <option value="transparent" <?= $dt_bg==="transparent"?"selected":"" ?>>Transparan</option>
                        <option value="solid"       <?= $dt_bg==="solid"?"selected":"" ?>>Warna Solid</option>
                        <option value="blur"        <?= $dt_bg==="blur"?"selected":"" ?>>Blur / Frosted Glass</option>
                        <option value="gradient"    <?= $dt_bg==="gradient"?"selected":"" ?>>Gradient</option>
                    </select>
                </div>
                <div id="dt_bg_opts"
                    style="display:<?= in_array($s['dt_bg_type'] ?? '', ['solid','blur','gradient']) ? 'grid' : 'none' ?>;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Warna BG</label>
                        <input type="color" name="dt_bg_color" value="<?= $s['dt_bg_color'] ?? '#000000' ?>"
                            style="width:100%;height:36px;border:none;border-radius:6px;cursor:pointer;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Intensitas Blur (px)</label>
                        <input type="number" name="dt_bg_blur" class="form-input"
                            value="<?= $s['dt_bg_blur'] ?? 8 ?>" min="0" max="40">
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════
                 TAB: BOTTOM BAR
            ══════════════════════════════════ -->
            <div class="ds-panel" id="ds-bar" style="display:none;">

                <div style="background:#f0f7ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:12px;color:#1e40af;">
                    📊 Mengatur background bar keseluruhan di bagian bawah layar.
                </div>

                <div class="form-group">
                    <label class="form-label">Background Bottom Bar</label>
                    <select name="bar_bg_type" class="form-select" onchange="toggleBgOpts('bar',this.value)">
                        <?php $bar_bg = $s['bar_bg_type'] ?? 'solid'; ?>
                        <option value="solid"       <?= $bar_bg==="solid"?"selected":"" ?>>Warna Solid</option>
                        <option value="transparent" <?= $bar_bg==="transparent"?"selected":"" ?>>Transparan</option>
                        <option value="blur"        <?= $bar_bg==="blur"?"selected":"" ?>>Blur / Frosted Glass</option>
                        <option value="gradient"    <?= $bar_bg==="gradient"?"selected":"" ?>>Gradient dari bawah</option>
                    </select>
                </div>
                <div id="bar_bg_opts"
                    style="display:<?= in_array($s['bar_bg_type'] ?? 'solid', ['solid','blur','gradient']) ? 'grid' : 'none' ?>;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Warna BG</label>
                        <div style="display:flex;gap:6px;align-items:center;">
                            <input type="color" name="bar_bg_color" id="bar_bg_color_pick"
                                value="<?= $s['bar_bg_color'] ?? '#000000' ?>"
                                style="width:40px;height:36px;border:none;border-radius:6px;cursor:pointer;flex-shrink:0;"
                                oninput="syncHex('bar_bg_color')">
                            <input type="text" id="bar_bg_color_hex" class="form-input"
                                value="<?= $s['bar_bg_color'] ?? '#000000' ?>"
                                style="font-size:12px;"
                                oninput="syncPicker('bar_bg_color')">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Intensitas Blur (px)</label>
                        <input type="number" name="bar_bg_blur" class="form-input"
                            value="<?= $s['bar_bg_blur'] ?? 8 ?>" min="0" max="40">
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════
                 TAB: SLIDER
            ══════════════════════════════════ -->
            <div class="ds-panel" id="ds-slide" style="display:none;">

                <div style="background:#f0f7ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:12px;color:#1e40af;">
                    🖼️ Mengatur jeda waktu perpindahan antar slide banner.
                </div>

                <div class="form-group">
                    <label class="form-label">Jarak Waktu Antar Slide (detik)</label>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <input type="range" name="slider_interval" id="slider_range"
                            value="<?= $s['slider_interval'] ?? 5 ?>" min="1" max="120" step="1"
                            style="flex:1;"
                            oninput="document.getElementById('slider_val').textContent=this.value">
                        <span id="slider_val" style="font-size:20px;font-weight:700;color:var(--blue);min-width:40px;text-align:center;">
                            <?= $s['slider_interval'] ?? 5 ?>
                        </span>
                        <span style="font-size:12px;color:var(--text-muted);">detik</span>
                    </div>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:6px;">
                        ⚡ Berlaku untuk banner Gambar dan URL. Banner Video otomatis pindah setelah video selesai.
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-top:8px;">
                    <button type="button" onclick="setSlider(5)"  class="btn-tbl" style="padding:8px;">5 detik</button>
                    <button type="button" onclick="setSlider(10)" class="btn-tbl" style="padding:8px;">10 detik</button>
                    <button type="button" onclick="setSlider(30)" class="btn-tbl" style="padding:8px;">30 detik</button>
                </div>
            </div>

            <!-- ══════════════════════════════════
                 TAB: MIRROR / REFLEKSI
            ══════════════════════════════════ -->
            <div class="ds-panel" id="ds-mirror" style="display:none;">

                <div style="background:#f0f7ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:12px;color:#1e40af;">
                    🌊 Efek bayangan air — gambar banner dicerminkan di bawahnya seperti refleksi permukaan air.
                </div>

                <div class="form-group">
                    <label class="form-label" style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                        <div style="position:relative;display:inline-block;width:48px;height:26px;">
                            <input type="checkbox" name="mirror_enable" id="mirror_toggle"
                                value="1"
                                <?= ($s['mirror_enable'] ?? '0') === '1' ? 'checked' : '' ?>
                                style="opacity:0;width:0;height:0;"
                                onchange="toggleMirrorOpts(this.checked)">
                            <span id="mirror_slider_track"
                                style="position:absolute;inset:0;background:<?= ($s['mirror_enable'] ?? '0')==='1'?'#2563eb':'#ccc' ?>;border-radius:26px;transition:background .2s;cursor:pointer;"
                                onclick="document.getElementById('mirror_toggle').click()">
                                <span style="position:absolute;width:20px;height:20px;background:#fff;border-radius:50%;top:3px;
                                    left:<?= ($s['mirror_enable'] ?? '0')==='1'?'25px':'3px' ?>;
                                    transition:left .2s;pointer-events:none;" id="mirror_knob"></span>
                            </span>
                        </div>
                        <span style="font-size:13px;font-weight:600;">Aktifkan Efek Mirror / Refleksi Air</span>
                    </label>
                </div>

                <div id="mirror_opts" style="display:<?= ($s['mirror_enable'] ?? '0')==='1'?'block':'none' ?>;">
                    <div class="form-group">
                        <label class="form-label">Tinggi Refleksi (%)</label>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <input type="range" name="mirror_height" id="mirror_range"
                                value="<?= $s['mirror_height'] ?? 18 ?>" min="5" max="50" step="1"
                                style="flex:1;"
                                oninput="document.getElementById('mirror_val').textContent=this.value">
                            <span id="mirror_val" style="font-size:20px;font-weight:700;color:var(--blue);min-width:36px;text-align:center;">
                                <?= $s['mirror_height'] ?? 18 ?>
                            </span>
                            <span style="font-size:12px;color:var(--text-muted);">%</span>
                        </div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">
                            Persentase tinggi banner yang dicerminkan. Semakin besar = refleksi lebih tinggi.
                        </div>
                    </div>

                    <!-- Preview mirror visual -->
                    <div style="margin-top:8px;background:#111;border-radius:10px;overflow:hidden;aspect-ratio:16/5;position:relative;">
                        <div style="width:100%;height:70%;background:linear-gradient(135deg,#1e3a5f,#0d0d0f);display:flex;align-items:center;justify-content:center;color:#333;font-size:11px;">
                            [ Banner Area ]
                        </div>
                        <div style="width:100%;height:30%;background:linear-gradient(to bottom,#111,#000);position:relative;">
                            <div style="width:100%;height:100%;background:linear-gradient(135deg,#1e3a5f,#0d0d0f);opacity:0.4;transform:scaleY(-1);transform-origin:top;"></div>
                            <div style="position:absolute;inset:0;background:linear-gradient(to bottom,rgba(0,0,0,0) 0%,rgba(0,0,0,.9) 100%);"></div>
                        </div>
                        <div style="position:absolute;bottom:4px;left:50%;transform:translateX(-50%);font-size:9px;color:#555;">Efek Refleksi Air</div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit" style="margin-top:20px;width:100%;">
                💾 Simpan Semua Pengaturan
            </button>
        </form>
    </div>
</div>

<script>
/* ─── Tab switching ─── */
function switchDsTab(name, btn) {
    document.querySelectorAll('.ds-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.ds-tab').forEach(b => {
        b.style.background = 'none'; b.style.color = '#666';
    });
    document.getElementById('ds-' + name).style.display = 'block';
    btn.style.background = '#1e3a5f'; btn.style.color = '#fff';
}

/* ─── BG options toggle ─── */
function toggleBgOpts(prefix, val) {
    const show = ['solid','blur','gradient'].includes(val);
    document.getElementById(prefix + '_bg_opts').style.display = show ? 'grid' : 'none';
}

/* ─── Color picker <-> hex input sync ─── */
function syncHex(name) {
    const pick = document.getElementById(name + '_pick');
    const hex  = document.getElementById(name + '_hex');
    if (pick && hex) hex.value = pick.value;
}
function syncPicker(name) {
    const pick = document.getElementById(name + '_pick');
    const hex  = document.getElementById(name + '_hex');
    if (pick && hex && /^#[0-9a-fA-F]{6}$/.test(hex.value)) pick.value = hex.value;
}
/* Legacy compat */
function syncColor(name, val) { syncPicker(name); }

/* ─── Running Text preview ─── */
function updatePreviewRT() {
    const size  = document.getElementById('rt_size_inp');
    const color = document.getElementById('rt_color_pick');
    const font  = document.querySelector('[name="rt_font"]');
    const text  = document.getElementById('rt-preview-text');
    if (size)  text.style.fontSize   = size.value + 'px';
    if (color) text.style.color      = color.value;
    if (font)  text.style.fontFamily = font.value;
}

/* ─── Slider interval quick buttons ─── */
function setSlider(val) {
    const range = document.getElementById('slider_range');
    const label = document.getElementById('slider_val');
    if (range) range.value = val;
    if (label) label.textContent = val;
}

/* ─── Mirror toggle ─── */
function toggleMirrorOpts(checked) {
    document.getElementById('mirror_opts').style.display = checked ? 'block' : 'none';
    document.getElementById('mirror_slider_track').style.background = checked ? '#2563eb' : '#ccc';
    document.getElementById('mirror_knob').style.left = checked ? '25px' : '3px';
}
</script>

<script>const BASE_URL = '<?= base_url() ?>';</script>
<script src="<?= base_url('assets/js/admin.js') ?>"></script>
</body>
</html>