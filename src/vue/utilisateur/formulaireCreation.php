<?php
/**
 * @var string $login
 * @var string $adresseMail
 */

use Symfony\Component\Routing\Generator\UrlGenerator;
use TheFeed\Lib\Conteneur;

/** @var UrlGenerator $generateurUrl */
$generateurUrl = Conteneur::recupererService("generateurUrl");
?>
<main>
    <form action="<?= $generateurUrl->generate("inscription_POST") ?>" id="form-access" class="center" method="post" enctype="multipart/form-data">
    <fieldset>
        <legend>Inscription</legend>
        <div class="access-container">
            <label for="login">Login</label>
            <p class="help-input-form">Entre 4 et 20 caractères</p>
            <input id="login" type="text" name="login" value="<?= $login ?? "" ?>" minlength="4" maxlength="20" required/>
        </div>
        <div class="access-container">
            <label for="password">Mot de passe</label>
            <p class="help-input-form">Entre 8 et 20 caractères, au moins une minuscule, une majuscule et un nombre</p>
            <input id="password" type="password" name="mot-de-passe" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,20}$" required/>
        </div>
        <div class="access-container">
            <label for="mail">Adresse mail</label>
            <input id="mail" type="email" value="<?= $adresseMail ?? "" ?>" name="email" required/>
        </div>
        <div class="access-container">
            <label for="profile-pic">Photo de profil</label>
            <input required type="file" id="profile-pic" name="donnees-photo-de-profil" accept="image/png, image/jpeg">
        </div>
        <input id="access-submit" type="submit" value="S'inscrire">
    </fieldset>
    </form>
</main>