<?php
/**
* @package		cms
* @subpackage	flash
* @author		Croës Gérald, Salleyron Julien
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe facilitant les tests sur les droits pour le module flash
 * @package		cms
 * @subpackage	flash 
 */
class AuthFlash {

	/**
	 * Indique si l'utilisateur courant dispose des droits de lecture sur les documents flash dans la rubrique donnée
	 * @param	string	$pIdHead	l'identifiant de la rubrique ou l'on veut tester les droits de l'utilisateur
	 * @return	boolean
	 */
	public static function canRead ($pIdHead){
		return self::canDo ($pIdHead, PROFILE_CCV_READ);
	}
	
	
	/**
	 * Indique si l'utilisateur courant dispose des droits d'écriture sur les documents flash dans la rubrique donnée
	 * @param	string	$pIdHead	l'identifiant de la rubrique ou l'on veut tester les droits de l'utilisateur
	 * @return	boolean
	 */
	public static function canWrite ($pIdHead){
		return self::canDo ($pIdHead, PROFILE_CCV_WRITE);
	}
	
	/**
	 * Test de droits dans une rubrique pour l'utilisteur courant
	 * @param	string	$pIdHead	la rubrique dans laquelle on veut tester les droits
	 * @param 	int		$pWhat		le niveau de droit que l'on souhaite tester
	 */
	public static function canDo ($pIdHead, $pWhat){
      $servicesHeading  = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      return CopixUserProfile::valueOf ('flash', $servicesHeading->getPath ($pIdHead)) >= $pWhat;
	}
}
?>