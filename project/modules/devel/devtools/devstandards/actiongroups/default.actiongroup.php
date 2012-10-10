<?php
/**
 * Test de description de fichier
 * 
 * @package devtools
 */

/**
 * Commentaire magique sur myFunction qui déchire
 * 
 * @param string $pParam18 Commentaire sur $pParam1
 * @param string $pParam2
 * @throws CopixException Commentaire
 * @throws CopixException
 */
function myFunction ($pParam1, $pParam2 = 'oui', $param3 = 18) {
	echo '{';
	throw new CopixException ('test');
	throw new CopixException ('test');
	throw new CopixException ('test');
	return true;
}

function myFunction2 () {
}

interface MyImplement {
	public function implementFunction ();
}

abstract class MyClassTest   extends ActionGroupDefault implements MyImplement {
	public final static function funcFinalPublicStatic () {
		CopixUrl::get ();
	}
	
	abstract public function funcAbstractPublicStatic ();
	
	private function funcPrivate () {
		
	}
	
	static private function funcPrivateStatic () {
		
	}
	
	static function funcOnlyStatic () {
		
	}
}

/**
 * Test de description de classe
 *
 * @package		test
 * @subpackage	oui
 */
class ActionGroupDefault extends CopixActionGroup {
	
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	public function processDefault () {
		_classInclude ('Tokenizer');
		$ppo = new CopixPPO ();
		//$file = COPIX_PATH . 'core/CopixConfig.class.php';
		$file = CopixModule::getPath ('devstandards') . 'classes/tokenizer.class.php';
		//$file = __FILE__;
		$ppo->file = $file;
		$ppo->arErrors = Tokenizer::parseFile ($file);
		$ppo->nbrErrors = count ($ppo->arErrors);
		
		return _arPPO ($ppo, 'errors.tpl');
	}	
}

function myFunction3 () {
	
}

?>