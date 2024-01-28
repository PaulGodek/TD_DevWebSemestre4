<?php

namespace TheFeed\Lib;

use TheFeed\Modele\DataObject\Utilisateur;
use TheFeed\Modele\HTTP\Session;
use TheFeed\Modele\Repository\UtilisateurRepository;

class ConnexionUtilisateur
{
    private static string $cleConnexion = "_utilisateurConnecte";

    // Note : Classe trop couplée avec les sessions
    public static function connecter(string $idUtilisateur): void
    {
        $session = Session::getInstance();
        $session->enregistrer(ConnexionUtilisateur::$cleConnexion, $idUtilisateur);
    }

    public static function estConnecte(): bool
    {
        $session = Session::getInstance();
        return $session->existeCle(ConnexionUtilisateur::$cleConnexion);
    }

    public static function deconnecter()
    {
        $session = Session::getInstance();
        $session->supprimer(ConnexionUtilisateur::$cleConnexion);
    }

    public static function getIdUtilisateurConnecte(): ?string
    {
        $session = Session::getInstance();
        if ($session->existeCle(ConnexionUtilisateur::$cleConnexion)) {
            return $session->lire(ConnexionUtilisateur::$cleConnexion);
        } else
            return null;
    }
}
