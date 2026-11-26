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

// Connexion et Repository
$depot = new SeanceRepository(db());

// Récupérer toutes les séances
$seances = $depot->lister();

$nombreAffiches = count($seances);

// Paramètres de la page
$titrePage = 'Liste des séances';
$pageActive = 'seances';

// Afficher le header
require __DIR__ . '/../includes/header.php';
?>

<div class="entete-page">

    <h1>Liste des séances</h1>

    <a class="btn btn-primaire"
       href="<?= e(url('seances/ajouter.php')) ?>">
        + Ajouter une séance
    </a>

</div>

<div class="defilement-horizontal">

    <table class="tableau">

        <thead>
            <tr>
                <th>ID</th>
                <th>Date et heure</th>
                <th>Type</th>
                <th>Groupe</th>
                <th>Module</th>
                <th>Formateur</th>
            </tr>
        </thead>

        <tbody>

            <?php if ($seances === []): ?>

                <tr>
                    <td colspan="6" class="vide">
                        Aucune séance trouvée.
                    </td>
                </tr>

            <?php endif; ?>

            <?php foreach ($seances as $seance): ?>

                <tr>

                    <td>
                        <?= (int) $seance['idSeance'] ?>
                    </td>

                    <td>
                        <?= e(
                            (new DateTimeImmutable($seance['dateSeance']))
                                ->format('d/m/Y H:i')
                        ) ?>
                    </td>

                    <td>
                        <span class="badge badge-actif">
                            <?= $seance['typeSeance'] === 'Presentiel'
                                ? 'Présentiel'
                                : 'Distanciel' ?>
                        </span>
                    </td>

                    <td>
                        <?= e(
                            $seance['codeGroupe'] . ' - ' .
                            $seance['nomGroupe']
                        ) ?>
                    </td>

                    <td>
                        <?= e(
                            $seance['codeModule'] . ' - ' .
                            $seance['nomModule']
                        ) ?>
                    </td>

                    <td>
                        <?= e(
                            $seance['nomFormateur'] . ' ' .
                            $seance['prenomFormateur']
                        ) ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</div>

<div class="pied-liste">

    <span>
        <?= $nombreAffiches ?>
        <?= $nombreAffiches > 1 ? 'séances affichées' : 'séance affichée' ?>
    </span>

</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>