<?php

namespace TheFeed\Controleur;

use Symfony\Component\HttpFoundation\Response;
use TheFeed\Configuration\Configuration;
use TheFeed\Lib\ConnexionUtilisateur;
use TheFeed\Lib\MessageFlash;
use TheFeed\Lib\MotDePasse;
use TheFeed\Modele\DataObject\Utilisateur;
use TheFeed\Modele\Repository\PublicationRepository;
use TheFeed\Modele\Repository\UtilisateurRepository;

class ControleurUtilisateur extends ControleurGenerique
{

    public static function afficherErreur($messageErreur = "", $controleur = ""): Response
    {
        return parent::afficherErreur($messageErreur, "utilisateur");
    }

    public static function afficherPublications($idUtilisateur): Response
    {
        $utilisateur = (new UtilisateurRepository())->recupererParClePrimaire($idUtilisateur);
        if ($utilisateur === null) {
            MessageFlash::ajouter("error", "Login inconnu.");
            return ControleurUtilisateur::rediriger("publications_GET");
        } else {
            $loginHTML = htmlspecialchars($utilisateur->getLogin());
            $publications = (new PublicationRepository())->recupererParAuteur($idUtilisateur);
            return ControleurUtilisateur::afficherVue('vueGenerale.php', [
                "publications" => $publications,
                "pagetitle" => "Page de $loginHTML",
                "cheminVueBody" => "utilisateur/page_perso.php"
            ]);
        }
    }

    public static function afficherFormulaireCreation(): Response
    {
        return ControleurUtilisateur::afficherVue('vueGenerale.php', [
            "pagetitle" => "Création d'un utilisateur",
            "cheminVueBody" => "utilisateur/formulaireCreation.php",
            "method" => Configuration::getDebug() ? "get" : "post",
        ]);
    }

    public static function creerDepuisFormulaire(): Response
    {
        if (
            isset($_POST['login']) && isset($_POST['mot-de-passe']) && isset($_POST['email'])
            && isset($_FILES['donnees-photo-de-profil'])
        ) {
            $login = $_POST['login'];
            $motDePasse = $_POST['mot-de-passe'];
            $email = $_POST['email'];
            $donneesPhotoDeProfil = $_FILES['donnees-photo-de-profil'];

            if (strlen($login) < 4 || strlen($login) > 20) {
                MessageFlash::ajouter("error", "Le login doit être compris entre 4 et 20 caractères!");
                return ControleurUtilisateur::rediriger("inscription_GET");
            }
            if (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,20}$#", $motDePasse)) {
                MessageFlash::ajouter("error", "Mot de passe invalide!");
                return ControleurUtilisateur::rediriger("inscription_GET");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                MessageFlash::ajouter("error", "L'adresse mail est incorrecte!");
                return ControleurUtilisateur::rediriger("inscription_GET");
            }

            $utilisateurRepository = new UtilisateurRepository();
            $utilisateur = $utilisateurRepository->recupererParLogin($login);
            if ($utilisateur != null) {
                MessageFlash::ajouter("error", "Ce login est déjà pris!");
                return ControleurUtilisateur::rediriger("inscription_GET");
            }

            $utilisateur = $utilisateurRepository->recupererParEmail($email);
            if ($utilisateur != null) {
                MessageFlash::ajouter("error", "Un compte est déjà enregistré avec cette adresse mail!");
                return ControleurUtilisateur::rediriger("inscription_GET");
            }

            $mdpHache = MotDePasse::hacher($motDePasse);

            // Upload des photos de profil
            // Plus d'informations :
            // http://romainlebreton.github.io/R3.01-DeveloppementWeb/assets/tut4-complement.html

            // On récupère l'extension du fichier
            $explosion = explode('.', $donneesPhotoDeProfil['name']);
            $extensionFichier = end($explosion);
            if (!in_array($extensionFichier, ['png', 'jpg', 'jpeg'])) {
                MessageFlash::ajouter("error", "La photo de profil n'est pas au bon format!");
                return ControleurUtilisateur::rediriger("inscription_GET");
            }
            // La photo de profil sera enregistrée avec un nom de fichier aléatoire
            $nomFichierPhoto = uniqid() . '.' . $extensionFichier;
            $source = $donneesPhotoDeProfil['tmp_name'];
            $destination = __DIR__ . "/../../ressources/img/utilisateurs/$nomFichierPhoto";
            move_uploaded_file($source, $destination);

            $utilisateur = Utilisateur::construire($login, $mdpHache, $email, $nomFichierPhoto);
            $utilisateurRepository->ajouter($utilisateur);

            MessageFlash::ajouter("success", "L'utilisateur a bien été créé !");
            return ControleurUtilisateur::rediriger("publications_GET");
        } else {
            MessageFlash::ajouter("error", "Login, nom, prenom ou mot de passe manquant.");
            return ControleurUtilisateur::rediriger("inscription_GET");
        }
    }

    public static function afficherFormulaireConnexion(): Response
    {
        return ControleurUtilisateur::afficherVue('vueGenerale.php', [
            "pagetitle" => "Formulaire de connexion",
            "cheminVueBody" => "utilisateur/formulaireConnexion.php",
            "method" => Configuration::getDebug() ? "get" : "post",
        ]);
    }

    public static function connecter(): Response
    {
        if (!(isset($_POST['login']) && isset($_POST['mot-de-passe']))) {
            MessageFlash::ajouter("error", "Login ou mot de passe manquant.");
            return ControleurUtilisateur::rediriger("connexion_GET");
        }
        $utilisateurRepository = new UtilisateurRepository();
        /** @var Utilisateur $utilisateur */
        $utilisateur = $utilisateurRepository->recupererParLogin($_POST["login"]);

        if ($utilisateur == null) {
            MessageFlash::ajouter("error", "Login inconnu.");
            return ControleurUtilisateur::rediriger("connexion_GET");
        }

        if (!MotDePasse::verifier($_POST["mot-de-passe"], $utilisateur->getMdpHache())) {
            MessageFlash::ajouter("error", "Mot de passe incorrect.");
            return ControleurUtilisateur::rediriger("connexion_GET");
        }

        ConnexionUtilisateur::connecter($utilisateur->getIdUtilisateur());
        MessageFlash::ajouter("success", "Connexion effectuée.");
        return ControleurUtilisateur::rediriger("publications_GET");
    }

    public static function deconnecter(): Response
    {
        if (!ConnexionUtilisateur::estConnecte()) {
            MessageFlash::ajouter("error", "Utilisateur non connecté.");
            return ControleurUtilisateur::rediriger("publications_GET");
        }
        ConnexionUtilisateur::deconnecter();
        MessageFlash::ajouter("success", "L'utilisateur a bien été déconnecté.");
        return ControleurUtilisateur::rediriger("publications_GET");
    }
}
