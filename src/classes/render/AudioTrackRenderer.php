<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists as lists;
use iutnc\deefy\audio\tracks as tracks;

/**
 * Classe abstraite AudioTrackRenderer
 *
 * Cette classe fournit une interface pour le rendu des pistes audio.
 */
abstract class AudioTrackRenderer implements Renderer
{
    /**
     * @var tracks\AudioTrack $audioTrack Instance de la piste audio
     */
    protected tracks\AudioTrack $audioTrack;

    /**
     * Constructeur de la classe AudioTrackRenderer
     *
     * @param tracks\AudioTrack $audioTrack Instance de la piste audio
     */
    public function __construct(tracks\AudioTrack $audioTrack)
    {
        $this->audioTrack = $audioTrack;
    }

    /**
     * Méthode de rendu
     *
     * @param int $type Le type de rendu
     * @return string Le rendu HTML de la piste audio
     */
    public function render(int $type): string
    {
        switch ($type) {
            case self::COMPACT:
                return $this->renderCompact() . "\n";
            case self::LONG:
                return $this->renderLong() . "\n";
            default:
                return "Type de rendu inconnu";
        }
    }

    /**
     * Méthode abstraite pour le rendu compact
     *
     * @return string Le rendu compact de la piste audio
     */
    abstract protected function renderCompact(): string;

    /**
     * Méthode abstraite pour le rendu détaillé
     *
     * @return string Le rendu détaillé de la piste audio
     */
    abstract protected function renderLong(): string;
}