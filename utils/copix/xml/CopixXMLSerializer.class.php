<?php
/**
 * @package   copix
 * @subpackage xml
 * @author    Guillaume Perréal
 * @copyright CopixTeam
 * @link      http://copix.org
 * @license   http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * CopixXMLSerializer permet de linéariser une valeur PHP en document XML, dans un format
 * approprié pour reconstituer la valeur PHP ultérieurement.
 *
 * CopixXMLSerializer utilise en interne la fonction PHP serialize(). La linéarisation est donc soumise
 * aux mêmes règles pour que pour cette fonction.

 * @package		copix
 * @subpackage	xml
 */
class CopixXMLSerializer {
	public static function getInstance() {
		if(extension_loaded("mbstring") && ((ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING) == MB_OVERLOAD_STRING)) {
			$instance = new CopixXMLSerializerWithMBOverload ();
		} else {
			$instance = new CopixXMLSerializerWithoutMBOverload ();
		}
		return $instance;
	}

	/**
	 * Sérialise des données au format XML.
	 *
	 * @param mixed $data Données à sérialiser en XML.
	 * @return string Document XML généré par la serialisation.
	 */
	public static function serialize (&$data) {
		return self::getInstance ()->serializedToXML (serialize ($data));
	}

	/**
	 * Désérialise des données au format XML.
	 *
	 * @param string $xml Document XML représentant les données.
	 * @return mixed Données désérialisées.
	 */
	public static function & unserialize ($xml) {
		$data = unserialize (self::getInstance ()->serializedFromXML ($xml));
		return $data;
	}
}