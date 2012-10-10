<?php
/**
 * @package	cms3
 * @author	 Vuidart Sylvain
 * @copyright 2001-2009 CopixTeam
 * @link      http://copix.org
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe qui encapsule la gestion des droits du CMS.
 * @package cms3
 */
class HeadingElementCredentials {

	/**
	 * Aucun droit
	 */
	const NONE    = 0;

	/**
	 * Affichage uniquement (mais pas consultation de la donnée, on sait juste qu'elle existe)
	 */
	const SHOW    = 10;

	/**
	 * Lecture de la donnée
	 */
	const READ    = 20;

	/**
	 * Ecriture de la donnée
	 */
	const WRITE   = 30;

	/**
	 * Validation de la donnée
	 */
	const PUBLISH   = 40;

	/**
	 * Administration complète des données
	 */
	const MODERATE = 50;


	/**
	 * Indique si l'on peut voir l'élément
	 * @param int $pPublicIdHei id de l'element
	 * @return boolean a les droits ou non
	 */
	public static function canShow ($pPublicIdHei = null){
		return self::_canDo (self::SHOW, $pPublicIdHei);
	}

	/**
	 * Indique si l'on peut lire l'élément
	 * @param int $pPublicIdHei id de l'element
	 * @return boolean a les droits ou non
	 */
	public static function canRead ($pPublicIdHei = null){
		return self::_canDo (self::READ, $pPublicIdHei);
	}

	/**
	 * Indique si l'on peut écrire des élément dans une rubrique ou modifier l'element
	 * @param int $pPublicIdHei id de l'element
	 * @return boolean a les droits ou non
	 */
	public static function canWrite ($pPublicIdHei = null){
		return self::_canDo (self::WRITE, $pPublicIdHei);
	}

	/**
	 * Indique si l'on peut publier d'élément
	 * @param int $pPublicIdHei id de l'element
	 * @return boolean a les droits ou non
	 */
	public static function canPublish ($pPublicIdHei = null){
		return self::_canDo (self::PUBLISH, $pPublicIdHei);
	}

	/**
	 * Indique si l'on peut modérer un element
	 * @param int $pPublicIdHei id de l'element
	 * @return boolean a les droits ou non
	 */
	public static function canModerate ($pPublicIdHei = null){
		return self::_canDo (self::MODERATE, $pPublicIdHei);
	}

	/**
	 * Indique si l'on dispose d'un niveau de droit pour un element
	 * @param int $pRightLevel id du droit
	 * @param int $pPublicIdHei id de l'element
	 * @return boolean a les droits ou non
	 */
	public static function _canDo ($pRightLevel, $pPublicIdHei){
		return CopixAuth::getCurrentUser()->testCredential("basic:admin") || 
                        CopixAuth::getCurrentUser()->testCredential("cms:$pRightLevel@$pPublicIdHei");
	}


	/**
	 * Retourne le libelle d'un droit
	 *
	 * @param int $pRightLevel
	 * @return String
	 */
	public function getCaption ($pRightLevel){
		switch ($pRightLevel){
			case self::NONE :
				return "Aucun";
			case self::READ :
				return "Lire";
			case self::WRITE :
				return "Ecrire";
			case self::SHOW :
				return "Voir";
			case self::MODERATE :
				return "Administrer";
			case self::PUBLISH :
				return "Publier";
			default:
				return "-- Heriter de la rubrique parente --";
		}
	}

	/**
	 * Retourne la liste des droits définis dans le CMS
	 *
	 * @return array
	 */
	public function getList (){
		$arRights = array (
		self::NONE => $this->getCaption(self::NONE),
		self::SHOW => $this->getCaption(self::SHOW),
		self::READ => $this->getCaption(self::READ),
		self::WRITE => $this->getCaption(self::WRITE),
		self::PUBLISH => $this->getCaption(self::PUBLISH),
		self::MODERATE => $this->getCaption(self::MODERATE)
		);
		return $arRights;
	}
}