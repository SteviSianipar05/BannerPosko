<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AutoStatus {
    public function update() {
        $CI =& get_instance();
        $now = date('Y-m-d H:i:s');

        // Nonaktifkan banner yang jadwal selesainya sudah lewat
        $CI->db->query("
            UPDATE banner 
            SET status = 'nonaktif' 
            WHERE status = 'aktif' 
            AND jadwal_selesai IS NOT NULL 
            AND jadwal_selesai < '$now'
        ");

        // Aktifkan banner yang jadwal mulainya sudah tiba
        $CI->db->query("
            UPDATE banner 
            SET status = 'aktif' 
            WHERE status = 'nonaktif' 
            AND jadwal_mulai IS NOT NULL 
            AND jadwal_mulai <= '$now' 
            AND jadwal_selesai >= '$now'
        ");
    }
}