<?php
namespace iutnc\deefy\audio\lists;

/**
 * Classe Playlist
 * Représente une playlist de pistes audio.
 */
class Playlist extends AudioList
{
    /**
     * Ajoute une piste à la playlist.
     *
     * @param mixed $piste La piste à ajouter.
     * @return void
     */
    public function ajouterPiste($piste): void
    {
        $this->pistes[] = $piste;
        $this->nombrePistes++;
        $this->dureeTotale += $piste->duree ?? 0;
    }

    /**
     * Supprime une piste de la playlist par son index.
     *
     * @param int $index L'index de la piste à supprimer.
     * @return void
     */
    public function supprimerPiste(int $index): void
    {
        unset($this->pistes[$index]);
    }

    /**
     * Ajoute une liste de pistes à la playlist.
     *
     * @param array $pistes Les pistes à ajouter.
     * @return void
     */
    public function ajouterListePistes(array $pistes): void {
        $this->pistes = array_unique(array_merge($this->pistes, $pistes));
        $this->nombrePistes = count($this->pistes);
        foreach ($this->pistes as $piste) {
            $this->dureeTotale += $piste->duree;
        }
    }
}