<?php
/**
 * @package		tools
 * @subpackage	soap_server
 * @author		Favre Brice
 * @copyright	2001-2007 CopixTeam
 * @link			http://copix.org
 * @licence		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Exemple  de Classe de Web Services
 * @package		tools
 * @subpackage	soap_server
 */
class SampleServices {

	/**
	 * @param string $test
	 * @return string
	 */
	public function protectedReturnParams ($test) {
		if (CopixAuth::getCurrentUser ()-> isConnected()) {
			// Le $res est un fichier xml
			// $xml = new SimpleXml ($res);
			return $test;
		} else {
			return new soapFault("Serveur","Non connecté");
		}
	}

	/**
	 * @param string $test
	 * @return string
	 */
	public function returnParams ($test) {
		return $test;
	}

	/**
	 * The connect function
	 * @param array $pParams
	 */
	public function connect ( $pParams ) {
		$arParams = array();
		foreach ($pParams->item as $item ) {
			$arParams [$item->key] = $item->value;
		}
		CopixAuth::getCurrentUser ()-> login ( $arParams ) ;

	}

	/**
	 * Test avec des objet complexes
	 * @param ExportObject $pParams
	 */
	public function testComplexe ($pParam){
		return $pParam;
	}
}

/**
 * Classe d'export utilisée dans la fonction testComplexe
 *
 */
class ExportObject{

	/**
	 * Une info sans véritable valeur
	 *
	 * @var string
	 */
	public $foo;

	/**
	 * Une autre info d'un type différent
	 *
	 * @var int
	 */
	public $bar;

}
?>