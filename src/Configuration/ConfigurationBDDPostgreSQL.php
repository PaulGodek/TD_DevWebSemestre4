<?php

namespace TheFeed\Configuration;

use PDO;

class ConfigurationBDDPostgreSQL implements ConfigurationBDDInterface
{
    private string $login = "";
    private string $motDePasse = "";
    private string $nomBDD = "iut";
    private string $hostname = "162.38.222.142";

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }

    public function getDSN() : string{
        //https://www.php.net/manual/en/ref.pdo-pgsql.connection.php
        //pgsql:host=localhost;port=5432;dbname=testdb;user=bruce;password=mypass
        return "pgsql:host={$this->hostname};port=5432;dbname={$this->nomBDD};";
    }
    public function getOptions() : array {
        // Option pour que toutes les chaines de caractères
        // en entrée et sortie de MySql soit dans le codage UTF-8
        return array();
    }
}