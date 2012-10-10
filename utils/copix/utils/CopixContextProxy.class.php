<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croes Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Proxy générique capable de définir et rétablir les contextes des modules pour les classes données.
 * @package copix
 * @subpackage core
 */
class CopixContextProxy extends CopixClassProxy {
	/**
	 * Le contexte de l'objet
	 *
	 * @var string
	 */
	protected $_context = null;

	/**
   	 * Constructeur, l'objet et sa définition s'il y a lieu
   	 * @param	object	$pObject	l'objet à placer dans la session
   	 * @param	string	$pFileName	le chemin de la définition du fichier
   	 */
	public function __construct ($pObject, $pContext){
		$this->_context = $pContext === null ? CopixContext::get () : $pContext;
		parent::__construct ($pObject);
   	}
   	
   	/**
   	 * Avant toute action automatique sur l'objet, on push le contexte initial
   	 */
   	protected function _beforeRemoteAction (){
   		CopixContext::push ($this->_context);
   		parent::_beforeRemoteAction ();
   	}

   	/**
   	 * Après toute action effectuée sur l'objet, on remet le contexte "courant"
   	 */
   	protected function _afterRemoteAction (){
   		CopixContext::pop ();
   		parent::_afterRemoteAction ();   		
   	}
}