<?php

namespace iutnc\deefy\audio\tracks;

/**
 * Classe AlbumTrack
 * Représente une piste d'album audio.
 */
class AlbumTrack extends AudioTrack
{
    protected string $artiste;
    protected string $album;
    protected int $annee;
    protected int $numero_piste;
    protected string $genre;

    /**
     * Constructeur de la classe AlbumTrack.
     *
     * @param string $titre Le titre de la piste.
     * @param string $chemin_fichier Le chemin du fichier audio.
     * @param string $album Le nom de l'album.
     * @param int $numero_piste Le numéro de la piste dans l'album.
     * @param int $duree La durée de la piste en secondes.
     */
    public function __construct($titre, $chemin_fichier, $album, $numero_piste, $duree = 0)
    {
        parent::__construct($titre, $chemin_fichier, $duree);
        $this->titre = $titre;
        $this->nom_du_fichier = $chemin_fichier;
        $this->album = $album;
        $this->numero_piste = $numero_piste;
        $this->artiste = "Inconnu";
        $this->annee = 0;
        $this->genre = "Inconnu";
    }

    /**
     * Définit l'artiste de la piste.
     *
     * @param string $artiste Le nom de l'artiste.
     * @return void
     */
    public function setArtiste(string $artiste): void
    {
        $this->artiste = $artiste;
    }

    /**
     * Définit l'année de sortie de la piste.
     *
     * @param int $annee L'année de sortie.
     * @return void
     */
    public function setAnnee(int $annee): void
    {
        $this->annee = $annee;
    }

    /**
     * Définit le genre de la piste.
     *
     * @param string $genre Le genre musical.
     * @return void
     */
    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }
}