<?php

namespace TheFeed\Modele\Repository;

use PDO;
use TheFeed\Configuration\ConfigurationBDDInterface;

interface ConnexionBaseDeDonneesInterface
{
    public function getPdo(): PDO;
    public function __construct(ConfigurationBDDInterface $configurationBDD);
}