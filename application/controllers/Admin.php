<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('Auth');
        }

        date_default_timezone_set('Asia/Jakarta');
        $now = date('Y-m-d H:i:s');

        $this->db->query("UPDATE banner SET status = 'aktif' WHERE status = 'nonaktif' AND jadwal_mulai IS NOT NULL AND jadwal_selesai IS NOT NULL AND jadwal_mulai <= '$now' AND jadwal_selesai > '$now'");
        $this->db->query("UPDATE banner SET status = 'nonaktif' WHERE status = 'aktif' AND jadwal_selesai IS NOT NULL AND jadwal_selesai < '$now'");
    }

    // Helper: ambil semua setting sekaligus
    private function _get_settings() {
        $rows = $this->db->get('setting')->result();
        $s = [];
        foreach ($rows as $r) $s[$r->kunci] = $r->nilai;
        return $s;
    }

    // Helper: upsert setting (update jika ada, insert jika belum)
    private function _save_setting($kunci, $nilai) {
        $exists = $this->db->get_where('setting', ['kunci' => $kunci])->num_rows();
        if ($exists) {
            $this->db->where('kunci', $kunci)->update('setting', ['nilai' => $nilai]);
        } else {
            $this->db->insert('setting', ['kunci' => $kunci, 'nilai' => $nilai]);
        }
    }

    public function index() {
        $data['banner_aktif']    = $this->db->get_where('banner', ['status' => 'aktif'])->num_rows();
        $data['banner_nonaktif'] = $this->db->get_where('banner', ['status' => 'nonaktif'])->num_rows();
        $last = $this->db->order_by('updated_at', 'DESC')->limit(1)->get('banner')->row();
        $data['terakhir_update'] = $last ? $last->updated_at : null;
        $data['banners'] = $this->db->order_by('updated_at', 'DESC')->get('banner')->result();
        $data['settings'] = $this->_get_settings();
        $data['running_text'] = $data['settings']['running_text'] ?? '';
        $this->load->view('admin/layout', $data);
    }

    public function upload_gambar() {
        $tipe = $this->input->post('tipe');
        $now = date('Y-m-d H:i:s');
        $jadwal_mulai   = $this->input->post('jadwal_mulai') ?: NULL;
        $jadwal_selesai = $this->input->post('jadwal_selesai') ?: NULL;

        if ($jadwal_mulai && $jadwal_selesai) {
            $status = ($now >= $jadwal_mulai && $now <= $jadwal_selesai) ? 'aktif' : 'nonaktif';
        } else {
            $status = 'nonaktif';
        }

        if ($tipe === 'gambar') {
            $config['upload_path']   = './uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size']      = 10240;
            $config['file_name']     = 'banner_' . time();
            if (!is_dir('./uploads/')) mkdir('./uploads/', 0777, TRUE);
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('gambar')) {
                $file = $this->upload->data();
                $this->db->insert('banner', [
                    'gambar'         => $file['file_name'],
                    'tipe'           => 'gambar',
                    'url'            => NULL,
                    'status'         => $status,
                    'jadwal_mulai'   => $jadwal_mulai,
                    'jadwal_selesai' => $jadwal_selesai,
                ]);
                $this->session->set_flashdata('success', 'Banner berhasil diupload!');
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
            }

        } elseif ($tipe === 'url') {
            $url = $this->input->post('url_input');
            if (empty($url)) {
                $this->session->set_flashdata('error', 'URL tidak boleh kosong!');
                redirect('Admin');
                return;
            }
            $this->db->insert('banner', [
                'gambar'         => NULL,
                'tipe'           => 'url',
                'url'            => $url,
                'status'         => $status,
                'jadwal_mulai'   => $jadwal_mulai,
                'jadwal_selesai' => $jadwal_selesai,
            ]);
            $this->session->set_flashdata('success', 'Banner URL berhasil ditambahkan!');

        } elseif ($tipe === 'video') {
            $config['upload_path']   = './uploads/';
            $config['allowed_types'] = 'mp4|webm|ogg';
            $config['max_size']      = 102400;
            $config['file_name']     = 'video_' . time();
            if (!is_dir('./uploads/')) mkdir('./uploads/', 0777, TRUE);
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('video_file')) {
                $file = $this->upload->data();
                $this->db->insert('banner', [
                    'gambar'         => $file['file_name'],
                    'tipe'           => 'video',
                    'url'            => NULL,
                    'status'         => $status,
                    'jadwal_mulai'   => $jadwal_mulai,
                    'jadwal_selesai' => $jadwal_selesai,
                ]);
                $this->session->set_flashdata('success', 'Video berhasil diupload!');
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
            }
        }

        redirect('Admin');
    }

    public function update_jadwal($id) {
        $now = date('Y-m-d H:i:s');
        $jadwal_mulai   = $this->input->post('jadwal_mulai') ?: NULL;
        $jadwal_selesai = $this->input->post('jadwal_selesai') ?: NULL;

        if ($jadwal_mulai && $jadwal_selesai) {
            $status = ($now >= $jadwal_mulai && $now <= $jadwal_selesai) ? 'aktif' : 'nonaktif';
        } else {
            $status = 'nonaktif';
        }

        $this->db->update('banner', [
            'jadwal_mulai'   => $jadwal_mulai,
            'jadwal_selesai' => $jadwal_selesai,
            'status'         => $status,
        ], ['id' => $id]);
        $this->session->set_flashdata('success', 'Jadwal berhasil diupdate!');
        redirect('Admin');
    }

    public function update_gambar($id) {
        $banner = $this->db->get_where('banner', ['id' => $id])->row();
        if (!$banner) {
            $this->session->set_flashdata('error', 'Banner tidak ditemukan.');
            redirect('Admin'); return;
        }

        $tipe = $this->input->post('tipe');

        if ($tipe === 'url') {
            $url = $this->input->post('url_input');
            if (empty($url)) {
                $this->session->set_flashdata('error', 'URL tidak boleh kosong!');
                redirect('Admin'); return;
            }
            if ($banner->gambar && file_exists('./uploads/' . $banner->gambar)) {
                unlink('./uploads/' . $banner->gambar);
            }
            $this->db->update('banner', ['tipe' => 'url', 'gambar' => NULL, 'url' => $url], ['id' => $id]);
            $this->session->set_flashdata('success', 'Banner URL berhasil diperbarui!');

        } elseif ($tipe === 'video') {
            $config['upload_path']   = './uploads/';
            $config['allowed_types'] = 'mp4|webm|ogg';
            $config['max_size']      = 102400;
            $config['file_name']     = 'video_' . time();
            if (!is_dir('./uploads/')) mkdir('./uploads/', 0777, TRUE);
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('video_file')) {
                if ($banner->gambar && file_exists('./uploads/' . $banner->gambar)) {
                    unlink('./uploads/' . $banner->gambar);
                }
                $file = $this->upload->data();
                $this->db->update('banner', [
                    'tipe'   => 'video',
                    'gambar' => $file['file_name'],
                    'url'    => NULL
                ], ['id' => $id]);
                $this->session->set_flashdata('success', 'Video berhasil diperbarui!');
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
            }

        } else {
            $config['upload_path']   = './uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size']      = 10240;
            $config['file_name']     = 'banner_' . time();
            if (!is_dir('./uploads/')) mkdir('./uploads/', 0777, TRUE);
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('gambar')) {
                if ($banner->gambar && file_exists('./uploads/' . $banner->gambar)) {
                    unlink('./uploads/' . $banner->gambar);
                }
                $file = $this->upload->data();
                $this->db->update('banner', [
                    'tipe'   => 'gambar',
                    'gambar' => $file['file_name'],
                    'url'    => NULL
                ], ['id' => $id]);
                $this->session->set_flashdata('success', 'Gambar berhasil diperbarui!');
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
            }
        }

        redirect('Admin');
    }

    public function hapus($id) {
        $banner = $this->db->get_where('banner', ['id' => $id])->row();
        if ($banner && $banner->gambar && file_exists('./uploads/' . $banner->gambar)) {
            unlink('./uploads/' . $banner->gambar);
        }
        $this->db->delete('banner', ['id' => $id]);
        $this->session->set_flashdata('success', 'Banner berhasil dihapus!');
        redirect('Admin');
    }

    // ================================================================
    // PENGATURAN
    // ================================================================

    public function pengaturan() {
        $data['user']    = $this->db->get_where('users', ['id' => $this->session->userdata('admin_id')])->row();
        $data['banners'] = $this->db->order_by('updated_at', 'DESC')->get('banner')->result();
        $this->load->view('admin/pengaturan', $data);
    }

    public function simpan_password() {
        $id         = $this->session->userdata('admin_id');
        $pw_lama    = $this->input->post('password_lama');
        $pw_baru    = $this->input->post('password_baru');
        $pw_konfirm = $this->input->post('password_konfirm');

        $user = $this->db->get_where('users', ['id' => $id])->row();

        if (md5($pw_lama) !== $user->password) {
            $this->session->set_flashdata('error_tab', 'password');
            $this->session->set_flashdata('error', 'Password lama tidak cocok.');
            redirect('Admin/pengaturan#password');
        }
        if ($pw_baru !== $pw_konfirm) {
            $this->session->set_flashdata('error_tab', 'password');
            $this->session->set_flashdata('error', 'Konfirmasi password baru tidak cocok.');
            redirect('Admin/pengaturan#password');
        }
        if (strlen($pw_baru) < 6) {
            $this->session->set_flashdata('error_tab', 'password');
            $this->session->set_flashdata('error', 'Password baru minimal 6 karakter.');
            redirect('Admin/pengaturan#password');
        }

        $this->db->update('users', ['password' => md5($pw_baru)], ['id' => $id]);
        $this->session->set_flashdata('success_tab', 'password');
        $this->session->set_flashdata('success', 'Password berhasil diubah!');
        redirect('Admin/pengaturan#password');
    }

    public function simpan_profil() {
        $id            = $this->session->userdata('admin_id');
        $nama_tampilan = trim($this->input->post('nama_tampilan'));
        $update_data   = ['nama_tampilan' => $nama_tampilan];

        if (!empty($_FILES['foto_profil']['name'])) {
            if (!is_dir('./uploads/profil/')) mkdir('./uploads/profil/', 0777, TRUE);

            $config = [
                'upload_path'   => './uploads/profil/',
                'allowed_types' => 'jpg|jpeg|png|webp',
                'max_size'      => 5120,
                'file_name'     => 'profil_' . $id . '_' . time(),
            ];
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('foto_profil')) {
                $file = $this->upload->data();
                $user = $this->db->get_where('users', ['id' => $id])->row();
                if ($user->foto_profil && file_exists('./uploads/profil/' . $user->foto_profil)) {
                    unlink('./uploads/profil/' . $user->foto_profil);
                }
                $update_data['foto_profil'] = $file['file_name'];
            } else {
                $this->session->set_flashdata('error_tab', 'profil');
                $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                redirect('Admin/pengaturan#profil');
            }
        }

        $this->db->update('users', $update_data, ['id' => $id]);
        $this->session->set_userdata('nama_tampilan', $nama_tampilan);
        if (isset($update_data['foto_profil'])) {
            $this->session->set_userdata('foto_profil', $update_data['foto_profil']);
        }

        $this->session->set_flashdata('success_tab', 'profil');
        $this->session->set_flashdata('success', 'Profil berhasil diperbarui!');
        redirect('Admin/pengaturan#profil');
    }

    public function hapus_foto_profil() {
        $id   = $this->session->userdata('admin_id');
        $user = $this->db->get_where('users', ['id' => $id])->row();

        if ($user->foto_profil && file_exists('./uploads/profil/' . $user->foto_profil)) {
            unlink('./uploads/profil/' . $user->foto_profil);
        }

        $this->db->update('users', ['foto_profil' => NULL], ['id' => $id]);
        $this->session->set_userdata('foto_profil', NULL);
        $this->session->set_flashdata('success_tab', 'profil');
        $this->session->set_flashdata('success', 'Foto profil berhasil dihapus.');
        redirect('Admin/pengaturan#profil');
    }

    public function simpan_running_text() {
        $teks = $this->input->post('running_text');
        $this->_save_setting('running_text', $teks);
        $this->session->set_flashdata('success', 'Running text berhasil diupdate!');
        redirect('Admin');
    }

    public function simpan_display_settings() {
        $fields = [
            'rt_font', 'rt_size', 'rt_speed', 'rt_color',
            'rt_bg_type', 'rt_bg_color', 'rt_bg_blur',
            'dt_font', 'dt_size', 'dt_jam_type', 'dt_color',
            'dt_bg_type', 'dt_bg_color', 'dt_bg_blur',
            'bar_bg_type', 'bar_bg_color', 'bar_bg_blur',
            'slider_interval',
        ];
        foreach ($fields as $f) {
            $val = $this->input->post($f);
            if ($val !== FALSE) $this->_save_setting($f, $val);
        }
        $this->session->set_flashdata('success', 'Pengaturan tampilan berhasil disimpan!');
        redirect('Admin');
    }
}