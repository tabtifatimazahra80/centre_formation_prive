<?php
declare(strict_types=1);

require_once __DIR__ . '/FormateurRepository.php';

/** Validation des données du formulaire formateur. */
final class FormateurForm
{
    public const STATUTS = ['actif' => 'Actif', 'inactif' => 'Inactif'];

    /** Valeurs d'un formulaire vide (ajout). */
    public static function valeursVides(): array
    {
        return [
            'matricule'  => '',
            'nom'        => '',
            'prenom'     => '',
            'email'      => '',
            'specialite' => '',
            'statut'     => 'actif',
        ];
    }

    /** Valeurs d'affichage à partir d'un formateur de la base (modification). */
    public static function valeursDepuisFormateur(array $formateur): array
    {
        return [
            'matricule'  => $formateur['matricule'] ?? '',
            'nom'        => $formateur['nom'],
            'prenom'     => $formateur['prenom'],
            'email'      => $formateur['email'],
            'specialite' => $formateur['specialite'] ?? '',
            'statut'     => $formateur['statut'],
        ];
    }

    /**
     * Valide les données postées.
     * Retourne ['valeurs' => à réafficher, 'donnees' => prêtes pour la base, 'erreurs' => par champ].
     * $idExclu : formateur en cours de modification (ignoré dans les contrôles d'unicité).
     */
    public static function valider(array $post, FormateurRepository $depot, ?int $idExclu = null): array
    {
        $valeurs = [
            'matricule'  => entree($post, 'matricule'),
            'nom'        => entree($post, 'nom'),
            'prenom'     => entree($post, 'prenom'),
            'email'      => entree($post, 'email'),
            'specialite' => entree($post, 'specialite'),
            'statut'     => entree($post, 'statut', 'actif'),
        ];
        $erreurs = [];

        if ($valeurs['matricule'] === '') {
            $erreurs['matricule'] = 'Le matricule est obligatoire.';
        } elseif (mb_strlen($valeurs['matricule']) > 100) {
            $erreurs['matricule'] = 'Le matricule ne doit pas dépasser 100 caractères.';
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

        if (mb_strlen($valeurs['specialite']) > 100) {
            $erreurs['specialite'] = 'La spécialité ne doit pas dépasser 100 caractères.';
        }

        if (!array_key_exists($valeurs['statut'], self::STATUTS)) {
            $erreurs['statut'] = 'Statut invalide.';
        }

        $donnees = $valeurs;
        $donnees['specialite'] = $valeurs['specialite'] === '' ? null : $valeurs['specialite'];

        return ['valeurs' => $valeurs, 'donnees' => $donnees, 'erreurs' => $erreurs];
    }
}
