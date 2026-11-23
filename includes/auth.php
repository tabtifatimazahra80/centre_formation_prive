<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

const LIBELLES_ROLES = [
    'administrateur' => 'Administrateur',
    'formateur'      => 'Formateur',
    'stagiaire'      => 'Étudiant',
];

function libelle_role(string $role): string
{
    return LIBELLES_ROLES[$role] ?? ucfirst($role);
}

/** Utilisateur connecté (id, nom, prenom, role) ou null. */
function utilisateur_courant(): ?array
{
    return $_SESSION['utilisateur'] ?? null;
}

/** Vérifie les identifiants ; ouvre la session si corrects et compte actif. */
function authentifier(string $email, string $motDePasse): bool
{
    $stmt = db()->prepare(
        'SELECT u.idUtilisateur, u.nom, u.prenom, u.motDePasse, u.statut, r.libelle AS role
         FROM utilisateur u
         INNER JOIN role r ON r.idRole = u.idRole
         WHERE u.email = :email'
    );
    $stmt->execute([':email' => $email]);
    $utilisateur = $stmt->fetch();

    if (
        $utilisateur === false
        || $utilisateur['statut'] !== 'actif'
        || !password_verify($motDePasse, $utilisateur['motDePasse'])
    ) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['utilisateur'] = [
        'id'     => (int) $utilisateur['idUtilisateur'],
        'nom'    => $utilisateur['nom'],
        'prenom' => $utilisateur['prenom'],
        'role'   => $utilisateur['role'],
    ];

    return true;
}

function deconnecter(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }

    session_destroy();
}

/** Redirige vers la connexion si personne n'est connecté. */
function exiger_connexion(): array
{
    $utilisateur = utilisateur_courant();

    if ($utilisateur === null) {
        rediriger('login.php');
    }

    return $utilisateur;
}

/** Redirige vers l'accueil si l'utilisateur n'a pas le rôle demandé. */
function exiger_role(string $role): array
{
    $utilisateur = exiger_connexion();

    if ($utilisateur['role'] !== $role) {
        rediriger('accueil.php');
    }

    return $utilisateur;
}
