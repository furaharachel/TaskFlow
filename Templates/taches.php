<?php

require_once __DIR__ . '/header.php';

/*
 * Fonctions de présentation des tâches.
 */

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
    if ($priorite === 'urgente') {
        return 'priorite priorite-urgente';
    }

    if ($priorite === 'haute') {
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

        <section class="introduction introduction-taches">
            <div>
                <p class="sur-titre">ESPACE DE TRAVAIL</p>

                <h1 class="titre-page">Mes tâches</h1>

                <p class="sous-titre">
                    Organisez vos activités, définissez vos priorités
                    et suivez votre progression au quotidien.
                </p>
            </div>

            <div class="introduction-action">
                <a
                    href="/TaskFlow/Web/creer-tache.php"
                    class="bouton bouton-principal"
                >
                    <span class="bouton-symbole">+</span>
                    Nouvelle tâche
                </a>
            </div>
        </section>

        <section
            class="resume-taches"
            aria-label="Résumé de mes tâches"
        >

            <article class="resume-tache resume-tache-total">
                <div class="resume-tache-entete">
                    <span class="resume-tache-icone">T</span>
                    <span class="resume-tache-label">Total</span>
                </div>

                <p class="resume-tache-valeur">
                    <?php echo (int) $totalTaches; ?>
                </p>

                <p class="resume-tache-description">
                    Tâche(s) enregistrée(s)
                </p>
            </article>

            <article class="resume-tache resume-tache-attente">
                <div class="resume-tache-entete">
                    <span class="resume-tache-icone">○</span>
                    <span class="resume-tache-label">À faire</span>
                </div>

                <p class="resume-tache-valeur">
                    <?php echo (int) $nombreAFaire; ?>
                </p>

                <p class="resume-tache-description">
                    À commencer
                </p>
            </article>

            <article class="resume-tache resume-tache-cours">
                <div class="resume-tache-entete">
                    <span class="resume-tache-icone">↗</span>
                    <span class="resume-tache-label">En cours</span>
                </div>

                <p class="resume-tache-valeur">
                    <?php echo (int) $nombreEnCours; ?>
                </p>

                <p class="resume-tache-description">
                    En cours de réalisation
                </p>
            </article>

            <article class="resume-tache resume-tache-termine">
                <div class="resume-tache-entete">
                    <span class="resume-tache-icone">✓</span>
                    <span class="resume-tache-label">Terminées</span>
                </div>

                <p class="resume-tache-valeur">
                    <?php echo (int) $nombreTerminees; ?>
                </p>

                <p class="resume-tache-description">
                    Tâches accomplies
                </p>
            </article>

        </section>

        <section class="section section-liste-taches">

            <div class="section-entete section-entete-taches">
                <div>
                    <h2>Liste de mes tâches</h2>

                    <p>
                        Consultez les détails et gérez vos activités.
                    </p>
                </div>

                <span class="compteur-taches">
                    <?php echo (int) $totalTaches; ?>
                    <?php echo $totalTaches > 1 ? 'tâches' : 'tâche'; ?>
                </span>
            </div>

            <?php if (empty($taches)): ?>

                <div class="carte vide vide-taches">
                    <div class="vide-icone">+</div>

                    <h3>Votre espace est prêt</h3>

                    <p>
                        Vous n'avez pas encore créé de tâche.
                        Ajoutez-en une pour commencer à organiser
                        vos journées.
                    </p>

                    <a
                        href="/TaskFlow/Web/creer-tache.php"
                        class="bouton bouton-principal"
                    >
                        Créer ma première tâche
                    </a>
                </div>

            <?php else: ?>

                <div class="grille-taches grille-taches-moderne">

                    <?php foreach ($taches as $tache): ?>

                        <article class="carte carte-tache carte-tache-moderne">

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
                                    Priorité :
                                    <?php
                                    echo taskflowE(
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
                                    Aucune description renseignée.
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
                                            && $tache->getCategorieNom() !== ''
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
                                            min
                                        </strong>
                                    </div>

                                <?php endif; ?>

                            </div>

                            <?php if ($tache->getNombreSousTaches() > 0): ?>

                                <div class="progression-tache">
                                    <div class="progression-entete">
                                        <span>Progression des sous-tâches</span>

                                        <strong>
                                            <?php
                                            echo max(
                                                0,
                                                min(
                                                    100,
                                                    (int) $tache->getProgression()
                                                )
                                            );
                                            ?> %
                                        </strong>
                                    </div>

                                    <div
                                        class="progression-barre"
                                        role="progressbar"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                        aria-valuenow="<?php
                                            echo max(
                                                0,
                                                min(
                                                    100,
                                                    (int) $tache->getProgression()
                                                )
                                            );
                                        ?>"
                                    >
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

                            <div class="actions actions-tache actions-tache-modernes">

                                <a
                                    href="/TaskFlow/Web/modifier-tache.php?id=<?php
                                        echo (int) $tache->getId();
                                    ?>"
                                    class="bouton bouton-secondaire bouton-petit"
                                >
                                    Modifier
                                </a>

                                <a
                                    href="/TaskFlow/Web/sous-taches.php?id=<?php
                                        echo (int) $tache->getId();
                                    ?>"
                                    class="bouton bouton-secondaire bouton-petit"
                                >
                                    Sous-tâches
                                </a>

                                <a
                                    href="/TaskFlow/Web/rappels.php?id=<?php
                                        echo (int) $tache->getId();
                                    ?>"
                                    class="bouton bouton-secondaire bouton-petit"
                                >
                                    Rappels
                                </a>

                                <a
                                    href="/TaskFlow/Web/supprimer-tache.php?id=<?php
                                        echo (int) $tache->getId();
                                    ?>"
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