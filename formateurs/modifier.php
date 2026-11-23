<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/FormateurForm.php';

exiger_role('administrateur');

$depot = new FormateurRepository(db());
$valeurs = FormateurForm::valeursVides();
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verifier();

    ['valeurs' => $valeurs, 'donnees' => $donnees, 'erreurs' => $erreurs] = FormateurForm::valider($_POST, $depot);

    if ($erreurs === []) {
        try {
            $depot->creer($donnees);
            flash('succes', 'Formateur ajouté.');
            rediriger('formateurs/liste.php');
        } catch (PDOException $e) {
            // 1062 = doublon (course entre deux enregistrements simultanés)
            if (($e->errorInfo[1] ?? 0) !== 1062) {
                throw $e;
            }
            $erreurs['general'] = 'Matricule et email doivent être uniques.';
        }
    }
}

$actionFormulaire = url('formateurs/ajouter.php');
$urlAnnuler = url('formateurs/liste.php');

$titrePage  = 'Ajouter un formateur';
$pageActive = 'formateurs';
require __DIR__ . '/../includes/header.php';
?>
<h1>Ajouter un formateur</h1>
<?php require __DIR__ . '/../includes/formulaire_formateur.php'; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
