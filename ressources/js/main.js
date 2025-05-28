/**
* @param {HTMLElement} button La balise <button> cliquée
*/
function supprimerPublication(button) {
    let idPublication = button.dataset.idPublication;
    let URL = apiBase + "publications/" + idPublication;

    fetch(URL, {method: "DELETE"})
        .then(response => {
            if (response.status === 204) {
                // Plus proche ancêtre <div class="feedy">
                let divFeedy = button.closest("div.feedy");
                divFeedy.remove();
            }
        });
}

for (let button of document.querySelectorAll('button.delete-feedy')) {
    button.addEventListener('click', (e) => {
        supprimerPublication(e.target);
    });
}

function templatePublication(publication, utilisateur) {
    if ("content" in document.createElement("template")) {

        let feed = document.getElementById('new-feed');
        let newFeed = feed.content.cloneNode(true);

        let a = newFeed.querySelector('#feed-lien-auteur');
        a.href = `${pagePersoBase + publication.auteur.idUtilisateur}`;

        let img = newFeed.querySelector('#feed-lien-auteur > img');
        img.src = `${imgBase}/utilisateurs/${utilisateur.nomPhotoDeProfil}`;

        let login = newFeed.querySelector('#feed-login-utilisateur');
        login.textContent = `${utilisateur.login}`;

        let date = newFeed.querySelector('#feed-date-publication');
        date.textContent = `${publication.date}`;

        let message = newFeed.querySelector('#feed-message');
        message.textContent = `${publication.message}`;

        let button = newFeed.querySelector('#feed-bouton-suppression');
        button.dataset.idPublication = `${publication.idPublication}`;
        button.addEventListener('click', (e) => {
            supprimerPublication(e.target);
        });

        return newFeed.firstElementChild;
    } else {
        let elem = document.createElement('p');
        elem.textContent = "Houston, we have a problem";
        return elem;
    }
}

async function soumettrePublication() {
    const messageElement = document.getElementById('message')
    // On récupère le message
    let message = messageElement.value;
    // On vide le formulaire
    messageElement.value = "";
    // On utilise la variable globale apiBase définie dans base.html.twig
    let URL = apiBase + "publications";

    let response = await fetch(URL, {
        method: 'POST',
        body: JSON.stringify({message: message}),
        headers: {
            'Accept': 'application/json',
            'Content-type': 'application/json; charset=UTF-8',
        },
    });
    if (response.status !== 201)
        // (Hors TD) Il faudrait traiter l'erreur
        return;
    let publication = await response.json();

    // Récupérer utilisateur
    let URL2 = apiBase + "utilisateurs/" + publication.auteur.idUtilisateur;
    let response2 = await fetch(URL2, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-type': 'application/json; charset=UTF-8',
        },
    });
    if (response2.status !== 201)
        // (Hors TD) Il faudrait traiter l'erreur
        return;
    let utilisateur = await response.json();


    // PAS FINITO



    let formElement = document.getElementById("feedy-new");
    formElement.insertAdjacentElement('afterend', templatePublication(publication, utilisateur));
}

document.getElementById('feedy-new-submit').addEventListener('click', (e) => {
    soumettrePublication();
});