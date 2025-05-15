<?php

namespace Tests\Unit\Configuration;

use TheFeed\Configuration\ConfigurationBDDInterface;

class ConfigurationBDDTestUnitaire implements ConfigurationBDDInterface
{
    public function getLogin(): string
    {
        return "";
    }

    public function getMotDePasse(): string
    {
        return "";
    }

    public function getDSN(): string
    {
        return "sqlite:".__DIR__."/db_test.db";
    }

    public function getOptions(): array
    {
        return array();
    }
}