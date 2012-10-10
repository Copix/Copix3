<?php
/**
 * @package standard
 * @subpackage copixtest
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Actions standards pour tester le fonctionnement global de Copix
 * @package standard
 * @subpackage copixtest
 */
 class ActionGroupDefault extends CopixActionGroup {
 	/**
 	 * Affichage d'une liste de données basique
 	 */
 	public function processDefault (){
 		$ppo = new CopixPPO ();
 		$ppo->arData = _ioDAO ('CopixTestMain')->findAll ();
 		$ppo->TITLE_PAGE = 'Titre de ma page';
 		return _arPPO ($ppo, 'data.list.tpl');
 	}
 	
 	/**
 	 * Récupération d'une liste de données basique au format XML
 	 */
 	public function processXml (){
		$ppo = new CopixPPO ();
 		$ppo->arData = _ioDAO ('CopixTestMain')->findAll (); 
 		return _arPpo ($ppo, array ('template'=>'data.xml.tpl', 
 								 'content-type'=>'application/xml', 
 								 'mainTemplate'=>null,
 								 'cache-control'=>'no-cache'));		
 	}
 	
 	/**
 	 * Récupération d'un document pour téléchargement
 	 */
 	public function processFile (){
 		return _arFile ('index.php', array ('filename'=>'test.ptpl'));
 	}
 	
 	/**
 	 * Récupération d'un contenu basique
 	 */
 	 public function processContent (){
 	 	$data = 'someContent';
 	 	return _arContent ($data, array ('filename'=>'data.zip',
 	 									'content-type'=>'application/zip'));
 	 }
 	 
 	 /**
 	  * Récupération d'un éditeur HTML
 	  */
 	 public function processHtmlEditor (){
 	 	return _arPpo (new CopixPPO (), 'htmleditor.tpl');
 	 }

 	 /**
 	  * Test de l'appartenance de l'utilisateur au groupe test
 	  */
 	 public function processGroupTest (){
 	 	CopixAuth::getCurrentUser ()->assertCredential ('group:[test]');
 	 	return _arNone ();
 	 }
 	 
 	 /**
 	  * Lancement des tests unitaires
 	  */
 	 public function processLaunch (){
 	 	return _arRedirect (_url ().'test.php');
 	 }
 }
?>