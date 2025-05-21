<?php

namespace TheFeed\Service;

use Exception;
use TheFeed\Modele\DataObject\Publication;
use TheFeed\Service\Exception\ServiceException;

interface PublicationServiceInterface
{
    /**
     * @throws Exception
     */
    public function recupererPublications(): array;

    /**
     * @throws ServiceException
     */
    public function creerPublication($idUtilisateur, $message): Publication;

    public function recupererPublicationsUtilisateur($idUtilisateur): array;

    public function supprimerPublication(int $idPublication, ?string $idUtilisateurConnecte): void;

    public function recupererPublicationParId($idPublication, $autoriserNull = true) : ?Publication;
}