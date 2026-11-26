<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/GroupeForm.php';

exiger_role('administrateur');

$depot = new GroupeRepository(db());
$valeurs = GroupeForm::valeursVides();
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verifier();

    ['valeurs' => $valeurs, 'donnees' => $donnees, 'erreurs' => $erreurs] = GroupeForm::valider($_POST, $depot);

    if ($erreurs === []) {
        try {
            $depot->creer($donnees);
            flash('succes', 'Groupe créé.');
            rediriger('groupes/liste.php');
        } catch (PDOException $e) {
            // 1062 = doublon (course entre deux enregistrements simultanés)
            if (($e->errorInfo[1] ?? 0) !== 1062) {
                throw $e;
            }
            $erreurs['general'] = 'Le code du groupe doit être unique.';
        }
    }
}

$actionFormulaire = url('groupes/ajouter.php');
$urlAnnuler = url('groupes/liste.php');

$titrePage  = 'Créer un groupe';
$pageActive = 'groupes';
require __DIR__ . '/../includes/header.php';
?>
<h1>Créer un groupe</h1>
<?php require __DIR__ . '/../includes/formulaire_groupe.php'; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
