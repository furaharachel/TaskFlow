<?php

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Categorie.php';
require_once __DIR__ . '/../App/Repository/CategorieRepository.php';

$database = new Database();
$repository = new CategorieRepository($database);

$categories = array();
$erreur = '';

try {

    $categories = $repository->trouverTous();

} catch (PDOException $exception) {

    $erreur = 'Impossible de charger les catégories.';
}

?>
<?php
$titrePage = 'Catégories';
require_once __DIR__ . '/../Templates/header.php';
?>

<section class="page">

    <div class="conteneur">

        <h1 class="titre-page">
            Catégories
        </h1>

        <p class="sous-titre">
            Organisez vos tâches par domaine.
        </p>

        <?php if ($erreur !== ''): ?>

            <div class="message">

                <?php
                echo htmlspecialchars(
                    $erreur,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php endif; ?>

        <?php if (empty($categories)): ?>

            <section class="carte">

                <h2>
                    Aucune catégorie
                </h2>

                <p>
                    Créez votre première catégorie pour organiser
                    vos tâches.
                </p>

                <br>

                <a
                    href="creer-categorie.php"
                    class="bouton bouton-principal"
                >
                    Créer une catégorie
                </a>

            </section>

        <?php else: ?>

            <?php foreach ($categories as $categorie): ?>

                <article class="carte">

                    <h2>

                        <?php
                        echo htmlspecialchars(
                            $categorie->getNom(),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>

                    </h2>

                    <?php if ($categorie->getDescription() !== ''): ?>

                        <p>

                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $categorie->getDescription(),
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            );
                            ?>

                        </p>

                    <?php endif; ?>

                    <div>

                        <a
                            href="modifier-categorie.php?id=<?php echo (int) $categorie->getId(); ?>"
                            class="bouton bouton-secondaire"
                        >
                            Modifier
                        </a>

                        <a
                            href="supprimer-categorie.php?id=<?php echo (int) $categorie->getId(); ?>"
                            class="bouton bouton-secondaire"
                        >
                            Supprimer
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</section>

<?php require_once __DIR__ . '/../Templates/footer.php'; ?>

</html>