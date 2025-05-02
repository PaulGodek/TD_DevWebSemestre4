<?php

namespace TheFeed\Controleur;

use Symfony\Component\Routing\Generator\UrlGenerator;
use TheFeed\Lib\Conteneur;
use TheFeed\Lib\MessageFlash;

class ControleurGenerique {

    protected static function afficherVue(string $cheminVue, array $parametres = []): void
    {
        extract($parametres);
        $messagesFlash = MessageFlash::lireTousMessages();
        require __DIR__ . "/../vue/$cheminVue";
    }

    // https://stackoverflow.com/questions/768431/how-do-i-make-a-redirect-in-php
    protected static function rediriger(string $name, array $parameters = []): void
    {
        $generateurUrl = Conteneur::recupererService("generateurUrl");

        /** @var UrlGenerator $generateurUrl */
        $url = $generateurUrl->generate($name, $parameters);

        // L'en-tête 'Location' permet d'effectuer une redirection
        // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Location
        $header = "Location: $url";
        header($header);
        exit();
    }

    public static function afficherErreur($messageErreur = "", $controleur = ""): void
    {
        $messageErreurVue = "Problème";
        if ($controleur !== "")
            $messageErreurVue .= " avec le contrôleur $controleur";
        if ($messageErreur !== "")
            $messageErreurVue .= " : $messageErreur";

        ControleurGenerique::afficherVue('vueGenerale.php', [
            "pagetitle" => "Problème",
            "cheminVueBody" => "erreur.php",
            "errorMessage" => $messageErreurVue
        ]);
    }

}