<?php

namespace TheFeed\Controleur;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use TheFeed\Lib\Conteneur;
use TheFeed\Lib\MessageFlash;

class ControleurGenerique {

    protected static function afficherVue(string $cheminVue, array $parametres = []): Response
    {
        extract($parametres);
        $messagesFlash = MessageFlash::lireTousMessages();
        ob_start();
        require __DIR__ . "/../vue/$cheminVue";
        $corpsReponse = ob_get_clean();
        return new Response($corpsReponse);
    }

    // https://stackoverflow.com/questions/768431/how-do-i-make-a-redirect-in-php
    protected static function rediriger(string $name, array $parameters = []): RedirectResponse
    {
        $generateurUrl = Conteneur::recupererService("generateurUrl");

        /** @var UrlGenerator $generateurUrl */
        $url = $generateurUrl->generate($name, $parameters);

        return new RedirectResponse($url);
    }

    public static function afficherErreur($messageErreur = "", $statusCode = 400): Response
    {
        $reponse = ControleurGenerique::afficherVue('vueGenerale.php', [
            "pagetitle" => "Problème",
            "cheminVueBody" => "erreur.php",
            "errorMessage" => $messageErreur
        ]);

        $reponse->setStatusCode($statusCode);
        return $reponse;
    }

}