<?php

require_once __DIR__ . '/header.php';

function creerTacheE($valeur)
{
    return htmlspecialchars(
        (string) $valeur,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<section class="page">
    <div class="conteneur">

        <section class="introduction">
            <div>
                <p class="sur-titre">Gestion des tâches</p>

                <h1 class="titre-page">Nouvelle tâche</h1>

                <p class="sous-titre">
                    Ajoutez une nouvelle tâche à votre espace.
                </p>
            </div>
        </section>

        <?php if ($message !== ''): ?>
            <div class="message">
                <?php echo creerTacheE($message); ?>
            </div>
        <?php endif; ?>

        <section class="carte">
            <form method="post" action="/TaskFlow/Web/creer-tache.php">

                <div>
                    <label for="titre">Titre</label>

                    <input
                        type="text"
                        id="titre"
                        name="titre"
                        value="<?php echo creerTacheE($titre); ?>"
                        maxlength="150"
                        required
                    >

                    <?php if ($erreurTitre !== ''): ?>
                        <p class="message">
                            <?php echo creerTacheE($erreurTitre); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="description">Description</label>

                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                    ><?php echo creerTacheE($description); ?></textarea>
                </div>

                <div>
                    <label for="categorie_id">Catégorie</label>

                    <select id="categorie_id" name="categorie_id">
                        <option value="">Sans catégorie</option>

                        <?php foreach ($categories as $categorie): ?>
                            <option
                                value="<?php echo (int) $categorie->getId(); ?>"
                                <?php
                                if (
                                    (string) $categorieId ===
                                    (string) $categorie->getId()
                                ) {
                                    echo 'selected';
                                }
                                ?>
                            >
                                <?php echo creerTacheE($categorie->getNom()); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="statut">Statut</label>

                    <select id="statut" name="statut" required>
                        <option
                            value="a_faire"
                            <?php echo $statut === 'a_faire' ? 'selected' : ''; ?>
                        >
                            À faire
                        </option>

                        <option
                            value="en_cours"
                            <?php echo $statut === 'en_cours' ? 'selected' : ''; ?>
                        >
                            En cours
                        </option>

                        <option
                            value="terminee"
                            <?php echo $statut === 'terminee' ? 'selected' : ''; ?>
                        >
                            Terminée
                        </option>
                    </select>

                    <?php if ($erreurStatut !== ''): ?>
                        <p class="message">
                            <?php echo creerTacheE($erreurStatut); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="priorite">Priorité</label>

                    <select id="priorite" name="priorite" required>
                        <option
                            value="basse"
                            <?php echo $priorite === 'basse' ? 'selected' : ''; ?>
                        >
                            Basse
                        </option>

                        <option
                            value="normale"
                            <?php echo $priorite === 'normale' ? 'selected' : ''; ?>
                        >
                            Normale
                        </option>

                        <option
                            value="haute"
                            <?php echo $priorite === 'haute' ? 'selected' : ''; ?>
                        >
                            Haute
                        </option>

                        <option
                            value="urgente"
                            <?php echo $priorite === 'urgente' ? 'selected' : ''; ?>
                        >
                            Urgente
                        </option>
                    </select>

                    <?php if ($erreurPriorite !== ''): ?>
                        <p class="message">
                            <?php echo creerTacheE($erreurPriorite); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="date_echeance">Date d'échéance</label>

                    <input
                        type="date"
                        id="date_echeance"
                        name="date_echeance"
                        value="<?php echo creerTacheE($dateEcheance); ?>"
                    >
                </div>

                <div>
                    <label for="duree_estimee">Durée estimée</label>

                    <input
                        type="number"
                        id="duree_estimee"
                        name="duree_estimee"
                        value="<?php echo creerTacheE($dureeEstimee); ?>"
                        min="1"
                        placeholder="Ex. 30"
                    >

                    <p>Durée en minutes.</p>

                    <?php if ($erreurDuree !== ''): ?>
                        <p class="message">
                            <?php echo creerTacheE($erreurDuree); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div>
                    <button
                        type="submit"
                        class="bouton bouton-principal"
                    >
                        Créer la tâche
                    </button>

                    <a
                        href="/TaskFlow/Web/taches.php"
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