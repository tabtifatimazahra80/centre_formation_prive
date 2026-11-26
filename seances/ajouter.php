<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/SeanceForm.php';
require_once __DIR__ . '/../includes/SeanceRepository.php';

// Vérifier les autorisations
$utilisateur = exiger_connexion();

if (!in_array($utilisateur['role'], ['administrateur', 'formateur'], true)) {
    rediriger('accueil.php');
}

// Initialisation
$depot = new SeanceRepository(db());

$valeurs = SeanceForm::valeursVides();
$erreurs = [];

// Récupérer les données pour les listes déroulantes
$groupes = $depot->groupesActifs();
$modules = $depot->modulesDisponibles();
$formateurs = $depot->formateursActifs();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    csrf_verifier();

    [
        'valeurs' => $valeurs,
        'donnees' => $donnees,
        'erreurs' => $erreurs
    ] = SeanceForm::valider($_POST);

    // Vérifier que les éléments sélectionnés existent
    if ($erreurs === []) {

        $idsGroupes = array_map('intval', array_column($groupes, 'idGroupe'));
        $idsModules = array_map('intval', array_column($modules, 'idModule'));
        $idsFormateurs = array_map('intval', array_column($formateurs, 'idUtilisateur'));

        if (!in_array($donnees['idGroupe'], $idsGroupes, true)) {
            $erreurs['idGroupe'] = 'Groupe invalide.';
        }

        if (!in_array($donnees['idModule'], $idsModules, true)) {
            $erreurs['idModule'] = 'Module invalide.';
        }

        if (!in_array($donnees['idFormateur'], $idsFormateurs, true)) {
            $erreurs['idFormateur'] = 'Formateur invalide.';
        }
    }

    // Enregistrer la séance
    if ($erreurs === []) {

        try {

            $depot->creer($donnees);

            flash('succes', 'La séance a été ajoutée avec succès.');

            rediriger('seances/ajouter.php');

        } catch (PDOException $e) {

            $erreurs['general'] = 'Une erreur est survenue lors de l’enregistrement.';
        }
    }
}

// Paramètres de la page
$actionFormulaire = url('seances/ajouter.php');
$urlAnnuler = url('accueil.php');

$titrePage = 'Ajouter une séance';
$pageActive = 'seances';

require __DIR__ . '/../includes/header.php';
?>

<h1>Ajouter une séance</h1>

<p>Remplissez les informations pour planifier une nouvelle séance.</p>

<?php require __DIR__ . '/../includes/formulaire_seance.php'; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>