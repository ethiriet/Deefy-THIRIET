<?php

namespace iutnc\deefy\audio\tracks;
use iutnc\deefy\exception\InvalidPropertyValueException;
use iutnc\deefy\exception\InvalidPropertyNameException;


abstract class AudioTrack
{
    private string $titre;
    private int $duree;
    private string $nom_du_fichier;

    public function __construct(string $titre, string $chemin_fichier, $duree)
    {
        $this->titre = $titre;
        $this->nom_du_fichier = $chemin_fichier;
        $this->setDuree($duree);
    }

    public function __toString(): string
    {
        return json_encode(get_object_vars($this), JSON_PRETTY_PRINT);
    }

    public function __get(string $property): mixed
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new InvalidPropertyNameException($property);
        }
    }

    public function setDuree($d): void
    {
        if($d>0){
            $this->duree = $d;
        } else {
            throw new InvalidPropertyValueException("La durée doit être supérieure à 0");
        }
    }
}