<?php

/**
 * @var Publication[] $publications
 */

use TheFeed\Modele\DataObject\Publication;
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
                        <a href="controleurFrontal.php?controleur=utilisateur&action=afficherPublications&idUser=<?= $publication->getAuteur()->getIdUtilisateur() ?>">
                            <img class="avatar" src="./assets/img/utilisateurs/<?= $publication->getAuteur()->getNomPhotoDeProfil() ?>" alt="avatar de l'utilisateur">
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