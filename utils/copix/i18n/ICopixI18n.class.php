<?php
/**
 * @package copix
 * @subpackage auth
 * @author Croës Gérald
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Interface des classes décrivant un sytème d'internatinalisation
 * 
 * @package copix
 * @subpackage i18n
 */
interface ICopixI18N {
	
	/**
	 * Retourne le message conrespondant à la clef, pour la langue $pLocale, ou la langue courante, ou la langue par défaut
	 * 
	 * @param string $pKey Clef
	 * @param mixed String ou array, paramètre(s) %s à remplacer dans le message
	 * @param string $pLocale Force à retourne ce couple langue_PAYS
	 * @return string
	 */
	public function get ($pKey, $pArgs = null, $pLocale = null);
	
	/**
	 * Indique si la clef $pKey existe
	 * 
	 * @param string $pKey Clef
	 * @param string $pLocale Couple langue_PAYS dont on veut vérifier l'existance, null pour le couple courant
	 * @return bool
	 */
	public function exists ($pKey, $pLocale = null);
	
}