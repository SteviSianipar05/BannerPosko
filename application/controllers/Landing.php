<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Landing extends CI_Controller {

    private function _get_settings() {
        $rows = $this->db->get('setting')->result();
        $s = [];
        foreach ($rows as $r) $s[$r->kunci] = $r->nilai;
        return $s;
    }

    public function index() {
        date_default_timezone_set('Asia/Jakarta');
        $now = date('Y-m-d H:i:s');

        $this->db->query("UPDATE banner SET status = 'aktif' WHERE status = 'nonaktif' AND jadwal_mulai IS NOT NULL AND jadwal_selesai IS NOT NULL AND jadwal_mulai <= '$now' AND jadwal_selesai > '$now'");
        $this->db->query("UPDATE banner SET status = 'nonaktif' WHERE status = 'aktif' AND jadwal_selesai IS NOT NULL AND jadwal_selesai < '$now'");

        $settings = $this->_get_settings();
        $data['banners']      = $this->db->get_where('banner', ['status' => 'aktif'])->result();
        $data['running_text'] = $settings['running_text'] ?? 'Selamat Datang di BannerPosko';
        $data['settings']     = $settings;
        $this->load->view('landing', $data);
    }

    public function preview($id) {
        $settings = $this->_get_settings();
        $data['banners']      = $this->db->get_where('banner', ['id' => $id])->result();
        $data['running_text'] = $settings['running_text'] ?? '';
        $data['settings']     = $settings;
        $this->load->view('landing', $data);
    }
}