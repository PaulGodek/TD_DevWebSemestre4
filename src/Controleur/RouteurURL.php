<?php

namespace TheFeed\Controleur;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\AttributeDirectoryLoader;
use TheFeed\Lib\AttributeRouteControllerLoader;
use TheFeed\Lib\ConnexionUtilisateur;
use TheFeed\Lib\Conteneur;
use TheFeed\Lib\MessageFlash;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use TheFeed\Controleur\ControleurPublication;
use TheFeed\Controleur\ControleurUtilisateur;
use TheFeed\Modele\Repository\ConnexionBaseDeDonnees;
use TheFeed\Modele\Repository\PublicationRepository;
use TheFeed\Modele\Repository\UtilisateurRepository;
use TheFeed\Service\PublicationService;
use TheFeed\Service\UtilisateurService;
use TheFeed\Configuration\ConfigurationBDDMySQL;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RouteurURL
{
    public static function traiterRequete(): void {

        $conteneur = new ContainerBuilder();

        $conteneur->register(ConfigurationBDDMySQL::class, ConfigurationBDDMySQL::class);

        $connexionBaseReference = $conteneur->register(ConnexionBaseDeDonnees::class, ConnexionBaseDeDonnees::class);
        $connexionBaseReference->setArguments([new Reference(ConfigurationBDDMySQL::class)]);

        $publicationsRepositoryReference = $conteneur->register(PublicationRepository::class, PublicationRepository::class);
        $publicationsRepositoryReference->setArguments([new Reference(ConnexionBaseDeDonnees::class)]);

        $utilisateurRepositoryReference = $conteneur->register(UtilisateurRepository::class, UtilisateurRepository::class);
        $utilisateurRepositoryReference->setArguments([new Reference(ConnexionBaseDeDonnees::class)]);

        $publicationServiceReference = $conteneur->register(PublicationService::class, PublicationService::class);
        $publicationServiceReference->setArguments([new Reference(UtilisateurRepository::class), new Reference(PublicationRepository::class)]);

        $publicationControleurReference = $conteneur->register(ControleurPublication::class, ControleurPublication::class);
        $publicationControleurReference->setArguments([new Reference(PublicationService::class)]);

        $utilisateurServiceReference = $conteneur->register(UtilisateurService::class, UtilisateurService::class);
        $utilisateurServiceReference->setArguments([new Reference(UtilisateurRepository::class)]);

        $publicationControleurReference = $conteneur->register(ControleurUtilisateur::class, ControleurUtilisateur::class);
        $publicationControleurReference->setArguments([new Reference(UtilisateurService::class), new Reference(PublicationService::class)]);

        $requete = Request::createFromGlobals();

        $fileLocator = new FileLocator(__DIR__);
        $attrClassLoader = new AttributeRouteControllerLoader();
        $routes = (new AttributeDirectoryLoader($fileLocator, $attrClassLoader))->load(__DIR__);

        $contexteRequete = (new RequestContext())->fromRequest($requete);

        $generateurUrl = new UrlGenerator($routes, $contexteRequete);
        $assistantUrl = new UrlHelper(new RequestStack(), $contexteRequete);

        Conteneur::ajouterService("generateurUrl", $generateurUrl);
        Conteneur::ajouterService("assistantUrl", $assistantUrl);

        $twigLoader = new FilesystemLoader(__DIR__ . '/../vue/');
        $twig = new Environment(
            $twigLoader,
            [
                'autoescape' => 'html',
                'strict_variables' => true
            ]
        );
        Conteneur::ajouterService("twig", $twig);

        $twig->addFunction(new TwigFunction("route", $generateurUrl->generate(...)));
        $twig->addFunction(new TwigFunction("asset", $assistantUrl->getAbsoluteUrl(...)));

        $twig->addGlobal("estConnecte", ConnexionUtilisateur::estConnecte() ? ConnexionUtilisateur::getIdUtilisateurConnecte() : null);

        $twig->addGlobal('messagesFlash', new MessageFlash());

        try {
            $associateurUrl = new UrlMatcher($routes, $contexteRequete);
            $donneesRoute = $associateurUrl->match($requete->getPathInfo());

            $requete->attributes->add($donneesRoute);

            $resolveurDeControleur = new ContainerControllerResolver($conteneur);
            $controleur = $resolveurDeControleur->getController($requete);

            $resolveurDArguments = new ArgumentResolver();
            $arguments = $resolveurDArguments->getArguments($requete, $controleur);

            $reponse = call_user_func_array($controleur, $arguments);
        } catch (MethodNotAllowedException $exception) {
            $reponse = (new ControleurGenerique())->afficherErreur($exception->getMessage(), 403);
        } catch (NoConfigurationException|ResourceNotFoundException $exception) {
            $reponse = (new ControleurGenerique())->afficherErreur($exception->getMessage(), 404);
        } catch (\Exception $exception) {
            $reponse = (new ControleurGenerique())->afficherErreur($exception->getMessage());
        }

        $reponse->send();
    }
}