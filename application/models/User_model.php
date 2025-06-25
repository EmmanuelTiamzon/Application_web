<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    public function register($firstname, $lastname, $email, $password) {
        $sql_check = "SELECT id FROM users WHERE email = ? LIMIT 1";
        $query = $this->db->query($sql_check, [$email]);

        if ($query->num_rows() > 0) {
            return false;
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $sql_insert = "
            INSERT INTO users (firstname, lastname, email, password)
            VALUES (?, ?, ?, ?)
        ";
        return $this->db->query($sql_insert, [$firstname, $lastname, $email, $hashed_password]);
    }

    /**
     * Vérifie si un email est unique
     */
    public function is_email_unique($email) {
        $sql = "SELECT id FROM users WHERE email = ? LIMIT 1";
        $query = $this->db->query($sql, [$email]);
        return $query->num_rows() === 0;
    }
    /**
     * Récupère les séries en favoris avec le nombre de saisons
     */
    public function get_favoris_with_season_count($user_id) {
        $sql = "
            SELECT tvshow.id, tvshow.name, poster.jpeg, COUNT(season.id) AS season_count
            FROM favoris
            JOIN tvshow ON tvshow.id = favoris.tvshow_id
            LEFT JOIN poster ON poster.id = tvshow.posterId
            LEFT JOIN season ON season.tvShowId = tvshow.id
            WHERE favoris.user_id = ?
            GROUP BY tvshow.id, tvshow.name, poster.jpeg
        ";
        return $this->db->query($sql, [$user_id])->result();
    }

    /**
     * Connexion utilisateur
     */
    public function login($email, $password) {
        $sql = "
            SELECT * FROM users
            WHERE email = ?
            LIMIT 1
        ";
        $query = $this->db->query($sql, [$email]);
        $user = $query->row();

        if ($user && password_verify($password, $user->password)) {
            return $user;
        }

        return false;
    }

    /**
     * Récupère un utilisateur par ID
     */
    public function get_user($id) {
        $sql = "
            SELECT * FROM users
            WHERE id = ?
            LIMIT 1
        ";
        return $this->db->query($sql, [$id])->row();
    }
}
