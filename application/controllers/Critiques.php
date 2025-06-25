<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Critiques extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Critique_model');
        $this->load->library('session');
        $this->load->helper(['url', 'form', 'html']);
    }
    
    public function add()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
            return;
        }
        
        $user_id = $this->session->userdata('user_id');
        $tvshow_id = $this->input->post('tvshow_id');
        $season_id = $this->input->post('season_id');
        $note = $this->input->post('note');
        $commentaire = $this->input->post('commentaire');
        
        if (empty($tvshow_id) || empty($note)) {
            $this->session->set_flashdata('error', 'Données manquantes pour ajouter la critique.');
            redirect('tvshow/detail/' . $tvshow_id);
            return;
        }
        
        $data = [
            'user_id' => $user_id,
            'tvshow_id' => $tvshow_id,
            'season_id' => $season_id ?: null,
            'note' => (int)$note,
            'commentaire' => $commentaire,
            'date_creation' => date('Y-m-d H:i:s')
        ];
        
        if ($this->Critique_model->add_critique($data)) {
            $this->session->set_flashdata('success', 'Critique ajoutée avec succès.');
        } else {
            $this->session->set_flashdata('error', 'Erreur lors de l\'ajout de la critique.');
        }
        
        redirect('tvshow/detail/' . $tvshow_id);
    }
    public function edit($id) {
        $this->load->model('Critique_model');
        
        $critique = $this->Critique_model->get_by_id($id);
        if (!$critique) {
            show_404();
        }
        
        if ($this->input->method() === 'post') {
            $note = $this->input->post('note');
            $commentaire = $this->input->post('commentaire');
            
            $data = [
                'note' => (int) $note,
                'commentaire' => $commentaire,
            ];
            
            $this->Critique_model->update_critique($id, $data);
            redirect('user/profile');
        }
        
        $data['critique'] = $critique;
        $this->load->view('layout/header');
        $this->load->view('edit_review', $data);
        $this->load->view('layout/footer');
        
    }
    
}



