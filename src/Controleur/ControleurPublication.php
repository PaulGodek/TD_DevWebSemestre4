<?php

namespace TheFeed\Controleur;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use TheFeed\Lib\ConnexionUtilisateur;
use TheFeed\Lib\MessageFlash;
use TheFeed\Service\Exception\ServiceException;
use TheFeed\Service\PublicationServiceInterface;

class ControleurPublication extends ControleurGenerique
{
    private PublicationServiceInterface $publicationService;

    public function __construct(PublicationServiceInterface $publicationService) {
        $this->publicationService = $publicationService;
    }

    #[Route(path: '/publications', name:'publications_GET', methods:["GET"])]
    public function afficherListe(): Response
    {
        $publications = $this->publicationService->recupererPublications();
        return $this->afficherTwig('publication/feed.html.twig', [
            "publications" => $publications
        ]);
    }

    #[Route(path: '/publications', name:'publications_POST', methods:["POST"])]
    public function creerDepuisFormulaire(): Response
    {
        $idUtilisateurConnecte = ConnexionUtilisateur::getIdUtilisateurConnecte();
        $message = $_POST['message'];
        try {
            $this->publicationService->creerPublication($idUtilisateurConnecte, $message);
        } catch (ServiceException $e) {
            MessageFlash::ajouter("error", $e->getMessage());
        }

        return $this->rediriger("publications_GET");
    }


}