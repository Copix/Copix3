<?php
/**
 * @package		webtools
 * @subpackage	wiki
 * @author		Brice Favre
 * @copyright 	2001-2008 CopixTeam
 * @link      	http://www.copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe d'interfaces des services
 * @package	webtools
 * @subpackage	wiki
 */
_classInclude ('wiki|iwikidata');

class WikiDataDb implements iWikiData {

	/**
	 * Récupérer le contenu d'une page
	 *
	 * @param string $pWikiname
	 * @param int pRevision
	 */
	public function getContent ($pWikiname, $pRevision = null){
		$sp = _daoSp ();

		if ($pRevision != null) {
			// Si la revision est différente de null on la cherche
			$sp = $sp->addCondition ('version', '=', $pRevision);
		} else {
			// Sinon on cherche la derniere
			$sp = $sp->orderBy(array('version', 'desc'));
		}
		// Fin de la création du search params
		$sp = $sp->addCondition ('name', '=', $pWikiname)->setLimit (0,1);
		 
		$results = _ioDao ('wiki')->findBy ($sp);
		if (isset ($results[0])) {
			return $results[0]->text;
		}
		return false;
			
	}

	/**
	 * Met un contenu (wiki) dans la base
	 *
	 * @param string $pWikiname
	 * @param string $pContent
	 * @param string $pComment
	 */
	public function addContent ($pWikiname, $pContent, $pAuthor = 'anonymous', $pComment = null){
		$addedRecord = _record ('wiki');
		
		// Intégration des paramètres et valeur calculable
		$addedRecord->name 		= $pWikiname;
		$addedRecord->time 		= time ();
		$addedRecord->author 	= $pAuthor;
		$addedRecord->ipnr 		= $_SERVER['REMOTE_ADDR'];
		$addedRecord->text 		= $pContent;
		$addedRecord->comment 	= $pComment;
		
		// Recherche de la dernière version
		$result = _ioDao ('wiki')->findBy (_daoSp()->orderBy(array('version', 'desc'))
		                                 ->addCondition ('name', '=', $pWikiname)
		                                 ->setLimit (0,1));

		if (isset($result[0])) {
			$addedRecord->version = ++$result[0]->version;
		} else {
			$addedRecord->version = 1;
		}
		
		return _ioDao ('wiki')->insert ($addedRecord);
	}

	/**
	 * Récupérer les différences entre deux pages
	 *
	 * @param int $pRevisionFirst
	 * @param int $pRevisionLast
	 */
	public function getDiff ($pRevisionFirst, $pRevisionLast){

	}

	/**
	 * Récupération des tags d'une page
	 *
	 * @param string $pWikiName
	 */
	public function getTags ($pWikiName){

	}

	/**
	 * Assignation de tags à une page
	 *
	 * @param string $pWikiName
	 * @param array $pArTags
	 */
	public function setTags ($pWikiName, $pArTags){

	}

	/**
	 * Récupérer les fichiers attachés à une page
	 *
	 * @param string $pWikiName
	 */
	public function getAttachment ($pWikiName){

	}

	/**
	 * Attacher un fichier à une page
	 *
	 * @param string $pWikiName
	 * @param string $pFilename
	 */
	public function setAttachment ($pWikiName, $pFilename){

	}

	/**
	 * Récupération de la liste des traductions de la page
	 *
	 * @param string $pWikiName
	 * @return array tableau contenant la liste des pages traduites
	 */
	public function getTranslation ($pWikiName){

	}

	/**
	 * Mise en place d'une information de traduction
	 *
	 * @param string $pWikiName
	 * @param string $pWikiNameTrans
	 * @param string $plang
	 */
	public function setTranslation ($pWikiName, $pWikinameTrans,  $pLang){

	}

	/**
	 * Récupérer les commentaires liés à une page et une revision
	 *
	 * @param string $pWikiName
	 * @param int $pRevision
	 */
	public function getComment ($pWikiName, $pRevision){

	}
}