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
	 * @var unknown_type
	 */
	protected $_context = null;

	/**
   	 * Constructeur, l'objet et sa définition s'il y a lieu
   	 * @param	object	$pObject	l'objet à placer dans la session
   	 * @param	string	$pFileName	le chemin de la définition du fichier
   	 */
	public function __construct ($pObject, $pContext){
		CopixContext::push ($this->_context = $pContext);
		parent::__construct ($pObject);
		CopixContext::pop ();
   	}

   	/**
   	 * Encapsulation de l'appel des fonctions pour les transmettre directement à l'objet tout 
   	 * en ayant au préalable indiqué les informations de contexte
   	 * 
   	 * @param	string	$pName	nom de la fonction
   	 * @param	array	$pArgs	arguments passés à la fonction
   	 * @return mixed
   	 */
   	public function __call ($pName, $pArgs){
   		CopixContext::push ($this->_context);
   		$toReturn = parent::__call ($pName, $pArgs);
   		CopixContext::pop ();
   		return $toReturn;
   	}
}
?>