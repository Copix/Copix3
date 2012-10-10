<?php
/**
* @package cms
* @subpackage document
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package cms
* @subpackage  document
* Accès aux données de la base documents
*/
class DAODocument {
	/**
    * Move a document and all it version in a new heading
    * @param string $id_doc
    * @param int $to
    */
	function moveHeading ($id_doc, $to) {
		$query  = 'update document set id_head='.($to === null ? 'NULL' : (is_numeric ($to) ? $to : intval ($to))).' where ';
		$query .= 'id_doc = '.$id_doc;

		CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
	}

	/**
    * Récupération de la dernière version du document
    *
    * @param string $id_doc l'identifiant du document dont on souhaites connaitre la dernière version
    * @return string la version du document, null si non trouvé
    */ 
	function getLastVersion ($id_doc){
		$query = 'select MAX(version_doc) as lastversion from document where id_doc='.(is_numeric ($id_doc) ? $id_doc : intval($id_doc));
		$results = CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
		return $results[0]->lastversion;
	}

	/**
    * Supression d'un document
    * @param string $id_doc l'identifiant du document à supprimer
    */
	function deleteById ($id_doc){
		$query = 'delete from document where id_doc='.(is_numeric ($id_doc) ? $id_doc : intval($id_doc));
		return CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
	}

	/**
    * Récupération des documents pour l'utilisateur courant
    * @param string $status le statut des documents que l'on souhaite récupérer.
    * @param string $id_head l'identifiant de la rubrique dans laquelel on va rechercher les documents
    */
	function getDocumentByUser ($status, $id_head) {
		$login      = CopixUserProfile::getLogin ();
		$sp        = CopixDAOFactory::createSearchParams ();
		$sp->addCondition ('id_head'   , '=', $id_head);
		$sp->addCondition ('status_doc', '=', $status);
		$sp->addCondition ('author_doc', '=', $login);
		return $this->_distinct ($this->findBy ($sp));
	}

	/**
    * Gets the document by status and author
    */
	function getDocumentByStatusAuthor ($status, $id_head, $login=null) {
		if ($login === null) {
			$login      = CopixUserProfile::getLogin ();
		}

		$sp = CopixDAOFactory::createSearchParams ();
		$sp->addCondition ('id_head'         , '=', $id_head);
		$sp->addCondition ('status_doc'      , '=', $status);
		$sp->addCondition ('statusauthor_doc', '=', $login);
		return $this->_distinct ($this->findBy ($sp));
	}

	function getDocumentByStatus ($status, $id_head) {
		$sp = CopixDAOFactory::createSearchParams ();
		$sp->addCondition ('id_head'   , '=', $id_head);
		$sp->addCondition ('status_doc', '=', $status);
		return $this->_distinct ($this->findBy ($sp));
	}

	function _distinct ($records){
		$toReturn = array ();
		foreach ($records as $record){
			if (! isset ($toReturn[$record->id_doc])){
				$toReturn[$record->id_doc] = $record;
			}else{
				if ($toReturn[$record->id_doc]->version_doc < $record->version_doc){
					$toReturn[$record->id_doc] = $record;
				}
			}
		}
		return $toReturn;
	}
}
?>
