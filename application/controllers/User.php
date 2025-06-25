<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('User_model');
        $this->load->model('Critique_model');
        $this->load->model('Favoris_model');
        $this->load->model('Model_tvshow');
    }
    
    public function profile()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        
        $user_id = $this->session->userdata('user_id');
        
        $critiques = $this->Critique_model->get_critiques_by_user($user_id);
        
        usort($critiques, function ($a, $b) {
            return strcmp($a->tvshow_name, $b->tvshow_name);
        });
        
        $favoris = $this->User_model->get_favoris_with_season_count($user_id);
        
        /* Infos utilisateur*/
        $user = $this->User_model->get_user($user_id);
        $prenom = $user->firstname ?? '';
        $nom = $user->lastname ?? '';
        
        $data = [
            'critiques' => $critiques,
            'all_genres' => $this->Model_tvshow->getAllGenres(),
            'favoris'   => $favoris,
            'prenom'    => $prenom,
            'nom'       => $nom
        ];
        
        $this->load->view('layout/header', $data);
        $this->load->view('user_profile', $data);
        $this->load->view('layout/footer');
    }
    
}