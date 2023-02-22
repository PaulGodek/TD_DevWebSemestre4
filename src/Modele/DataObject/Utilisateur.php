<?php

namespace TheFeed\Modele\DataObject;

class Utilisateur
{


    private int $idUtilisateur;
    private string $login;
    //Chiffré
    private string $password;
    private string $adresseMail;
    private string $profilePictureName;

    public function __construct()
    {
    }

    public static function create(string $login,
                                  string $passwordChiffre,
                                  string $addresseMail,
                                  string $profilePictureName
    ): Utilisateur
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setLogin($login);
        $utilisateur->setPassword($passwordChiffre);
        $utilisateur->setAdresseMail($addresseMail);
        $utilisateur->setProfilePictureName($profilePictureName);
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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getAdresseMail(): string
    {
        return $this->adresseMail;
    }

    public function getProfilePictureName(): string
    {
        return $this->profilePictureName;
    }

    public function setIdUtilisateur($idUtilisateur): void
    {
        $this->idUtilisateur = $idUtilisateur;
    }

    public function setLogin($login): void
    {
        $this->login = $login;
    }

    public function setPassword($password): void
    {
        $this->password = $password;
    }

    public function setAdresseMail($adresseMail): void
    {
        $this->adresseMail = $adresseMail;
    }

    public function setProfilePictureName($profilePictureName): void
    {
        $this->profilePictureName = $profilePictureName;
    }
}
