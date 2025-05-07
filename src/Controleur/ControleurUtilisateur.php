<?php

namespace TheFeed\Controleur;

use Symfony\Component\HttpFoundation\Response;
use TheFeed\Configuration\Configuration;
use TheFeed\Lib\MessageFlash;
use TheFeed\Service\Exception\ServiceException;
use TheFeed\Service\PublicationService;
use TheFeed\Service\UtilisateurService;

class ControleurUtilisateur extends ControleurGenerique
{

    public static function afficherErreur($messageErreur = "", $statusCode = 400): Response
    {
        return parent::afficherErreur($messageErreur, $statusCode);
    }

    public static function afficherPublications($idUtilisateur): Response
    {
        $publications = [];
        try {
            $utilisateur = (new UtilisateurService())->recupererUtilisateurParId($idUtilisateur, false);
            $publications = (new PublicationService())->recupererPublicationsUtilisateur($idUtilisateur);
        } catch (ServiceException $e) {
            MessageFlash::ajouter("error", $e->getMessage());
            return ControleurUtilisateur::rediriger("publications_GET");
        }
        return ControleurUtilisateur::afficherTwig('publication/page_perso.html.twig', [
            "publications" => $publications,
            "idUtilisateur" => $utilisateur->getLogin()
        ]);
    }

    public static function afficherFormulaireCreation(): Response
    {
        return ControleurUtilisateur::afficherTwig('utilisateur/inscription.html.twig', [
            "method" => Configuration::getDebug() ? "get" : "post"
        ]);
    }

    public static function creerDepuisFormulaire(): Response
    {
        $login = $_POST['login'] ?? null;
        $motDePasse = $_POST['mot-de-passe'] ?? null;
        $email = $_POST['email'] ?? null;
        $donneesPhotoDeProfil = $_FILES['donnees-photo-de-profil'] ?? null;

        try {
            (new UtilisateurService())->creerUtilisateur($login, $motDePasse, $email, $donneesPhotoDeProfil);
        } catch (ServiceException $e) {
            MessageFlash::ajouter("error", $e->getMessage());
            return ControleurUtilisateur::rediriger("inscription_GET");
        }

        MessageFlash::ajouter("success", "L'utilisateur a bien été créé !");
        return ControleurUtilisateur::rediriger("publications_GET");
    }

    public static function afficherFormulaireConnexion(): Response
    {
        return ControleurUtilisateur::afficherTwig('utilisateur/connexion.html.twig', [
            "method" => Configuration::getDebug() ? "get" : "post"
        ]);
    }

    public static function connecter(): Response
    {
        $login = $_POST["login"] ?? null;
        $motDePasse = $_POST["mot-de-passe"] ?? null;

        try {
            (new UtilisateurService())->connecterUtilisateur($login, $motDePasse);
        } catch (ServiceException $e) {
            MessageFlash::ajouter("error", $e->getMessage());
            return ControleurUtilisateur::rediriger("connexion_GET");
        }

        MessageFlash::ajouter("success", "Connexion effectuée.");
        return ControleurUtilisateur::rediriger("publications_GET");
    }

    public static function deconnecter(): Response
    {
        try {
            (new UtilisateurService())->deconnecter();
        } catch (ServiceException $e) {
            MessageFlash::ajouter("error", $e->getMessage());
            return ControleurUtilisateur::rediriger("publications_GET");
        }
        MessageFlash::ajouter("success", "L'utilisateur a bien été déconnecté.");
        return ControleurUtilisateur::rediriger("publications_GET");
    }
}
