<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/menu.php';

$utilisateur = exiger_connexion();

$rubriques = array_filter(
    menu_pour_role($utilisateur['role']),
    static fn (array $rubrique): bool => $rubrique['cle'] !== 'accueil'
);

$titrePage  = 'Accueil';
$pageActive = 'accueil';
require __DIR__ . '/includes/header.php';
?>
<h1>Bienvenue, <?= e(libelle_role($utilisateur['role'])) ?></h1>
<p class="sous-titre">Choisissez une rubrique dans le menu à gauche.</p>

<div class="cartes">
    <?php foreach ($rubriques as $rubrique): ?>
        <a class="carte" href="<?= e(url($rubrique['page'])) ?>">
            <strong><?= e($rubrique['libelle']) ?></strong>
            <?php if (!empty($rubrique['description'])): ?>
                <span><?= e($rubrique['description']) ?></span>
            <?php endif; ?>
        </a>
    <?php endforeach; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
