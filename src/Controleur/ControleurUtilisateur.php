<?php

namespace TheFeed\Controleur;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use TheFeed\Configuration\Configuration;
use TheFeed\Lib\MessageFlash;
use TheFeed\Service\Exception\ServiceException;
use TheFeed\Service\PublicationServiceInterface;
use TheFeed\Service\UtilisateurServiceInterface;

class ControleurUtilisateur extends ControleurGenerique
{

    public function __construct(ContainerInterface $container, private UtilisateurServiceInterface $utilisateurService, private PublicationServiceInterface $publicationService) {
        parent::__construct($container);
    }

    #[Route(path: '/utilisateurs/{idUtilisateur}/publications', name:'publicationsUtilisateur_GET', methods:["GET"])]
    public function afficherPublications($idUtilisateur): Response
    {
        $publications = [];
        try {
            $utilisateur = $this->utilisateurService->recupererUtilisateurParId($idUtilisateur, false);
            $publications = $this->publicationService->recupererPublicationsUtilisateur($idUtilisateur);
        } catch (ServiceException $e) {
            MessageFlash::ajouter("error", $e->getMessage());
            return $this->rediriger("publications_GET");
        }
        return $this->afficherTwig('publication/page_perso.html.twig', [
            "publications" => $publications,
            "idUtilisateur" => $utilisateur->getLogin()
        ]);
    }

    #[Route(path: '/inscription', name:'inscription_GET', methods:["GET"])]
    public function afficherFormulaireCreation(): Response
    {
        return $this->afficherTwig('utilisateur/inscription.html.twig', [
            "method" => Configuration::getDebug() ? "get" : "post"
        ]);
    }

    #[Route(path: '/inscription', name:'inscription_POST', methods:["POST"])]
    public function creerDepuisFormulaire(): Response
    {
        $login = $_POST['login'] ?? null;
        $motDePasse = $_POST['mot-de-passe'] ?? null;
        $email = $_POST['email'] ?? null;
        $donneesPhotoDeProfil = $_FILES['donnees-photo-de-profil'] ?? null;

        try {
            $this->utilisateurService->creerUtilisateur($login, $motDePasse, $email, $donneesPhotoDeProfil);
        } catch (ServiceException $e) {
            MessageFlash::ajouter("error", $e->getMessage());
            return $this->rediriger("inscription_GET");
        }

        MessageFlash::ajouter("success", "L'utilisateur a bien été créé !");
        return $this->rediriger("publications_GET");
    }

    #[Route(path: '/connexion', name:'connexion_GET', methods:["GET"])]
    public function afficherFormulaireConnexion(): Response
    {
        return $this->afficherTwig('utilisateur/connexion.html.twig', [
            "method" => Configuration::getDebug() ? "get" : "post"
        ]);
    }

    #[Route(path: '/connexion', name:'connexion_POST', methods:["POST"])]
    public function connecter(): Response
    {
        $login = $_POST["login"] ?? null;
        $motDePasse = $_POST["mot-de-passe"] ?? null;

        try {
            $this->utilisateurService->connecterUtilisateur($login, $motDePasse);
        } catch (ServiceException $e) {
            MessageFlash::ajouter("error", $e->getMessage());
            return $this->rediriger("connexion_GET");
        }

        MessageFlash::ajouter("success", "Connexion effectuée.");
        return $this->rediriger("publications_GET");
    }

    #[Route(path: '/deconnexion', name:'deconnexion_GET', methods:["GET"])]
    public function deconnecter(): Response
    {
        try {
            $this->utilisateurService->deconnecter();
        } catch (ServiceException $e) {
            MessageFlash::ajouter("error", $e->getMessage());
            return $this->rediriger("publications_GET");
        }
        MessageFlash::ajouter("success", "L'utilisateur a bien été déconnecté.");
        return $this->rediriger("publications_GET");
    }
}
