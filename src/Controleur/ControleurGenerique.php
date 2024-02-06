<?php

namespace TheFeed\Controleur;

use TheFeed\Lib\MessageFlash;

class ControleurGenerique {

    protected static function afficherVue(string $cheminVue, array $parametres = []): void
    {
        extract($parametres);
        $messagesFlash = MessageFlash::lireTousMessages();
        require __DIR__ . "/../vue/$cheminVue";
    }

    // https://stackoverflow.com/questions/768431/how-do-i-make-a-redirect-in-php
    protected static function rediriger(string $controleur = "", string $action = ""): void
    {
        $controleurURL = rawurlencode($controleur);
        $actionURL = rawurlencode($action);
        $url = "./controleurFrontal.php?controleur=$controleurURL&action=$actionURL";

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