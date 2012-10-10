<?php
/**
* @package   copix
* @author	<o.veujoz@miasmatik.net>, Bertrand Yan
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 *
 * Fichier de configuration de la classe CopixPager pour un projet entier
 * Ce fichier sert de valeurs par défaut pour un projet entier, mais rien n'empêche de les paramétrer directement
 * lors de la création d'un objet Multipage
 *
 */


/**************************************
 * Paramètrage global du multipage
 **************************************/

$options['PAGER'] = Array(
    'perPage'        => 10,                 // Nombre de résultats par page
    'delta'          => 5,                  // Le nombre de liens maximum souhaité dans le multipage (0 = tous les liens)
    'alwaysShow'     => true,               // Que faire si le multipage n'est pas nécessaire? L'afficher ou non?
    'toHtmlEntities' => false,              // Positionné à true, les caractères spéciaux des libellés seront traduits en leur entité HTML
    'encodeVarUrl'   => false,              // Positionné à true, les paramètres passés par Url seront encodés
    'display'        => 'sliding'           // 'sliding' || 'jumping'
);



/****************************
 * Paramètrage divers
 ****************************/

$options['PARAMS'] = Array(
    'nextPage'        => '&nbsp;<img src="'.CopixUrl::get ().'/img/tools/next.png" alt="next"/>&nbsp;',         // libellé lien vers la page suivante
    'previousPage'    => '&nbsp;<img src="'.CopixUrl::get ().'/img/tools/back.png" alt="previous"/>&nbsp;',       // libellé lien vers la page précédente
    'lastPage'        => '&nbsp;<img src="'.CopixUrl::get ().'img/tools/last.png" alt="last"/>',              // libellé lien vers la dernière page
    'firstPage'       => '<img src="'.CopixUrl::get ().'img/tools/first.png" alt="first"/>&nbsp;',              // libellé lien vers la première page
    'separator'       => '&nbsp;-&nbsp;',   // Séparateur de page
    'curPageSpanPre'  => '<b>',          // Chaine préfixant la page courante
    'curPageSpanPost' => '</b>',        // Chaine suffixant la page courante
    'linkClass'       => 'multipage'    // Classe CSS à ajouter aux liens
);


/**************************************************************************************************
 * Configuration avancée (optionnel, vous pouvez laisser les paramètres par défaut.
 * C'est uniquement pour ceux qui aiment bidouiller.)
 **************************************************************************************************/

$options['ADVANCED'] = Array(
    'varUrl'             => 'p',            // Nom de la variable dans l'url servant à indiquer la page en cours
);
?>