<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\exception as exception;

/**
 * Classe AudioList
 * Représente une liste de pistes audio.
 */
class AudioList
{
    protected string $nom;
    protected int $nombrePistes = 0;
    protected array $pistes = [];
    protected int $dureeTotale = 0;

    /**
     * Constructeur de la classe AudioList.
     *
     * @param string $nom Le nom de la liste audio.
     * @param array $pistes Les pistes de la liste audio.
     */
    public function __construct(string $nom, array $pistes = [])
    {
        $this->nom = $nom;
        $this->nombrePistes = count($pistes);
        $this->pistes = $pistes;
        $this->dureeTotale = $this->calculerDureeTotale();
    }

    /**
     * Calcule la durée totale des pistes.
     *
     * @return int La durée totale des pistes en secondes.
     */
    private function calculerDureeTotale(): int
    {
        $duree = 0;
        foreach ($this->pistes as $piste) {
            $duree += $piste->duree;
        }
        return $duree;
    }

    /**
     * Méthode magique pour accéder aux propriétés.
     *
     * @param string $property Le nom de la propriété.
     * @return mixed La valeur de la propriété.
     * @throws exception\InvalidPropertyNameException Si la propriété n'existe pas.
     */
    public function __get($property)
    {
        if(property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new exception\InvalidPropertyNameException($property);
        }
    }
}