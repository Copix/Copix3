<?php
/**
 * @package		webtools
 * @subpackage	simplewiki
 * @author		Brice Favre
 * @copyright	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Implémentation de fonction simple pour le nouveau wiki
 * @package	webtools
 * @subpackage	simplewiki
 */
class ActionGroupDefault extends CopixActionGroup {

	/**
	 * 
	 */
	private $_content;
	
	/**
	 * Fonction beforeAction 
	 *
	 * Vérification de la présence du WikiName
	 */
	public function beforeAction ($pActionName){
		CopixRequest::assert('WikiName');
	}
	
	/**
	 * Fonction des éditions
	 * 
	 * @return CopixActionReturn retourne le formulaire 
	 */
	public function processEdit (){
		$ppo = new CopixPPO ();
		$ppo->preview = _request ('preview');
		if (isset ($this->_content)) {
			$ppo->content = $this->_content; 
		} else {
			$ppo->content = _ioClass ('wiki|wikidatadb')->getContent (_request ('WikiName'));
		}
		$ppo->WikiName = _request ('WikiName');
		$ppo->comment = _request ('comment');
		$ppo->action = _url ('simplewiki|default|save');
		return _arPPO ($ppo, 'temp.form.php');
	}

	/**
	 * Fonction de sauvegarde des pages wiki
	 * 
	 * @return CopixActionReturn action show ou edit selon que l'on prévisualise ou pas 
	 */
	public function processSave (){
		CopixRequest::assert('WikiName');
		$preview = _request ('preview', false);
		if ($preview !== false) {
			$this->_content = _request ('content');
			return $this->processEdit();
		} else {
			_ioClass ('wiki|wikidatadb')->addContent (_request ('WikiName'), _request('content'), _request ('comment'));
			return $this->processShow();
		}
	}
	
	/**
	 * Fonction d'affichages des contenus
	 * 
	 * @return CopixActionReturn
	 */
	public function processShow (){
		$ppo = new CopixPPO ();
		
		$ppo->content = _ioClass ('wikirenderer|renderer')->render (_ioClass ('wiki|wikidatadb')->getContent (_request ('WikiName')));
		// Pas de contenu on appelle l'édition
		if ($ppo->content === false) {
			return $this->processEdit();
		}
		return _arPPO ($ppo, 'temp.show.php');
	}
}