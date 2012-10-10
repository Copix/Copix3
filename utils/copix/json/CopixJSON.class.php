<?php
/**
 * @package    copix
 * @subpackage json
 * @author     Guillaume Perréal
 * @copyright  2001-2008 CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Wrapper pour les fonctions json_encode et json_decode.
 * @package copix
 * @subpackage json
 */
class CopixJSON {
	
	/**
	 * Encode des données PHP en JSON.
	 * 
	 * @uses CopixJSONDecoder::encode ()
	 * 
	 * @param mixed $pValue Données à convertir.
	 * @return string Chaîne JSON.
	 */
	public static function encode ($pValue) {
		return json_encode ($pValue);
		//return CopixJSONEncoder::encode($pValue);
	}
	
	/**
	 * Décode de données JSON.
	 * 
	 * @uses CopixJSONDecoder::decode ()
	 *
	 * @param string $pJSON Données encodées en JSON.
	 * @param boolean $pAssoc Si vrai, crée des tableaux associatifs plutôt que des objets.
	 * @return mixed Données PHP.
	 */
	public static function decode ($pJSON, $pAssoc = false) {
		return json_decode ($pJSON, $pAssoc);
		//return CopixJSONEncoder::decode($pValue);
	}
}

if (!function_exists ('json_encode')) {
	/// @ignore
	function json_encode ($pValue) {
		try {
			return CopixJSONEncoder::encode ($pValue);
		} catch (CopixJSONEncoderException $e) {
			return false;
		}
	}	
}

if (!function_exists ('json_decode')) {
	/// @ignore
	function json_decode ($pJSON, $pAssoc = false) {
		try {
			return CopixJSONDecoder::decode ($pJSON, $pAssoc);
		} catch (CopixJSONDecoderException $e) {
			return false;
		}
	}	
}