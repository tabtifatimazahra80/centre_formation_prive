<?php
declare(strict_types=1);

const DB_HOTE = '';
const DB_PORT = '';
const DB_NOM = '';
const DB_UTILISATEUR = '';
const DB_MOT_DE_PASSE = '';

/** Retourne l'unique connexion PDO de la requête. */
function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOTE . ';port=' . DB_PORT . ';dbname=' . DB_NOM . ';charset=utf8mb4',
                DB_UTILISATEUR,
                DB_MOT_DE_PASSE,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            error_log('Connexion à la base impossible : ' . $e->getMessage());
            http_response_code(500);
            exit('Erreur de connexion à la base de données.');
        }
    }

    return $pdo;
}