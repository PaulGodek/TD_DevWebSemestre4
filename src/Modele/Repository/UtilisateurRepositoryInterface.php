<?php

namespace TheFeed\Modele\Repository;

use PDOStatement;
use TheFeed\Modele\DataObject\Utilisateur;

interface UtilisateurRepositoryInterface
{
    /**
     * @return Utilisateur[]
     */
    public function recuperer(): array;

    public function recupererParClePrimaire($id): ?Utilisateur;

    public function recupererParLogin($login);

    public function recupererParEmail($email);

    public function ajouter($entite);

    public function mettreAJour($entite);

    public function supprimer($entite);

    /**
     * @param bool|PDOStatement $statement
     * @param array $values
     * @return Utilisateur|void
     */
    public function extraireUtilisateur(PDOStatement $statement, array $values);
}