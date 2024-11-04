<?php

namespace iutnc\deefy\action;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class AddUserAction extends Action
{
    public function execute(): string
{
    $html = "";

    if ($this->http_method === 'GET') {
        $html = <<<HTML
            <h2>Inscription</h2>
            <form method="post" action="?action=add-user">
                <label>Email:
                <input type="email" name="email" placeholder="email@example.com" required></label><br>
                <label>Password:
                <input type="password" name="passwd" required></label><br>
                <label>Confirm Password:
                <input type="password" name="confirm_passwd" required></label><br>
                <button type="submit">Register</button>
            </form>
            HTML;
    } elseif ($this->http_method === 'POST') {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'passwd', FILTER_SANITIZE_SPECIAL_CHARS);
        $confirm_password = filter_input(INPUT_POST, 'confirm_passwd', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($password !== $confirm_password) {
            $html = "<div>Erreur : Meilleurs mot de passe requis</div>";
        } else {
            try {
                AuthnProvider::register($email, $password);
                $html = "<div>Bienvenue, $email!</div>";
            } catch (AuthnException $e) {
                $html = "<div>Erreur: " . $e->getMessage() . "</div>";
            }
        }
    }
    
    return $html;
}
}