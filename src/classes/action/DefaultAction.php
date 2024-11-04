<?php

namespace iutnc\deefy\action;

class DefaultAction extends Action
{

    public function execute(): string
    {
        return "<h3>Bienvenue !</h3>";
        $repository = \iutnc\deefy\repository\DeefyRepository::getInstance();
        $playlist = $repository->findPlaylistById(1);
        echo $playlist;
    }
}