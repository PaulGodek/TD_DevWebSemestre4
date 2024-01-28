<main>
    <form action="controleurFrontal.php?controleur=utilisateur&action=connecter" id="form-access" class="center" method="post">
    <fieldset>
        <legend>Connexion</legend>
        <div class="access-container">
            <label for="login">Login</label>
            <input id="login" type="text" name="login" required/>
        </div>
        <div class="access-container">
            <label for="password">Mot de passe</label>
            <input id="password" type="password" name="mot-de-passe" required/>
        </div>
        <input id="access-submit" type="submit" value="Se connecter">
    </fieldset>
    </form>
</main>