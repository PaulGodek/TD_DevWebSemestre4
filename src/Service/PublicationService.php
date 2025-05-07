<?php

namespace TheFeed\Service;

use Exception;
use TheFeed\Modele\DataObject\Publication;
use TheFeed\Modele\Repository\PublicationRepository;
use TheFeed\Modele\Repository\UtilisateurRepository;
use TheFeed\Service\Exception\ServiceException;

class PublicationService
{
    /**
     * @throws Exception
     */
    public function recupererPublications(): array {
        return (new PublicationRepository())->recuperer();
    }

    /**
     * @throws ServiceException
     */
    public function creerPublication($idUtilisateur, $message) : void {
        $utilisateur = (new UtilisateurRepository())->recupererParClePrimaire($idUtilisateur);

        if ($utilisateur == null) {
            throw new ServiceException("Il faut être connecté pour publier un feed.");
        }

        if ($message == null || $message == "") {
            throw new ServiceException("Le message ne peut pas être vide !");
        }
        if (strlen($message) > 250) {
            throw new ServiceException("Le message ne peut pas dépasser 250 caractères !");
        }

        $publication = Publication::construire($message, $utilisateur);
        (new PublicationRepository())->ajouter($publication);
    }

    public function recupererPublicationsUtilisateur($idUtilisateur): array
    {
        return (new PublicationRepository())->recupererParAuteur($idUtilisateur);
    }
}