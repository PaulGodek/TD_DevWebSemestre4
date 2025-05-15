<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use TheFeed\Modele\DataObject\Utilisateur;
use TheFeed\Modele\Repository\ConnexionBaseDeDonnees;
use TheFeed\Modele\Repository\ConnexionBaseDeDonneesInterface;
use TheFeed\Modele\Repository\UtilisateurRepository;
use TheFeed\Modele\Repository\UtilisateurRepositoryInterface;
use Tests\Unit\Configuration\ConfigurationBDDTestUnitaire;

class UtilisateurRepositoryTest extends TestCase
{
    private static UtilisateurRepositoryInterface  $utilisateurRepository;

    private static ConnexionBaseDeDonneesInterface $connexionBaseDeDonnees;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$connexionBaseDeDonnees = new ConnexionBaseDeDonnees(new ConfigurationBDDTestUnitaire());
        self::$utilisateurRepository = new UtilisateurRepository(self::$connexionBaseDeDonnees);
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::$connexionBaseDeDonnees->getPdo()->query("INSERT INTO 
                                                        utilisateurs (idUtilisateur, login, mdpHache, email, nomPhotoDeProfil) 
                                                        VALUES (1, 'test', 'test', 'test@example.com', 'test.png')");
        self::$connexionBaseDeDonnees->getPdo()->query("INSERT INTO 
                                                        utilisateurs (idUtilisateur, login, mdpHache, email, nomPhotoDeProfil) 
                                                        VALUES (2, 'test2', 'test2', 'test2@example.com', 'test2.png')");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        self::$connexionBaseDeDonnees->getPdo()->query("DELETE FROM utilisateurs");
        self::$connexionBaseDeDonnees->getPdo()->query("DELETE FROM sqlite_sequence WHERE name='utilisateurs'");
    }

    public function testSimpleNombreUtilisateurs() {
        $this->assertCount(2, self::$utilisateurRepository->recuperer());
    }

    public function test_recupererParClePrimaire() {
        $this->assertEquals(1, self::$utilisateurRepository->recupererParClePrimaire(1)->getIdUtilisateur());
    }

    public function test_recupererParLogin() {
        $this->assertEquals(2, self::$utilisateurRepository->recupererParLogin("test2")->getIdUtilisateur());
    }

    public function test_recupererParEmail() {
        $this->assertEquals(1, self::$utilisateurRepository->recupererParEmail("test@example.com")->getIdUtilisateur());
    }

    public function test_ajouter() {
        $utilisateur = new Utilisateur();
        $utilisateur->setLogin("test3");
        $utilisateur->setMdpHache("test3");
        $utilisateur->setEmail("test3@example.com");
        $utilisateur->setNomPhotoDeProfil("test3.png");
        $this->assertEquals(3, self::$utilisateurRepository->ajouter($utilisateur));

        $this->assertCount(3, self::$utilisateurRepository->recuperer());
    }

    public function test_mettreAJour() {
        $utilisateur = new Utilisateur();
        $utilisateur->setIdUtilisateur(2);
        $utilisateur->setLogin("test2");
        $utilisateur->setMdpHache("test2deOuf");
        $utilisateur->setEmail("test2@example.com");
        $utilisateur->setNomPhotoDeProfil("test2.png");
        self::$utilisateurRepository->mettreAJour($utilisateur);

        $this->assertEquals("test2deOuf", self::$utilisateurRepository->recupererParClePrimaire(2)->getMdpHache());
    }

    public function test_supprimer() {
        $utilisateur = new Utilisateur();
        $utilisateur->setIdUtilisateur(2);
        self::$utilisateurRepository->supprimer($utilisateur);
        $this->assertCount(1, self::$utilisateurRepository->recuperer());
        $this->assertEquals(1, self::$utilisateurRepository->recupererParClePrimaire(1)->getIdUtilisateur());
    }
}