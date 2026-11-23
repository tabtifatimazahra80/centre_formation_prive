<?php
declare(strict_types=1);

require_once __DIR__ . '/StagiaireRepository.php';

/** Validation et conversion des données du formulaire étudiant. */
final class StagiaireForm
{
    public const STATUTS = ['actif' => 'Actif', 'inactif' => 'Inactif'];

    /** Valeurs d'un formulaire vide (ajout). */
    public static function valeursVides(): array
    {
        return [
            'matricule'      => '',
            'nom'            => '',
            'prenom'         => '',
            'email'          => '',
            'telephone'      => '',
            'date_naissance' => '',
            'statut'         => 'actif',
        ];
    }

    /** Valeurs d'affichage à partir d'un étudiant de la base (modification). */
    public static function valeursDepuisEtudiant(array $etudiant): array
    {
        return [
            'matricule'      => $etudiant['matricule'],
            'nom'            => $etudiant['nom'],
            'prenom'         => $etudiant['prenom'],
            'email'          => $etudiant['email'],
            'telephone'      => $etudiant['telephone'] ?? '',
            'date_naissance' => self::dateDepuisSql($etudiant['date_naissance']),
            'statut'         => $etudiant['statut'],
        ];
    }

    /**
     * Valide les données postées.
     * Retourne ['valeurs' => à réafficher, 'donnees' => prêtes pour la base, 'erreurs' => par champ].
     * $idExclu : étudiant en cours de modification (ignoré dans les contrôles d'unicité).
     */
    public static function valider(array $post, StagiaireRepository $depot, ?int $idExclu = null): array
    {
        $valeurs = [
            'matricule'      => entree($post, 'matricule'),
            'nom'            => entree($post, 'nom'),
            'prenom'         => entree($post, 'prenom'),
            'email'          => entree($post, 'email'),
            'telephone'      => entree($post, 'telephone'),
            'date_naissance' => entree($post, 'date_naissance'),
            'statut'         => entree($post, 'statut', 'actif'),
        ];
        $erreurs = [];

        if ($valeurs['matricule'] === '') {
            $erreurs['matricule'] = 'Le matricule est obligatoire.';
        } elseif (mb_strlen($valeurs['matricule']) > 50) {
            $erreurs['matricule'] = 'Le matricule ne doit pas dépasser 50 caractères.';
        } elseif ($depot->matriculeExiste($valeurs['matricule'], $idExclu)) {
            $erreurs['matricule'] = 'Ce matricule est déjà utilisé.';
        }

        foreach (['nom' => 'Le nom', 'prenom' => 'Le prénom'] as $champ => $libelle) {
            if ($valeurs[$champ] === '') {
                $erreurs[$champ] = $libelle . ' est obligatoire.';
            } elseif (mb_strlen($valeurs[$champ]) > 50) {
                $erreurs[$champ] = $libelle . ' ne doit pas dépasser 50 caractères.';
            }
        }

        if ($valeurs['email'] === '') {
            $erreurs['email'] = "L'email est obligatoire.";
        } elseif (mb_strlen($valeurs['email']) > 100 || !filter_var($valeurs['email'], FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] = "L'email n'est pas valide.";
        } elseif ($depot->emailExiste($valeurs['email'], $idExclu)) {
            $erreurs['email'] = 'Cet email est déjà utilisé.';
        }

        if (mb_strlen($valeurs['telephone']) > 20) {
            $erreurs['telephone'] = 'Le téléphone ne doit pas dépasser 20 caractères.';
        }

        $dateSql = null;
        if ($valeurs['date_naissance'] !== '') {
            $dateSql = self::dateVersSql($valeurs['date_naissance']);
            if ($dateSql === null) {
                $erreurs['date_naissance'] = 'Date invalide (format jj/mm/aaaa).';
            }
        }

        if (!array_key_exists($valeurs['statut'], self::STATUTS)) {
            $erreurs['statut'] = 'Statut invalide.';
        }

        $donnees = $valeurs;
        $donnees['telephone'] = $valeurs['telephone'] === '' ? null : $valeurs['telephone'];
        $donnees['date_naissance'] = $dateSql;

        return ['valeurs' => $valeurs, 'donnees' => $donnees, 'erreurs' => $erreurs];
    }

    /** « jj/mm/aaaa » → « aaaa-mm-jj » (null si la date n'existe pas). */
    public static function dateVersSql(string $texte): ?string
    {
        $date = DateTimeImmutable::createFromFormat('!d/m/Y', $texte);

        if ($date === false || $date->format('d/m/Y') !== $texte) {
            return null;
        }

        return $date->format('Y-m-d');
    }

    /** « aaaa-mm-jj » → « jj/mm/aaaa » (chaîne vide si absente). */
    public static function dateDepuisSql(?string $dateSql): string
    {
        if ($dateSql === null || $dateSql === '') {
            return '';
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $dateSql);

        return $date === false ? '' : $date->format('d/m/Y');
    }
}
