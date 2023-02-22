<?php

namespace TheFeed\Modele\Repository;

use TheFeed\Modele\DataObject\Utilisateur;
use PDOStatement;

class UtilisateurRepository
{

    /**
     * @return Utilisateur[]
     */
    public function getAll(): array
    {
        $statement = ConnexionBaseDeDonnees::getPdo()->prepare("SELECT * FROM utilisateurs");
        $statement->execute();

        $utilisateurs = [];

        foreach ($statement as $data) {
            $utilisateur = new Utilisateur();
            $utilisateur->setIdUtilisateur($data["idUtilisateur"]);
            $utilisateur->setLogin($data["login"]);
            $utilisateur->setPassword($data["password"]);
            $utilisateur->setAdresseMail($data["adresseMail"]);
            $utilisateur->setProfilePictureName($data["profilePictureName"]);
            $utilisateurs[] = $utilisateur;
        }

        return $utilisateurs;
    }

    public function get($id): ?Utilisateur
    {
        $values = [
            "idUtilisateur" => $id,
        ];
        $statement = ConnexionBaseDeDonnees::getPdo()->prepare("SELECT * FROM utilisateurs WHERE idUtilisateur = :idUtilisateur");
        return $this->extractUtilisateur($statement, $values);
    }

    public function getByLogin($login)
    {
        $values = [
            "login" => $login,
        ];
        $statement = ConnexionBaseDeDonnees::getPdo()->prepare("SELECT * FROM utilisateurs WHERE login = :login");
        return $this->extractUtilisateur($statement, $values);
    }

    public function getByAdresseMail($adresseMail)
    {
        $values = [
            "adresseMail" => $adresseMail,
        ];
        $statement = ConnexionBaseDeDonnees::getPdo()->prepare("SELECT * FROM utilisateurs WHERE adresseMail = :adresseMail");
        return $this->extractUtilisateur($statement, $values);
    }

    public function create($entity)
    {
        $values = [
            "login" => $entity->getLogin(),
            "password" => $entity->getPassword(),
            "adresseMail" => $entity->getAdresseMail(),
            "profilePictureName" => $entity->getProfilePictureName()
        ];
        $pdo = ConnexionBaseDeDonnees::getPdo();
        $statement = $pdo->prepare("INSERT INTO utilisateurs (login, password, adresseMail, profilePictureName) VALUES(:login, :password, :adresseMail, :profilePictureName);");
        $statement->execute($values);
        return $pdo->lastInsertId();
    }

    public function update($entity)
    {
        $values = [
            "idUtilisateur" => $entity->getIdUtilisateur(),
            "login" => $entity->getLogin(),
            "password" => $entity->getPassword(),
            "adresseMail" => $entity->getAdresseMail(),
            "profilePictureName" => $entity->getProfilePictureName()
        ];
        $statement = ConnexionBaseDeDonnees::getPdo()->prepare("UPDATE utilisateurs SET login = :login, password = :password, adresseMail = :adresseMail, profilePictureName = :profilePictureName WHERE idUtilisateur = :idUtilisateur;");
        $statement->execute($values);
    }

    public function remove($entity)
    {
        $values = [
            "idUtilisateur" => $entity->getIdUtilisateur(),
        ];
        $statement = ConnexionBaseDeDonnees::getPdo()->prepare("DELETE FROM utilisateurs WHERE idUtilisateur = :idUtilisateur");
        $statement->execute($values);
    }

    /**
     * @param bool|PDOStatement $statement
     * @param array $values
     * @return Utilisateur|void
     */
    public function extractUtilisateur(PDOStatement $statement, array $values)
    {
        $statement->execute($values);
        $data = $statement->fetch();
        if ($data) {
            $utilisateur = new Utilisateur();
            $utilisateur->setIdUtilisateur($data["idUtilisateur"]);
            $utilisateur->setLogin($data["login"]);
            $utilisateur->setPassword($data["password"]);
            $utilisateur->setAdresseMail($data["adresseMail"]);
            $utilisateur->setProfilePictureName($data["profilePictureName"]);
            return $utilisateur;
        }
    }
}