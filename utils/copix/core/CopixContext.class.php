<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de gestion des contextes de l'application.
 * Nous allons gérer le fait des entrées sorties dans les différents modules.
 *
 * @package		copix
 * @subpackage	core
 */
class CopixContext {
	/**
	 * Pile de gestion des contextes
	 *
	 * @var array
	 */
	static private $_contextStack = array ();

	/**
	 * Variable privée pour le contexte courant
	 *
	 * @var string
	 */
	static private $_currentContext = 'default';

	/**
	 * Empilement d'un contexte.
	 * <code>
	 *	//on récupère une classe d'un module X ou Y
	 *	$object = CopixClassesFactory::create ('moduleY|ClasseExemple');
	 *	//cette classe utilise d'autres sous classes, en considérant qu'elle est exécutée
	 *	//dans le module pour lequel elle a été écrite.
	 *	//On va donc forcer le contexte d'exécution
	 *	CopixContext::push ('moduleY');
	 *	$object->doStuff ();
	 *	//On rétabli le contexte d'exécution
	 *	CopixContext::pop ();
	 * </code>
	 *
	 * @param string	$pModule Nnom du module dont on empile le contexte
	 */
	static public function push ($pModule) {
		self::$_currentContext = CopixContext::$_contextStack[] = $pModule;
	}

	/**
	 * Dépilement d'un contexte.
	 *
	 * @return string Elément dépilé, soit le contexte qui n'est plus d'atualité
	 */
	static public function pop () {
		$lastContext = ($value = array_pop (CopixContext::$_contextStack)) === null ? 'default' : $value;
		self::$_currentContext = (($last = (count (CopixContext::$_contextStack) - 1)) >= 0) ? CopixContext::$_contextStack[$last] : 'default';
		return $lastContext; 
	}

	/**
	 * Récupère le contexte actuel
	 * <code>
	 *	echo 'Le code suivant s\'exécute dans le module ' . CopixContext::get ();
	 * </code>
	 *
	 * @return string Nom du contexte actuel si défini, si pas de contexte retourne default
	 */
	static public function get () {
		return self::$_currentContext;
	}

	/**
	 * Réinitialise le contexte
	 * Il existe très peu de cas ou vous devrez vous même appeler cette méthode.
	 * Cette méthode existe principalement pour permettre à CopixController de manipuler
	 * la pile de contexte complète
	 */
	static public function clear () {
		CopixContext::$_contextStack = array ();
		self::$_currentContext = 'default';
	}
	
	/**
	 * Retourne la pile de contexte
	 * @return array
	 */
	static public function getStack (){
		return array_reverse (self::$_contextStack);
	}
}