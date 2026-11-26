<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/GroupeRepository.php';

exiger_role('administrateur');

$groupes = (new GroupeRepository(db()))->lister();

$titrePage  = 'Liste des groupes';
$pageActive = 'groupes';
require __DIR__ . '/../includes/header.php';
?>
<div class="entete-page">
    <h1>Liste des groupes</h1>
    <a class="btn btn-primaire" href="<?= e(url('groupes/ajouter.php')) ?>">+ Créer un groupe</a>
</div>

<div class="defilement-horizontal">
    <table class="tableau">
        <thead>
            <tr>
                <th>Code groupe</th>
                <th>Nom</th>
                <th>Filière</th>
                <th>Année</th>
                <th>Capacité</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($groupes === []): ?>
                <tr><td colspan="7" class="vide">Aucun groupe trouvé.</td></tr>
            <?php endif; ?>
            <?php foreach ($groupes as $groupe): ?>
                <?php $actif = $groupe['statut'] === 'actif'; ?>
                <tr>
                    <td><?= e($groupe['code']) ?></td>
                    <td><?= e($groupe['nom']) ?></td>
                    <td><?= e($groupe['filiere']) ?></td>
                    <td><?= e($groupe['annee']) ?></td>
                    <td><?= (int) $groupe['capacite'] ?></td>
                    <td>
                        <span class="badge <?= $actif ? 'badge-actif' : 'badge-inactif' ?>">
                            <?= $actif ? 'Actif' : 'Inactif' ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="<?= e(url('groupes/modifier.php?id=' . $groupe['id'])) ?>">Modifier</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
