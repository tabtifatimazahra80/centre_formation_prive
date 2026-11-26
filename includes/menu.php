<?php

/**
 * Menu latéral selon le rôle.
 * Les rubriques fonctionnelles sont accessibles directement.
 */


function menu_pour_role(string $role): array
{
    return match ($role) {
        'administrateur' => [
            ['cle' => 'accueil',    'libelle' => 'Tableau de bord', 'page' => 'accueil.php'],
            ['cle' => 'etudiants',  'libelle' => 'Étudiants',       'page' => 'etudiants/liste.php', 'description' => 'Gérer les étudiants'],
            ['cle' => 'formateurs', 'libelle' => 'Formateurs',      'page' => 'formateurs/liste.php', 'description' => 'Gérer les formateurs'],
            ['cle' => 'groupes',    'libelle' => 'Groupes',         'page' => 'groupes/liste.php', 'description' => 'Gérer les groupes'],
            ['cle' => 'modules',    'libelle' => 'Modules',         'page' => 'a_venir.php?r=modules', 'description' => 'Gérer les modules'],
            ['cle' => 'seances', 'libelle' => 'Séances', 'page' => 'seances/listes.php', 'description' => 'Gérer les séances'],
        ],

        'formateur' => [
            ['cle' => 'mes_groupes', 'libelle' => 'Mes groupes et modules', 'page' => 'formateur/mes_groupes.php'],
            ['cle' => 'seances',     'libelle' => 'Séances',                'page' => 'a_venir.php?r=seances'],
            ['cle' => 'presences',   'libelle' => 'Présences',              'page' => 'a_venir.php?r=presences'],
            ['cle' => 'evaluations', 'libelle' => 'Évaluations',            'page' => 'a_venir.php?r=evaluations'],
        ],

        'stagiaire' => [
            ['cle' => 'informations', 'libelle' => 'Mes informations', 'page' => 'a_venir.php?r=informations'],
            ['cle' => 'presences',    'libelle' => 'Mes présences',    'page' => 'a_venir.php?r=presences'],
            ['cle' => 'resultats',    'libelle' => 'Mes résultats',    'page' => 'a_venir.php?r=resultats'],
        ],

        default => [],
    };
}