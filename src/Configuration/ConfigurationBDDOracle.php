<?php

namespace TheFeed\Configuration;

use PDO;

class ConfigurationBDDOracle implements ConfigurationBDDInterface
{
    private string $login = "";
    private string $motDePasse = "";
    private string $nomBDD = "IUT";
    private string $hostname = "orainfo.iutmontp.univ-montp2.fr";
    /** Port par défaut : 1521
     * @var string
     */
    private string  $port = '1521';
    public function getLogin(): string
    {
        return $this->login;
    }

    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }

    public function getDSN() : string{
        // https://www.php.net/manual/en/ref.pdo-oci.connection.php
        // oci:dbname=//hostname:port-number/database
        // oci:dbname=//orainfo.iutmontp.univ-montp2.fr:1521/IUT
        return "oci:dbname=//{$this->hostname}:{$this->port}/{$this->nomBDD}";
    }
    public function getOptions() : array {
        // Option pour que toutes les chaines de caractères
        // en entrée et sortie de MySql soit dans le codage UTF-8
        return array();
    }
}