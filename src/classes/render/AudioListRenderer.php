<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists as lists;
use iutnc\deefy\audio\tracks as tracks;

/**
 * Classe AudioListRenderer
 *
 * Cette classe est responsable du rendu d'une liste de pistes audio.
 */
class AudioListRenderer implements Renderer
{
    private lists\AudioList $audioList;

    /**
     * Constructeur de la classe AudioListRenderer.
     *
     * @param lists\AudioList $audioList La liste de pistes audio à rendre.
     */
    public function __construct(lists\AudioList $audioList)
    {
        $this->audioList = $audioList;
    }

    /**
     * Méthode de rendu.
     *
     * @param int $type Le type de rendu.
     * @return string Le rendu HTML de la liste de pistes audio.
     */
    public function render(int $type): string
    {
        return $this->afficher();
    }

    /**
     * Méthode privée pour afficher la liste de pistes audio.
     *
     * @return string Le rendu HTML de la liste de pistes audio.
     */
    private function afficher(): string
    {
        $html = "<div>";
        $html .= "<h2>{$this->audioList->nom} :</h2>";
        foreach ($this->audioList->pistes as $piste) {
            if ($piste instanceof tracks\AlbumTrack) {
                $renderer = new AlbumTrackRenderer($piste);
            } elseif ($piste instanceof tracks\PodcastTrack) {
                $renderer = new PodcastRenderer($piste);
            }
            $html .= $renderer->render(Renderer::COMPACT);
        }

        $html .= "<p><strong>Nombre de pistes :</strong> {$this->audioList->nombrePistes}</p>";
        $html .= "<p><strong>Durée totale :</strong> {$this->audioList->dureeTotale} secondes</p>";
        $html .= "</div>";
        return $html;
    }
}