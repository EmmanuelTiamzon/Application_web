<?php
class Favoris_model extends CI_Model {

    public function get_favoris_by_user($user_id)
    {
        $sql = "
            SELECT f.*, t.name, t.id as tvshow_id
            FROM favoris f
            JOIN tvshow t ON t.id = f.tvshow_id
            WHERE f.user_id = ?
        ";
        return $this->db->query($sql, [$user_id])->result();
    }

    public function is_favori($user_id, $tvshow_id)
    {
        $sql = "
            SELECT 1
            FROM favoris
            WHERE user_id = ? AND tvshow_id = ?
            LIMIT 1
        ";
        $query = $this->db->query($sql, [$user_id, $tvshow_id]);
        return $query->num_rows() > 0;
    }

    public function ajouter_favori($user_id, $tvshow_id)
    {
        if (!$this->is_favori($user_id, $tvshow_id)) {
            $sql = "
                INSERT INTO favoris (user_id, tvshow_id)
                VALUES (?, ?)
            ";
            $this->db->query($sql, [$user_id, $tvshow_id]);
        }
    }

    public function supprimer_favori($user_id, $tvshow_id)
    {
        $sql = "
            DELETE FROM favoris
            WHERE user_id = ? AND tvshow_id = ?
        ";
        $this->db->query($sql, [$user_id, $tvshow_id]);
    }
}
