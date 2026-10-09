<?php
$titrePage = 'Mes tâches';
require_once __DIR__ . '/header.php';

function taskflowE($valeur)
{
    return htmlspecialchars(
        (string) $valeur,
        ENT_QUOTES,
        'UTF-8'
    );
}

function taskflowStatut($statut)
{
    if ($statut === 'a_faire') {
        return 'À faire';
    }

    if ($statut === 'en_cours') {
        return 'En cours';
    }

    if ($statut === 'terminee') {
        return 'Terminée';
    }

    return 'Non défini';
}

function taskflowClasseStatut($statut)
{
    if ($statut === 'terminee') {
        return 'statut statut-termine';
    }

    if ($statut === 'en_cours') {
        return 'statut statut-en-cours';
    }

    return 'statut statut-a-faire';
}

function taskflowPriorite($priorite)
{
    if ($priorite === 'basse') {
        return 'Basse';
    }

    if ($priorite === 'normale') {
        return 'Normale';
    }

    if ($priorite === 'haute') {
        return 'Haute';
    }

    if ($priorite === 'urgente') {
        return 'Urgente';
    }

    return 'Non définie';
}

function taskflowClassePriorite($priorite)
{
    if ($priorite === 'urgente' || $priorite === 'haute') {
        return 'priorite priorite-haute';
    }

    if ($priorite === 'basse') {
        return 'priorite priorite-basse';
    }

    return 'priorite priorite-normale';
}
?>

<main class="page">
    <div class="conteneur">

        <section class="introduction">
            <div>
                <p class="sur-titre">ORGANISATION</p>

                <h1 class="titre-page">Mes tâches</h1>

                <p class="sous-titre">
                    Organisez vos activités, suivez votre progression
                    et gardez le contrôle de vos priorités.
                </p>
            </div>

            <div class="introduction-action">
                <a
                    href="/TaskFlow/Web/creer-tache.php"
                    class="bouton bouton-principal"
                >
                    + Nouvelle tâche
                </a>
            </div>
        </section>

        <section class="section">

            <div class="section-entete">
                <div>
                    <h2>Toutes mes tâches</h2>

                    <p>
                        <?php echo count($taches); ?>
                        tâche(s) enregistrée(s)
                    </p>
                </div>
            </div>

            <?php if (empty($taches)): ?>

                <div class="carte vide">
                    <div class="vide-icone">+</div>

                    <h3>Aucune tâche pour le moment</h3>

                    <p>
                        Créez votre première tâche pour commencer
                        à organiser votre travail.
                    </p>

                    <a
                        href="/TaskFlow/Web/creer-tache.php"
                        class="bouton bouton-principal"
                    >
                        Créer ma première tâche
                    </a>
                </div>

            <?php else: ?>

                <div class="grille-taches">

                    <?php foreach ($taches as $tache): ?>

                        <article class="carte carte-tache">

                            <div class="carte-tache-entete">
                                <span class="<?php
                                    echo taskflowClasseStatut(
                                        $tache->getStatut()
                                    );
                                ?>">
                                    <?php
                                    echo taskflowE(
                                        taskflowStatut(
                                            $tache->getStatut()
                                        )
                                    );
                                    ?>
                                </span>

                                <span class="<?php
                                    echo taskflowClassePriorite(
                                        $tache->getPriorite()
                                    );
                                ?>">
                                    <?php
                                    echo 'Priorité : '
                                        . taskflowE(
                                            taskflowPriorite(
                                                $tache->getPriorite()
                                            )
                                        );
                                    ?>
                                </span>
                            </div>

                            <h2 class="tache-titre">
                                <?php
                                echo taskflowE($tache->getTitre());
                                ?>
                            </h2>

                            <?php if ($tache->getDescription() !== ''): ?>
                                <p class="tache-description">
                                    <?php
                                    echo nl2br(
                                        taskflowE(
                                            $tache->getDescription()
                                        )
                                    );
                                    ?>
                                </p>
                            <?php else: ?>
                                <p class="tache-description tache-sans-description">
                                    Aucune description.
                                </p>
                            <?php endif; ?>

                            <div class="tache-details">

                                <div class="tache-detail">
                                    <span class="tache-detail-label">
                                        Catégorie
                                    </span>

                                    <strong>
                                        <?php
                                        echo $tache->getCategorieNom() !== null
                                            ? taskflowE(
                                                $tache->getCategorieNom()
                                            )
                                            : 'Sans catégorie';
                                        ?>
                                    </strong>
                                </div>

                                <?php if ($tache->getDateEcheance() !== null): ?>
                                    <div class="tache-detail">
                                        <span class="tache-detail-label">
                                            Échéance
                                        </span>

                                        <strong>
                                            <?php
                                            echo taskflowE(
                                                $tache->getDateEcheance()
                                            );
                                            ?>
                                        </strong>
                                    </div>
                                <?php endif; ?>

                                <?php if ($tache->getDureeEstimee() !== null): ?>
                                    <div class="tache-detail">
                                        <span class="tache-detail-label">
                                            Durée estimée
                                        </span>

                                        <strong>
                                            <?php
                                            echo (int) $tache->getDureeEstimee();
                                            ?>
                                            minutes
                                        </strong>
                                    </div>
                                <?php endif; ?>

                            </div>

                            <?php if ($tache->getNombreSousTaches() > 0): ?>
                                <div class="progression-tache">
                                    <div class="progression-entete">
                                        <span>Progression</span>

                                        <strong>
                                            <?php
                                            echo (int) $tache->getProgression();
                                            ?> %
                                        </strong>
                                    </div>

                                    <div class="progression-barre">
                                        <div
                                            class="progression-valeur"
                                            style="width: <?php
                                                echo max(
                                                    0,
                                                    min(
                                                        100,
                                                        (int) $tache->getProgression()
                                                    )
                                                );
                                            ?>%;"
                                        ></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="actions actions-tache">

                                <a
                                    href="/TaskFlow/Web/modifier-tache.php?id=<?php echo (int) $tache->getId(); ?>"
                                    class="bouton bouton-secondaire bouton-petit"
                                >
                                    Modifier
                                </a>

                                <a
                                    href="/TaskFlow/Web/sous-taches.php?id=<?php echo (int) $tache->getId(); ?>"
                                    class="bouton bouton-secondaire bouton-petit"
                                >
                                    Sous-tâches
                                </a>

                                <a
                                    href="/TaskFlow/Web/rappels.php?id=<?php echo (int) $tache->getId(); ?>"
                                    class="bouton bouton-secondaire bouton-petit"
                                >
                                    Rappels
                                </a>

                                <a
                                    href="/TaskFlow/Web/supprimer-tache.php?id=<?php echo (int) $tache->getId(); ?>"
                                    class="bouton bouton-danger bouton-petit"
                                >
                                    Supprimer
                                </a>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>