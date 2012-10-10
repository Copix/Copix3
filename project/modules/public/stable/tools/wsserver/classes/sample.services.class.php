<?php
/**
* @package		tools 
 * @subpackage	wsserver
 * @author		Favre Brice
 * @copyright	2001-2007 CopixTeam
 * @link			http://copix.org
 * @licence		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Exemple  de Classe de Web Services
* @package		tools 
 * @subpackage	wsserver
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
	
	/** The connect function
	 * @param array $pParams
	 */
	public function connect ( $pParams ) {
		$arParams = array();
		foreach ($pParams->item as $item ) {
			$arParams [$item->key] = $item->value;
		}
		CopixAuth::getCurrentUser ()-> login ( $arParams ) ;
		
	}
}