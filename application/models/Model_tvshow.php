<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_tvshow extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /** Toutes les séries + poster + nb de saisons */
    public function getTvshows()
    {
        $sql = "
            SELECT tvshow.id,
                   tvshow.name,
                   poster.jpeg,
                   (
                       SELECT COUNT(*)
                       FROM season
                       WHERE season.tvShowId = tvshow.id
                       AND season.seasonNumber != 2147483647
                   ) AS seasons_count
            FROM tvshow
            JOIN poster ON poster.id = tvshow.posterId
        ";
        return $this->db->query($sql)->result();
    }

    /** Suggestions basées sur les genres en commun */
    public function getSuggestionsByTvshowId($tvshowId)
    {
        $sql = "
            SELECT ts.id,
                   ts.name,
                   p.jpeg,
                   COUNT(DISTINCT tsg1.genreId) AS common_genres,
                   COUNT(DISTINCT s.id) AS seasons_count
            FROM tvshow ts
            JOIN poster p ON p.id = ts.posterId
            JOIN tvshow_genre tsg1 ON tsg1.tvShowId = ts.id
            JOIN tvshow_genre tsg2 ON tsg2.genreId = tsg1.genreId
            LEFT JOIN season s ON s.tvShowId = ts.id
            WHERE tsg2.tvShowId = ?
              AND ts.id != ?
            GROUP BY ts.id, ts.name, p.jpeg
            ORDER BY common_genres DESC, ts.name
            LIMIT 8
        ";
        return $this->db->query($sql, [$tvshowId, $tvshowId])->result();
    }

    /** Une série par son id */
    public function getTvshowById($id)
    {
        $sql = "
            SELECT tvshow.*, poster.jpeg
            FROM tvshow
            JOIN poster ON poster.id = tvshow.posterId
            WHERE tvshow.id = ?
        ";
        return $this->db->query($sql, [$id])->row();
    }

    /** Homepage d'une série */
    public function getHomepageById($id)
    {
        $sql = "
            SELECT homepage
            FROM tvshow
            WHERE id = ?
        ";
        $query = $this->db->query($sql, [$id]);
        return $query->row() ? $query->row()->homepage : null;
    }

    /** Tous les épisodes d’une série, triés par saison puis numéro d’épisode */
    public function getEpisodesByTvshowId($tvShowId)
    {
        $sql = "
            SELECT season.seasonNumber AS season_number,
                   episode.episodeNumber AS episode_number,
                   episode.name AS episode_name,
                   episode.overview AS episode_overview
            FROM season
            JOIN episode ON episode.seasonId = season.id
            WHERE season.tvShowId = ?
            ORDER BY season.seasonNumber ASC, episode.episodeNumber ASC
        ";
        return $this->db->query($sql, [$tvShowId])->result();
    }
    /** Récupère l'ID d'une saison par numéro de saison et ID de série */
    public function getSeasonIdByNumber($tvshow_id, $season_number)
    {
        $sql = "
            SELECT id
            FROM season
            WHERE tvShowId = ? AND seasonNumber = ?
            LIMIT 1
        ";
        return $this->db->query($sql, [$tvshow_id, $season_number])->row();
    }

    /** Toutes les séries d'un genre donné */
    public function getTvshowsByGenre($genre)
    {
        $sql = "
            SELECT tvshow.id,
                   tvshow.name,
                   poster.jpeg,
                   (
                       SELECT COUNT(*)
                       FROM season
                       WHERE season.tvShowId = tvshow.id
                         AND season.seasonNumber != 2147483647
                   ) AS seasons_count
            FROM tvshow
            JOIN poster ON poster.id = tvshow.posterId
            JOIN tvshow_genre ON tvshow_genre.tvShowId = tvshow.id
            JOIN genre ON genre.id = tvshow_genre.genreId
            WHERE genre.name = ?
              AND EXISTS (
                  SELECT 1 FROM season
                  WHERE season.tvShowId = tvshow.id
                    AND season.seasonNumber != 2147483647
              )
        ";
        return $this->db->query($sql, [$genre])->result();
    }
    public function getAllFiltered($genre = null, $search = null, $order_by = null, $min_rating = null)
{
    $sql = "
        SELECT 
            tvshow.id,
            tvshow.name,
            tvshow.overview,
            tvshow.homepage,
            poster.jpeg,
            AVG(critiques.note) AS average_rating,
            COUNT(DISTINCT season.id) AS seasons_count
        FROM tvshow
        JOIN poster ON poster.id = tvshow.posterId
        LEFT JOIN tvshow_genre ON tvshow_genre.tvShowId = tvshow.id
        LEFT JOIN genre ON genre.id = tvshow_genre.genreId
        LEFT JOIN critiques ON critiques.tvshow_id = tvshow.id
        LEFT JOIN season ON season.tvShowId = tvshow.id
    ";

    $conditions = [];
    $params = [];

    if ($genre) {
        $conditions[] = "genre.name = ?";
        $params[] = $genre;
    }

    if ($search) {
        $conditions[] = "tvshow.name LIKE ?";
        $params[] = '%' . $search . '%';
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " GROUP BY tvshow.id";

    if ($min_rating) {
        $sql .= " HAVING average_rating >= ?";
        $params[] = $min_rating;
    }

    if ($order_by) {
        foreach ($order_by as $column => $direction) {
            $sql .= " ORDER BY $column $direction";
            break;
        }
    }

    return $this->db->query($sql, $params)->result();
}

    /** Tous les genres associés à une série */
    public function getGenresByTvshowId($tvshowId)
    {
        $sql = "
            SELECT genre.name
            FROM genre
            JOIN tvshow_genre ON tvshow_genre.genreId = genre.id
            WHERE tvshow_genre.tvShowId = ?
            ORDER BY genre.name
        ";
        return $this->db->query($sql, [$tvshowId])->result();
    }

    /** Tous les genres disponibles */
    public function getAllGenres()
    {
        $sql = "
            SELECT name
            FROM genre
            ORDER BY name
        ";
        return $this->db->query($sql)->result();
    }

    /** Recherche de séries par nom, avec filtre optionnel par genre */
    public function searchTvshows($query, $genre = null)
    {
        $sql = "
            SELECT tvshow.id,
                   tvshow.name,
                   poster.jpeg,
                   (
                       SELECT COUNT(*)
                       FROM season
                       WHERE season.tvShowId = tvshow.id
                   ) AS seasons_count
            FROM tvshow
            JOIN poster ON poster.id = tvshow.posterId
            WHERE tvshow.name LIKE ?
        ";

        $params = ['%' . $query . '%'];

        if (!empty($genre)) {
            $sql .= "
                AND tvshow.id IN (
                    SELECT tvshow_genre.tvShowId
                    FROM tvshow_genre
                    JOIN genre ON genre.id = tvshow_genre.genreId
                    WHERE genre.name = ?
                )
            ";
            $params[] = $genre;
        }

        return $this->db->query($sql, $params)->result();
    }
    /** Récupère une saison précise par série et numéro */
public function getSeasonByNumber($tvshow_id, $season_number)
{
    $sql = "
        SELECT season.*, poster.jpeg
        FROM season
        LEFT JOIN poster ON poster.id = season.posterId
        WHERE season.tvShowId = ? AND season.seasonNumber = ?
        LIMIT 1
    ";
    return $this->db->query($sql, [$tvshow_id, $season_number])->row();
}


/** Récupère les épisodes d’une saison spécifique */
public function getEpisodesBySeason($tvshow_id, $season_number)
{
    $sql = "
        SELECT episode.episodeNumber, episode.name, episode.overview
        FROM episode
        JOIN season ON season.id = episode.seasonId
        WHERE season.tvShowId = ? AND season.seasonNumber = ?
        ORDER BY episode.episodeNumber
    ";
    return $this->db->query($sql, [$tvshow_id, $season_number])->result();
}

}
