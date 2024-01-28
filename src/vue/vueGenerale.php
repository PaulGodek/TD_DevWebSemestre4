<?php

use TheFeed\Lib\ConnexionUtilisateur;

/**
 * @var string $pagetitle
 * @var string $cheminVueBody
 * @var String[][] $messagesFlash
 */
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title><?= $pagetitle ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../ressources/css/styles.css">
</head>

<body>
    <header>
        <div id="titre" class="center">
            <a href="controleurFrontal.php?controleur=publication&action=afficherListe"><span>The Feed</span></a>
            <nav>
                <a href="controleurFrontal.php?controleur=publication&action=afficherListe">Accueil</a>
                <?php
                if (!ConnexionUtilisateur::estConnecte()) {
                ?>
                    <a href="controleurFrontal.php?action=afficherFormulaireCreation&controleur=utilisateur">Inscription</a>
                    <a href="controleurFrontal.php?action=afficherFormulaireConnexion&controleur=utilisateur">Connexion</a>
                <?php
                } else {
                    $idUtilisateurURL = rawurlencode(ConnexionUtilisateur::getIdUtilisateurConnecte());
                ?>
                    <a href="controleurFrontal.php?action=afficherPublications&controleur=utilisateur&idUser=<?= $idUtilisateurURL ?>">Ma
                        page</a>
                    <a href="controleurFrontal.php?action=deconnecter&controleur=utilisateur">Déconnexion</a>
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