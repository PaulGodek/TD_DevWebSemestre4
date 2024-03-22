<?php

namespace TheFeed\Modele\HTTP;

class Cookie
{

    public static function existeCle($cle) : bool {
        return isset($_COOKIE[$cle]);
    }

    public static function enregistrer(string $cle, mixed $valeur, ?int $dureeExpiration = null): void
    {
        $valeurFormatTexte = serialize($valeur);
        if ($dureeExpiration === null)
            setcookie($cle, $valeurFormatTexte, 0);
        else
            setcookie($cle, $valeurFormatTexte, time() + $dureeExpiration);
    }

    public static function lire(string $cle): mixed
    {
        return unserialize($_COOKIE[$cle]);
    }

    public static function supprimer($cle) : void
    {
        unset($_COOKIE[$cle]);
        setcookie($cle, "", 1);
    }
}
