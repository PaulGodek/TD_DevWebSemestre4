<?php

namespace TheFeed\Controleur;

use TheFeed\Configuration\Configuration;
use TheFeed\Lib\ConnexionUtilisateur;
use TheFeed\Lib\MessageFlash;
use TheFeed\Lib\MotDePasse;
use TheFeed\Modele\DataObject\Utilisateur;
use TheFeed\Modele\Repository\PublicationRepository;
use TheFeed\Modele\Repository\UtilisateurRepository;

class ControleurUtilisateur extends ControleurGenerique
{

    public static function afficherErreur($messageErreur = "", $controleur = ""): void
    {
        parent::afficherErreur($messageErreur, "utilisateur");
    }

    public static function afficherPublications(): void
    {
        if (isset($_REQUEST['idUtilisateur'])) {
            $idUtilisateur = $_REQUEST['idUtilisateur'];
            $utilisateur = (new UtilisateurRepository())->recupererParClePrimaire($idUtilisateur);
            if ($utilisateur === null) {
                MessageFlash::ajouter("error", "Login inconnu.");
                ControleurUtilisateur::rediriger("publication", "afficherListe");
            } else {
                $loginHTML = htmlspecialchars($utilisateur->getLogin());
                $publications = (new PublicationRepository())->recupererParAuteur($idUtilisateur);
                ControleurUtilisateur::afficherVue('vueGenerale.php', [
                    "publications" => $publications,
                    "pagetitle" => "Page de $loginHTML",
                    "cheminVueBody" => "utilisateur/page_perso.php"
                ]);
            }
        } else {
            MessageFlash::ajouter("error", "Login manquant.");
            ControleurUtilisateur::rediriger("publication", "afficherListe");
        }
    }

    public static function afficherFormulaireCreation(): void
    {
        ControleurUtilisateur::afficherVue('vueGenerale.php', [
            "pagetitle" => "Création d'un utilisateur",
            "cheminVueBody" => "utilisateur/formulaireCreation.php",
            "method" => Configuration::getDebug() ? "get" : "post",
        ]);
    }

    public static function creerDepuisFormulaire(): void
    {
        if (
            isset($_POST['login']) && isset($_POST['mot-de-passe']) && isset($_POST['email'])
            && isset($_FILES['donnees-photo-de-profil'])
        ) {
            $login = $_POST['login'];
            $motDePasse = $_POST['mot-de-passe'];
            $adresseMail = $_POST['email'];
            $donneesPhotoDeProfil = $_FILES['donnees-photo-de-profil'];

            if (strlen($login) < 4 || strlen($login) > 20) {
                MessageFlash::ajouter("error", "Le login doit être compris entre 4 et 20 caractères!");
                ControleurUtilisateur::rediriger("utilisateur", "afficherFormulaireCreation");
            }
            if (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,20}$#", $motDePasse)) {
                MessageFlash::ajouter("error", "Mot de passe invalide!");
                ControleurUtilisateur::rediriger("utilisateur", "afficherFormulaireCreation");
            }
            if (!filter_var($adresseMail, FILTER_VALIDATE_EMAIL)) {
                MessageFlash::ajouter("error", "L'adresse mail est incorrecte!");
                ControleurUtilisateur::rediriger("utilisateur", "afficherFormulaireCreation");
            }

            $utilisateurRepository = new UtilisateurRepository();
            $utilisateur = $utilisateurRepository->recupererParLogin($login);
            if ($utilisateur != null) {
                MessageFlash::ajouter("error", "Ce login est déjà pris!");
                ControleurUtilisateur::rediriger("utilisateur", "afficherFormulaireCreation");
            }

            $utilisateur = $utilisateurRepository->recupererParEmail($adresseMail);
            if ($utilisateur != null) {
                MessageFlash::ajouter("error", "Un compte est déjà enregistré avec cette adresse mail!");
                ControleurUtilisateur::rediriger("utilisateur", "afficherFormulaireCreation");
            }

            $mdpHache = MotDePasse::hacher($motDePasse);

            // Upload des photos de profil
            // Plus d'informations :
            // http://romainlebreton.github.io/R3.01-DeveloppementWeb/assets/tut4-complement.html

            // On récupère l'extension du fichier
            $explosion = explode('.', $donneesPhotoDeProfil['name']);
            $fileExtension = end($explosion);
            if (!in_array($fileExtension, ['png', 'jpg', 'jpeg'])) {
                MessageFlash::ajouter("error", "La photo de profil n'est pas au bon format!");
                ControleurUtilisateur::rediriger("utilisateur", "afficherFormulaireCreation");
            }
            // La photo de profil sera enregistrée avec un nom de fichier aléatoire
            $pictureName = uniqid() . '.' . $fileExtension;
            $from = $donneesPhotoDeProfil['tmp_name'];
            $to = __DIR__ . "/../../ressources/img/utilisateurs/$pictureName";
            move_uploaded_file($from, $to);

            $utilisateur = Utilisateur::construire($login, $mdpHache, $adresseMail, $pictureName);
            $utilisateurRepository->ajouter($utilisateur);

            MessageFlash::ajouter("success", "L'utilisateur a bien été créé !");
            ControleurUtilisateur::rediriger("publication", "afficherListe");
        } else {
            MessageFlash::ajouter("error", "Login, nom, prenom ou mot de passe manquant.");
            ControleurUtilisateur::rediriger("utilisateur", "afficherFormulaireCreation");
        }
    }

    public static function afficherFormulaireConnexion(): void
    {
        ControleurUtilisateur::afficherVue('vueGenerale.php', [
            "pagetitle" => "Formulaire de connexion",
            "cheminVueBody" => "utilisateur/formulaireConnexion.php",
            "method" => Configuration::getDebug() ? "get" : "post",
        ]);
    }

    public static function connecter(): void
    {
        if (!(isset($_POST['login']) && isset($_POST['mot-de-passe']))) {
            MessageFlash::ajouter("error", "Login ou mot de passe manquant.");
            ControleurUtilisateur::rediriger("utilisateur", "afficherFormulaireConnexion");
        }
        $utilisateurRepository = new UtilisateurRepository();
        /** @var Utilisateur $utilisateur */
        $utilisateur = $utilisateurRepository->recupererParLogin($_POST["login"]);

        if ($utilisateur == null) {
            MessageFlash::ajouter("error", "Login inconnu.");
            ControleurUtilisateur::rediriger("utilisateur", "afficherFormulaireConnexion");
        }

        if (!MotDePasse::verifier($_POST["mot-de-passe"], $utilisateur->getMdpHache())) {
            MessageFlash::ajouter("error", "Mot de passe incorrect.");
            ControleurUtilisateur::rediriger("utilisateur", "afficherFormulaireConnexion");
        }

        ConnexionUtilisateur::connecter($utilisateur->getIdUtilisateur());
        MessageFlash::ajouter("success", "Connexion effectuée.");
        ControleurUtilisateur::rediriger("publication", "afficherListe");
    }

    public static function deconnecter(): void
    {
        if (!ConnexionUtilisateur::estConnecte()) {
            MessageFlash::ajouter("error", "Utilisateur non connecté.");
            ControleurUtilisateur::rediriger("publication", "afficherListe");
        }
        ConnexionUtilisateur::deconnecter();
        MessageFlash::ajouter("success", "L'utilisateur a bien été déconnecté.");
        ControleurUtilisateur::rediriger("publication", "afficherListe");
    }
}
