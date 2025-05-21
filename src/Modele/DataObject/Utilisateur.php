<?php

namespace TheFeed\Modele\DataObject;

use JsonSerializable;

class Utilisateur implements JsonSerializable
{


    private int $idUtilisateur;
    private string $login;
    private string $mdpHache;
    private string $email;
    private string $nomPhotoDeProfil;

    public function __construct()
    {
    }

    public static function construire(string $login,
                                      string $mdpHache,
                                      string $email,
                                      string $nomPhotoDeProfil
    ): Utilisateur
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setLogin($login);
        $utilisateur->setMdpHache($mdpHache);
        $utilisateur->setEmail($email);
        $utilisateur->setNomPhotoDeProfil($nomPhotoDeProfil);
        return $utilisateur;
    }

    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getMdpHache(): string
    {
        return $this->mdpHache;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getNomPhotoDeProfil(): string
    {
        return $this->nomPhotoDeProfil;
    }

    public function setIdUtilisateur($idUtilisateur): void
    {
        $this->idUtilisateur = $idUtilisateur;
    }

    public function setLogin($login): void
    {
        $this->login = $login;
    }

    public function setMdpHache($mdpHache): void
    {
        $this->mdpHache = $mdpHache;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function setNomPhotoDeProfil($nomPhotoDeProfil): void
    {
        $this->nomPhotoDeProfil = $nomPhotoDeProfil;
    }

    public function jsonSerialize(): array
    {
        return [
            "idUtilisateur" => $this->getIdUtilisateur(),
            "login" => $this->getLogin(),
            "nomPhotoDeProfil" => $this->getNomPhotoDeProfil()
        ];
    }
}
