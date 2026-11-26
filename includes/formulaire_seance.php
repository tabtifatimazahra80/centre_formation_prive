<?php
declare(strict_types=1);

$erreurChamp = static fn (string $champ): string => isset($erreurs[$champ])
    ? '<span class="erreur-champ">' . e($erreurs[$champ]) . '</span>'
    : '';
?>

<form class="formulaire" method="post" action="<?= e($actionFormulaire) ?>">

    <?= csrf_champ() ?>

    <?php if (isset($erreurs['general'])): ?>
        <div class="alerte alerte-erreur" role="alert">
            <?= e($erreurs['general']) ?>
        </div>
    <?php endif; ?>

    <div class="grille-formulaire">

        <div class="champ">
            <label for="dateSeance">Date et heure *</label>

            <input
                type="datetime-local"
                id="dateSeance"
                name="dateSeance"
                required
                value="<?= e($valeurs['dateSeance']) ?>"
            >

            <?= $erreurChamp('dateSeance') ?>
        </div>

        <div class="champ">
            <label for="typeSeance">Type de séance *</label>

            <select id="typeSeance" name="typeSeance" required>

                <option value="Presentiel"
                    <?= $valeurs['typeSeance'] === 'Presentiel' ? 'selected' : '' ?>>
                    Présentiel
                </option>

                <option value="Distanciel"
                    <?= $valeurs['typeSeance'] === 'Distanciel' ? 'selected' : '' ?>>
                    Distanciel
                </option>

            </select>

            <?= $erreurChamp('typeSeance') ?>
        </div>

        <div class="champ">
            <label for="idGroupe">Groupe *</label>

            <select id="idGroupe" name="idGroupe" required>

                <option value="">-- Choisir un groupe --</option>

                <?php foreach ($groupes as $groupe): ?>

                    <option
                        value="<?= (int) $groupe['idGroupe'] ?>"
                        <?= (string) $valeurs['idGroupe'] === (string) $groupe['idGroupe'] ? 'selected' : '' ?>
                    >
                        <?= e($groupe['codeGroupe'] . ' - ' . $groupe['nom']) ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <?= $erreurChamp('idGroupe') ?>
        </div>

        <div class="champ">
            <label for="idModule">Module *</label>

            <select id="idModule" name="idModule" required>

                <option value="">-- Choisir un module --</option>

                <?php foreach ($modules as $module): ?>

                    <option
                        value="<?= (int) $module['idModule'] ?>"
                        <?= (string) $valeurs['idModule'] === (string) $module['idModule'] ? 'selected' : '' ?>
                    >
                        <?= e($module['codeModule'] . ' - ' . $module['intitule']) ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <?= $erreurChamp('idModule') ?>
        </div>

        <div class="champ">
            <label for="idFormateur">Formateur *</label>

            <select id="idFormateur" name="idFormateur" required>

                <option value="">-- Choisir un formateur --</option>

                <?php foreach ($formateurs as $formateur): ?>

                    <option
                        value="<?= (int) $formateur['idUtilisateur'] ?>"
                        <?= (string) $valeurs['idFormateur'] === (string) $formateur['idUtilisateur'] ? 'selected' : '' ?>
                    >
                        <?= e($formateur['nom'] . ' ' . $formateur['prenom']) ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <?= $erreurChamp('idFormateur') ?>
        </div>

        <div class="champ champ-actions">

            <p class="note">* Champs obligatoires</p>

            <div class="boutons">

                <button class="btn btn-primaire" type="submit">
                    Enregistrer
                </button>

                <a class="btn btn-contour" href="<?= e($urlAnnuler) ?>">
                    Annuler
                </a>

            </div>

        </div>

    </div>

</form>