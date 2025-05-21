<?php

namespace TheFeed\Service;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use TheFeed\Modele\DataObject\Publication;
use TheFeed\Modele\DataObject\Utilisateur;
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
    public function creerPublication($idUtilisateur, $message): Publication
    {
        if ($idUtilisateur == null) throw new ServiceException("Il faut être connecté pour publier un feed", Response::HTTP_UNAUTHORIZED);
        if ($message == null || $message == "") throw new ServiceException("Le message ne peut pas être vide!", Response::HTTP_BAD_REQUEST);
        if (strlen($message) > 250) throw new ServiceException("Le message ne peut pas dépasser 250 caractères!", Response::HTTP_BAD_REQUEST);

        $auteur = new Utilisateur();
        $auteur->setIdUtilisateur($idUtilisateur);
        $publication = Publication::construire($message, $auteur);
        $idPublication = $this->publicationRepository->ajouter($publication);
        $publication->setIdPublication($idPublication);
        return $publication;
    }

    public function recupererPublicationsUtilisateur($idUtilisateur): array
    {
        return $this->publicationRepository->recupererParAuteur($idUtilisateur);
    }

    /**
     * @throws ServiceException
     */
    public function supprimerPublication(int $idPublication, ?string $idUtilisateurConnecte): void
    {
        $publication = $this->publicationRepository->recupererParClePrimaire($idPublication);

        if (is_null($idUtilisateurConnecte))
            throw new ServiceException("Il faut être connecté pour supprimer une publication", Response::HTTP_UNAUTHORIZED);

        if ($publication === null)
            throw new ServiceException("Publication inconnue.", Response::HTTP_NOT_FOUND);

        if ($publication->getAuteur()->getIdUtilisateur() !== intval($idUtilisateurConnecte))
            throw new ServiceException("Seul l'auteur de la publication peut la supprimer", Response::HTTP_FORBIDDEN);

        $this->publicationRepository->supprimer($publication);
    }

    /**
     * @throws ServiceException
     */
    public function recupererPublicationParId($idPublication, $autoriserNull = true) : ?Publication {
        $publication = $this->publicationRepository->recupererParClePrimaire($idPublication);
        if(!$autoriserNull && $publication == null) {
            throw new ServiceException("La publication n'existe pas.", Response::HTTP_NOT_FOUND);
        }
        return $publication;
    }
}