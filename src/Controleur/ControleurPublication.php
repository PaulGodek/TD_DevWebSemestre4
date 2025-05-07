<?php

namespace TheFeed\Controleur;

use Symfony\Component\HttpFoundation\Response;
use TheFeed\Lib\ConnexionUtilisateur;
use TheFeed\Lib\MessageFlash;
use TheFeed\Modele\DataObject\Publication;
use TheFeed\Modele\Repository\PublicationRepository;
use TheFeed\Modele\Repository\UtilisateurRepository;
use TheFeed\Service\Exception\ServiceException;
use TheFeed\Service\PublicationService;

class ControleurPublication extends ControleurGenerique
{

    public static function afficherListe(): Response
    {
        $publications = (new PublicationService())->recupererPublications();
        return ControleurPublication::afficherTwig('publication/feed.html.twig', [
            "publications" => $publications
        ]);
    }

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