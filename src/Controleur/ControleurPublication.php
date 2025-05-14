<?php

namespace TheFeed\Controleur;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use TheFeed\Lib\ConnexionUtilisateur;
use TheFeed\Lib\MessageFlash;
use TheFeed\Service\Exception\ServiceException;
use TheFeed\Service\PublicationService;

class ControleurPublication extends ControleurGenerique
{

    #[Route(path: '/publications', name:'publications_GET', methods:["GET"])]
    public static function afficherListe(): Response
    {
        $publications = (new PublicationService())->recupererPublications();
        return ControleurPublication::afficherTwig('publication/feed.html.twig', [
            "publications" => $publications
        ]);
    }

    #[Route(path: '/publications', name:'publications_POST', methods:["POST"])]
    public static function creerDepuisFormulaire(): Response
    {
        $idUtilisateurConnecte = ConnexionUtilisateur::getIdUtilisateurConnecte();
        $message = $_POST['message'];
        try {
            (new PublicationService())->creerPublication($idUtilisateurConnecte, $message);
        } catch (ServiceException $e) {
            MessageFlash::ajouter("error", $e->getMessage());
        }

        return ControleurPublication::rediriger("publications_GET");
    }


}