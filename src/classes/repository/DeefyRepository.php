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

    /**
     * Constructeur privé pour empêcher l'instanciation directe
     *
     * @param array $conf Configuration de la base de données
     */
    private function __construct(array $conf)
    {
        $dsn = 'mysql:host=' . $conf['host'] . ';dbname=' . $conf['dbname'];
        $this->pdo = new PDO($dsn, $conf['user'], $conf['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    /**
     * Définit la configuration de la base de données à partir d'un fichier
     *
     * @param string $file Chemin vers le fichier de configuration
     * @throws PDOException Si le fichier de configuration ne peut pas être lu
     */
    public static function setConfig(string $file)
    {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new PDOException("Erreur lors de la lecture du fichier de configuration.");
        }

        self::$config = [
            'host' => $conf['host'] ?? null,
            'dbname' => $conf['dbname'] ?? null,
            'user' => $conf['username'] ?? null,
            'pass' => $conf['password'] ?? null
        ];
    }

    /**
     * Retourne l'instance unique de DeefyRepository
     *
     * @return DeefyRepository|null
     */
    public static function getInstance(): ?DeefyRepository
    {
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    /*
     * Méthodes de recherche dans la base de données :
     */

    /**
     * Recherche une playlist par son identifiant
     *
     * @param int $id Identifiant de la playlist
     * @return Playlist
     * @throws PDOException Si la playlist n'est pas trouvée
     */
    public function findPlaylistById(int $id): Playlist
    {
        $stmt = $this->pdo->prepare('SELECT * FROM playlist WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $playlistData = $stmt->fetch();
        if ($playlistData === false) {
            throw new PDOException("Playlist non trouvée.");
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
                $trackData['artiste_album'] ?? 'Artiste inconnu',
                $trackData['numero_album'] ?? 0,
                $trackData['duree']);
            $playlist->ajouterPiste($track);
        }

        return $playlist;
    }

    /**
     * Sauvegarde une playlist vide dans la base de données
     *
     * @param Playlist $playlist La playlist à sauvegarder
     * @return Playlist
     */
    public function saveEmptyPlaylist(Playlist $playlist): Playlist
    {
        $stmt = $this->pdo->prepare('INSERT INTO playlist (nom) VALUES (:nom)');
        $stmt->execute(['nom' => $playlist->nom]);
        $playlistId = $this->pdo->lastInsertId();
        $_SESSION['playlist_id'] = $playlistId;

        $stmt = $this->pdo->prepare('INSERT INTO user2playlist (id_user, id_pl) VALUES (:id_user, :id_pl)');
        $stmt->execute(['id_user' => unserialize($_SESSION['user'])->id, 'id_pl' => $playlistId]);

        return $playlist;
    }

    /**
     * Sauvegarde une piste audio dans la base de données
     *
     * @param AudioTrack $track La piste audio à sauvegarder
     * @return int L'identifiant de la piste audio sauvegardée
     * @throws PDOException Si le type de piste n'est pas supporté
     */
    public function saveTrack(AudioTrack $track): int
    {
        if ($track instanceof AlbumTrack) {
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
            throw new PDOException("Type de piste non supporté.");
        }
        return $this->pdo->lastInsertId();
    }

    /**
     * Ajoute une piste existante à une playlist existante
     *
     * @param int $id_track Identifiant de la piste
     * @param int $id_pl Identifiant de la playlist
     * @return void
     * @throws PDOException Si l'identifiant de la piste ou de la playlist n'est pas trouvé
     */
    public function addTrackToPlaylist(int $id_track, int $id_pl): void
    {
        // Vérifier si l'id_track existe
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM track WHERE id = :id_track');
        $stmt->execute(['id_track' => $id_track]);
        if ($stmt->fetchColumn() == 0) {
            throw new PDOException("Identifiant de piste non trouvé.");
        }

        // Vérifier si l'id_pl existe
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM playlist WHERE id = :id_pl');
        $stmt->execute(['id_pl' => $id_pl]);
        if ($stmt->fetchColumn() == 0) {
            throw new PDOException("Identifiant de playlist non trouvé.");
        }

        $stmt = $this->pdo->prepare('INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) VALUES (:id_pl, :id_track, :no_piste_dans_liste)');
        $stmt->execute(['id_pl' => $id_pl, 'id_track' => $id_track, 'no_piste_dans_liste' => $this->getTrackCountInPlaylist($id_pl) + 1]);
    }

    /**
     * Retourne le nombre de pistes dans une playlist
     *
     * @param int $id_pl Identifiant de la playlist
     * @return int Le nombre de pistes dans la playlist
     */
    public function getTrackCountInPlaylist(int $id_pl): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM playlist2track WHERE id_pl = :id_pl');
        $stmt->execute(['id_pl' => $id_pl]);
        return $stmt->fetchColumn();
    }

    /**
     * Recherche les playlists d'un utilisateur par son identifiant
     *
     * @param int $id Identifiant de l'utilisateur
     * @return array Les playlists de l'utilisateur
     */
    public function findPlaylistsByUser(int $id): array
    {
        $stmt = $this->pdo->prepare('SELECT id_pl FROM user2playlist WHERE id_user = :id');
        $stmt->execute(['id' => $id]);
        $playlists = $stmt->fetchAll();
        return $playlists;
    }

    /**
     * Retourne l'instance PDO
     *
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }
}