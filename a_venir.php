<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/menu.php';

$utilisateur = exiger_connexion();
$cle = entree($_GET, 'r');

$rubrique = null;
foreach (menu_pour_role($utilisateur['role']) as $element) {
    if ($element['cle'] === $cle) {
        $rubrique = $element;
        break;
    }
}

if ($rubrique === null) {
    rediriger('accueil.php');
}

$titrePage  = $rubrique['libelle'];
$pageActive = $rubrique['cle'];
require __DIR__ . '/includes/header.php';
?>
<h1><?= e($rubrique['libelle']) ?></h1>
<p class="sous-titre">Cette rubrique sera disponible dans un prochain sprint.</p>
<?php require __DIR__ . '/includes/footer.php'; ?>
