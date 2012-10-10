<?php
/**
* @package		copix
* @subpackage	utils
* @author		Bertrand Yan
* @copyright	2001-2005 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Pagination à partir d'un tableau de données.
 * @package copix
 * @subpackage utils
 */
class CopixArrayPager extends CopixPager {

	/**
     * Tableau des données à traiter
     *
     * Valeur par défaut : tableau vide
     * @var string $query
     * @see createSQL(), sql2array()
     */
	var $recordSet;

	/**
	 * COnstructeur
	 */
	public function __construct($options) {
		$this-> recordSet = array ();
		parent::CopixPager($options);
	}


	/**
     * Retourne le nombre d'enregistrement contenu dans le tableau des données
     *
     * @access private
     * @since 3.2
     */
	public function getNbRecord() {
		return count($this-> recordSet);
	}

	/**
     * Retourne le tableau des données "découpé"
     *
     * @access private
     * @return array
     * @since 3.2
     */
	public function getRecords() {

		$aTmp = Array();
		if (count ($this-> recordSet) > 0){
			for ($i = $this-> firstline; $i < ($this-> firstline + $this-> perPage); $i++) {
				$aTmp[$i] = $this-> recordSet[$i];
	
				if (!isSet($this-> recordSet[$i+1])) {
					break;
				}
			}
		}

		return $aTmp;
	}

	/**
     * Initialisation de la classe mode tableau
     *
     * @access private
     * @return void
     * @since 3.2
      */
	public function init() {
		if (!is_array($this-> recordSet)) trigger_error('Propriété <b>recordSet</b> mal configurée <br>', E_USER_ERROR);
	}

	/**
     * Termine l'appel à la classe
     *
     * @access public
     */
	public function close() {
		unset($this-> recordSet);
		return true;
	}
}
?>