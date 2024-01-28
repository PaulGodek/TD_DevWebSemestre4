<?php

////////////////////
// Initialisation //
////////////////////
use TheFeed\Lib\Psr4AutoloaderClass;

require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';


// initialisation en désactivant l'affichage de débogage
$chargeurDeClasse = new Psr4AutoloaderClass(false);
$chargeurDeClasse->register();
// enregistrement d'une association "espace de nom" → "dossier"
$chargeurDeClasse->addNamespace('TheFeed', __DIR__ . '/../src');


/////////////
// Routage //
/////////////

// Syntaxe alternative
// The null coalescing operator returns its first operand if it exists and is not null
$action = $_REQUEST['action'] ?? 'afficherListe';


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

