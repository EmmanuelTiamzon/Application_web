<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('session');
        $this->load->helper(['url', 'form']);
    }
      /* Connexion*/
    public function login() {
        if ($this->input->method() == 'post') {
            $email = $this->input->post('email', true);
            $password = $this->input->post('password', true);
            
            $user = $this->User_model->login($email, $password);
            if ($user) {
                $this->session->set_userdata([
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'logged_in' => true
                ]);
                redirect('tvshow');
            } else {
                $this->session->set_flashdata('error', 'Identifiants invalides');
                redirect('login'); 
            }
        } else {
            $this->load->view('login');
        }
    }
    
    /* Déconnexion*/
    public function logout() {
        $this->session->sess_destroy();
        redirect('tvshow');
    }
    
    /* Page inscription*/
    public function register() {
        if ($this->input->method() == 'post') {
            $firstname = trim($this->input->post('firstname', true));
            $lastname = trim($this->input->post('lastname', true));
            $email = trim($this->input->post('email', true));
            $password = $this->input->post('password', true);
            $password_confirm = $this->input->post('password_confirm', true);
            
            if (!$firstname || !$lastname || !$email || !$password) {
                $data['error'] = "Tous les champs sont obligatoires.";
            } elseif ($password !== $password_confirm) {
                $data['error'] = "Les mots de passe ne correspondent pas.";
            } elseif (!$this->User_model->is_email_unique($email)) {
                $data['error'] = "Un compte avec cet email existe déjà.";
            } elseif ($this->User_model->register($firstname, $lastname, $email, $password)) {
                redirect('login');
            } else {
                $data['error'] = "Erreur lors de l'inscription.";
            }
        }
        
        $this->load->view('register', isset($data) ? $data : []);
    }
    
    
}