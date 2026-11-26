<?php
declare(strict_types=1);

require_once __DIR__ . '/GroupeRepository.php';

/** Validation des données du formulaire groupe. */
final class GroupeForm
{
    public const STATUTS = ['actif' => 'Actif', 'inactif' => 'Inactif'];

    /** Valeurs d'un formulaire vide (création). */
    public static function valeursVides(): array
    {
        return [
            'code'     => '',
            'nom'      => '',
            'filiere'  => '',
            'annee'    => '',
            'capacite' => '',
            'statut'   => 'actif',
        ];
    }

    /** Valeurs d'affichage à partir d'un groupe de la base (modification). */
    public static function valeursDepuisGroupe(array $groupe): array
    {
        return [
            'code'     => $groupe['code'],
            'nom'      => $groupe['nom'],
            'filiere'  => $groupe['filiere'],
            'annee'    => $groupe['annee'],
            'capacite' => (string) $groupe['capacite'],
            'statut'   => $groupe['statut'],
        ];
    }

    /**
     * Valide les données postées.
     * Retourne ['valeurs' => à réafficher, 'donnees' => prêtes pour la base, 'erreurs' => par champ].
     * $idExclu : groupe en cours de modification (ignoré dans le contrôle d'unicité du code).
     */
    public static function valider(array $post, GroupeRepository $depot, ?int $idExclu = null): array
    {
        $valeurs = [
            'code'     => entree($post, 'code'),
            'nom'      => entree($post, 'nom'),
            'filiere'  => entree($post, 'filiere'),
            'annee'    => entree($post, 'annee'),
            'capacite' => entree($post, 'capacite'),
            'statut'   => entree($post, 'statut', 'actif'),
        ];
        $erreurs = [];

        if ($valeurs['code'] === '') {
            $erreurs['code'] = 'Le code du groupe est obligatoire.';
        } elseif (mb_strlen($valeurs['code']) > 50) {
            $erreurs['code'] = 'Le code ne doit pas dépasser 50 caractères.';
        } elseif ($depot->codeExiste($valeurs['code'], $idExclu)) {
            $erreurs['code'] = 'Ce code de groupe est déjà utilisé.';
        }

        $limites = [
            'nom'     => ['Le nom', 100],
            'filiere' => ['La filière', 100],
            'annee'   => ["L'année", 50],
        ];
        foreach ($limites as $champ => [$libelle, $maximum]) {
            if ($valeurs[$champ] === '') {
                $erreurs[$champ] = $libelle . ' est obligatoire.';
            } elseif (mb_strlen($valeurs[$champ]) > $maximum) {
                $erreurs[$champ] = $libelle . " ne doit pas dépasser $maximum caractères.";
            }
        }

        $capacite = filter_var($valeurs['capacite'], FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 2147483647],
        ]);
        if ($capacite === false) {
            $erreurs['capacite'] = 'La capacité doit être un nombre entier supérieur à 0.';
        }

        if (!array_key_exists($valeurs['statut'], self::STATUTS)) {
            $erreurs['statut'] = 'Statut invalide.';
        }

        $donnees = $valeurs;
        $donnees['capacite'] = $capacite === false ? null : $capacite;

        return ['valeurs' => $valeurs, 'donnees' => $donnees, 'erreurs' => $erreurs];
    }
}
