<?php

namespace TheFeed\Service;

use Exception;
use TheFeed\Modele\DataObject\Publication;
use TheFeed\Modele\Repository\PublicationRepositoryInterface;
use TheFeed\Modele\Repository\UtilisateurRepositoryInterface;
use TheFeed\Service\Exception\ServiceException;

class PublicationService implements PublicationServiceInterface
{
    private UtilisateurRepositoryInterface $utilisateurRepository;
    private PublicationRepositoryInterface $publicationRepository;

    public function __construct(
        UtilisateurRepositoryInterface $uri,
        PublicationRepositoryInterface $pri
    ) {
        $this->utilisateurRepository = $uri;
        $this->publicationRepository = $pri;
    }

    /**
     * @throws Exception
     */
    public function recupererPublications(): array {
        return $this->publicationRepository->recuperer();
    }

    /**
     * @throws ServiceException
     */
    public function creerPublication($idUtilisateur, $message) : void {
        $utilisateur = $this->utilisateurRepository->recupererParClePrimaire($idUtilisateur);

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
        $this->publicationRepository->ajouter($publication);
    }

    public function recupererPublicationsUtilisateur($idUtilisateur): array
    {
        return $this->publicationRepository->recupererParAuteur($idUtilisateur);
    }
}