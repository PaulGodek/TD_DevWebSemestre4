<?php

namespace TheFeed\Controleur;

use TheFeed\Lib\ConnexionUtilisateur;
use TheFeed\Lib\MessageFlash;
use TheFeed\Modele\DataObject\Publication;
use TheFeed\Modele\Repository\PublicationRepository;
use TheFeed\Modele\Repository\UtilisateurRepository;

class ControleurPublication extends ControleurGenerique
{

    public static function feed()
    {
        $publications = (new PublicationRepository())->getAll();
        ControleurUtilisateur::afficherVue('vueGenerale.php', [
            "publications" => $publications,
            "pagetitle" => "The Feed",
            "cheminVueBody" => "publication/liste.php"
        ]);
    }

    public static function submitFeedy()
    {
        $idUtilisateurConnecte = ConnexionUtilisateur::getIdUtilisateurConnecte();
        $utilisateur = (new UtilisateurRepository())->get($idUtilisateurConnecte);

        if ($utilisateur == null) {
            MessageFlash::ajouter("error", "Il faut être connecté pour publier un feed");
            return ControleurPublication::rediriger('connecter');
        }
        
        $message = $_POST['message'];
        if ($message == null || $message == "") {
            MessageFlash::ajouter("error", "Le message ne peut pas être vide!");
            ControleurPublication::rediriger('publication', 'feed');
        }
        if (strlen($message) > 250) {
            MessageFlash::ajouter("error", "Le message ne peut pas dépasser 250 caractères!");
            ControleurPublication::rediriger('publication', 'feed');
        }

        $publication = Publication::create($message, $utilisateur);
        (new PublicationRepository())->create($publication);

        ControleurPublication::rediriger('publication', 'feed');
    }


}