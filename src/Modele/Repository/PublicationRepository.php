<?php

namespace TheFeed\Modele\Repository;

use TheFeed\Modele\DataObject\Publication;
use TheFeed\Modele\DataObject\Utilisateur;
use DateTime;

class PublicationRepository
{
    /**
     * @return Publication[]
     * @throws \Exception
     */
    public function recuperer(): array
    {
        $statement = ConnexionBaseDeDonnees::getPdo()->prepare("SELECT idPublication, message, date, idUtilisateur, login, nomPhotoDeProfil
                                                FROM publications p
                                                JOIN utilisateurs u on p.idAuteur = u.idUtilisateur
                                                ORDER BY date DESC");
        $statement->execute();

        $publications = [];

        foreach ($statement as $data) {
            $publication = new Publication();
            $publication->setIdPublication($data["idPublication"]);
            $publication->setMessage($data["message"]);
            $publication->setDate(new DateTime($data["date"]));
            $utilisateur = new Utilisateur();
            $utilisateur->setIdUtilisateur($data["idUtilisateur"]);
            $utilisateur->setLogin($data["login"]);
            $utilisateur->setNomPhotoDeProfil($data["nomPhotoDeProfil"]);
            $publication->setAuteur($utilisateur);
            $publications[] = $publication;
        }

        return $publications;
    }

    /**
     * @param $idUtilisateur
     * @return Publication[]
     * @throws \Exception
     */
    public function recupererParAuteur($idUtilisateur): array
    {
        $values = [
            "idAuteur" => $idUtilisateur,
        ];
        $statement = ConnexionBaseDeDonnees::getPdo()->prepare("SELECT idPublication, message, date, idUtilisateur, login, nomPhotoDeProfil
                                                FROM publications p
                                                JOIN utilisateurs u on p.idAuteur = u.idUtilisateur
                                                WHERE idAuteur = :idAuteur
                                                ORDER BY date DESC");
        $statement->execute($values);

        $publis = [];

        foreach ($statement as $data) {
            $publi = new Publication();
            $publi->setIdPublication($data["idPublication"]);
            $publi->setMessage($data["message"]);
            $publi->setDate(new DateTime($data["date"]));
            $utilisateur = new Utilisateur();
            $utilisateur->setIdUtilisateur($data["idUtilisateur"]);
            $utilisateur->setLogin($data["login"]);
            $utilisateur->setNomPhotoDeProfil($data["nomPhotoDeProfil"]);
            $publi->setAuteur($utilisateur);
            $publis[] = $publi;
        }

        return $publis;
    }

    public function ajouter(Publication $publication)
    {
        $values = [
            "message" => $publication->getMessage(),
            "date" => $publication->getDate()->format('Y-m-d H:i:s'),
            "idAuteur" => $publication->getAuteur()->getIdUtilisateur()
        ];
        $pdo = ConnexionBaseDeDonnees::getPdo();
        $statement = $pdo->prepare("INSERT INTO publications (message, date, idAuteur) VALUES(:message, :date, :idAuteur);");
        $statement->execute($values);
        return $pdo->lastInsertId();
    }

    public function recupererParClePrimaire($id) : ?Publication
    {
        $values = [
            "idPublication" => $id,
        ];
        $statement = ConnexionBaseDeDonnees::getPdo()->prepare("SELECT idPublication, message, date, idUtilisateur, login, nomPhotoDeProfil
                                                FROM publications p
                                                JOIN utilisateurs u on p.idAuteur = u.idUtilisateur
                                                WHERE idPublication = :idPublication");
        $statement->execute($values);
        $data = $statement->fetch();
        if ($data) {
            $publication = new Publication();
            $publication->setIdPublication($data["idPublication"]);
            $publication->setMessage($data["message"]);
            $publication->setDate(new DateTime($data["date"]));
            $utilisateur = new Utilisateur();
            $utilisateur->setIdUtilisateur($data["idUtilisateur"]);
            $utilisateur->setLogin($data["login"]);
            $utilisateur->setNomPhotoDeProfil($data["nomPhotoDeProfil"]);
            $publication->setAuteur($utilisateur);
            return $publication;
        }
        return null;
    }

    public function mettreAJour(Publication $publication)
    {
        $values = [
            "idPublication" => $publication->getIdPublication(),
            "message" => $publication->getMessage(),
        ];
        $statement = ConnexionBaseDeDonnees::getPdo()->prepare("UPDATE publications SET message = :message WHERE idPublication = :idPublication;");
        $statement->execute($values);
    }

    public function supprimer(Publication $publication)
    {
        $values = [
            "idPublication" => $publication->getIdPublication(),
        ];
        $statement = ConnexionBaseDeDonnees::getPdo()->prepare("DELETE FROM publications WHERE idPublication = :idPublication");
        $statement->execute($values);
    }

}