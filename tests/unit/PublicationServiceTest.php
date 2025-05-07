<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use TheFeed\Service\Exception\ServiceException;
use TheFeed\Service\PublicationService;

class PublicationServiceTest extends TestCase
{
    private $service;

    public function setUp(): void
    {
        $this->service = new PublicationService();
    }

    public function testCreerPublicationUtilisateurInexistant(): void {
        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage("Il faut être connecté pour publier un feed.");
        $this->service->creerPublication(-1, "Je poste des folies");
    }

    public function testCreerPublicationVide(): void {
        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage("Le message ne peut pas être vide !");
        $this->service->creerPublication(9, "");
    }

    public function testCreerPublicationTropGrande(): void {
        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage("Le message ne peut pas dépasser 250 caractères !");
        $this->service->creerPublication(9, str_repeat("salut", 200));
    }

    public function testNombrePublications() {
        $number = sizeof($this->service->recupererPublications());
        $this->assertEquals(6, $number);
    }

    public function testNombrePublicationsUtilisateur() {
        $number = sizeof($this->service->recupererPublicationsUtilisateur(9));
        $this->assertEquals(4, $number);
    }

    public function testNombrePublicationsUtilisateurInexistant() {
        $number = sizeof($this->service->recupererPublicationsUtilisateur(-1));
        $this->assertEquals(0, $number);
    }
}