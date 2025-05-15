<?php

////////////////////
// Initialisation //
////////////////////

require_once __DIR__ . '/../vendor/autoload.php';


/////////////
// Routage //
/////////////
$requete = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
$reponse = \TheFeed\Controleur\RouteurURL::traiterRequete($requete);
$reponse->send();