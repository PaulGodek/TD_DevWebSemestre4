<?php

////////////////////
// Initialisation //
////////////////////
use TheFeed\Lib\Psr4AutoloaderClass;

require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';

// instantiate the loader
$loader = new Psr4AutoloaderClass();
// register the base directories for the namespace prefix
$loader->addNamespace('TheFeed', __DIR__ . '/../src');
// register the autoloader
$loader->register();

/////////////
// Routage //
/////////////

// Syntaxe alternative
// The null coalescing operator returns its first operand if it exists and is not null
$action = $_REQUEST['action'] ?? 'feed';


$controleur = "publication";
if (isset($_REQUEST['controleur']))
    $controleur = $_REQUEST['controleur'];

$controleurClassName = 'TheFeed\Controleur\Controleur' . ucfirst($controleur);

if (class_exists($controleurClassName)) {
    if (in_array($action, get_class_methods($controleurClassName))) {
        $controleurClassName::$action();
    } else {
        $controleurClassName::afficherErreur("Erreur d'action");
    }
} else {
    TheFeed\Controleur\ControleurGenerique::afficherErreur("Erreur de contrôleur");
}

