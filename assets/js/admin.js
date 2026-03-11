function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const isMobile = window.innerWidth <= 768;

    if (isMobile) {
        sidebar.classList.toggle('mobile-open');
        let overlay = document.getElementById('sidebarOverlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            overlay.id = 'sidebarOverlay';
            overlay.onclick = toggleSidebar;
            document.body.appendChild(overlay);
        }
        overlay.classList.toggle('show');
    } else {
        sidebar.classList.toggle('hidden');
        document.getElementById('main').classList.toggle('expanded');
    }
}

function toggleAccordion() {
    document.getElementById('landingSubmenu').classList.toggle('show');
    document.getElementById('accordionArrow').classList.toggle('open');
}

function openModal(id) { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

// Tutup modal kalau klik area luar
document.querySelectorAll('.modal-overlay').forEach(function(o) {
    o.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });
});

// Modal edit jadwal (dari tombol di tabel)
function openJadwal(id, mulai, selesai) {
    document.getElementById('formJadwal').action = BASE_URL + 'Admin/update_jadwal/' + id;
    document.getElementById('jadwalMulai').value = mulai ? mulai.replace(' ', 'T') : '';
    document.getElementById('jadwalSelesai').value = selesai ? selesai.replace(' ', 'T') : '';
    openModal('modalJadwal');
}

// Modal update penjadwalan (dari sidebar)
function pilihBanner(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt.value) return;
    document.getElementById('formJadwalBaru').action = BASE_URL + 'Admin/update_jadwal/' + opt.value;
    document.getElementById('jadwalMulai2').value = opt.dataset.mulai ? opt.dataset.mulai.replace(' ', 'T') : '';
    document.getElementById('jadwalSelesai2').value = opt.dataset.selesai ? opt.dataset.selesai.replace(' ', 'T') : '';
}

// Modal update media — otomatis sesuai tipe banner yang dipilih
function previewBannerLama(select) {
    const opt     = select.options[select.selectedIndex];
    const tipe    = opt.getAttribute('data-tipe');
    const src     = opt.getAttribute('data-src');

    const box       = document.getElementById('previewLama');
    const img       = document.getElementById('previewLamaImg');
    const vid       = document.getElementById('previewLamaVideo');
    const urlText   = document.getElementById('previewLamaUrl');
    const btnSimpan = document.getElementById('btnSimpanMedia');

    // Reset semua input
    document.getElementById('uinput-gambar').style.display = 'none';
    document.getElementById('uinput-url').style.display    = 'none';
    document.getElementById('uinput-video').style.display  = 'none';

    if (!opt.value) {
        box.style.display       = 'none';
        btnSimpan.style.display = 'none';
        return;
    }

    // Tampilkan preview media lama
    box.style.display     = 'block';
    img.style.display     = 'none';
    vid.style.display     = 'none';
    urlText.style.display = 'none';

    if (tipe === 'video') {
        vid.src           = src;
        vid.style.display = 'block';
        document.getElementById('uinput-video').style.display = 'block';
    } else if (tipe === 'url') {
        urlText.textContent   = src;
        urlText.style.display = 'block';
        document.getElementById('uinput-url').style.display = 'block';
    } else {
        img.src           = src;
        img.style.display = 'block';
        document.getElementById('uinput-gambar').style.display = 'block';
    }

    // Set hidden tipe & action form
    document.getElementById('hiddenTipe').value = tipe;
    document.getElementById('formUpdateGambar').action = BASE_URL + 'Admin/update_gambar/' + opt.value;
    btnSimpan.style.display = 'block';
}

// Tampilkan nama file yang dipilih
function showFileName(input) {
    if (input.files[0]) document.getElementById('fileName').textContent = '📎 ' + input.files[0].name;
}
function showFileNameUpdate(input) {
    if (input.files[0]) document.getElementById('fileNameUpdate').textContent = '📎 ' + input.files[0].name;
}

function switchTipe(tipe) {
    document.getElementById('input-gambar').style.display = tipe === 'gambar' ? 'block' : 'none';
    document.getElementById('input-url').style.display    = tipe === 'url'    ? 'block' : 'none';
    document.getElementById('input-video').style.display  = tipe === 'video'  ? 'block' : 'none';

    ['gambar','url','video'].forEach(function(t) {
        document.getElementById('tipe-' + t).classList.remove('active');
    });
    document.getElementById('tipe-' + tipe).classList.add('active');
}

function showVideoName(input) {
    if (input.files[0]) document.getElementById('videoName').textContent = '🎬 ' + input.files[0].name;
}