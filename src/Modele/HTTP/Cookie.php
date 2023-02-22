<?php

namespace TheFeed\Modele\HTTP;

class Cookie
{

    public static function existeCle($cle) : bool {
        return isset($_COOKIE[$cle]);
    }

    public static function enregistrer(string $cle, mixed $valeur, ?int $dureeExpiration = null): void
    {
        // TODO : toujour sérialiser ?
        // $valeurString = is_string($valeur) ? $valeur : serialize($valeur);
        $valeurJSON = serialize($valeur);
        if ($dureeExpiration === null)
            setcookie($cle, $valeurJSON, 0);
        else
            setcookie($cle, $valeurJSON, time() + $dureeExpiration);
    }

    // public static function lire(string $cle, bool $isString = true): mixed
    // {
    //     if ($isString)
    //         return $_COOKIE[$cle];
    //     else
    //         return unserialize($_COOKIE[$cle]);
    // }

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
