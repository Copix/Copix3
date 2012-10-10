<?php
/**
 * @package		tools
 * @subpackage	breadcrumb
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author		Steevan BARBOYON
 * @link		http://www.copix.org
 */

/**
 * Sauvegarde les informations sur le fil d'ariane indiqué vi l'événement breadcrumb
 * 
 * @package		tools
 * @subpackage	breadcrumb
 */
class BreadCrumb {
	/**
	 * Informations sur le fil d'ariane
	 *
	 * @var array Clef : CopixURL, valeur : libellé du lien
	 */
	private $_breadCrumb = array ();
	
	/**
	 * Indique si on veut afficher le dernier lien ou pas
	 *
	 * @var boolean
	 */
	private $_showLastLink = false;
	
	/**
	 * Retourne le nombre d'éléments du fil d'ariane
	 *
	 * @param string $pId Identifiant du fil d'ariane
	 * @return int
	 */
	public function count ($pId = 'default') {
		return count ($this->get ($pId));
	}
	
	/**
	 * Retourne le fil d'ariane
	 *
	 * @param string $pId Indentifiant du fil d'ariane
	 * @return BredCrumbLink[]
	 */
	public function get ($pId = 'default') {
		return (array_key_exists ($pId, $this->_breadCrumb)) ? $this->_breadCrumb[$pId] : array ();
	}
	
	/**
	 * Ajoute des chemins au fil d'ariane
	 * 
	 * @param $pBreadCrumbLink $pBreadCrumbLink Informations sur le lien
	 * @param string $pId Identifiant du fil d'ariane auquel on veut ajouter ce lien
	 * @throws ModuleBreadCrumbException Breadcrumb à ajouter invalide, code ModuleBreadCrumbException::INVALID_LINK
	 */
	public function add ($pBreadCrumbLink, $pId = 'default') {
		if ($pBreadCrumbLink instanceof BreadCrumbLink) {
			$this->_breadCrumb[$pId][] = $pBreadCrumbLink;
		} else {
			throw new ModuleBreadCrumbException (_i18n ('breadcrumb|module.error.invalidBreadcrumb'), ModuleBreadCrumbException::INVALID_LINK);
		}
	}
	
	/**
	 * Réinitialise les chemins du fil d'ariane
	 * 
	 * @param string $pId Indentifiant du fil d'ariane
	 */
	public function reset ($pId = 'default') {
		if (array_key_exists ($pId, $this->_breadCrumb)) {
			$this->_breadCrumb[$pId] = array ();
		}
	}
	
	/**
	 * Définition de l'affichage du dernier lien
	 *
	 * @param boolean $pShow Indique si on veut afficher le dernier lien ou pas
	 * @param string $pId Identifiant du fil d'ariane
	 */
	public function setShowLastLink ($pShow, $pId = 'default') {
		$this->_showLastLink = $pShow;
	}
	
	/**
	 * Retourne l'affichage du dernier lien ou pas
	 *
	 * @param string $pId Identifiant du fil d'ariane
	 * @return boolean
	 */
	public function getShowLastLink ($pId = 'default') {
		return $this->_showLastLink;
	}
}