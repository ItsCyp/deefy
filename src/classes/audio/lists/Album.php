<?php

namespace iutnc\deefy\audio\lists;

/**
 * Classe Album
 * Représente un album.
 */
class Album extends AudioList
{
    /**
     * @var string Le nom de l'artiste.
     */
    private string $artiste;

    /**
     * @var string La date de sortie de l'album.
     */
    private string $dateSortie;

    /**
     * Constructeur de la classe Album.
     *
     * @param string $nom Le nom de l'album.
     * @param array $pistes Les pistes de l'album.
     */
    public function __construct(string $nom, array $pistes)
    {
        parent::__construct($nom, $pistes);
    }

    /**
     * Définit le nom de l'artiste.
     *
     * @param string $artiste Le nom de l'artiste.
     */
    public function setArtiste(string $artiste): void
    {
        $this->artiste = $artiste;
    }

    /**
     * Définit la date de sortie de l'album.
     *
     * @param string $dateSortie La date de sortie de l'album.
     */
    public function setDateSortie(string $dateSortie): void
    {
        $this->dateSortie = $dateSortie;
    }
}