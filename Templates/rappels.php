<?php
$titrePage = 'Rappels';
require_once __DIR__ . '/header.php';
?>

<section class="page">
<div class="conteneur">

    <div class="page-entete">

        <div>

            <span class="sur-titre">
                Gestion
            </span>

            <h1>
                Rappels
            </h1>

            <p>
                Tâche :
                <?php echo htmlspecialchars(
                    $tache->getTitre(),
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </p>

        </div>

        <a
            href="/TaskFlow/Web/taches.php"
            class="bouton bouton-secondaire"
        >
            Retour aux tâches
        </a>

    </div>


    <section class="carte">

        <h2>
            Ajouter un rappel
        </h2>

        <form
            method="post"
            action="/TaskFlow/Web/rappels.php?id=<?php echo (int) $tacheId; ?>"
        >

            <div class="champ">

                <label for="date_rappel">
                    Date et heure du rappel
                </label>

                <input
                    type="datetime-local"
                    id="date_rappel"
                    name="date_rappel"
                    value="<?php echo htmlspecialchars(
                        $dateRappel,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>"
                >

                <?php if ($erreurDateRappel !== ''): ?>

                    <p class="erreur">
                        <?php echo htmlspecialchars(
                            $erreurDateRappel,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </p>

                <?php endif; ?>

            </div>


            <div class="champ">

                <label for="message">
                    Message
                </label>

                <input
                    type="text"
                    id="message"
                    name="message"
                    maxlength="255"
                    value="<?php echo htmlspecialchars(
                        $messageRappel,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>"
                >

                <?php if ($erreurMessageRappel !== ''): ?>

                    <p class="erreur">
                        <?php echo htmlspecialchars(
                            $erreurMessageRappel,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </p>

                <?php endif; ?>

            </div>


            <button
                type="submit"
                class="bouton bouton-principal"
            >
                Ajouter le rappel
            </button>

        </form>

    </section>


    <section class="liste-taches">

        <div class="section-entete">

            <h2>
                Mes rappels
            </h2>

            <p>
                <?php echo count($rappels); ?> rappel(s).
            </p>

        </div>


        <?php if (empty($rappels)): ?>

            <article class="carte vide">

                <h2>
                    Aucun rappel
                </h2>

                <p>
                    Aucun rappel n'a encore été créé pour cette tâche.
                </p>

            </article>

        <?php else: ?>


            <?php foreach ($rappels as $rappel): ?>

                <article class="carte tache">

                    <div class="tache-entete">

                        <div>

                            <h3>
                                <?php echo htmlspecialchars(
                                    $rappel->getMessage(),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </h3>

                            <p>
                                <?php echo htmlspecialchars(
                                    $rappel->getDateRappel(),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </p>

                        </div>


                        <span class="badge">

                            <?php
                            if (
                                $rappel->getStatut() === 'actif'
                            ) {
                                echo 'Actif';
                            } else {
                                echo 'Terminé';
                            }
                            ?>

                        </span>

                    </div>


                    <div class="tache-actions">

                        <a
                            href="/TaskFlow/Web/modifier-rappel.php?id=<?php echo (int) $rappel->getId(); ?>"
                            class="bouton bouton-secondaire"
                        >
                            Modifier
                        </a>

                        <a
                            href="/TaskFlow/Web/supprimer-rappel.php?id=<?php echo (int) $rappel->getId(); ?>"
                            class="bouton bouton-danger"
                        >
                            Supprimer
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>


        <?php endif; ?>

    </section>

</div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>