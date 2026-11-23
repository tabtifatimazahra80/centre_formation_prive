<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/menu.php';

/*
 * Mise en page commune (en-tête + menu latéral).
 * La page qui l'inclut définit $titrePage et $pageActive.
 */
$utilisateur = exiger_connexion();
$titrePage   = $titrePage ?? 'Centre de Formation';
$pageActive  = $pageActive ?? '';
$flash       = flash_recuperer();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titrePage) ?> — Centre de Formation</title>
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
</head>
<body>
<header class="barre-haut">
    <a class="marque" href="<?= e(url('accueil.php')) ?>">Centre de Formation</a>
    <div class="barre-utilisateur">
        <span><?= e(libelle_role($utilisateur['role'])) ?> |</span>
        <form method="post" action="<?= e(url('logout.php')) ?>">
            <?= csrf_champ() ?>
            <button class="btn btn-clair" type="submit">Déconnexion</button>
        </form>
    </div>
</header>
<div class="corps">
    <nav class="menu-lateral" aria-label="Menu principal">
        <ul>
            <?php foreach (menu_pour_role($utilisateur['role']) as $rubrique): ?>
                <li>
                    <a href="<?= e(url($rubrique['page'])) ?>"
                       <?= $rubrique['cle'] === $pageActive ? 'class="actif" aria-current="page"' : '' ?>>
                        <?= e($rubrique['libelle']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <main class="contenu">
        <?php if ($flash !== null): ?>
            <div class="alerte <?= $flash['type'] === 'succes' ? 'alerte-succes' : 'alerte-erreur' ?>" role="status">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>
