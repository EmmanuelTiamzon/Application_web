<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Critique_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Ajouter une critique
     */
    public function add_critique($data) {
        if (empty($data['user_id']) || empty($data['note'])) {
            return false;
        }

        $sql = "
            INSERT INTO critiques (user_id, tvshow_id, season_id, note, commentaire, date_creation)
            VALUES (?, ?, ?, ?, ?, ?)
        ";
        return $this->db->query($sql, [
            $data['user_id'],
            $data['tvshow_id'],
            $data['season_id'],
            $data['note'],
            $data['commentaire'],
            $data['date_creation']
        ]);
    }

    /**
     * Moyenne des notes pour une série
     */
    public function getAverageRating($tvshow_id) {
        $sql = "
            SELECT AVG(note) AS moyenne, COUNT(*) AS nb_votes
            FROM critiques
            WHERE tvshow_id = ?
        ";
        return $this->db->query($sql, [$tvshow_id])->row_array();
    }

    /**
     * Récupère les critiques d'une série avec email auteur
     */
    public function get_critiques_by_tvshow($tvshow_id) {
        $sql = "
            SELECT c.*, u.email AS auteur
            FROM critiques c
            JOIN users u ON u.id = c.user_id
            WHERE c.tvshow_id = ?
        ";
        return $this->db->query($sql, [$tvshow_id])->result();
    }

    /**
     * Récupère les critiques d'une saison
     */
    public function get_critiques_by_season($season_id) {
        $sql = "
            SELECT c.*, u.username
            FROM critiques c
            JOIN users u ON u.id = c.user_id
            WHERE c.season_id = ?
            ORDER BY c.date_creation DESC
        ";
        return $this->db->query($sql, [$season_id])->result();
    }

    /**
     * Récupère les critiques d'un utilisateur
     */
    public function get_critiques_by_user($user_id) {
        $sql = "
            SELECT c.*, 
                   t.name AS tvshow_name, 
                   t.id AS tvshow_id,
                   p.jpeg,
                   u.email AS auteur
            FROM critiques c
            JOIN users u ON u.id = c.user_id
            JOIN tvshow t ON t.id = c.tvshow_id
            LEFT JOIN poster p ON p.id = t.posterId
            WHERE c.user_id = ?
            ORDER BY c.id DESC
        ";
        return $this->db->query($sql, [$user_id])->result();
    }

    /**
     * Récupère toutes les critiques
     */
    public function get_all_critiques() {
        $sql = "
            SELECT c.*, u.username, t.name AS tvshow_name, s.season_number
            FROM critiques c
            JOIN users u ON u.id = c.user_id
            LEFT JOIN tvshow t ON t.id = c.tvshow_id
            LEFT JOIN season s ON s.id = c.season_id
            ORDER BY c.date_creation DESC
        ";
        return $this->db->query($sql)->result();
    }

    /**
     * Récupère une critique par ID
     */
    public function get_by_id($id) {
        $sql = "
            SELECT c.*, 
                   t.name AS tvshow_name,
                   t.id AS tvshow_id,
                   p.jpeg,
                   u.email AS auteur
            FROM critiques c
            JOIN users u ON u.id = c.user_id
            JOIN tvshow t ON t.id = c.tvshow_id
            LEFT JOIN poster p ON p.id = t.posterId
            WHERE c.id = ?
            LIMIT 1
        ";
        return $this->db->query($sql, [$id])->row();
    }

    /**
     * Mise à jour d'une critique
     */
    public function update_critique($id, $data) {
        $sql = "
            UPDATE critiques
            SET note = ?, commentaire = ?, date_creation = NOW()
            WHERE id = ?
        ";

        return $this->db->query($sql, [
            $data['note'],
            $data['commentaire'],
            $id
        ]);
    }
    }
