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
* @package		cms
* @subpackage	flash
* DAO
*/
class DAOFlash {
	/**
	 * Recherche des dernières versions des documents flash dans une rubrique donnée
	 * @param	string	$pIdHead	l'identifiant de rubrique ou l'on va chercher les documents flash
	 */
	public function findAllLastVersionByHeading ($pIdHead){
       return $this->_distinct ($this->findAll ());
	}
	
	/**
    * Récupération de la dernière version du document
    *
    * @param string $id_doc l'identifiant du document dont on souhaites connaitre la dernière version
    * @return string la version du document, null si non trouvé
    */ 
	function getLastVersion ($pIdDoc){
		$query = 'select MAX(version_flash) as lastversion from flash where id_flash='.(is_numeric ($pIdDoc) ? $pIdDoc : intval($pIdDoc));
		$results = CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
		return $results[0]->lastversion;
	}
	
    /**
     * Récupération d'un enregistrement flash en fonction de sa dernière version
     * @param	string	$pIdFlash	l'identifiant de l'élément
     */
    function getByLastVersion ($pIdFlash){
    	return $this->get ($pIdFlash, $this->getLastVersion ($pIdFlash));
    }
    
	/**
	 * Filtrage des dernières versions
	 */
	function _distinct ($pRecords){
		$toReturn = array ();
		foreach ($pRecords as $record){
			if (! isset ($toReturn[$record->id_flash])){
				$toReturn[$record->id_flash] = $record;
			}else{
				if ($toReturn[$record->id_flash]->version_flash < $record->version_flash){
					$toReturn[$record->id_flash] = $record;
				}
			}
		}
		return $toReturn;
	}
}
?>
