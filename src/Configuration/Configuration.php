<?php

namespace TheFeed\Configuration;

class Configuration
{
    public ConfigurationBDDInterface $configurationBDD;

    static public function getDebug(): bool
    {
        return true;
    }

    public static function getDureeExpirationSession() : string
    {
        // Durée d'expiration des sessions en secondes
        return 120;
    }

    public static function getAbsoluteURL() : string
    {
        return "http://localhost/~lebreton/PHP2223/Solutions/TD8AnProchain/web/controleurFrontal.php";
    }

}