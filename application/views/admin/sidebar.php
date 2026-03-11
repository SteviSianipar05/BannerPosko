<!-- ===== SIDEBAR ===== -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"></div>
        <span class="brand-name">BannerPosko</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Menu</div>
        <a class="nav-link <?= (uri_string() == 'Admin' || uri_string() == 'Admin/index') ? 'active' : '' ?>"
           href="<?= base_url('Admin') ?>">
            <span class="icon"></span> Dashboard
        </a>

        <div class="nav-label" style="margin-top:10px;">Landing Page</div>
        <div class="nav-accordion-header" onclick="toggleAccordion()">
            <div class="left"><span class="icon"></span> Update Landing Page</div>
            <span class="accordion-arrow" id="accordionArrow">></span>
        </div>
        <div class="nav-submenu" id="landingSubmenu">
            <a class="nav-link" href="#" onclick="openModal('modalUpdateGambar'); return false;">
                <span class="icon"></span> Update Media
            </a>
            <a class="nav-link" href="#" onclick="openModal('modalJadwalBaru'); return false;">
                <span class="icon"></span> Update Penjadwalan
            </a>
            <a class="nav-link" href="#" onclick="openModal('modalRunningText'); return false;">
                <span class="icon"></span> Update Running Text
            </a>
        </div>

        <div class="nav-label" style="margin-top:10px;">Lainnya</div>
        <button class="nav-link" onclick="openModal('modalDisplaySettings')" style="width:100%;text-align:left;background:none;border:none;cursor:pointer;">
            <span class="icon"></span> Display Settings
        </button>
        <a class="nav-link <?= (uri_string() == 'Admin/pengaturan') ? 'active' : '' ?>"
           href="<?= base_url('Admin/pengaturan') ?>">
            <span class="icon"></span> Pengaturan
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-box">
            <?php $foto = $this->session->userdata('foto_profil'); ?>
            <?php if ($foto): ?>
                <img src="<?= base_url('uploads/profil/' . $foto) ?>"
                     style="width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0;">
            <?php else: ?>
                <div class="user-ava"><?= strtoupper(substr($this->session->userdata('username'), 0, 1)) ?></div>
            <?php endif; ?>
            <div>
                <div class="user-name">
                    <?= $this->session->userdata('nama_tampilan') ?: $this->session->userdata('username') ?>
                </div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
    </div>
</div>