<?php require_once __DIR__ . '/header.php'; ?>

<section class="page">
    <div class="conteneur">

        <h1 class="titre-page">Modifier la catégorie</h1>

        <p class="sous-titre">
            Modifiez les informations de votre catégorie.
        </p>

        <?php if ($message !== ''): ?>
            <div class="message">
                <?php
                echo htmlspecialchars(
                    $message,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
            </div>
        <?php endif; ?>

        <section class="carte">
            <form
                method="post"
                action="modifier-categorie.php?id=<?php echo (int) $id; ?>"
            >

                <div>
                    <label for="nom">Nom de la catégorie</label>

                    <input
                        type="text"
                        id="nom"
                        name="nom"
                        maxlength="100"
                        value="<?php
                        echo htmlspecialchars(
                            $categorie->getNom(),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>"
                        required
                    >

                    <?php if (isset($erreurs['nom'])): ?>
                        <p>
                            <?php
                            echo htmlspecialchars(
                                $erreurs['nom'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="description">Description</label>

                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                    ><?php
                    echo htmlspecialchars(
                        $categorie->getDescription(),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?></textarea>
                </div>

                <div>
                    <button
                        type="submit"
                        class="bouton bouton-principal"
                    >
                        Enregistrer les modifications
                    </button>

                    <a
                        href="categories.php"
                        class="bouton bouton-secondaire"
                    >
                        Annuler
                    </a>
                </div>

            </form>
        </section>

    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>