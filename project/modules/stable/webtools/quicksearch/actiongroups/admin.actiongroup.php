<?php
/**
 * @package		webtools
 * @subpackage	quicksearch
 * @author	Gérald Croës
 * @copyright CopixTeam
 * @link      http://copix.org
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Opérations d'administration sur le moteur de recherche
 * @package		webtools
 * @subpackage	quicksearch
 */
class ActionGroupAdmin extends CopixActionGroup {
	/**
	 * Page de base pour afficher la progression de l'indexation
	 */
	public function processDefault (){
		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>_i18n ('admin.indexing'))), 'indexing.php');
	}

	/**
	 * Page qui va préparer la demande de réindexation globale des contenus du site.
	 */
	public function processPrepareIndexAll (){
		//Supression de tout l'index
		_ioDAO ('quicksearchindex')->deleteBy (_daoSp ());
	
		//Création du ppo de base
		$ppo = new CopixPPO ();
		$ppo->arLinks = array ();
		
		//on simule pour le moment afin de tester la barre de progression
		$response = _notify ('ListContent');
		foreach ($response->getResponse () as $key=>$responses){
			if (isset ($responses['url']) && is_array ($responses['url'])){
				$ppo->arLinks = array_merge ($ppo->arLinks, $responses['url']);
			}
		}
		return _arDirectPpo ($ppo, 'prepareindexall.response.xml', array ('content-type'=>CopixMimeTypes::getFromExtension ('.xml')));
	}
}
?>