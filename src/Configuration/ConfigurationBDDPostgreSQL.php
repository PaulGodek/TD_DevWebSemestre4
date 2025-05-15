<?php

namespace TheFeed\Configuration;

class ConfigurationBDDPostgreSQL implements ConfigurationBDDInterface
{
    private string $login = "";
    private string $motDePasse = "";
    private string $nomBDD = "iut";
    private string $hostname = "162.38.222.151";

    /** À l'IUT, le port de MySQL est particulier : 5673
     * Ailleurs, on utilise le port par défaut : 5432
     * @var string
     */
    private string  $port = '5673';

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
        return "pgsql:host={$this->hostname};port={$this->port};dbname={$this->nomBDD};";
    }
    public function getOptions() : array {
        // Option pour que toutes les chaines de caractères
        // en entrée et sortie de MySql soit dans le codage UTF-8
        return array();
    }
}