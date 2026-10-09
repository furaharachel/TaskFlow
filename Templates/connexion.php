<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Connexion — TaskFlow</title>

    <link
        rel="stylesheet"
        href="/TaskFlow/Assets/css/style.css"
    >
</head>
<body>

<main class="page">
    <div class="conteneur">

        <section class="carte">

            <div>
                <a
                    href="/TaskFlow/Web/connexion.php"
                    class="logo"
                >
                    TaskFlow
                </a>

                <h1 class="titre-page">Connexion</h1>

                <p class="sous-titre">
                    Connectez-vous à votre espace TaskFlow.
                </p>
            </div>

            <?php if ($erreur !== ''): ?>
                <div class="message message-erreur" role="alert">
                    <?php
                    echo htmlspecialchars(
                        $erreur,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </div>
            <?php endif; ?>

            <form
                method="post"
                action="/TaskFlow/Web/connexion.php"
            >

                <div>
                    <label for="email">Adresse e-mail</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php
                        echo htmlspecialchars(
                            $email,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>"
                        maxlength="150"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="champ-mot-de-passe">
                    <label for="mot_de_passe">Mot de passe</label>

                    <input
                        type="password"
                        id="mot_de_passe"
                        name="mot_de_passe"
                        autocomplete="current-password"
                        required
                    >

                    <div class="lien-mot-de-passe-oublie">
                        <a href="/TaskFlow/Web/mot-de-passe-oublie.php">
                            Mot de passe oublié ?
                        </a>
                    </div>
                </div>

                <button
                    type="submit"
                    class="bouton bouton-principal"
                >
                    Se connecter
                </button>

            </form>

        </section>

    </div>
</main>

</body>
</html>