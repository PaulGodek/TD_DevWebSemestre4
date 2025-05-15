<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use TheFeed\Modele\DataObject\Publication;
use TheFeed\Modele\DataObject\Utilisateur;
use TheFeed\Modele\Repository\PublicationRepositoryInterface;
use TheFeed\Modele\Repository\UtilisateurRepositoryInterface;
use TheFeed\Service\Exception\ServiceException;
use TheFeed\Service\PublicationService;

class PublicationServiceTest extends TestCase
{
    private $service;
    private $publicationRepositoryMock;
    private $utilisateurRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->publicationRepositoryMock = $this->createMock(PublicationRepositoryInterface::class);
        $this->utilisateurRepositoryMock = $this->createMock(UtilisateurRepositoryInterface::class);
        $this->service = new PublicationService($this->utilisateurRepositoryMock, $this->publicationRepositoryMock);
    }

    public function testCreerPublicationUtilisateurInexistant(): void {
        $this->utilisateurRepositoryMock->method("recupererParClePrimaire")->willReturn(null);

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage("Il faut être connecté pour publier un feed.");
        $this->service->creerPublication(-1, "Je poste des folies");
    }

    public function testCreerPublicationVide(): void {
        $this->utilisateurRepositoryMock->method("recupererParClePrimaire")->willReturn(new Utilisateur());

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage("Le message ne peut pas être vide !");
        $this->service->creerPublication(9, "");
    }

    public function testCreerPublicationTropGrande(): void {
        $this->utilisateurRepositoryMock->method("recupererParClePrimaire")->willReturn(new Utilisateur());

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage("Le message ne peut pas dépasser 250 caractères !");
        $this->service->creerPublication(9, str_repeat("salut", 200));
    }

    public function testNombrePublications() {
        //Fausses publications, vides
        $fakePublications = [new Publication(), new Publication()];
        //On configure notre faux repository pour qu'il renvoie nos publications définies ci-dessus
        $this->publicationRepositoryMock->method("recuperer")->willReturn($fakePublications);
        //Test
        $this->assertCount(2, $this->service->recupererPublications());
    }

    public function testNombrePublicationsUtilisateur() {
        $fakePublications = [new Publication(), new Publication()];

        $this->publicationRepositoryMock->method("recupererParAuteur")->willReturn($fakePublications);

        $this->assertCount(2, $this->service->recupererPublicationsUtilisateur(null)); // Paramètre pas important avec Mock
    }

    public function testNombrePublicationsUtilisateurInexistant() {
        $this->publicationRepositoryMock->method("recupererParAuteur")->willReturn([]);

        $this->assertCount(0, $this->service->recupererPublicationsUtilisateur(-1));
    }

    public function testCreerPublicationValide() {
        $fakeUser = new Utilisateur();

        $this->utilisateurRepositoryMock->method("recupererParClePrimaire")->willReturn($fakeUser);

        $this->publicationRepositoryMock->method("ajouter")->willReturnCallback(function($fakePublication) {
            self::assertEquals("Je suis le créateur de feed que je pense être", $fakePublication->getMessage());
            self::assertEquals(new Utilisateur(), $fakePublication->getAuteur());
        });

        $this->service->creerPublication(null, "Je suis le créateur de feed que je pense être");
    }
}