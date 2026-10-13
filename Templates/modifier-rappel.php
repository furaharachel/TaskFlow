<?php

require_once __DIR__ . '/header.php';

function modifierRappelE($valeur)
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

        <div class="page-entete">
            <div>
                <span class="sur-titre">Gestion</span>

                <h1>Modifier un rappel</h1>

                <p>
                    Tâche :
                    <?php
                    echo modifierRappelE($tache->getTitre());
                    ?>
                </p>
            </div>

            <a
                href="/TaskFlow/Web/rappels.php?id=<?php echo (int) $tacheId; ?>"
                class="bouton bouton-secondaire"
            >
                Retour aux rappels
            </a>
        </div>

        <?php if ($message !== ''): ?>
            <div class="message">
                <?php echo modifierRappelE($message); ?>
            </div>
        <?php endif; ?>

        <section class="carte">
            <h2>Informations du rappel</h2>

            <form
                method="post"
                action="/TaskFlow/Web/modifier-rappel.php?id=<?php echo (int) $id; ?>"
            >

                <div class="champ">
                    <label for="date_rappel">Date et heure</label>

                    <input
                        type="datetime-local"
                        id="date_rappel"
                        name="date_rappel"
                        value="<?php echo modifierRappelE($dateRappel); ?>"
                        required
                    >

                    <?php if ($erreurDateRappel !== ''): ?>
                        <p class="erreur">
                            <?php
                            echo modifierRappelE($erreurDateRappel);
                            ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="champ">
                    <label for="message">Message</label>

                    <input
                        type="text"
                        id="message"
                        name="message"
                        maxlength="255"
                        value="<?php echo modifierRappelE($messageRappel); ?>"
                        required
                    >

                    <?php if ($erreurMessageRappel !== ''): ?>
                        <p class="erreur">
                            <?php
                            echo modifierRappelE($erreurMessageRappel);
                            ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="champ">
                    <label for="statut">Statut</label>

                    <select id="statut" name="statut" required>
                        <option
                            value="actif"
                            <?php echo $statut === 'actif' ? 'selected' : ''; ?>
                        >
                            Actif
                        </option>

                        <option
                            value="termine"
                            <?php echo $statut === 'termine' ? 'selected' : ''; ?>
                        >
                            Terminé
                        </option>
                    </select>

                    <?php if ($erreurStatut !== ''): ?>
                        <p class="erreur">
                            <?php echo modifierRappelE($erreurStatut); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div>
                    <button
                        type="submit"
                        class="bouton bouton-principal"
                    >
                        Enregistrer les modifications
                    </button>

                    <a
                        href="/TaskFlow/Web/rappels.php?id=<?php echo (int) $tacheId; ?>"
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