<?php

namespace iutnc\deefy\audio\tracks;

class PodcastTrack extends AudioTrack
{
    protected string $auteur;
    protected string $date;
    protected string $genre;

    public function __construct($titre, $chemin, $duree = 1)
    {
        parent::__construct($titre, $chemin, $duree);
        $this->auteur = "Inconnu";
        $this->date = "Inconnue";
        $this->genre = "Inconnu";
    }

    public function setAuteur(string $auteur): void
    {
        $this->auteur = $auteur;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }
}
