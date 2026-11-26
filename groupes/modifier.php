<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/GroupeForm.php';

exiger_role('administrateur');

$depot = new GroupeRepository(db());

$id = (int) entree($_GET, 'id');
$groupe = $depot->trouver($id);

if ($groupe === null) {
    flash('erreur', 'Groupe introuvable.');
    rediriger('groupes/liste.php');
}

$valeurs = GroupeForm::valeursDepuisGroupe($groupe);
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verifier();

    ['valeurs' => $valeurs, 'donnees' => $donnees, 'erreurs' => $erreurs] = GroupeForm::valider($_POST, $depot, $id);

    if ($erreurs === []) {
        try {
            $depot->modifier($id, $donnees);
            flash('succes', 'Groupe modifié.');
            rediriger('groupes/liste.php');
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? 0) !== 1062) {
                throw $e;
            }
            $erreurs['general'] = 'Le code du groupe doit être unique.';
        }
    }
}

$actionFormulaire = url('groupes/modifier.php?id=' . $id);
$urlAnnuler = url('groupes/liste.php');

$titrePage  = 'Modifier un groupe';
$pageActive = 'groupes';
require __DIR__ . '/../includes/header.php';
?>
<h1>Modifier un groupe</h1>
<?php require __DIR__ . '/../includes/formulaire_groupe.php'; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
