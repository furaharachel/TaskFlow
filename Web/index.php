<?php

session_start();

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Tache.php';
require_once __DIR__ . '/../App/Repository/TacheRepository.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: /TaskFlow/Web/connexion.php');
    exit;
}

$utilisateurId = (int) $_SESSION['utilisateur_id'];

$prenom = isset($_SESSION['utilisateur_prenom'])
    ? $_SESSION['utilisateur_prenom']
    : '';

$totalTaches = 0;
$tachesAFaire = 0;
$tachesEnCours = 0;
$tachesTerminees = 0;
$taches = array();
$message = '';

try {
    $database = new Database();
    $tacheRepository = new TacheRepository($database);

    $totalTaches = $tacheRepository->compterParUtilisateur(
        $utilisateurId
    );

    $tachesAFaire = $tacheRepository->compterParUtilisateurEtStatut(
        $utilisateurId,
        'a_faire'
    );

    $tachesEnCours = $tacheRepository->compterParUtilisateurEtStatut(
        $utilisateurId,
        'en_cours'
    );

    $tachesTerminees = $tacheRepository->compterParUtilisateurEtStatut(
        $utilisateurId,
        'terminee'
    );

    $taches = $tacheRepository->trouverParUtilisateur(
        $utilisateurId
    );

} catch (PDOException $exception) {
    $message = 'Impossible de charger les données du tableau de bord.';
}

function e($valeur)
{
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}

function afficherStatut($statut)
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

function afficherPriorite($priorite)
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

$titrePage = 'Tableau de bord';

require_once __DIR__ . '/../Templates/header.php';

?>

<main class="page">
    <div class="conteneur">

        <section class="introduction">
            <div>
                <p class="sur-titre">VOTRE ESPACE DE TRAVAIL</p>

                <h1 class="titre-page">
                    Bonjour<?php echo $prenom !== '' ? ', ' . e($prenom) : ''; ?> !
                </h1>

                <p class="sous-titre">
                    Retrouvez vos priorités et suivez votre progression.
                </p>
            </div>

            <div class="introduction-action">
                <a href="/TaskFlow/Web/creer-tache.php"
                   class="bouton bouton-principal">
                    + Nouvelle tâche
                </a>
            </div>
        </section>

        <?php if ($message !== ''): ?>
            <div class="message message-erreur">
                <?php echo e($message); ?>
            </div>
        <?php endif; ?>

        <section class="statistiques" aria-label="Résumé des tâches">

            <article class="statistique statistique-total">
                <div class="statistique-icone">T</div>
                <p class="statistique-label">Toutes les tâches</p>
                <p class="statistique-valeur">
                    <?php echo (int) $totalTaches; ?>
                </p>
                <p class="statistique-description">Tâches enregistrées</p>
            </article>

            <article class="statistique statistique-a-faire">
                <div class="statistique-icone">A</div>
                <p class="statistique-label">À faire</p>
                <p class="statistique-valeur">
                    <?php echo (int) $tachesAFaire; ?>
                </p>
                <p class="statistique-description">En attente</p>
            </article>

            <article class="statistique statistique-en-cours">
                <div class="statistique-icone">C</div>
                <p class="statistique-label">En cours</p>
                <p class="statistique-valeur">
                    <?php echo (int) $tachesEnCours; ?>
                </p>
                <p class="statistique-description">En progression</p>
            </article>

            <article class="statistique statistique-terminees">
                <div class="statistique-icone">✓</div>
                <p class="statistique-label">Terminées</p>
                <p class="statistique-valeur">
                    <?php echo (int) $tachesTerminees; ?>
                </p>
                <p class="statistique-description">Travail accompli</p>
            </article>

        </section>

        <section class="section">

            <div class="section-entete">
                <div>
                    <h2>Mes tâches</h2>
                    <p>Un aperçu de vos tâches enregistrées.</p>
                </div>

                <a href="/TaskFlow/Web/taches.php" class="lien">
                    Voir toutes les tâches →
                </a>
            </div>

            <?php if (empty($taches)): ?>

                <div class="carte vide">
                    <h3>Aucune tâche pour le moment</h3>

                    <p>
                        Créez votre première tâche pour commencer à organiser
                        votre journée.
                    </p>

                    <a href="/TaskFlow/Web/creer-tache.php"
                       class="bouton bouton-principal">
                        Créer ma première tâche
                    </a>
                </div>

            <?php else: ?>

                <div class="liste-taches">

                    <?php foreach ($taches as $tache): ?>

                        <article class="tache">

                            <div class="tache-contenu">
                                <h3>
                                    <?php echo e($tache->getTitre()); ?>
                                </h3>

                                <?php if ($tache->getDescription() !== ''): ?>
                                    <p class="tache-description">
                                        <?php echo e($tache->getDescription()); ?>
                                    </p>
                                <?php endif; ?>

                                <div class="tache-informations">

                                    <span>
                                        Catégorie :
                                        <strong>
                                            <?php
                                            echo e(
                                                $tache->getCategorieNom() !== null
                                                    ? $tache->getCategorieNom()
                                                    : 'Sans catégorie'
                                            );
                                            ?>
                                        </strong>
                                    </span>

                                    <span>
                                        Statut :
                                        <strong>
                                            <?php
                                            echo e(
                                                afficherStatut(
                                                    $tache->getStatut()
                                                )
                                            );
                                            ?>
                                        </strong>
                                    </span>

                                    <span>
                                        Priorité :
                                        <strong>
                                            <?php
                                            echo e(
                                                afficherPriorite(
                                                    $tache->getPriorite()
                                                )
                                            );
                                            ?>
                                        </strong>
                                    </span>

                                    <?php if ($tache->getDateEcheance() !== null): ?>
                                        <span>
                                            Échéance :
                                            <strong>
                                                <?php echo e($tache->getDateEcheance()); ?>
                                            </strong>
                                        </span>
                                    <?php endif; ?>

                                </div>
                            </div>

                            <div class="tache-actions">
                                <a
                                    href="/TaskFlow/Web/modifier-tache.php?id=<?php echo (int) $tache->getId(); ?>"
                                    class="bouton bouton-secondaire">
                                    Modifier
                                </a>
                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </div>
</main>

<?php require_once __DIR__ . '/../Templates/footer.php'; ?>