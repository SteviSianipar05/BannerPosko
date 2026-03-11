<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function index() {
        if ($this->session->userdata('logged_in')) {
            redirect('Admin');
        }
        $this->load->view('auth/login');
    }

    public function login() {
        $username = $this->input->post('username');
        $password = md5($this->input->post('password'));

        $user = $this->db->get_where('users', [
            'username' => $username,
            'password' => $password
        ])->row();

        if ($user) {
            $this->session->set_userdata([
                'logged_in'      => TRUE,
                'admin_id'       => $user->id,
                'username'       => $user->username,
                'role'           => $user->role,
                'nama_tampilan'  => $user->nama_tampilan,
                'foto_profil'    => $user->foto_profil,
            ]);
            redirect('Admin');
        } else {
            $this->session->set_flashdata('error', 'Username atau password salah!');
            redirect('Auth');
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('Auth');
    }
}