<?php
/**
 * @package		rssevent
 * @author		Steevan BARBOYON
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

interface myInterface {
	public static function funcStatique ();
}

interface myInterfaceNumero2 {
	static public function funcStaticPublic ();
}

class myFirstTest implements myInterface {
	/**
	 * Propriété publique
	 *
	 * @var int
	 */
	public $property = 'Valeur de la propriété';
	
	/**
	 * Constructeur
	 *
	 * @param string $pParam1 Commentaire sur le Paramètre
	 */
	public function __construct ($pParam1) {
		$this->property = $pParam1;
	}
	
	/**
	 * Méthode privée qui fait quelque chose
	 *
	 * @param int $pParam1 Commentaire param1
	 * @param boolean $pParam2 Commentauire
	 * @return array
	 */
	private function _funcPrivate ($pParam1 = 12) {
		return array ($pParam1, $pParam2);
	}
	
	/**
	 * Méthode statique
	 * Avec commentaire sur plusieurs lignes
	 *
	 * @return boolean
	 */
	public static function funcStatique () {
		return true;
	}
}

class myTest extends MyFirstTest implements myInterfaceNumero2 {
	public $propPublicStr = 'prop public';
	public $propPublicInt = 18;
	public $propPublicBool = true;
	public $propPublicArray = array ('clef' => 'valeur', 0 => true);
	public $propPublicNull = null;
	public $propPublicMyFirstTest = null;
	
	static public $staticPublic = 'statique publique';
	static public $staticPublicNull = null;
	
	private $_propPrivate = 'test 2';
	protected $_proProtected = 'protégée';
	
	const C_STRING = 'valeur const';
	const C_BOOLEAN_TRUE = true;
	const C_BOOLEAN_FALSE = false;
	const C_INTEGER = 12;
	const C_NULL = null;
	
	
	static private $_staticPrivate = 'statique privée';
	static protected $_staticProtected = 'statique protégée';
	
	/**
	 * Ma fonction publique
	 *
	 * @param int $pParam1
	 * @param boolean $pParam2
	 */
	public function funcPublic ($pParam1, $pParam2 = true, $pParam3 = 'oui') {
		
	}
	
	private function _funcPrivate ($pParam1 = 12) {
		
	}
	
	protected function _funcProtected ($pParam1 = 'oui') {
		
	}
	
	/**
	 * Méga fonction de la mort qui fait un static public, en plus
	 * Avec un commentaire sur plusieurs lignes
	 */
	static public function funcStaticPublic () {
		
	}
	
	/**
	 * Constructeur super génial qui déchire toute la famille
	 *
	 * @param int $pParam1
	 * @param int $pParam2
	 */
	public function __construct ($pParam1, $pParam2 = 'valeur') {
		
	}
}

/**
 * @package		rssevent
 * @subpackage	adminflux
 */
class ActionGroupDefault extends CopixActionGroup {
    /**
     * Tester CopixDebug::object_dump
     */
    public function processDefault () {
    	$test = new MyTest ('value param1');
    	$test->propPublic = 18;
    	$test->newPropString = 'test new prop';
    	$test->newPropNULL = null;
    	$test->newPropBool = true;
    	$test->newPropInt = 19;
    	$test->newPropClassTest = new myTest (12);
    	$test->newPropClassArray = array ('clef1' => 'valeur 1', 'clef2' => 'valeur 2', 2 => true, 3 => 18, 21 => array ('oui'));
    	$test->propPublicMyFirstTest = new MyFirstTest ('oui');
    	
    	//_dump ($test);
    	_dump (new myTest ('Changement de valeur'));
    	_dump (new myTest ('Changement de valeur'));
    	
    	$testArray = array (
		    'clef1' => 'valeur 1',
		    'clef2' => array (
		        'sousClef1' => 'sous valeur 1',
		        'sousClef2' => new CopixPPO ()
		    ),
		    2 => true,
		    3 => 18,
		    21 => array (
		        0 => 'oui',
		        1 => array (
		            0 => 'sous niveau 3'
		        )
		    )
		);
		//_dump ($testArray, false, false);
    	//_dump ('test string');
		
		return _arNone ();
    }
}
?>