<?php

namespace TheFeed\Configuration;

use PDO;

class ConfigurationBDDMySQL implements ConfigurationBDDInterface
{
    private string $login = "";
    private string $motDePasse = "";

    /** À l'IUT, vous avez une base de données nommée comme votre login
     * @var string
     */
    private string $nomBDD = "";
    private string $hostname = "webinfo.iutmontp.univ-montp2.fr";


    /** À l'IUT, le port de MySQL est particulier : 3316
     * Ailleurs, on utilise le port par défaut : 3306
     * @var string
     */
    private string  $port = '3316';

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }

    public function getDSN() : string{
        return "mysql:host={$this->hostname};port={$this->port};dbname={$this->nomBDD}";
    }
    public function getOptions() : array {
        // Option pour que toutes les chaines de caractères
        // en entrée et sortie de MySql soit dans le codage UTF-8
        return array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        );
    }
}