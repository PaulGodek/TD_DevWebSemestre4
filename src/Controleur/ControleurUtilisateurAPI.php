<?php

namespace TheFeed\Controleur;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use TheFeed\Service\Exception\ServiceException;
use TheFeed\Service\UtilisateurServiceInterface;

class ControleurUtilisateurAPI extends ControleurGenerique {

    public function __construct (
        ContainerInterface $container,
        private readonly UtilisateurServiceInterface $utilisateurService
    )
    {
        parent::__construct($container);
    }

    #[Route(path: '/api/utilisateurs/{idUtilisateur}', name:'utilisateurDetail_GET', methods:["GET"])]
    public function afficherDetail($idUtilisateur): Response
    {
        try {
            $utilisateur = $this->utilisateurService->recupererUtilisateurParId($idUtilisateur, false);
            return new JsonResponse($utilisateur, Response::HTTP_OK);
        } catch (ServiceException $exception) {
            return new JsonResponse(["error" => $exception->getMessage()], $exception->getCode());
        }
    }
}