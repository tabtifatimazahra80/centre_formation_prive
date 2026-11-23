<?php
declare(strict_types=1);

/*
 * Formulaire commun « Ajouter / Modifier un étudiant ».
 * Variables attendues : $valeurs, $erreurs, $actionFormulaire, $urlAnnuler.
 */
$erreurChamp = static fn (string $champ): string => isset($erreurs[$champ])
    ? '<span class="erreur-champ">' . e($erreurs[$champ]) . '</span>'
    : '';
?>
<form class="formulaire" method="post" action="<?= e($actionFormulaire) ?>">
    <?= csrf_champ() ?>

    <?php if (isset($erreurs['general'])): ?>
        <div class="alerte alerte-erreur" role="alert"><?= e($erreurs['general']) ?></div>
    <?php endif; ?>

    <div class="grille-formulaire">
        <div class="champ">
            <label for="matricule">Matricule *</label>
            <input id="matricule" name="matricule" type="text" maxlength="50" required
                   placeholder="E0006" value="<?= e($valeurs['matricule']) ?>">
            <?= $erreurChamp('matricule') ?>
        </div>

        <div class="champ">
            <label for="email">Email *</label>
            <input id="email" name="email" type="email" maxlength="100" required
                   placeholder="nom@centre.ma" value="<?= e($valeurs['email']) ?>">
            <?= $erreurChamp('email') ?>
        </div>

        <div class="champ">
            <label for="nom">Nom *</label>
            <input id="nom" name="nom" type="text" maxlength="50" required
                   value="<?= e($valeurs['nom']) ?>">
            <?= $erreurChamp('nom') ?>
        </div>

        <div class="champ">
            <label for="prenom">Prénom *</label>
            <input id="prenom" name="prenom" type="text" maxlength="50" required
                   value="<?= e($valeurs['prenom']) ?>">
            <?= $erreurChamp('prenom') ?>
        </div>

        <div class="champ">
            <label for="telephone">Téléphone</label>
            <input id="telephone" name="telephone" type="tel" maxlength="20"
                   placeholder="06XXXXXXXX" value="<?= e($valeurs['telephone']) ?>">
            <?= $erreurChamp('telephone') ?>
        </div>

        <div class="champ">
            <label for="date_naissance">Date de naissance</label>
            <input id="date_naissance" name="date_naissance" type="text" inputmode="numeric"
                   maxlength="10" pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}" title="Format : jj/mm/aaaa"
                   placeholder="jj/mm/aaaa" value="<?= e($valeurs['date_naissance']) ?>">
            <?= $erreurChamp('date_naissance') ?>
        </div>

        <div class="champ">
            <label for="statut">Statut</label>
            <select id="statut" name="statut">
                <?php foreach (StagiaireForm::STATUTS as $cle => $libelle): ?>
                    <option value="<?= e($cle) ?>" <?= $valeurs['statut'] === $cle ? 'selected' : '' ?>>
                        <?= e($libelle) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?= $erreurChamp('statut') ?>
        </div>

        <div class="champ champ-actions">
            <p class="note">* champs obligatoires</p>
            <p class="note note-erreur">Matricule et email doivent être uniques</p>
            <div class="boutons">
                <button class="btn btn-primaire" type="submit">Enregistrer</button>
                <a class="btn btn-contour" href="<?= e($urlAnnuler) ?>">Annuler</a>
            </div>
        </div>
    </div>
</form>
