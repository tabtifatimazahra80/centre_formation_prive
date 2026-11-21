<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/StagiaireForm.php';

exiger_role('administrateur');

$depot = new StagiaireRepository(db());
$valeurs = StagiaireForm::valeursVides();
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verifier();

    ['valeurs' => $valeurs, 'donnees' => $donnees, 'erreurs' => $erreurs] = StagiaireForm::valider($_POST, $depot);

    if ($erreurs === []) {
        try {
            $depot->creer($donnees);
            flash('succes', 'Étudiant ajouté.');
            rediriger('etudiants/liste.php');
        } catch (PDOException $e) {
            // 1062 = doublon (course entre deux enregistrements simultanés)
            if (($e->errorInfo[1] ?? 0) !== 1062) {
                throw $e;
            }
            $erreurs['general'] = 'Matricule et email doivent être uniques.';
        }
    }
}

$actionFormulaire = url('etudiants/ajouter.php');
$urlAnnuler = url('etudiants/liste.php');

$titrePage  = 'Ajouter un étudiant';
$pageActive = 'etudiants';
require __DIR__ . '/../includes/header.php';
?>
<h1>Ajouter un étudiant</h1>
<?php require __DIR__ . '/../includes/formulaire_etudiant.php'; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
