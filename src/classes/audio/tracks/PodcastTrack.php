<?php

namespace iutnc\deefy\audio\tracks;

class PodcastTrack extends AudioTrack
{
    protected string $auteur;
    protected string $date;
    protected string $genre;

    public function __construct($titre, $chemin, $duree = 0)
    {
        parent::__construct($titre, $chemin, $duree);
        $this->auteur = "Inconnu";
        $this->date = "Inconnue";
        $this->genre = "Inconnu";
    }
}