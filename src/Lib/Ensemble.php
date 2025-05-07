<?php

namespace TheFeed\Lib;

use Exception;

class Ensemble {

    private array $tableauEnsemble;

    public function __construct() {
        $this->tableauEnsemble = [];
    }

    public function contient($valeur): bool
    {
        return in_array($valeur, $this->tableauEnsemble);
    }

    public function ajouter($valeur): void
    {
        if(!$this->contient($valeur)) {
            $this->tableauEnsemble[] = $valeur;
        }
    }

    public function getTaille(): int
    {
        return count($this->tableauEnsemble);
    }

    public function estVide(): bool
    {
        return $this->getTaille() == 0;
    }

    /**
     * @throws Exception
     */
    public function pop() {
        if($this->estVide()) {
            throw new Exception("L'ensemble est vide!");
        }
        return array_pop($this->tableauEnsemble);
    }
}