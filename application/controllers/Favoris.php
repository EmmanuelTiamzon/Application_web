<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Favoris extends CI_Controller {

    public function ajouter($tvshow_id)
    {
        if ($this->session->userdata('logged_in')) {
            $user_id = $this->session->userdata('user_id');
            $this->load->model('Favoris_model');
            $this->Favoris_model->ajouter_favori($user_id, $tvshow_id);
        }
        redirect('tvshow/detail/' . $tvshow_id);
    }

    public function supprimer($tvshow_id)
    {
        if ($this->session->userdata('logged_in')) {
            $user_id = $this->session->userdata('user_id');
            $this->load->model('Favoris_model');
            $this->Favoris_model->supprimer_favori($user_id, $tvshow_id);
        }
        redirect('tvshow/detail/' . $tvshow_id);
    }
}
