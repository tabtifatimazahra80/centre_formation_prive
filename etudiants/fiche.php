<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/StagiaireForm.php';

exiger_role('administrateur');

$depot = new StagiaireRepository(db());
$etudiant = $depot->trouver((int) entree($_GET, 'id'));

if ($etudiant === null) {
    flash('erreur', 'Étudiant introuvable.');
    rediriger('etudiants/liste.php');
}

$actif = $etudiant['statut'] === 'actif';
$confirmer = $actif && isset($_GET['confirmer']);
$urlFiche = url('etudiants/fiche.php?id=' . $etudiant['id']);

$lignes = [
    'Matricule'         => $etudiant['matricule'],
    'Nom'               => $etudiant['nom'],
    'Prénom'            => $etudiant['prenom'],
    'Email'             => $etudiant['email'],
    'Téléphone'         => $etudiant['telephone'] ?? '—',
    'Date de naissance' => StagiaireForm::dateDepuisSql($etudiant['date_naissance']) ?: '—',
    'Statut'            => $actif ? 'Actif' : 'Inactif',
];

$titrePage  = "Fiche de l'étudiant";
$pageActive = 'etudiants';
require __DIR__ . '/../includes/header.php';
?>
<h1>Fiche de l'étudiant</h1>
<a class="lien-retour" href="<?= e(url('etudiants/liste.php')) ?>">‹ Retour à la liste</a>

<div class="disposition-fiche">
    <section class="fiche">
        <dl>
            <?php foreach ($lignes as $libelle => $valeur): ?>
                <div class="ligne-fiche">
                    <dt><?= e($libelle) ?></dt>
                    <dd><?= e($valeur) ?></dd>
                </div>
            <?php endforeach; ?>
        </dl>

        <div class="boutons">
            <a class="btn btn-primaire" href="<?= e(url('etudiants/modifier.php?id=' . $etudiant['id'])) ?>">Modifier</a>
            <?php if ($actif): ?>
                <a class="btn btn-danger" href="<?= e($urlFiche . '&confirmer=1') ?>">Désactiver</a>
            <?php else: ?>
                <span class="btn btn-danger btn-desactive" aria-disabled="true">Désactiver</span>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($confirmer): ?>
        <aside class="confirmation" role="alertdialog" aria-labelledby="titre-confirmation">
            <h2 id="titre-confirmation">Confirmation</h2>
            <p>Voulez-vous vraiment désactiver cet étudiant ?</p>
            <div class="boutons boutons-centres">
                <form method="post" action="<?= e(url('etudiants/desactiver.php')) ?>">
                    <?= csrf_champ() ?>
                    <input type="hidden" name="id" value="<?= (int) $etudiant['id'] ?>">
                    <button class="btn btn-danger" type="submit">Oui</button>
                </form>
                <a class="btn btn-contour" href="<?= e($urlFiche) ?>">Non</a>
            </div>
        </aside>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
