<?php

namespace TheFeed\Service;

use TheFeed\Lib\ConnexionUtilisateur;
use TheFeed\Lib\MotDePasse;
use TheFeed\Modele\DataObject\Utilisateur;
use TheFeed\Modele\Repository\UtilisateurRepository;
use TheFeed\Service\Exception\ServiceException;

class UtilisateurService
{
    /**
     * @throws ServiceException
     */
    public function creerUtilisateur($login, $motDePasse, $email, $donneesPhotoDeProfil) : void {
        if (
            !is_null($login) && !is_null($motDePasse) && !is_null($email)
            && !is_null($donneesPhotoDeProfil)
        ) {

            if (strlen($login) < 4 || strlen($login) > 20) {
                throw new ServiceException("Le login doit être compris entre 4 et 20 caractères!");
            }
            if (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,20}$#", $motDePasse)) {
                throw new ServiceException("Mot de passe invalide!");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new ServiceException("L'adresse mail est incorrecte!");
            }

            $utilisateurRepository = new UtilisateurRepository();
            $utilisateur = $utilisateurRepository->recupererParLogin($login);
            if ($utilisateur != null) {
                throw new ServiceException("Ce login est déjà pris!");
            }

            $utilisateur = $utilisateurRepository->recupererParEmail($email);
            if ($utilisateur != null) {
                throw new ServiceException("Un compte est déjà enregistré avec cette adresse mail!");
            }

            $mdpHache = MotDePasse::hacher($motDePasse);

            // Upload des photos de profil
            // Plus d'informations :
            // http://romainlebreton.github.io/R3.01-DeveloppementWeb/assets/tut4-complement.html

            // On récupère l'extension du fichier
            $explosion = explode('.', $donneesPhotoDeProfil['name']);
            $extensionFichier = end($explosion);
            if (!in_array($extensionFichier, ['png', 'jpg', 'jpeg'])) {
                throw new ServiceException("La photo de profil n'est pas au bon format!");
            }
            // La photo de profil sera enregistrée avec un nom de fichier aléatoire
            $nomFichierPhoto = uniqid() . '.' . $extensionFichier;
            $source = $donneesPhotoDeProfil['tmp_name'];
            $destination = __DIR__ . "/../../ressources/img/utilisateurs/$nomFichierPhoto";
            move_uploaded_file($source, $destination);

            $utilisateur = Utilisateur::construire($login, $mdpHache, $email, $nomFichierPhoto);
            $utilisateurRepository->ajouter($utilisateur);
        } else {
            throw new ServiceException("Login, nom, prenom ou mot de passe manquant.");
        }
    }

    /**
     * @throws ServiceException
     */
    public function recupererUtilisateurParId($idUtilisateur, $autoriserNull = true): ?Utilisateur
    {
        $utilisateur = (new UtilisateurRepository())->recupererParClePrimaire($idUtilisateur);
        if (!$autoriserNull && is_null($utilisateur)) {
            throw new ServiceException("Cet utilisateur n'existe pas !");
        }
        return $utilisateur;
    }

    /**
     * @throws ServiceException
     */
    public function connecterUtilisateur($login, $motDePasse): void
    {

        if (!is_null($login) && !is_null($motDePasse)) {
            $utilisateurRepository = new UtilisateurRepository();
            /** @var Utilisateur $utilisateur */
            $utilisateur = $utilisateurRepository->recupererParLogin($login);

            if (is_null($utilisateur)) {
                throw new ServiceException("Login inconnu.");
            }

            if (!MotDePasse::verifier($motDePasse, $utilisateur->getMdpHache())) {
                throw new ServiceException("Mot de passe incorrect.");
            }

            ConnexionUtilisateur::connecter($utilisateur->getIdUtilisateur());
        }
    }

    /**
     * @throws ServiceException
     */
    public function deconnecter(): void
    {
        if (!ConnexionUtilisateur::estConnecte()) {
            throw new ServiceException("Utilisateur non connecté.");
        }
        ConnexionUtilisateur::deconnecter();
    }
}