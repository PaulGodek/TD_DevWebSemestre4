<?php

namespace TheFeed\Service;

use Exception;
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
    public function creerPublication($idUtilisateur, $message): void;

    public function recupererPublicationsUtilisateur($idUtilisateur): array;
}