<?php

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\AudioTrack;
use PDO;
use PDOException;

class DeefyRepository
{
    private PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf)
    {
        $dsn = 'mysql:host=' . $conf['host'] . ';dbname=' . $conf['dbname'];
        $this->pdo = new PDO($dsn, $conf['user'], $conf['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    public static function setConfig(string $file)
    {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new PDOException("Error reading configuration file.");
        }

        self::$config = [
            'host' => $conf['host'] ?? null,
            'dbname' => $conf['dbname'] ?? null,
            'user' => $conf['username'] ?? null,
            'pass' => $conf['password'] ?? null
        ];
    }

    public static function getInstance(): ?DeefyRepository
    {
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    public function findPlaylistById(int $id): Playlist
    {
        $stmt = $this->pdo->prepare('SELECT * FROM playlist WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $playlist = $stmt->fetch();
        if ($playlist === false) {
            throw new PDOException("Playlist not found.");
        }
        return new Playlist($playlist['nom']);
    }

    public function saveEmptyPlaylist(Playlist $playlist): Playlist
    {
        $stmt = $this->pdo->prepare('INSERT INTO playlist (nom) VALUES (:nom)');
        $stmt->execute(['nom' => $playlist->nom]);
        return $playlist;
    }

    public function saveTrack(AudioTrack $track): AudioTrack
    {
        if($track instanceof AlbumTrack) {
            $stmt = $this->pdo->prepare('INSERT INTO track (titre, filename, auteur, duree) VALUES (:titre, :filename, :auteur, :duree)');
            $stmt->execute([
                'titre' => $track->titre,
                'filename' => $track->nom_du_fichier,
                'auteur' => $track->auteur,
                'duree' => $track->duree
            ]);
        } else {
            throw new PDOException("Track type not supported.");
        }
        return $track;
    }

    public function addTrackToPlaylist(AudioTrack $track, Playlist $playlist): Playlist
    {
        $trackId = $this->getTrackId($track);
        $playlistId = $this->getPlaylistId($playlist);
        $trackCount = $this->getTrackCountInPlaylist($playlistId);

        $stmt = $this->pdo->prepare('INSERT INTO playlist2track (id_pl, id_track, ordre) VALUES (:id_pl, :id_track, :ordre)');
        $stmt->execute(['id_pl' => $playlistId, 'id_track' => $trackId, 'ordre' => $trackCount + 1]);
        return $playlist;
    }

    private function getTrackId(AlbumTrack $track): int
    {
        $stmt = $this->pdo->prepare('SELECT id FROM track WHERE titre = :titre AND filename = :chemin_fichier');
        $stmt->execute(['titre' => $track->titre, 'chemin_fichier' => $track->nom_du_fichier]);
        $result = $stmt->fetch();
        if ($result === false) {
            throw new PDOException("Track not found.");
        }
        return (int)$result['id'];
    }

    private function getPlaylistId(Playlist $playlist): int
    {
        $stmt = $this->pdo->prepare('SELECT id FROM playlist WHERE nom = :nom');
        $stmt->execute(['nom' => $playlist->nom]);
        $result = $stmt->fetch();
        if ($result === false) {
            throw new PDOException("Playlist not found.");
        }
        return (int)$result['id'];
    }

    private function getTrackCountInPlaylist(int $playlistId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) as count FROM playlist2track WHERE id_pl = :id_pl');
        $stmt->execute(['id_pl' => $playlistId]);
        $result = $stmt->fetch();
        return (int)$result['count'];
    }

    public function getPdo()
    {
        return $this->pdo;
    }

}