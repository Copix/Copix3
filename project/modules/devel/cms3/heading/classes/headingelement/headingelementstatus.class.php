<?php
/**
 * @package    standard
 * @subpackage heading
 * @author     Gérald CROËS, Alexandre JULIEN
 * @copyright  2001-2008 CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Représentation des status possibles dans le workflow des contenus
 */
class HeadingElementStatus {
	/**
	 * Constantes correspondants aux statuts possibles pour les éléments du CMS
	 */
	const DRAFT = 0; 
	const PROPOSED = 1;
	const PLANNED = 2;
	const PUBLISHED = 3;
	const ARCHIVE = 4;
	const DELETED = 5;

	
	/**
	 * Retourne le libellé du statut
	 *
	 * @param String $pStatus : Identifiant du status
	 * @return unknown Libellé du statut
	 */
	public function getCaption ($pStatus) {
		
		if (array_key_exists ($pStatus, $status = self::_getArray ())){
			return $status[$pStatus]; 
		}
		throw new CopixException (_i18n('heading|heading.status.error.statusnotvalid'));
	}
	
	/**
	 * Retourne un tableau contenant les status et leurs libellés
	 *
	 * @return array () $_status Tableau associatif des constantes
	 */
	public function getList () {
		return $this->_getArray ();
	}
	
	/**
	 * Retourne un tableau contenant les clés I18N des libellés des statuts possibles
	 *
	 * @return array ()
	 */
	private function _getArray (){
		return array (self::DRAFT	 	=> _i18n ('heading|heading.status.draft'),
					  self::PROPOSED 	=> _i18n ('heading|heading.status.proposed'),
					  self::PLANNED     => _i18n ('heading|heading.status.planned'),
					  self::PUBLISHED	=> _i18n ('heading|heading.status.published'),
					  self::ARCHIVE		=> _i18n ('heading|heading.status.archive'),
					  self::DELETED		=> _i18n ('heading|heading.status.deleted')
					);
	}
}