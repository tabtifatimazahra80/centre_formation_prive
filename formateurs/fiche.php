<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/FormateurRepository.php';

exiger_role('administrateur');

$depot = new FormateurRepository(db());

$id = (int) entree($_GET, 'id');
$formateur = $depot->trouver($id);

if ($formateur === null) {
    flash('erreur', 'Formateur introuvable.');
    rediriger('formateurs/liste.php');
}

$titrePage = 'Fiche formateur';
$pageActive = 'formateurs';

require __DIR__ . '/../includes/header.php';
?>

<h1>Fiche formateur</h1>

<div class="carte">
    <p><strong>Matricule :</strong> <?= e($formateur['matricule']) ?></p>
    <p><strong>Nom :</strong> <?= e($formateur['nom']) ?></p>
    <p><strong>Prénom :</strong> <?= e($formateur['prenom']) ?></p>
    <p><strong>Email :</strong> <?= e($formateur['email']) ?></p>
    <p><strong>Spécialité :</strong> <?= e($formateur['specialite']) ?></p>
    <p><strong>Statut :</strong> <?= e($formateur['statut']) ?></p>
</div>

<div class="boutons">
    <a class="btn btn-primaire"
       href="<?= e(url('formateurs/modifier.php?id=' . $id)) ?>">
       Modifier
    </a>

    <a class="btn btn-contour"
       href="<?= e(url('formateurs/liste.php')) ?>">
       Retour
    </a>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>