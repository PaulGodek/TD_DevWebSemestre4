<?php

/**
 * @var Publication[] $publications
 */

use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGenerator;
use TheFeed\Lib\Conteneur;
use TheFeed\Modele\DataObject\Publication;

/** @var UrlGenerator $generateurUrl */
$generateurUrl = Conteneur::recupererService("generateurUrl");
/** @var UrlHelper $assistantUrl */
$assistantUrl = Conteneur::recupererService("assistantUrl");
?>
<main id="the-feed-main">
    <div id="feed">
        <?php
        if (!empty($publications)) {
            foreach ($publications as $publication) {
                $loginHTML = htmlspecialchars($publication->getAuteur()->getLogin());
                $messageHTML = htmlspecialchars($publication->getMessage());
        ?>
                <div class="feedy">
                    <div class="feedy-header">
                        <a href="<?= $generateurUrl->generate("publicationsUtilisateur_GET", ["idUtilisateur" => $publication->getAuteur()->getIdUtilisateur()]) ?>">
                            <img class="avatar" src="<?= $assistantUrl->getAbsoluteUrl("../ressources/img/utilisateurs/".$publication->getAuteur()->getNomPhotoDeProfil()) ?>" alt="avatar de l'utilisateur">
                        </a>
                        <div class="feedy-info">
                            <span><?= $loginHTML ?></span>
                            <span> - </span>
                            <span><?= $publication->getDate()->format('d F Y') ?></span>
                            <p><?= $messageHTML ?></p>
                        </div>
                    </div>
                </div>
            <?php
            }
        } else {
            ?>
            <p id="no-publications" class="center">Pas de publications pour le moment!</p>
        <?php
        }
        ?>
    </div>
</main>