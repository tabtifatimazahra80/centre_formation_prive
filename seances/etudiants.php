<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/SeanceRepository.php';

// Vérifier la connexion
$utilisateur = exiger_connexion();

// Vérifier le rôle
if (!in_array($utilisateur['role'], ['administrateur', 'formateur'], true)) {
    rediriger('accueil.php');
}

// Vérifier l'identifiant de la séance
$idSeance = filter_input(INPUT_GET, 'idSeance', FILTER_VALIDATE_INT);

if (!$idSeance || $idSeance <= 0) {
    rediriger('seances/listes.php');
}

// Initialiser le Repository
$depot = new SeanceRepository(db());

// Récupérer la séance
$seance = $depot->trouver($idSeance);

if ($seance === null) {
    rediriger('seances/listes.php');
}

// Récupérer les étudiants
$etudiants = $depot->etudiantsParSeance($idSeance);

$nombreEtudiants = count($etudiants);

// Paramètres de la page
$titrePage = 'Étudiants de la séance';
$pageActive = 'seances';

require __DIR__ . '/../includes/header.php';
?>

<div class="entete-page">

    <h1>Étudiants de la séance</h1>

    <a class="btn btn-contour"
       href="<?= e(url('seances/listes.php')) ?>">
        Retour à la liste
    </a>

</div>

<div class="carte">

    <h2>Informations de la séance</h2>

    <p>
        <strong>Date :</strong>
        <?= e(
            (new DateTimeImmutable($seance['dateSeance']))
                ->format('d/m/Y H:i')
        ) ?>
    </p>

    <p>
        <strong>Groupe :</strong>
        <?= e($seance['codeGroupe'] . ' - ' . $seance['nomGroupe']) ?>
    </p>

    <p>
        <strong>Module :</strong>
        <?= e($seance['codeModule'] . ' - ' . $seance['nomModule']) ?>
    </p>

    <p>
        <strong>Formateur :</strong>
        <?= e($seance['nomFormateur'] . ' ' . $seance['prenomFormateur']) ?>
    </p>

</div>

<h2>Liste des étudiants</h2>

<div class="defilement-horizontal">

    <table class="tableau">

        <thead>
            <tr>
                <th>N° Inscription</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Présence</th>
            </tr>
        </thead>

        <tbody>

            <?php if ($etudiants === []): ?>

                <tr>
                    <td colspan="5" class="vide">
                        Aucun étudiant trouvé dans ce groupe.
                    </td>
                </tr>

            <?php endif; ?>

            <?php foreach ($etudiants as $etudiant): ?>

                <tr>

                    <td>
                        <?= e($etudiant['numInscription']) ?>
                    </td>

                    <td>
                        <?= e($etudiant['nom']) ?>
                    </td>

                    <td>
                        <?= e($etudiant['prenom']) ?>
                    </td>

                    <td>
                        <?= e($etudiant['email']) ?>
                    </td>

                    <td>

                        <?php if ($etudiant['statutPresence'] === 'Present'): ?>

                            <span class="badge badge-actif">
                                Présent
                            </span>

                        <?php elseif ($etudiant['statutPresence'] === 'Absent'): ?>

                            <span class="badge badge-inactif">
                                Absent
                            </span>

                        <?php else: ?>

                            <span>Non renseignée</span>

                        <?php endif; ?>

                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</div>

<div class="pied-liste">

    <?= $nombreEtudiants ?>
    <?= $nombreEtudiants > 1 ? 'étudiants' : 'étudiant' ?>

</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>