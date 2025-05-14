<?php

namespace TheFeed\Controleur;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use TheFeed\Lib\Conteneur;
use TheFeed\Lib\MessageFlash;
use Twig\Environment;

class ControleurGenerique {

    protected function afficherVue(string $cheminVue, array $parametres = []): Response
    {
        extract($parametres);
        $messagesFlash = MessageFlash::lireTousMessages();
        ob_start();
        require __DIR__ . "/../vue/$cheminVue";
        $corpsReponse = ob_get_clean();
        return new Response($corpsReponse);
    }

    protected function afficherTwig(string $cheminVue, array $parametres = []): Response
    {
        /** @var Environment $twig */
        $twig = Conteneur::recupererService("twig");
        $corpsReponse = $twig->render($cheminVue, $parametres);
        return new Response($corpsReponse);
    }

    // https://stackoverflow.com/questions/768431/how-do-i-make-a-redirect-in-php
    protected function rediriger(string $name, array $parameters = []): RedirectResponse
    {
        $generateurUrl = Conteneur::recupererService("generateurUrl");

        /** @var UrlGenerator $generateurUrl */
        $url = $generateurUrl->generate($name, $parameters);

        return new RedirectResponse($url);
    }

    public function afficherErreur($messageErreur = "", $statusCode = 400): Response
    {
        $reponse = $this->afficherTwig('erreur.html.twig', [
            "errorMessage" => $messageErreur
        ]);

        $reponse->setStatusCode($statusCode);
        return $reponse;
    }

}