<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tvshow extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_tvshow');
        $this->load->model('Critique_model');
        $this->load->model('Favoris_model');
        $this->load->helper(['url', 'html']);
        $this->load->library('session');

        if (!$this->session->has_userdata('visitor_id')) {
            $this->session->set_userdata('visitor_id', uniqid('visitor_', true));
        }
    }

    /* Liste des séries avec possibilité de recherche */
    public function index()
    {
        $search = $this->input->get('search');
        $genre = $this->input->get('type');
        $sort = $this->input->get('sort');
        $min_rating = $this->input->get('min_rating');

        $order_by = null;
        if ($sort === 'note_desc') {
            $order_by = ['average_rating' => 'DESC'];
        } elseif ($sort === 'note_asc') {
            $order_by = ['average_rating' => 'ASC'];
        }

        $tvshows = $this->Model_tvshow->getAllFiltered($genre, $search, $order_by, $min_rating);

        $data = [
            'tvshows' => $tvshows,
            'all_genres' => $this->Model_tvshow->getAllGenres(),
            'session' => $this->session,
            'search' => $search,
        ];

        $this->load->view('layout/header', $data);

        if (!empty($search) && empty($tvshows)) {
            $this->load->view('search_results', $data);
        } else {
            $this->load->view('tvshow_list', $data);
        }

        $this->load->view('layout/footer');
    }


    /* Détail d’une série (avec épisodes et critiques globales) */
    public function detail($id = 0)
    {
        $tvshow = $this->Model_tvshow->getTvshowById($id);
        if (!$tvshow) show_404();

        $episodes = $this->Model_tvshow->getEpisodesByTvshowId($id);
        $critiques = $this->Critique_model->get_critiques_by_tvshow($id);
        $genres = $this->Model_tvshow->getGenresByTvshowId($id);
        $homepage = $this->Model_tvshow->getHomepageById($id);
        $suggestions = $this->Model_tvshow->getSuggestionsByTvshowId($id);
        $all_genres = $this->Model_tvshow->getAllGenres();

        $est_favori = false;
        if ($this->session->userdata('logged_in')) {
            $user_id = $this->session->userdata('user_id');
            $est_favori = $this->Favoris_model->is_favori($user_id, $id);
        }

        $ratingData = $this->Critique_model->getAverageRating($id);
        $moyenne = $ratingData['moyenne'];
        $nb_votes = $ratingData['nb_votes'];

        $data = [
            'tvshow' => $tvshow,
            'episodes' => $episodes,
            'critiques' => $critiques,
            'genres' => $genres,
            'all_genres' => $all_genres,
            'session' => $this->session,
            'homepage' => $homepage,
            'moyenne' => $moyenne,
            'est_favori' => $est_favori,
            'nb_votes' => $nb_votes,
            'suggestions' => $suggestions,
        ];

        $this->load->view('layout/header', $data);
        $this->load->view('tvshow_detail', $data);
        $this->load->view('layout/footer');
    }

    /* Détail d’une saison spécifique */
    public function saison($tvshow_id, $season_number)
    {
        $tvshow = $this->Model_tvshow->getTvshowById($tvshow_id);
        if (!$tvshow) show_404();

        $season = $this->Model_tvshow->getSeasonByNumber($tvshow_id, $season_number);
        if (!$season) show_404();

        $episodes = $this->Model_tvshow->getEpisodesBySeason($tvshow_id, $season_number);
        $all_genres = $this->Model_tvshow->getAllGenres();
        $critiques = $this->Critique_model->get_critiques_by_tvshow($tvshow_id);
        $season_id = $season->id;
        $critiques_saison = array_filter($critiques, fn($c) => $c->season_id == $season_id);

        $data = [
            'tvshow' => $tvshow,
            'season' => $season,
            'episodes' => $episodes,
            'all_genres' => $all_genres,
            'session' => $this->session,
            'critiques_saison' => $critiques_saison
        ];

        $this->load->view('layout/header', $data);
        $this->load->view('season_detail', $data);
        $this->load->view('layout/footer');
    }

    /* Ajout d’une critique */public function add_critique()
{
    if (!$this->session->userdata('logged_in')) {
        redirect('login');
        return;
    }

    $user_id = $this->session->userdata('user_id');
    $tvshow_id = $this->input->post('tvshow_id');
    $season_number = $this->input->post('season_id');
    $note = (int)$this->input->post('note');
    $commentaire = $this->input->post('commentaire');

    if (!$tvshow_id || !$note || $note < 1 || $note > 5) {
        $this->session->set_flashdata('error', 'Critique invalide.');
        redirect('tvshow/detail/' . $tvshow_id);
        return;
    }

    $season_id = null;

    if ($season_number !== '' && $season_number !== null) {
        $season = $this->Model_tvshow->getSeasonIdByNumber($tvshow_id, $season_number);
        $season_id = $season ? $season->id : null;
    }

    $data = [
        'user_id' => $user_id,
        'tvshow_id' => $tvshow_id,
        'season_id' => $season_id, // NULL si série entière
        'note' => $note,
        'commentaire' => $commentaire,
        'date_creation' => date('Y-m-d H:i:s')
    ];

    $this->Critique_model->add_critique($data);
    $this->session->set_flashdata('success', 'Critique ajoutée !');

    // Redirection selon le type d’avis
    if ($season_number && $season_number != 2147483647) {
        redirect('tvshow/saison/' . $tvshow_id . '/' . $season_number);
    } else {
        redirect('tvshow/detail/' . $tvshow_id);
    }
}

}
