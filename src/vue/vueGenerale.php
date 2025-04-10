<?php

use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGenerator;
use TheFeed\Lib\ConnexionUtilisateur;
use TheFeed\Lib\Conteneur;

/**
 * @var string $pagetitle
 * @var string $cheminVueBody
 * @var String[][] $messagesFlash
 */

/** @var UrlGenerator $generateurUrl */
$generateurUrl = Conteneur::recupererService("generateurUrl");
/** @var UrlHelper $assistantUrl */
$assistantUrl = Conteneur::recupererService("assistantUrl");
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title><?= $pagetitle ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="<?= $assistantUrl->getAbsoluteUrl("../ressources/css/styles.css")?>">
</head>

<body>
    <header>
        <div id="titre" class="center">
            <a href="<?= $generateurUrl->generate("publications_GET") ?>"><span>The Feed</span></a>
            <nav>
                <a href="<?= $generateurUrl->generate("publications_GET") ?>">Accueil</a>
                <?php
                if (!ConnexionUtilisateur::estConnecte()) {
                ?>
                    <a href="<?= $generateurUrl->generate("inscription_GET") ?>">Inscription</a>
                    <a href="<?= $generateurUrl->generate("connexion_GET") ?>">Connexion</a>
                <?php
                } else {
                    $idUtilisateurURL = rawurlencode(ConnexionUtilisateur::getIdUtilisateurConnecte());
                ?>
                    <a href="<?= $generateurUrl->generate("publicationsUtilisateur_GET", ["idUtilisateur" => $idUtilisateurURL]) ?>">Ma
                        page</a>
                    <a href="<?= $generateurUrl->generate("deconnexion_GET") ?>">Déconnexion</a>
                <?php } ?>
            </nav>
        </div>
    </header>
    <div id="flashes-container">
        <?php
        foreach (["success", "error"] as $type) {
            foreach ($messagesFlash[$type] as $messageFlash) {
        ?>
                <span class="flashes flashes-<?= $type ?>"><?= $messageFlash ?></span>
        <?php
            }
        }
        ?>
    </div>
    <?php
    require __DIR__ . "/{$cheminVueBody}";
    ?>
</body>

</html>