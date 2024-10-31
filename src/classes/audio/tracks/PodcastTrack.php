<?php

namespace iutnc\deefy\audio\tracks;

/**
 * Classe PodcastTrack
 * Représente une piste de podcast audio.
 */
class PodcastTrack extends AudioTrack
{
    protected string $auteur;
    protected string $date;
    protected string $genre;

    /**
     * Constructeur de la classe PodcastTrack.
     *
     * @param string $titre Le titre de la piste.
     * @param string $chemin Le chemin du fichier audio.
     * @param int $duree La durée de la piste en secondes.
     */
    public function __construct($titre, $chemin, $duree = 0)
    {
        parent::__construct($titre, $chemin, $duree);
        $this->auteur = "Inconnu";
        $this->date = "Inconnue";
        $this->genre = "Inconnu";
    }
}