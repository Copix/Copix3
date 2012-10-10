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
interface IWikiData {

	/**
	 * Récupérer le contenu d'une page
	 *
	 * @param string $pWikiname
	 * @param int pRevision
	 */
	public function getContent ($pWikiname, $pRevision = null);

	/**
	 * Met un contenu (wiki) dans la base
	 *
	 * @param string $pWikiname
	 * @param string $pContent
	 * @param string $pComment
	 */
	public function addContent ($pWikiname, $pContent, $pAuthor = 'anonymous', $pComment = null);
	
	/**
	 * Récupérer les différences entre deux pages
	 *
	 * @param int $pRevisionFirst
	 * @param int $pRevisionLast
	 */
	public function getDiff ($pRevisionFirst, $pRevisionLast);
	
	/**
	 * Récupération des tags d'une page
	 *
	 * @param string $pWikiName
	 */
	public function getTags ($pWikiName);
	
	/**
	 * Assignation de tags à une page
	 *
	 * @param string $pWikiName
	 * @param array $pArTags
	 */
	public function setTags ($pWikiName, $pArTags);
	
	/**
	 * Récupérer les fichiers attachés à une page
	 *
	 * @param string $pWikiName
	 */
	public function getAttachment ($pWikiName);

	/**
	 * Attacher un fichier à une page
	 *
	 * @param string $pWikiName
	 * @param string $pFilename
	 */
	public function setAttachment ($pWikiName, $pFilename);

	/**
	 * Récupération de la liste des traductions de la page
	 *
	 * @param string $pWikiName
	 * @return array tableau contenant la liste des pages traduites
	 */
	public function getTranslation ($pWikiName);
	
	/**
	 * Mise en place d'une information de traduction
	 *
	 * @param string $pWikiName
	 * @param string $pWikiNameTrans
	 * @param string $plang
	 */
	public function setTranslation ($pWikiName, $pWikinameTrans,  $pLang);

	/**
	 * Récupérer les commentaires liés à une page et une revision
	 *
	 * @param string $pWikiName
	 * @param int $pRevision
	 */
	public function getComment ($pWikiName, $pRevision);
}
?>