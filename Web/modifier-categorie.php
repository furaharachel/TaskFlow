<?php

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Categorie.php';
require_once __DIR__ . '/../App/CategorieValidator.php';
require_once __DIR__ . '/../App/Repository/CategorieRepository.php';

$database = new Database();

$categorieRepository = new CategorieRepository($database);
$validator = new CategorieValidator();

$erreurs = array();
$message = '';

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

$categorie = $categorieRepository->trouverParId($id);

if ($categorie === null) {
    header('Location: categories.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = isset($_POST['nom'])
        ? trim($_POST['nom'])
        : '';

    $description = isset($_POST['description'])
        ? trim($_POST['description'])
        : '';

    $categorie->setNom($nom);
    $categorie->setDescription($description);

    if ($validator->valider($categorie)) {

        try {

            $categorieRepository->modifier($categorie);

            header('Location: categories.php');
            exit;

        } catch (PDOException $exception) {

            $message = 'Impossible de modifier la catégorie.';
        }

    } else {

        $erreurs = $validator->getErreurs();
    }
}

?>
<?php
$titrePage = 'Modifier une catégorie';
require_once __DIR__ . '/../Templates/header.php';
?>

<section class="page">

    <div class="conteneur">

        <h1 class="titre-page">
            Modifier la catégorie
        </h1>

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

                    <label for="nom">
                        Nom de la catégorie
                    </label>

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

                    <label for="description">
                        Description
                    </label>

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

<?php require_once __DIR__ . '/../Templates/footer.php'; ?>