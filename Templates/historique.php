<?php
$titrePage = 'Historique';
require_once __DIR__ . '/header.php';
?>

<div class="conteneur">

    <div class="titre-page">
        <h1>Historique</h1>
        <p>Retrouvez les actions effectuées dans TaskFlow.</p>
    </div>

    <?php if (empty($historiques)): ?>

        <div class="carte">
            <p>Aucune activité enregistrée.</p>
        </div>

    <?php else: ?>

        <div class="grille">

            <?php foreach ($historiques as $historique): ?>

                <article class="carte">

                    <h2>
                        <?php
                        echo htmlspecialchars(
                            $historique->getAction(),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </h2>

                    <p>
                        <?php
                        echo htmlspecialchars(
                            $historique->getDescription(),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </p>

                    <?php if ($historique->getTacheTitre() !== null): ?>

                        <p>
                            <strong>Tâche :</strong>
                            <?php
                            echo htmlspecialchars(
                                $historique->getTacheTitre(),
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </p>

                    <?php endif; ?>

                    <p>
                        <strong>Date :</strong>
                        <?php
                        echo htmlspecialchars(
                            $historique->getDateAction(),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </p>

                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<?php
require_once __DIR__ . '/footer.php';
?>