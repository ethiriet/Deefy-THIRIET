<?php

namespace iutnc\deefy\audio\tracks;

class AlbumTrack extends AudioTrack
{
    protected string $artiste;
    protected string $album;
    protected int $annee;
    protected int $numero_piste;
    protected string $genre;

    public function __construct($titre, $chemin_fichier, $album, $numero_piste, $duree = 0, $artiste = "Inconnu")
    {
        parent::__construct($titre, $chemin_fichier, $duree);
        $this->album = $album;
        $this->numero_piste = $numero_piste;
        $this->artiste = $artiste;
        $this->annee = 0;
        $this->genre = "Inconnu";
    }

    public function setArtiste(string $artiste): void
    {
        $this->artiste = $artiste;
    }

    public function setAnnee(int $annee): void
    {
        $this->annee = $annee;
    }

    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }
}
