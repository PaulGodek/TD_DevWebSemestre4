<?php

namespace TheFeed\Controleur;

use Exception;
use JsonException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Attribute\Route;
use TheFeed\Lib\ConnexionUtilisateur;
use TheFeed\Service\PublicationServiceInterface;
use TheFeed\Service\Exception\ServiceException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControleurPublicationAPI extends ControleurGenerique
{

    public function __construct (
        ContainerInterface $container,
        private readonly PublicationServiceInterface $publicationService
    )
    {
        parent::__construct($container);
    }

    #[Route(path: '/api/publications/{idPublication}', name:'API_publicationSupprimer_DELETE', methods:["DELETE"])]
    public function supprimer($idPublication): Response
    {
        try {
            $idUtilisateurConnecte = ConnexionUtilisateur::getIdUtilisateurConnecte();
            $this->publicationService->supprimerPublication($idPublication, $idUtilisateurConnecte);
            return new JsonResponse('', Response::HTTP_NO_CONTENT);
        } catch (ServiceException $exception) {
            return new JsonResponse(["error" => $exception->getMessage()], $exception->getCode());
        }
    }

    #[Route(path: '/api/publications/{idPublication}', name:'API_publicationDetail_GET', methods:["GET"])]
    public function afficherDetail($idPublication): Response
    {
        try {
            $publication = $this->publicationService->recupererPublicationParId($idPublication, false);
            return new JsonResponse($publication, Response::HTTP_OK);
        } catch (ServiceException $exception) {
            return new JsonResponse(["error" => $exception->getMessage()], $exception->getCode());
        }
    }

    #[Route(path: '/api/publications', name:'API_publications_GET', methods:["GET"])]
    public function afficherListe(): Response
    {
        try {
            $publications = $this->publicationService->recupererPublications();
            return new JsonResponse($publications, Response::HTTP_OK);
        } catch (Exception $exception) {
            return new JsonResponse(["error" => $exception->getMessage()], $exception->getCode());
        }
    }

    #[Route(path: '/api/publications', name:'API_publicationPoster_POST', methods:["POST"])]
    public function posterPublication(Request $request): Response
    {
        try {
            $message = json_decode($request->getContent(), flags: JSON_THROW_ON_ERROR)->message ?? null;
            $idUtilisateurConnecte = ConnexionUtilisateur::getIdUtilisateurConnecte();
            $publication = $this->publicationService->creerPublication($idUtilisateurConnecte, $message);
            return new JsonResponse($publication, Response::HTTP_CREATED);
        } catch (ServiceException $exception) {
            return new JsonResponse(["error" => $exception->getMessage()], $exception->getCode());
        } catch (JsonException $exception) {
            return new JsonResponse(["error" => "Corps de la requête mal formé"], Response::HTTP_BAD_REQUEST            );
        }
    }
}