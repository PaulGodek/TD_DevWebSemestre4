<?php

namespace TheFeed\Service;

use TheFeed\Modele\DataObject\Utilisateur;
use TheFeed\Service\Exception\ServiceException;

interface UtilisateurServiceInterface
{
    /**
     * @throws ServiceException
     */
    public function creerUtilisateur($login, $motDePasse, $email, $donneesPhotoDeProfil): void;

    /**
     * @throws ServiceException
     */
    public function recupererUtilisateurParId($idUtilisateur, $autoriserNull = true): ?Utilisateur;

    /**
     * @throws ServiceException
     */
    public function connecterUtilisateur($login, $motDePasse): void;

    /**
     * @throws ServiceException
     */
    public function deconnecter(): void;
}