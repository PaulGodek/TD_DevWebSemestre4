<?php
/**
 * @var string $login
 * @var string $adresseMail
 */
?>
<main>
    <form action="controleurFrontal.php?controleur=utilisateur&action=creerDepuisFormulaire" id="form-access" class="center" method="post" enctype="multipart/form-data">
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
            <input id="password" type="password" name="password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,20}$" required/>
        </div>
        <div class="access-container">
            <label for="mail">Adresse mail</label>
            <input id="mail" type="email" value="<?= $adresseMail ?? "" ?>" name="adresseMail" required/>
        </div>
        <div class="access-container">
            <label for="profile-pic">Photo de profil</label>
            <input required type="file" id="profile-pic" name="profilePicture" accept="image/png, image/jpeg">
        </div>
        <input type="hidden" name="XDEBUG_TRIGGER">
        <input id="access-submit" type="submit" value="S'inscrire">
    </fieldset>
    </form>
</main>