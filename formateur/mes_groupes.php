<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

$utilisateur = exiger_role('formateur');

// Modules du formateur et les groupes qui suivent ces modules.
$sql = "SELECT
            g.idGroupe,
            g.codeGroupe,
            g.nom AS nomGroupe,
            g.filiere,
            g.annee,
            g.capacite,
            g.statut AS statutGroupe,
            m.idModule,
            m.codeModule,
            m.intitule AS intituleModule,
            m.volumeHoraire,
            COUNT(DISTINCT s.idUtilisateur) AS nombreStagiaires
        FROM module m
        INNER JOIN programmer p ON p.idModule = m.idModule
        INNER JOIN groupe g ON g.idGroupe = p.idGroupe
        LEFT JOIN stagiaire s ON s.idGroupe = g.idGroupe
        WHERE m.idFormateur = :idFormateur
        GROUP BY
            g.idGroupe, g.codeGroupe, g.nom, g.filiere, g.annee,
            g.capacite, g.statut, m.idModule, m.codeModule,
            m.intitule, m.volumeHoraire
        ORDER BY g.nom, m.intitule";

$stmt = db()->prepare($sql);
$stmt->execute([':idFormateur' => $utilisateur['id']]);
$affectations = $stmt->fetchAll();

// Groupes distincts et modules distincts pour les indicateurs.
$groupes = [];
$modules = [];
foreach ($affectations as $ligne) {
    $groupes[$ligne['idGroupe']] = true;
    $modules[$ligne['idModule']] = true;
}


$titrePage  = 'Mes groupes et modules';
$pageActive = 'mes_groupes';
require __DIR__ . '/../includes/header.php';
?>
<h1>Mes groupes et modules</h1>
<p class="sous-titre">Consultez les groupes et les modules qui vous sont affectés.</p>


<div class="cartes">
    <div class="carte">
        <strong><?= count($groupes) ?></strong>
        <span>Groupes affectés</span>
    </div>
    <div class="carte">
        <strong><?= count($modules) ?></strong>
        <span>Modules enseignés</span>
    </div>
</div>

<div class="defilement-horizontal">
    <table class="tableau">
        <thead>
            <tr>
                <th>Code groupe</th>
                <th>Nom du groupe</th>
                <th>Filière</th>
                <th>Année</th>
                <th>Module</th>
                <th>Volume horaire</th>
                <th>Étudiants</th>
                <th>Capacité</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($affectations === []): ?>
                <tr>
                    <td colspan="9" class="vide">Aucun groupe ou module ne vous est affecté pour le moment.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($affectations as $ligne): ?>
                <tr>
                    <td><?= e($ligne['codeGroupe']) ?></td>
                    <td><?= e($ligne['nomGroupe']) ?></td>
                    <td><?= e($ligne['filiere']) ?></td>
                    <td><?= e($ligne['annee']) ?></td>
                    <td><?= e($ligne['codeModule'] . ' — ' . $ligne['intituleModule']) ?></td>
                    <td><?= (int) $ligne['volumeHoraire'] ?> h</td>
                    <td><?= (int) $ligne['nombreStagiaires'] ?></td>
                    <td><?= (int) $ligne['capacite'] ?></td>
                    <td>
                        <span class="badge <?= $ligne['statutGroupe'] === 'actif' ? 'badge-actif' : 'badge-inactif' ?>">
                            <?= e(ucfirst($ligne['statutGroupe'])) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
