<?php

namespace TheFeed\Controleur;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\AttributeDirectoryLoader;
use TheFeed\Lib\AttributeRouteControllerLoader;
use TheFeed\Lib\ConnexionUtilisateur;
use TheFeed\Lib\MessageFlash;
use Twig\TwigFunction;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RouteurURL
{
    public static function traiterRequete(Request $requete): Response {

        // Récupération de la requête
        $contexteRequete = (new RequestContext())->fromRequest($requete);

        // Création du conteneur et récupéraion de ses informations
        $conteneur = new ContainerBuilder();

        $conteneur->setParameter('project_root', __DIR__.'/../..');

        //On indique au FileLocator de chercher à partir du dossier de configuration
        $loader = new YamlFileLoader($conteneur, new FileLocator(__DIR__."/../Configuration"));
        //On remplit le conteneur avec les données fournies dans le fichier de configuration
        $loader->load("conteneur.yml");

        // Récupération des routes
        $fileLocator = new FileLocator(__DIR__);
        $attrClassLoader = new AttributeRouteControllerLoader();
        $routes = (new AttributeDirectoryLoader($fileLocator, $attrClassLoader))->load(__DIR__);

        // Création du générateur d'URL et ajout au conteneur
        $generateurUrl = new UrlGenerator($routes, $contexteRequete);
        $conteneur->set(UrlGenerator::class, $generateurUrl);

        $assistantUrl = new UrlHelper(new RequestStack(), $contexteRequete);

        // Twig
        $twig = $conteneur->get('Twig\Environment');

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
            $reponse = $conteneur->get('TheFeed\Controleur\ControleurGenerique')->afficherErreur($exception->getMessage(), 403);
        } catch (NoConfigurationException|ResourceNotFoundException $exception) {
            $reponse = $conteneur->get('TheFeed\Controleur\ControleurGenerique')->afficherErreur($exception->getMessage(), 404);
        } catch (\Exception $exception) {
            $reponse = $conteneur->get('TheFeed\Controleur\ControleurGenerique')->afficherErreur($exception->getMessage());
        }

        return $reponse;
    }
}