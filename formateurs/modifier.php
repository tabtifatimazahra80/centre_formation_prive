<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/FormateurForm.php';

exiger_role('administrateur');

$depot = new FormateurRepository(db());

$id = (int) entree($_GET, 'id');
$formateur = $depot->trouver($id);

if ($formateur === null) {
    flash('erreur', 'Formateur introuvable.');
    rediriger('formateurs/liste.php');
}

$valeurs = FormateurForm::valeursDepuisFormateur($formateur);
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verifier();

    ['valeurs' => $valeurs, 'donnees' => $donnees, 'erreurs' => $erreurs] = FormateurForm::valider($_POST, $depot, $id);

    if ($erreurs === []) {
        try {
            $depot->modifier($id, $donnees);
            flash('succes', 'Informations modifiées.');
            rediriger('formateurs/liste.php');
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? 0) !== 1062) {
                throw $e;
            }
            $erreurs['general'] = 'Matricule et email doivent être uniques.';
        }
    }
}

$actionFormulaire = url('formateurs/modifier.php?id=' . $id);
$urlAnnuler = url('formateurs/liste.php');

$titrePage = 'Modifier un formateur';
$pageActive = 'formateurs';

require __DIR__ . '/../includes/header.php';
?>

<h1>Modifier un formateur</h1>

<?php require __DIR__ . '/../includes/formulaire_formateur.php'; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>