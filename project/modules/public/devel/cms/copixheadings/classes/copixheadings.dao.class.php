<?php
/**
* @package		cms
* @subpackage copixheadings
* @author		Croes Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Classe enregistrement
* @package cms
* @subpackage copixheadings
*/
class DAORecordCopixHeadings {
    /**
    * says if the given heading is a new one or an existing one.
    * @return boolean

    */
    function isNew (){
        return $this->id_head === null;
    }
}

/**
 * DAO pour les rubriques
 * @package cms
 * @subpackage copixheadings 
 */
class DAOCopixHeadings {
	/**
	 * Récupération de la liste des noms de domaines pris en charge
	 * @return array of string
	 */
	function getDomainList (){
		$toReturn = array ();		
		foreach (CopixDB::getConnection()->doQuery ('select distinct(url_head) from copixheadings') as $result){
			$toReturn[] = $result->url_head;
		}
		return $toReturn;
	}
}
?>