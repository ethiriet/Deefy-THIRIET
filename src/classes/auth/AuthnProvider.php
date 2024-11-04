<?php

namespace iutnc\deefy\auth;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\exception\AuthnException;


class AuthnProvider
{
  
    public static function signin(string $email, string $password): void {
        $repo = DeefyRepository::getInstance();
        try {
            $stmt = $repo->getPdo()->prepare('SELECT * FROM user WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $userData = $stmt->fetch();
    
            // Vérifier si l'utilisateur a été trouvé
            if (!$userData) {
                throw new AuthnException("Utilisateur non trouvé ou mot de passe incorrect.");
            }
    
            // Créer l'objet User
            $user = new User($userData['id'], $userData['email'], $userData['passwd'], $userData['role']);
    
            // Vérifier le mot de passe
            if ($user->verifyPassword($password)) {
                $_SESSION['user'] = serialize($user);
            } else {
                throw new AuthnException("Mauvais identifiant ou mot de passe.");
            }
        } catch (\PDOException $e) {
            echo "Erreur de base de données : " . $e->getMessage();
        }
    }

    public static function register(string $email, string $password): void
    {
        $repo = DeefyRepository::getInstance();
        try {
            $stmt = $repo->getPdo()->prepare('SELECT email FROM user WHERE email = :email');
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                throw new AuthnException("Email deja utilisé.");
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $repo->getPdo()->prepare('INSERT INTO user (email, passwd, role) VALUES (:email, :passwd, 1)');
            $stmt->execute(['email' => $email, 'passwd' => $hashed_password]);

            self::signin($email, $password);
        } catch (\PDOException $e) {
            throw new AuthnException("Erreur de base de données: " . $e->getMessage());
        }
    }


    public static function getSignedInUser(): User
    {
        if (!isset($_SESSION['user'])) {
            throw new AuthnException("Vous devez vous connecter avec un compte pour acceder à ce contenu");
        }

        return unserialize($_SESSION['user']);
    }
}