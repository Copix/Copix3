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
 * Interface pour les objets capables de fournir leur propre représentation JSON.
 * @package copix
 * @subpackage json
 */
interface ICopixJSONEncodable {

	/**
	 * Retourne la représentation JSON de l'objet.
	 *
	 * @return string Représentation JSON De l'objet.
	 */
	public function toJSON();
	
}

/**
 * Classe d'encodage en JSON
 * @package copix
 * @subpackage json
 */
class CopixJSONEncoder {
	
	/**
	 * Profondeur maximale d'imbrication.
	 *
	 */
	const MAX_DEPTH = 128;
	
	/**
	 * Encode une chaîne en JSON.
	 *
	 * @param string $pString Chaîne à encoder.
	 * @param array $pBuffer Buffer JSON.
	 */
	private static function _encodeString ($pString, &$pBuffer) {
		$pBuffer[] = '"';
		
		// Remplacements faciles
		$escapedString = str_replace (
			array ('\\',   "\t", "\r", "\n", "\x08", "\x0C", '"',  '/' ),
			array ('\\\\', '\t', '\r', '\n', '\b',   '\f',   '\"', '\/'), 
			_toString($pString)
		);
		
		// Sépare la chaîne en partie UTF8 et parties non-UTF8
		$parts = preg_split (
			'/((?:[\x20-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3})+)/',
			$escapedString,
			-1,
			PREG_SPLIT_DELIM_CAPTURE
		);
		
		foreach ($parts as $i=>$part) {
			if ($part) {
				if ($i & 1 == 1) {
					// Partie UTF8
					$pBuffer[] = $part;
				} else {
					// Partie non-UTF8
					foreach (preg_split ('"//"', $part) as $character) {
						$pBuffer[] = sprintf ('\\u%04X', ord ($character));
					}
				}
			}
		}
		$pBuffer[] = '"';		
	}
	
	/**
	 * Encode une valeur PHP en JSON.
	 * 
	 * L'implémentation est itérative pour éviter les problèmes de récursion au niveau de la pile PHP.
	 *
	 * @param mixed $pValue Valeur à encoder.
	 * @return string Représentation JSON
	 * @throws CopixJSONEncoderException Une erreur s'est produite.
	 */
	public static function encode ($pValue) {
		
		// Buffer JSON dans lequel seront accumulées les morceaux de chaîne
		$buffer = array ();		
		
		// Pile
		$stack = array ();
		$top = -1;

		// Etat  
		$state = 'end';
		$index = -1;
		$keys = array (0);
		$values = array ($pValue);
		
		// Le "stop" se fait au milieu de la boucle
		while (true) {

			// Si l'on est à la fin de la structure
			while (++$index >= count($keys)) {
				
				// Supprime la virgule qui pourrait trainer
				if ($buffer[count($buffer)-1] == ',') {
					array_pop ($buffer);
				}
				
				// Terminé ?
				if ($state == 'end') {
					return implode ('', $buffer);
				}
				
				// Ferme la structure
				$buffer[] = ($state == 'object') ? '}' : ']';
				
				// Dépile l'état précédent
				list ($state, $index, $keys, $values) = $stack[$top--];
				
				// Ajoute une virgule
				$buffer[] = ',';
			}

			// Dans une structure 'objet', il faut ajouter la clef
			if ($state == 'object') {
				self::_encodeString ($keys[$index], $buffer);
				$buffer[] = ':';
			}

			// Récupère la valeur 
			$value =& $values[$index];
			
			// Selon le type...
			switch (gettype ($value)) {
			
				// Nouvelle structure
				case 'object':
					// Gère le cas des ICopixJSONEncodable
					if($value instanceof ICopixJSONEncodable) {
						$buffer[] = $value->toJSON();
						$buffer[] = ',';
						break;
					}					
				case 'array':
					
					// Empile l'état courant sauf si on atteint la profondeur maximale
					if (++$top == self::MAX_DEPTH) {
						throw new CopixJSONEncoderException (_i18n ('copix:copix.error.json.encoder.tooMuchRecursion'));
					}
					$stack[$top] = array ($state, $index, $keys, $values);
					
					// Détermine le nouvel état 
					if (is_object ($value)) {
						$state = 'object';
						$value = get_object_vars ($value);
					} else {
						$state = 'array';
					}
					
					// Initialise le nouvel état
					$index = -1;
					$keys = array_keys ($value);
					$values = array_values ($value);	
					
					// Si on a un tableau, vérifie que toutes les clefs soient numériques et dans l'ordre
					// Si ce n'est pas le cas, on le représente comme un objet.
					if ($state == 'array') {
						foreach ($keys as $i=>$key) {
							if ($i !== $key) {
								$state = 'object';
								break;
							}
						}
					}
					
					// Ouvre la structure
					$buffer[] = ($state == 'object') ? '{' : '[';
					break;
				
				// Scalaires : ajoute la valeur suivie d'une virgule
					
				case 'string':
					self::_encodeString ($value, $buffer);
					$buffer[] = ',';
					break;
					
				case 'integer':
					$buffer[] = sprintf('%d', $value);
					$buffer[] = ',';
					break;
					
				case 'double':
					$buffer[] = sprintf ("%f", $value);
					$buffer[] = ',';
					break;
					
				case 'boolean':
					$buffer[] = $value ? 'true' : 'false';
					$buffer[] = ',';
					break;
					
				case 'NULL':
					$buffer[] = 'null';
					$buffer[] = ',';
					break;
					
				default:
					throw new CopixJSONEncoderException (_i18n ('copix:copix.error.json.encoder.cannotEncodetype', gettype ($value)));	
			}
			
		}
			
	}
	
}