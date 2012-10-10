<?php
/**
 * @package webtools
 * @subpackage search
 * @author Gérald Croës, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Administration des moteurs de recherche
 *
 * @package webtools
 * @subpackage search
 */
class ActionGroupIndexing extends CopixActionGroup {
	protected function _beforeAction ($pAction) {
		_currentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Page de base pour afficher la progression de l'indexation
	 */
	public function processDefault (){
		_notify ('BeforeIndexing');
		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>_i18n ('admin.indexing'))), 'indexing.php');
	}

	/**
	 * Page qui va préparer la demande de réindexation globale des contenus du site.
	 */
	public function processPrepareIndexAll (){
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