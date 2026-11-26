<?php
declare(strict_types=1);

/** Validation des données du formulaire séance. */
final class SeanceForm
{
    public const TYPES = [
        'Presentiel' => 'Présentiel',
        'Distanciel' => 'Distanciel'
    ];

    /** Valeurs d'un formulaire vide (ajout). */
    public static function valeursVides(): array
    {
        return [
            'dateSeance' => '',
            'typeSeance' => 'Presentiel',
            'idGroupe' => '',
            'idModule' => '',
            'idFormateur' => ''
        ];
    }

    /** Valeurs d'affichage à partir d'une séance existante. */
    public static function valeursDepuisSeance(array $seance): array
    {
        $date = '';

        if (!empty($seance['dateSeance'])) {
            $date = (new DateTimeImmutable($seance['dateSeance']))
                ->format('Y-m-d\TH:i');
        }

        return [
            'dateSeance' => $date,
            'typeSeance' => $seance['typeSeance'],
            'idGroupe' => (string) $seance['idGroupe'],
            'idModule' => (string) $seance['idModule'],
            'idFormateur' => (string) $seance['idFormateur']
        ];
    }

    /**
     * Valide les données postées.
     * Retourne valeurs, donnees et erreurs.
     */
    public static function valider(array $post): array
    {
        $valeurs = [
            'dateSeance' => entree($post, 'dateSeance'),
            'typeSeance' => entree($post, 'typeSeance', 'Presentiel'),
            'idGroupe' => entree($post, 'idGroupe'),
            'idModule' => entree($post, 'idModule'),
            'idFormateur' => entree($post, 'idFormateur')
        ];

        $erreurs = [];

        // Validation de la date
        if ($valeurs['dateSeance'] === '') {
            $erreurs['dateSeance'] = 'La date de séance est obligatoire.';
        } else {
            $date = DateTimeImmutable::createFromFormat(
                '!Y-m-d\TH:i',
                $valeurs['dateSeance']
            );

            if (
                $date === false ||
                $date->format('Y-m-d\TH:i') !== $valeurs['dateSeance']
            ) {
                $erreurs['dateSeance'] = 'Date ou heure invalide.';
            }
        }

        // Validation du type
        if (!array_key_exists($valeurs['typeSeance'], self::TYPES)) {
            $erreurs['typeSeance'] = 'Type de séance invalide.';
        }

        // Validation des identifiants
        foreach (
            ['idGroupe' => 'groupe',
             'idModule' => 'module',
             'idFormateur' => 'formateur'] as $champ => $libelle
        ) {
            if (
                $valeurs[$champ] === '' ||
                filter_var($valeurs[$champ], FILTER_VALIDATE_INT) === false ||
                (int) $valeurs[$champ] <= 0
            ) {
                $erreurs[$champ] = 'Veuillez choisir un ' . $libelle . ' valide.';
            }
        }

        // Données préparées pour la base de données
        $donnees = $valeurs;

        if (!isset($erreurs['dateSeance'])) {
            $date = DateTimeImmutable::createFromFormat(
                '!Y-m-d\TH:i',
                $valeurs['dateSeance']
            );

            $donnees['dateSeance'] = $date->format('Y-m-d H:i:s');
        }

        $donnees['idGroupe'] = (int) $valeurs['idGroupe'];
        $donnees['idModule'] = (int) $valeurs['idModule'];
        $donnees['idFormateur'] = (int) $valeurs['idFormateur'];

        return [
            'valeurs' => $valeurs,
            'donnees' => $donnees,
            'erreurs' => $erreurs
        ];
    }
}