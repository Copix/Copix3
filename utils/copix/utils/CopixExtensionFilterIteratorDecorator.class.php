<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Decorateur pour iterateur capable de filter une liste en fonction des extension d'un fichier
 * @package copix
 * @subpackage utils
 */
class CopixExtensionFilterIteratorDecorator extends FilterIterator {
	/**
	 * extension actuellement filtrée
	 *
	 * @var string
	 */
	private $_ext;
	
	/**
	 * Indique si l'élément courant est accepté
	 *
	 * @return boolean
	 */
	public function accept (){
		if (substr ($this->current (), -1 * strlen ($this->_ext)) === $this->_ext){
			return is_readable ($this->current ());
		}
		return false;
	}

	/**
	 * Définition de l'extension sur laquelle on veut filtrer les données.
	 *
	 * @param string $pExt
	 */
	public function setExtension ($pExt){
		$this->_ext = $pExt;
	}
}