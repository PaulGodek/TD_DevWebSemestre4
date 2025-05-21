<?php

namespace TheFeed\Modele\DataObject;

use DateTime;
use JsonSerializable;

class Publication implements JsonSerializable
{

    private int $idPublication;
    private string $message;
    private DateTime $date;
    private Utilisateur $auteur;

    public static function construire(string $message, Utilisateur $auteur) : Publication {
        $publication = new Publication();
        $publication->message = $message;
        $publication->date = new DateTime();
        $publication->auteur = $auteur;
        return $publication;
    }

    public function __construct() {  }

    public function getIdPublication(): int
    {
        return $this->idPublication;
    }

    public function setIdPublication(int $idPublication): void
    {
        $this->idPublication = $idPublication;
    }

    public function getMessage() : string
    {
        return $this->message;
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    public function getDate() : DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    public function getAuteur(): Utilisateur
    {
        return $this->auteur;
    }

    public function setAuteur(Utilisateur $auteur): void
    {
        $this->auteur = $auteur;
    }

    public function jsonSerialize(): array
    {
        return [
            "idPublication" => $this->getIdPublication(),
            "message" => $this->getMessage(),
            "date" => $this->getDate()->format('d F Y'),
            "auteur" => [
                "idUtilisateur" => $this->getAuteur()->getIdUtilisateur()
            ]
        ];
    }
}
