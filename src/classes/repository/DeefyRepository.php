<?php

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\lists\Playlist;
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
            [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]);
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
            'user'=> $conf['username'] ?? null,
            'pass'=> $conf['password'] ?? null
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
}