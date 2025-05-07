<?php

namespace TheFeed\Controleur;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use TheFeed\Lib\ConnexionUtilisateur;
use TheFeed\Lib\Conteneur;
use TheFeed\Lib\MessageFlash;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class RouteurURL
{
    public static function traiterRequete(): void {

        $requete = Request::createFromGlobals();

        $routes = new RouteCollection();


        // ---------------------
        //       ROUTE GET
        // ---------------------

        // Route afficherPublications GET
        $route = new Route("/publications", [
            "_controller" => "\TheFeed\Controleur\ControleurPublication::afficherListe",
        ]);
        $route->setMethods(["GET"]);
        $routes->add("publications_GET", $route);

        // Route afficherFormulaireConnexion GET
        $route = new Route("/connexion", [
            "_controller" => "\TheFeed\Controleur\ControleurUtilisateur::afficherFormulaireConnexion",
        ]);
        $route->setMethods(["GET"]);
        $routes->add("connexion_GET", $route);

        // Route deconnection GET
        $route = new Route("/deconnexion", [
            "_controller" => "\TheFeed\Controleur\ControleurUtilisateur::deconnecter",
        ]);
        $route->setMethods(["GET"]);
        $routes->add("deconnexion_GET", $route);

        // Route inscription GET
        $route = new Route("/inscription", [
            "_controller" => "\TheFeed\Controleur\ControleurUtilisateur::afficherFormulaireCreation",
        ]);
        $route->setMethods(["GET"]);
        $routes->add("inscription_GET", $route);

        // Route publicationsUtilisateur GET
        $route = new Route("/utilisateurs/{idUtilisateur}/publications", [
            "_controller" => "\TheFeed\Controleur\ControleurUtilisateur::afficherPublications",
        ]);
        $route->setMethods(["GET"]);
        $routes->add("publicationsUtilisateur_GET", $route);


        // ----------------------
        //       ROUTE POST
        // ----------------------

        // Route connexion POST
        $route = new Route("/connexion", [
            "_controller" => "\TheFeed\Controleur\ControleurUtilisateur::connecter",
        ]);
        $route->setMethods(["POST"]);
        $routes->add("connexion_POST", $route);

        // Route inscription POST
        $route = new Route("/inscription", [
            "_controller" => "\TheFeed\Controleur\ControleurUtilisateur::creerDepuisFormulaire",
        ]);
        $route->setMethods(["POST"]);
        $routes->add("inscription_POST", $route);

        // Route publications POST
        $route = new Route("/publications", [
            "_controller" => "\TheFeed\Controleur\ControleurPublication::creerDepuisFormulaire",
        ]);
        $route->setMethods(["POST"]);
        $routes->add("publications_POST", $route);





        // Route TEMPORARY
        $route = new Route("/", [
            "_controller" => "\TheFeed\Controleur\ControleurPublication::afficherListe",
        ]);
        $route->setMethods(["GET"]);
        $routes->add("home_TEMP", $route);



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

            $resolveurDeControleur = new ControllerResolver();
            $controleur = $resolveurDeControleur->getController($requete);

            $resolveurDArguments = new ArgumentResolver();
            $arguments = $resolveurDArguments->getArguments($requete, $controleur);

            $reponse = call_user_func_array($controleur, $arguments);
        } catch (MethodNotAllowedException $exception) {
            $reponse = ControleurGenerique::afficherErreur($exception->getMessage(), 403);
        } catch (NoConfigurationException|ResourceNotFoundException $exception) {
            $reponse = ControleurGenerique::afficherErreur($exception->getMessage(), 404);
        } catch (\Exception $exception) {
            $reponse = ControleurGenerique::afficherErreur($exception->getMessage());
        }

        $reponse->send();
    }
}