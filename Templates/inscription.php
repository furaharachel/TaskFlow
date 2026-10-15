<?php

$nom = isset($nom) ? $nom : '';
$prenom = isset($prenom) ? $prenom : '';
$email = isset($email) ? $email : '';
$erreur = isset($erreur) ? $erreur : '';

function inscriptionE($valeur)
{
    return htmlspecialchars(
        (string) $valeur,
        ENT_QUOTES,
        'UTF-8'
    );
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Créer un compte | TaskFlow</title>

    <link
        rel="stylesheet"
        href="/TaskFlow/Assets/style.css"
    >

    <style>
        .inscription-actions {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 24px;
        }

        .inscription-actions button {
            display: block;
            width: 100%;
            min-height: 48px;
            border: none;
            cursor: pointer;
        }

        .inscription-retour {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>

<main class="page">
    <div class="conteneur">

        <section class="carte">

            <a href="/TaskFlow/Web/connexion.php" class="logo">
                TaskFlow
            </a>

            <h1 class="titre-page">Créer un compte</h1>

            <p class="sous-titre">
                Créez votre espace personnel TaskFlow.
            </p>

            <?php if ($erreur !== ''): ?>
                <div class="message message-erreur" role="alert">
                    <?php echo inscriptionE($erreur); ?>
                </div>
            <?php endif; ?>

            <form
                method="post"
                action="/TaskFlow/Web/inscription.php"
            >

                <div>
                    <label for="prenom">Prénom</label>

                    <input
                        type="text"
                        id="prenom"
                        name="prenom"
                        value="<?php echo inscriptionE($prenom); ?>"
                        maxlength="100"
                        autocomplete="given-name"
                        required
                    >
                </div>

                <div>
                    <label for="nom">Nom</label>

                    <input
                        type="text"
                        id="nom"
                        name="nom"
                        value="<?php echo inscriptionE($nom); ?>"
                        maxlength="100"
                        autocomplete="family-name"
                        required
                    >
                </div>

                <div>
                    <label for="email">Adresse e-mail</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo inscriptionE($email); ?>"
                        maxlength="150"
                        autocomplete="email"
                        required
                    >
                </div>

                <div>
                    <label for="mot_de_passe">Mot de passe</label>

                    <input
                        type="password"
                        id="mot_de_passe"
                        name="mot_de_passe"
                        minlength="6"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div>
                    <label for="confirmation">
                        Confirmer le mot de passe
                    </label>

                    <input
                        type="password"
                        id="confirmation"
                        name="confirmation"
                        minlength="6"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div class="inscription-actions">
                    <button
                        type="submit"
                        class="bouton bouton-principal"
                    >
                        Créer mon compte
                    </button>
                </div>

            </form>

            <p class="inscription-retour">
                Vous avez déjà un compte ?

                <a
                    href="/TaskFlow/Web/connexion.php"
                    class="lien"
                >
                    Se connecter
                </a>
            </p>

        </section>

    </div>
</main>

</body>
</html>