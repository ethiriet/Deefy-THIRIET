<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class SignInAction extends Action
{

    public function execute(): string
    {
        $html = "";
        if (isset($_SESSION['user'])) {
            return "<div>Connecté en tant que " . unserialize($_SESSION['user'])->email . ".</div>";
        }
        if($this->http_method === 'GET'){
            $html = <<<HTML
                <h2>Connexion</h2>
                <form method="post" action="?action=signin">
                    <label>Email:
                    <input type="email" name="email" placeholder="email@example.com" required></label><br>
                    <label>Mot de Passe:
                    <input type="password" name="passwd" required></label><br>
                    <button type="submit">Inscription</button>
                </form>
                HTML;
        }
        
        elseif($this->http_method === 'POST'){
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'passwd', FILTER_SANITIZE_SPECIAL_CHARS);
            try {
                AuthnProvider::signin($email, $password);
                $html = "<div>Bienvenue, $email!</div>";
            } catch (AuthnException $e) {
                $html = "<div>Erreur: " . $e->getMessage() . "</div>";
            }
        }
        return $html;
    }
}