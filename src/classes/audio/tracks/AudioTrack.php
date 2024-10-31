<?php

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception as exception;

/**
 * Classe AudioTrack
 * Classe abstraite représentant une piste audio.
 */
abstract class AudioTrack
{
    private string $titre;
    private int $duree;
    private string $nom_du_fichier;

    /**
     * Constructeur de la classe AudioTrack.
     *
     * @param string $titre Le titre de la piste.
     * @param string $chemin_fichier Le chemin du fichier audio.
     * @param int $duree La durée de la piste en secondes.
     */
    public function __construct(string $titre, string $chemin_fichier, int $duree)
    {
        $this->titre = $titre;
        $this->nom_du_fichier = $chemin_fichier;
        $this->setDuree($duree);
    }

    /**
     * Retourne une représentation JSON de l'objet.
     *
     * @return string La représentation JSON de l'objet.
     */
    public function __toString(): string
    {
        return json_encode(get_object_vars($this), JSON_PRETTY_PRINT);
    }

    /**
     * Accesseur magique pour les propriétés de la classe.
     *
     * @param string $property Le nom de la propriété.
     * @return mixed La valeur de la propriété.
     * @throws exception\InvalidPropertyNameException Si la propriété n'existe pas.
     */
    public function __get(string $property): mixed
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new exception\InvalidPropertyNameException($property);
        }
    }

    /**
     * Définit la durée de la piste.
     *
     * @param int $d La durée de la piste en secondes.
     * @return void
     * @throws exception\InvalidPropertyValueException Si la durée est inférieure ou égale à 0.
     */
    public function setDuree(int $d): void
    {
        if ($d > 0) {
            $this->duree = $d;
        } else {
            throw new exception\InvalidPropertyValueException("La durée doit être supérieure à 0");
        }
    }
}