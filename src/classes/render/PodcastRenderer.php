<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists as lists;
use iutnc\deefy\audio\tracks as tracks;

/**
 * Classe PodcastRenderer
 *
 * Cette classe est responsable du rendu des pistes de podcast.
 */
class PodcastRenderer extends AudioTrackRenderer
{
    /**
     * @var tracks\PodcastTrack $podcastTrack Instance de la piste de podcast
     */
    private tracks\PodcastTrack $podcastTrack;

    /**
     * Constructeur de la classe PodcastRenderer
     *
     * @param tracks\PodcastTrack $a Instance de la piste de podcast
     */
    public function __construct(tracks\PodcastTrack $a)
    {
        $this->podcastTrack = $a;
    }

    /**
     * Rend le contenu compact de la piste de podcast
     *
     * @return string Le rendu compact de la piste de podcast
     */
    protected function renderCompact(): string
    {
        return "
        <div classes='track-compact'>
            <h3>{$this->podcastTrack->titre} - {$this->podcastTrack->auteur}</h3>
            <audio controls>
                <source src='{$this->podcastTrack->nom_du_fichier}' type='audio/mpeg'>
                Votre navigateur ne supporte pas la balise audio.
            </audio>
        </div>
        ";
    }

    /**
     * Rend le contenu détaillé de la piste de podcast
     *
     * @return string Le rendu détaillé de la piste de podcast
     */
    protected function renderLong(): string
    {
        return "
        <div classes='track-long'>
            <h3>{$this->podcastTrack->titre} - {$this->podcastTrack->auteur}</h3>
            <p><strong>Date :</strong> {$this->podcastTrack->date}</p>
            <p><strong>Genre :</strong> {$this->podcastTrack->genre}</p>
            <audio controls>
                <source src='{$this->podcastTrack->nom_du_fichier}' type='audio/mpeg'>
                Votre navigateur ne supporte pas la balise audio.
            </audio>
        </div>
        ";
    }
}