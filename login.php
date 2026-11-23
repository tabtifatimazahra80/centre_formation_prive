<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

if (utilisateur_courant() !== null) {
    rediriger('accueil.php');
}

$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verifier();

    if (authentifier(entree($_POST, 'email'), (string) ($_POST['mot_de_passe'] ?? ''))) {
        rediriger('accueil.php');
    }

    $erreur = 'Email ou mot de passe incorrect';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — Centre de Formation</title>
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
</head>
<body class="page-connexion">
<main class="carte-connexion">
    <h1>Centre de Formation</h1>
    <p class="sous-titre">Connexion à votre espace</p>

    <?php if ($erreur !== null): ?>
        <div class="alerte alerte-erreur" role="alert"><?= e($erreur) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= e(url('login.php')) ?>">
        <?= csrf_champ() ?>

        <div class="champ">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required autofocus
                   placeholder="exemple@centre.ma" value="<?= e(entree($_POST, 'email')) ?>">
        </div>

        <div class="champ">
            <label for="mot_de_passe">Mot de passe</label>
            <input id="mot_de_passe" name="mot_de_passe" type="password" required
                   placeholder="••••••••">
        </div>

        <button class="btn btn-primaire btn-plein" type="submit">Se connecter</button>
    </form>
</main>
</body>
</html>
