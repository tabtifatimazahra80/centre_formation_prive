<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/StagiaireRepository.php';

exiger_role('administrateur');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    rediriger('etudiants/liste.php');
}

csrf_verifier();

$id = (int) entree($_POST, 'id');
$depot = new StagiaireRepository(db());

if ($depot->desactiver($id)) {
    flash('succes', 'Étudiant désactivé.');
} else {
    flash('erreur', 'Impossible de désactiver cet étudiant.');
}

rediriger('etudiants/fiche.php?id=' . $id);
