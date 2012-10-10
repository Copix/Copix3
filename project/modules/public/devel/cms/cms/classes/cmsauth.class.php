<?php
/**
* @package	cms
* @author	 Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Classe qui encapsule la gestion des droits du CMS.
* @package cms
*/
class CMSAuth {
	/**
	* Indique si l'on peut voir les élément d'une rubrique 
	* @param int $pInHead la rubrique dans lauquelle on veut tester les droits.
	* @return boolean a les droits ou non
	*/
	public static function canShow ($pInHead = null){
		return self::_canDo (PROFILE_CCV_SHOW, $pInHead);
	}
	
	/**
	* Indique si l'on peut lire les élément d'une rubrique 
	* @param int $pInHead la rubrique dans lauquelle on veut tester les droits.
	* @return boolean a les droits ou non
	*/
	public static function canRead ($pInHead = null){
		return self::_canDo (PROFILE_CCV_READ, $pInHead);
	}

	/**
	* Indique si l'on peut écrire des élément dans une rubrique 
	* @param int $pInHead la rubrique dans lauquelle on veut tester les droits.
	* @return boolean a les droits ou non
	*/
	public static function canWrite ($pInHead = null){
		return self::_canDo (PROFILE_CCV_WRITE, $pInHead);
	}
	
	/**
	* Indique si l'on peut valider des élément dans une rubrique 
	* @param int $pInHead la rubrique dans lauquelle on veut tester les droits.
	* @return boolean a les droits ou non
	*/
	public static function canValidate ($pInHead = null){
		return self::_canDo (PROFILE_CCV_VALID, $pInHead);
	}

	/**
	* Indique si l'on peut publier des élément dans une rubrique 
	* @param int $pInHead la rubrique dans lauquelle on veut tester les droits.
	* @return boolean a les droits ou non
	*/
	public static function canPublish ($pInHead = null){
		return self::_canDo (PROFILE_CCV_PUBLISH, $pInHead);
	}

	/**
	* Indique si l'on peut modérer les éléments d'une une rubrique 
	* @param int $pInHead la rubrique dans lauquelle on veut tester les droits.
	* @return boolean a les droits ou non
	*/
	public static function canModerate ($pInHead = null){
		return self::_canDo (PROFILE_CCV_MODERATE, $pInHead);
	}

	/**
	* Indique si l'on dispose d'un niveau de droit donné dans une rubrique
	* @param int $pRightLevel la rubrique dans lauquelle on veut tester les droits.
	* @param int $pInHead la rubrique dans lauquelle on veut tester les droits.
	* @return boolean a les droits ou non
	*/
	public static function _canDo ($pRightLevel, $pInHead){
      $servicesHeading  = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      return CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($pInHead)) >= $pRightLevel;
	}
	
	/**
	* Récupère l'objet utilisateur courant.
	* @return CopixUser
	*/
	public static function getUser (){
      return CopixController::instance ()->getPlugin ('auth|auth')->getuser ();
	}
}
?>