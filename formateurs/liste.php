<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/FormateurRepository.php';
require_once __DIR__ . '/../includes/pagination.php';

exiger_role('administrateur');

$depot = new FormateurRepository(db());

$recherche = entree($_GET, 'q');
$statut = entree($_GET, 'statut', 'tous');
if (!in_array($statut, ['tous', 'actif', 'inactif'], true)) {
    $statut = 'tous';
}

$total = $depot->compter($recherche, $statut);
$nombrePages = max(1, (int) ceil($total / FORMATEURS_PAR_PAGE));
$page = min(max(1, (int) entree($_GET, 'page', '1')), $nombrePages);

$formateurs = $depot->rechercher($recherche, $statut, FORMATEURS_PAR_PAGE, ($page - 1) * FORMATEURS_PAR_PAGE);
$nombreAffiches = count($formateurs);

/** Lien vers une page de la liste en conservant la recherche et le filtre. */
$lienPage = static function (int $numero) use ($recherche, $statut): string {
    $parametres = ['page' => $numero];
    if ($recherche !== '') {
        $parametres['q'] = $recherche;
    }
    if ($statut !== 'tous') {
        $parametres['statut'] = $statut;
    }

    return url('formateurs/liste.php') . '?' . http_build_query($parametres);
};

$titrePage  = 'Liste des formateurs';
$pageActive = 'formateurs';
require __DIR__ . '/../includes/header.php';
?>
<div class="entete-page">
    <h1>Liste des formateurs</h1>
    <a class="btn btn-primaire" href="<?= e(url('formateurs/ajouter.php')) ?>">+ Ajouter un formateur</a>
</div>

<form class="recherche" method="get" action="<?= e(url('formateurs/liste.php')) ?>">
    <input type="search" name="q" value="<?= e($recherche) ?>"
           placeholder="Rechercher par nom, prénom, matricule ou spécialité..." aria-label="Rechercher un formateur">
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
                <th>Spécialité</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($formateurs === []): ?>
                <tr><td colspan="7" class="vide">Aucun formateur trouvé.</td></tr>
            <?php endif; ?>
            <?php foreach ($formateurs as $formateur): ?>
                <?php $actif = $formateur['statut'] === 'actif'; ?>
                <tr>
                    <td><?= e($formateur['matricule']) ?></td>
                    <td><?= e($formateur['nom']) ?></td>
                    <td><?= e($formateur['prenom']) ?></td>
                    <td><?= e($formateur['email']) ?></td>
                    <td><?= e($formateur['specialite']) ?></td>
                    <td>
                        <span class="badge <?= $actif ? 'badge-actif' : 'badge-inactif' ?>">
                            <?= $actif ? 'Actif' : 'Inactif' ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="<?= e(url('formateurs/fiche.php?id=' . $formateur['id'])) ?>">Consulter</a>
                        <a href="<?= e(url('formateurs/modifier.php?id=' . $formateur['id'])) ?>">Modifier</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="pied-liste">
    <span><?= $nombreAffiches ?> <?= $nombreAffiches > 1 ? 'formateurs affichés' : 'formateur affiché' ?></span>
    <?= rendre_pagination($page, $nombrePages, $lienPage) ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
