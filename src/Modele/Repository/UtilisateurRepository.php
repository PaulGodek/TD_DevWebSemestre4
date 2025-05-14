<?php

namespace TheFeed\Modele\Repository;

use TheFeed\Modele\DataObject\Utilisateur;
use PDOStatement;

class UtilisateurRepository implements UtilisateurRepositoryInterface
{
    private ConnexionBaseDeDonnees $connexionBaseDeDonnee;

    public function __construct(ConnexionBaseDeDonnees $connexionBaseDeDonnee) {
        $this->connexionBaseDeDonnee = $connexionBaseDeDonnee;
    }

    /**
     * @return Utilisateur[]
     */
    public function recuperer(): array
    {
        $statement = $this->connexionBaseDeDonnee->getPdo()->prepare("SELECT * FROM utilisateurs");
        $statement->execute();

        $utilisateurs = [];

        foreach ($statement as $data) {
            $utilisateur = new Utilisateur();
            $utilisateur->setIdUtilisateur($data["idUtilisateur"]);
            $utilisateur->setLogin($data["login"]);
            $utilisateur->setMdpHache($data["mdpHache"]);
            $utilisateur->setEmail($data["email"]);
            $utilisateur->setNomPhotoDeProfil($data["nomPhotoDeProfil"]);
            $utilisateurs[] = $utilisateur;
        }

        return $utilisateurs;
    }

    public function recupererParClePrimaire($id): ?Utilisateur
    {
        $values = [
            "idUtilisateur" => $id,
        ];
        $statement = $this->connexionBaseDeDonnee->getPdo()->prepare("SELECT * FROM utilisateurs WHERE idUtilisateur = :idUtilisateur");
        return $this->extraireUtilisateur($statement, $values);
    }

    public function recupererParLogin($login)
    {
        $values = [
            "login" => $login,
        ];
        $statement = $this->connexionBaseDeDonnee->getPdo()->prepare("SELECT * FROM utilisateurs WHERE login = :login");
        return $this->extraireUtilisateur($statement, $values);
    }

    public function recupererParEmail($email)
    {
        $values = [
            "email" => $email,
        ];
        $statement = $this->connexionBaseDeDonnee->getPdo()->prepare("SELECT * FROM utilisateurs WHERE email = :email");
        return $this->extraireUtilisateur($statement, $values);
    }

    public function ajouter($entite)
    {
        $values = [
            "login" => $entite->getLogin(),
            "mdpHache" => $entite->getMdpHache(),
            "email" => $entite->getEmail(),
            "nomPhotoDeProfil" => $entite->getNomPhotoDeProfil()
        ];
        $pdo = $this->connexionBaseDeDonnee->getPdo();
        $statement = $pdo->prepare("INSERT INTO utilisateurs (login, mdpHache, email, nomPhotoDeProfil) VALUES(:login, :mdpHache, :email, :nomPhotoDeProfil);");
        $statement->execute($values);
        return $pdo->lastInsertId();
    }

    public function mettreAJour($entite)
    {
        $values = [
            "idUtilisateur" => $entite->getIdUtilisateur(),
            "login" => $entite->getLogin(),
            "mdpHache" => $entite->getMdpHache(),
            "email" => $entite->getEmail(),
            "nomPhotoDeProfil" => $entite->getNomPhotoDeProfil()
        ];
        $statement = $this->connexionBaseDeDonnee->getPdo()->prepare("UPDATE utilisateurs SET login = :login, mdpHache = :mdpHache, email = :email, nomPhotoDeProfil = :nomPhotoDeProfil WHERE idUtilisateur = :idUtilisateur;");
        $statement->execute($values);
    }

    public function supprimer($entite)
    {
        $values = [
            "idUtilisateur" => $entite->getIdUtilisateur(),
        ];
        $statement = $this->connexionBaseDeDonnee->getPdo()->prepare("DELETE FROM utilisateurs WHERE idUtilisateur = :idUtilisateur");
        $statement->execute($values);
    }

    /**
     * @param bool|PDOStatement $statement
     * @param array $values
     * @return Utilisateur|void
     */
    public function extraireUtilisateur(PDOStatement $statement, array $values)
    {
        $statement->execute($values);
        $data = $statement->fetch();
        if ($data) {
            $utilisateur = new Utilisateur();
            $utilisateur->setIdUtilisateur($data["idUtilisateur"]);
            $utilisateur->setLogin($data["login"]);
            $utilisateur->setMdpHache($data["mdpHache"]);
            $utilisateur->setEmail($data["email"]);
            $utilisateur->setNomPhotoDeProfil($data["nomPhotoDeProfil"]);
            return $utilisateur;
        }
    }
}