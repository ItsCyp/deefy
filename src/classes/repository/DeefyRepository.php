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


    /*
     * Methode de recherche dans la base de données :
     */

    /**
     * Methode de recherche d'une playlist par son identifiant
     * @param int $id
     * @return Playlist
     */
    public function findPlaylistById(int $id): Playlist
    {
        $stmt = $this->pdo->prepare('SELECT * FROM playlist WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $playlistData = $stmt->fetch();
        if ($playlistData === false) {
            throw new PDOException("Playlist not found.");
        }

        $playlist = new Playlist($playlistData['nom']);

        $stmt = $this->pdo->prepare('SELECT t.* FROM track t 
                                 JOIN playlist2track p2t ON t.id = p2t.id_track 
                                 WHERE p2t.id_pl = :id');
        $stmt->execute(['id' => $id]);
        $tracks = $stmt->fetchAll();

        foreach ($tracks as $trackData) {
            $track = new AlbumTrack(
                $trackData['titre'],
                $trackData['filename'],
                $trackData['artiste_album'] ?? 'Unknown Artist',
                $trackData['numero_album'] ?? 0,
                $trackData['duree']);
            $playlist->ajouterPiste($track);
        }

        return $playlist;
    }

    public function saveEmptyPlaylist(Playlist $playlist): Playlist
    {
        $stmt = $this->pdo->prepare('INSERT INTO playlist (nom) VALUES (:nom)');
        $stmt->execute(['nom' => $playlist->nom]);
        $playlistId = $this->pdo->lastInsertId();
        $_SESSION['playlist_id']=$playlistId;

        $stmt = $this->pdo->prepare('INSERT INTO user2playlist (id_user, id_pl) VALUES (:id_user, :id_pl)');
        $stmt->execute(['id_user' => unserialize($_SESSION['user'])->id, 'id_pl' => $playlistId]);

        return $playlist;
    }

    public function saveTrack(AudioTrack $track): int
    {
        if($track instanceof AlbumTrack) {
            $stmt = $this->pdo->prepare('INSERT INTO track (titre, filename, artiste_album, numero_album, duree) 
                                         VALUES (:titre, :filename, :artiste_album, :numero_album, :duree)');
            $stmt->execute([
                'titre' => $track->titre,
                'filename' => $track->nom_du_fichier,
                'artiste_album' => $track->artiste,
                'numero_album' => $track->numero_piste,
                'duree' => $track->duree
            ]);
        } else {
            throw new PDOException("Track type not supported.");
        }
        return $this->pdo->lastInsertId();
    }

    /**
     * Methode addTrackToPlaylist() permettant d'ajouter un piste existante (dans la db) à une playlist existante (dans la db)
     * @param int $id_track
     * @param int $id_pl
     * @return void
     */
    public function addTrackToPlaylist(int $id_track, int $id_pl): void
    {
        // Vérifier si l'id_track existe
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM track WHERE id = :id_track');
        $stmt->execute(['id_track' => $id_track]);
        if ($stmt->fetchColumn() == 0) {
            throw new PDOException("Track ID not found.");
        }

        // Vérifier si l'id_pl existe
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM playlist WHERE id = :id_pl');
        $stmt->execute(['id_pl' => $id_pl]);
        if ($stmt->fetchColumn() == 0) {
            throw new PDOException("Playlist ID not found.");
        }

        $stmt = $this->pdo->prepare('INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) VALUES (:id_pl, :id_track, :no_piste_dans_liste)');
        $stmt->execute(['id_pl' => $id_pl, 'id_track' => $id_track, 'no_piste_dans_liste' => $this->getTrackCountInPlaylist($id_pl) + 1]);
    }

    public function getTrackCountInPlaylist(int $id_pl): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM playlist2track WHERE id_pl = :id_pl');
        $stmt->execute(['id_pl' => $id_pl]);
        return $stmt->fetchColumn();
    }

//    public function addTrackToPlaylist(AudioTrack $track, Playlist $playlist): Playlist
//    {
//        $trackId = $this->getTrackId($track);
//        $playlistId = $this->getPlaylistId($playlist);
//        $trackCount = $this->getTrackCountInPlaylist($playlistId);
//
//        $stmt = $this->pdo->prepare('INSERT INTO playlist2track (id_pl, id_track, ordre) VALUES (:id_pl, :id_track, :ordre)');
//        $stmt->execute(['id_pl' => $playlistId, 'id_track' => $trackId, 'ordre' => $trackCount + 1]);
//        return $playlist;
//    }

    public function findPlaylistsByUser(int $id): array
    {
        $stmt = $this->pdo->prepare('SELECT id_pl FROM user2playlist WHERE id_user = :id');
        $stmt->execute(['id' => $id]);
        $playlists = $stmt->fetchAll();
        return $playlists;
    }

    public function getPdo()
    {
        return $this->pdo;
    }

}