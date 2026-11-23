<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/StagiaireForm.php';

exiger_role('administrateur');

$depot = new StagiaireRepository(db());

$id = (int) entree($_GET, 'id');
$etudiant = $depot->trouver($id);

if ($etudiant === null) {
    flash('erreur', 'Étudiant introuvable.');
    rediriger('etudiants/liste.php');
}

$valeurs = StagiaireForm::valeursDepuisEtudiant($etudiant);
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verifier();

    ['valeurs' => $valeurs, 'donnees' => $donnees, 'erreurs' => $erreurs] = StagiaireForm::valider($_POST, $depot, $id);

    if ($erreurs === []) {
        try {
            $depot->modifier($id, $donnees);
            flash('succes', 'Informations modifiées.');
            rediriger('etudiants/fiche.php?id=' . $id);
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? 0) !== 1062) {
                throw $e;
            }
            $erreurs['general'] = 'Matricule et email doivent être uniques.';
        }
    }
}

$actionFormulaire = url('etudiants/modifier.php?id=' . $id);
$urlAnnuler = url('etudiants/fiche.php?id=' . $id);

$titrePage  = 'Modifier un étudiant';
$pageActive = 'etudiants';
require __DIR__ . '/../includes/header.php';
?>
<h1>Modifier un étudiant</h1>
<?php require __DIR__ . '/../includes/formulaire_etudiant.php'; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
