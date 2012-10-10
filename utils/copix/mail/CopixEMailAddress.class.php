<?php
/**
 * @package copix
 * @subpackage mail
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Adresse email
 *
 * @package copix
 * @subpackage mail
 */
class CopixEMailAddress {
	/**
	 * Nom de la boite mail (partie avant le @)
	 *
	 * @var string
	 */
	private $_mailBox = null;
	
	/**
	 * Nom de domaine (partie après le @)
	 *
	 * @var string
	 */
	private $_host = null;
	
	/**
	 * Adresse mail
	 *
	 * @var string
	 */
	private $_address = null;
	
	/**
	 * Nom de la personne
	 *
	 * @var string
	 */
	private $_name = null;
	
	/**
	 * Constructeur
	 *
	 * @param string $pAddress Adresse mail
	 * @param string $pName Nom de la personne
	 */
	public function __construct ($pAddress = null, $pName = null) {
		$this->setAddress ($pAddress);
		$this->setName ($pName);
	}
	
	/**
	 * Définit l'adresse mail
	 *
	 * @param string $pAddress Adresse mail
	 */
	public function setAddress ($pAddress) {
		list ($this->_mailBox, $this->_host) = explode ('@', $pAddress);
		$this->_address = $pAddress;
	}
	
	/**
	 * Retourne l'adresse mail
	 *
	 * @return string
	 */
	public function getAddress () {
		return $this->_address;
	}
	
	/**
	 * Retourne l'adresse mail complète (Exemple : Mon prénom <adresse@mail.com>)
	 *
	 * @return string
	 */
	public function getFullAddress () {
		return ($this->getName() !== null) ? $this->getName() . ' <' . $this->getAddress () . '>' : $this->getAddress ();
	}
	
	/**
	 * Définit le nom de la personne
	 *
	 * @param string $pName Nom de la personne
	 */
	public function setName ($pName) {
		$this->_name = $pName;
	}
	
	/**
	 * Retourne le nom de la personne
	 *
	 * @return string
	 */
	public function getName () {
		return $this->_name;
	}
	
	/**
	 * Retourne le nom de la boite mail (partie avant le @)
	 *
	 * @return string
	 */
	public function getMailBox () {
		return $this->_mailBox;
	}
	
	/**
	 * Retourne le nom de domaine (partie après le @)
	 *
	 * @return string
	 */
	public function getHost () {
		return $this->_host;
	}
}