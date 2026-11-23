<?php
declare(strict_types=1);

/**
 * Génère la navigation « ‹ Précédent 1 2 Suivant › ».
 * $lien reçoit un numéro de page et retourne l'URL correspondante.
 */
function rendre_pagination(int $page, int $nombrePages, Closure $lien): string
{
    $html = '<nav class="pagination" aria-label="Pagination">';

    $html .= $page > 1
        ? '<a href="' . e($lien($page - 1)) . '">‹ Précédent</a>'
        : '<span class="desactive">‹ Précédent</span>';

    for ($numero = 1; $numero <= $nombrePages; $numero++) {
        $html .= $numero === $page
            ? '<span class="courante" aria-current="page">' . $numero . '</span>'
            : '<a href="' . e($lien($numero)) . '">' . $numero . '</a>';
    }

    $html .= $page < $nombrePages
        ? '<a href="' . e($lien($page + 1)) . '">Suivant ›</a>'
        : '<span class="desactive">Suivant ›</span>';

    return $html . '</nav>';
}
