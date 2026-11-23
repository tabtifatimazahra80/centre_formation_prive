<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/StagiaireRepository.php';

exiger_role('administrateur');

$depot = new StagiaireRepository(db());

$recherche = entree($_GET, 'q');
$statut = entree($_GET, 'statut', 'tous');
if (!in_array($statut, ['tous', 'actif', 'inactif'], true)) {
    $statut = 'tous';
}

$total = $depot->compter($recherche, $statut);
$nombrePages = max(1, (int) ceil($total / ETUDIANTS_PAR_PAGE));
$page = min(max(1, (int) entree($_GET, 'page', '1')), $nombrePages);

$etudiants = $depot->rechercher($recherche, $statut, ETUDIANTS_PAR_PAGE, ($page - 1) * ETUDIANTS_PAR_PAGE);
$nombreAffiches = count($etudiants);

/** Lien vers une page de la liste en conservant la recherche et le filtre. */
$lienPage = static function (int $numero) use ($recherche, $statut): string {
    $parametres = ['page' => $numero];
    if ($recherche !== '') {
        $parametres['q'] = $recherche;
    }
    if ($statut !== 'tous') {
        $parametres['statut'] = $statut;
    }

    return url('etudiants/liste.php') . '?' . http_build_query($parametres);
};

$titrePage  = 'Liste des étudiants';
$pageActive = 'etudiants';
require __DIR__ . '/../includes/header.php';
?>
<div class="entete-page">
    <h1>Liste des étudiants</h1>
    <a class="btn btn-primaire" href="<?= e(url('etudiants/ajouter.php')) ?>">+ Ajouter un étudiant</a>
</div>

<form class="recherche" method="get" action="<?= e(url('etudiants/liste.php')) ?>">
    <input type="search" name="q" value="<?= e($recherche) ?>"
           placeholder="Rechercher par nom, prénom ou matricule..." aria-label="Rechercher un étudiant">
    <select name="statut" aria-label="Filtrer par statut">
        <option value="tous" <?= $statut === 'tous' ? 'selected' : '' ?>>Statut : Tous</option>
        <option value="actif" <?= $statut === 'actif' ? 'selected' : '' ?>>Statut : Actif</option>
        <option value="inactif" <?= $statut === 'inactif' ? 'selected' : '' ?>>Statut : Inactif</option>
    </select>
    <button class="btn btn-primaire" type="submit">Rechercher</button>
</form>

<div class="defilement-horizontal">
    <table class="tableau">
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($etudiants === []): ?>
                <tr><td colspan="6" class="vide">Aucun étudiant trouvé.</td></tr>
            <?php endif; ?>
            <?php foreach ($etudiants as $etudiant): ?>
                <?php $actif = $etudiant['statut'] === 'actif'; ?>
                <tr>
                    <td><?= e($etudiant['matricule']) ?></td>
                    <td><?= e($etudiant['nom']) ?></td>
                    <td><?= e($etudiant['prenom']) ?></td>
                    <td><?= e($etudiant['email']) ?></td>
                    <td>
                        <span class="badge <?= $actif ? 'badge-actif' : 'badge-inactif' ?>">
                            <?= $actif ? 'Actif' : 'Inactif' ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="<?= e(url('etudiants/fiche.php?id=' . $etudiant['id'])) ?>">Consulter</a>
                        <a href="<?= e(url('etudiants/modifier.php?id=' . $etudiant['id'])) ?>">Modifier</a>
                        <?php if ($actif): ?>
                            <a class="action-danger"
                               href="<?= e(url('etudiants/fiche.php?id=' . $etudiant['id'] . '&confirmer=1')) ?>">Désactiver</a>
                        <?php else: ?>
                            <span class="action-inactive" aria-disabled="true">Désactiver</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="pied-liste">
    <span><?= $nombreAffiches ?> <?= $nombreAffiches > 1 ? 'étudiants affichés' : 'étudiant affiché' ?></span>

    <nav class="pagination" aria-label="Pagination">
        <?php if ($page > 1): ?>
            <a href="<?= e($lienPage($page - 1)) ?>">‹ Précédent</a>
        <?php else: ?>
            <span class="desactive">‹ Précédent</span>
        <?php endif; ?>

        <?php for ($numero = 1; $numero <= $nombrePages; $numero++): ?>
            <?php if ($numero === $page): ?>
                <span class="courante" aria-current="page"><?= $numero ?></span>
            <?php else: ?>
                <a href="<?= e($lienPage($numero)) ?>"><?= $numero ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $nombrePages): ?>
            <a href="<?= e($lienPage($page + 1)) ?>">Suivant ›</a>
        <?php else: ?>
            <span class="desactive">Suivant ›</span>
        <?php endif; ?>
    </nav>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
