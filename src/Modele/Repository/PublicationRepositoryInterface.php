<?php

namespace TheFeed\Modele\Repository;

use TheFeed\Modele\DataObject\Publication;

interface PublicationRepositoryInterface
{
    /**
     * @return Publication[]
     * @throws \Exception
     */
    public function recuperer(): array;

    /**
     * @param $idUtilisateur
     * @return Publication[]
     * @throws \Exception
     */
    public function recupererParAuteur($idUtilisateur): array;

    public function ajouter(Publication $publication);

    public function recupererParClePrimaire($id): ?Publication;

    public function mettreAJour(Publication $publication);

    public function supprimer(Publication $publication);
}