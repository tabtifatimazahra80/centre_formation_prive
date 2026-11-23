<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

/** Échappe un texte pour l'affichage HTML. */
function e(?string $texte): string
{
    return htmlspecialchars($texte ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Construit une URL à partir de la racine du projet. */
function url(string $chemin = ''): string
{
    return BASE_URL . '/' . ltrim($chemin, '/');
}

/** Redirige vers une page du projet puis arrête le script. */
function rediriger(string $chemin): void
{
    header('Location: ' . url($chemin));
    exit;
}

/** Lit une valeur texte (nettoyée) dans $_GET / $_POST ; ignore les tableaux. */
function entree(array $source, string $cle, string $defaut = ''): string
{
    $valeur = $source[$cle] ?? $defaut;

    return is_string($valeur) ? trim($valeur) : $defaut;
}

/** Enregistre un message affiché une seule fois à la prochaine page. */
function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_recuperer(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $flash;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function csrf_champ(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/** Arrête la requête si le jeton CSRF du formulaire est absent ou faux. */
function csrf_verifier(): void
{
    $envoye = $_POST['csrf'] ?? '';

    if (!is_string($envoye) || !hash_equals(csrf_token(), $envoye)) {
        http_response_code(400);
        exit('Requête invalide.');
    }
}
