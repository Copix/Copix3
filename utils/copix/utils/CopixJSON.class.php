<?php
/**
 * @package    copix
 * @subpackage utils
 * @author     Guillaume Perréal
 * @copyright  2001-2008 CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

class CopixJSONException extends CopixException {};
class CopixJSONEncoderException extends CopixJSONException {};
class CopixJSONDecoderException extends CopixJSONException {};

/**
 * Object capable de fournir sa propre représentation JSON.
 *
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
 *
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
	static private function _encodeString ($pString, &$pBuffer) {
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
					foreach (split ('//', $part) as $character) {
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
	static public function encode ($pValue) {
		
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

/**
 * Classe décodant une chaîne JSON en valeur PHP.
 *
 * Implémenté par une machine à état.
 * 
 * Afin d'optimiser, des constantes entières et des variables locales sont utilisés dès que possible.
 * De la même façon, les appels de fonctions sont limitées au minimum.
 */
class CopixJSONDecoder {
	
	/**#@+
	 * Code de token.
	 */

	/** Pseudo-token: fin de la chaîne. */
	const T_EOS           = -1;
	
	/** Accolade ouvrante. */
	const T_OPEN_BRACE    = 0;
	
	/** Accolade fermante. */ 
	const T_CLOSE_BRACE   = 1;
	
	/** Crochet ouvrant. */
	const T_OPEN_BRACKET  = 2;
	
	/** Crochet fermant. */
	const T_CLOSE_BRACKET = 3;
	
	/** Virgule. */
	const T_COMMA         = 4;
	
	/** Deux points. */
	const T_COLON         = 5;
	
	/** Valeur booléenne. */
	const T_BOOLEAN       = 6;
	
	/** NULL */
	const T_NULL          = 7;
	
	/** Valeur flottante. */
	const T_FLOAT         = 8;
	
	/** Valeur entière. */
	const T_INTEGER       = 9;
	
	/** Chaîne de caractère. */
	const T_STRING        = 10;
	
	/**#@-*/
	
	/**#@+
	 * Code d'action.
	 */
	
	/** Empiler la valeur. */
	const A_PUSHVALUE = 1;
	
	/** Créer un nouvel object. */
	const A_NEWOBJECT = 2;
	
	/** Affecter la valeur à une propriété d'un objet. */
	const A_SETPROP   = 3;
	
	/** Créer un nouveau tableau. */
	const A_NEWARRAY  = 4;
	
	/** Ajoute la valeur au tableau. */
	const A_ADDITEM   = 5;
	
	/** Retourne la valeur (fin du décodage). */
	const A_RETURN    = 6;
	
	/**#@-*/
	
	/**#@+
	 * Code d'état.
	 */
	const S_START        = 0;
	const S_END          = 1;
	const S_OBJECT_HEAD  = 2;
	const S_OBJECT_KEY   = 3;
	const S_OBJECT_COLON = 4;
	const S_OBJECT_VALUE = 5;
	const S_OBJECT_NEXT  = 6;	
	const S_ARRAY_HEAD   = 7;
	const S_ARRAY_VALUE  = 8;
	const S_ARRAY_NEXT   = 9;
	/**#@-*/
	
	/**
	 * Expression régulière permettant d'extraire le token suivant de la chaîne JSON.
	 * 
	 * L'expression est en mode "verbose" (modificateur 'x') donc les blancs de la définition sont ignorés.
	 * 
	 * Attention : l'ordre des captures doit correspondre aux constantes T_*.
	 */
	const TOKEN_REGEX = '@\s*(?:
				  ( \{ )
				| ( \} )
				| ( \[ )
				| ( \] )
				| ( , )
				| ( : )
				| ( true | false )
				| ( null )
				| ( -? (?: [1-9] \d* | 0 ) \. \d+ (?: e [-+]? \d+ )? )
				| ( -? [1-9] \d* | 0 )
				| ( " (?: [^"/\x5C] | \x5C [ntrfb"/\x5C] | \x5C u [0-9a-f]{4} )*? " )
			)\s*@xiS';
	
	/**
	 * Table de la machine à états.
	 * 
	 * Chaque entrée de premier niveau correspond à un état de la machine.
	 * Pour chaque état sont listes les tokens acceptés et les actions à entreprendre.
	 * A chaque token est associé un triplet (action, etatAEmpiler, etatSuivant).
	 * Si etatAEmpiler est non nul, il est empilé sur la pile d'états.
	 * Si etatSuivant est non nul, on enchaine, sinon il est dépilé de la pile d'états.
	 *
	 * @var array
	 */
	static private $_machineTable = array (		
		self::S_START => array ( 
			self::T_BOOLEAN       => array (self::A_PUSHVALUE, null,                self::S_END),
			self::T_NULL          => array (self::A_PUSHVALUE, null,                self::S_END),
			self::T_INTEGER       => array (self::A_PUSHVALUE, null,                self::S_END),
			self::T_FLOAT         => array (self::A_PUSHVALUE, null,                self::S_END),
			self::T_STRING        => array (self::A_PUSHVALUE, null,                self::S_END),
			self::T_OPEN_BRACE    => array (self::A_NEWOBJECT, self::S_END,         self::S_OBJECT_HEAD),
			self::T_OPEN_BRACKET  => array (self::A_NEWARRAY,  self::S_END,         self::S_ARRAY_HEAD),
		),
		self::S_END => array ( 
			self::T_EOS           => array (self::A_RETURN,    null,                null),
		),
		self::S_OBJECT_HEAD => array (
			self::T_STRING        => array (self::A_PUSHVALUE, null,                self::S_OBJECT_COLON),
			self::T_CLOSE_BRACE   => array (null,              null,                null),
		),
		self::S_OBJECT_KEY => array (
			self::T_STRING        => array (self::A_PUSHVALUE, null,                self::S_OBJECT_COLON),
		),
		self::S_OBJECT_COLON => array (  
			self::T_COLON         => array (null,              null,                self::S_OBJECT_VALUE),
		),
		self::S_OBJECT_VALUE => array (
			self::T_BOOLEAN       => array (self::A_PUSHVALUE, null,                self::S_OBJECT_NEXT),
			self::T_NULL          => array (self::A_PUSHVALUE, null,                self::S_OBJECT_NEXT),
			self::T_INTEGER       => array (self::A_PUSHVALUE, null,                self::S_OBJECT_NEXT),
			self::T_FLOAT         => array (self::A_PUSHVALUE, null,                self::S_OBJECT_NEXT),
			self::T_STRING        => array (self::A_PUSHVALUE, null,                self::S_OBJECT_NEXT),
			self::T_OPEN_BRACE    => array (self::A_NEWOBJECT, self::S_OBJECT_NEXT, self::S_OBJECT_KEY),
			self::T_OPEN_BRACKET  => array (self::A_NEWARRAY,  self::S_OBJECT_NEXT, self::S_ARRAY_HEAD),
		),		
		self::S_OBJECT_NEXT => array (
			self::T_COMMA         => array (self::A_SETPROP,   null,                self::S_OBJECT_KEY),
			self::T_CLOSE_BRACE   => array (self::A_SETPROP,   null,                null),
		),
		self::S_ARRAY_HEAD => array (
			self::T_BOOLEAN       => array (self::A_PUSHVALUE, null,                self::S_ARRAY_NEXT),
			self::T_NULL          => array (self::A_PUSHVALUE, null,                self::S_ARRAY_NEXT),
			self::T_INTEGER       => array (self::A_PUSHVALUE, null,                self::S_ARRAY_NEXT),
			self::T_FLOAT         => array (self::A_PUSHVALUE, null,                self::S_ARRAY_NEXT),
			self::T_STRING        => array (self::A_PUSHVALUE, null,                self::S_ARRAY_NEXT),
			self::T_OPEN_BRACE    => array (self::A_NEWOBJECT, self::S_ARRAY_NEXT,  self::S_OBJECT_HEAD),
			self::T_OPEN_BRACKET  => array (self::A_NEWARRAY,  self::S_ARRAY_NEXT,  self::S_ARRAY_HEAD),
			self::T_CLOSE_BRACKET => array (null,              null,                null),
		),
		self::S_ARRAY_VALUE => array (
			self::T_BOOLEAN       => array (self::A_PUSHVALUE, null,                self::S_ARRAY_NEXT),
			self::T_NULL          => array (self::A_PUSHVALUE, null,                self::S_ARRAY_NEXT),
			self::T_INTEGER       => array (self::A_PUSHVALUE, null,                self::S_ARRAY_NEXT),
			self::T_FLOAT         => array (self::A_PUSHVALUE, null,                self::S_ARRAY_NEXT),
			self::T_STRING        => array (self::A_PUSHVALUE, null,                self::S_ARRAY_NEXT),
			self::T_OPEN_BRACE    => array (self::A_NEWOBJECT, self::S_ARRAY_NEXT,  self::S_OBJECT_HEAD),
			self::T_OPEN_BRACKET  => array (self::A_NEWARRAY,  self::S_ARRAY_NEXT,  self::S_ARRAY_HEAD),
		),
		self::S_ARRAY_NEXT => array (
			self::T_COMMA         => array (self::A_ADDITEM,   null,                self::S_ARRAY_VALUE),
			self::T_CLOSE_BRACKET => array (self::A_ADDITEM,   null,                null),
		), 			
	);
	
	/**
	 * Décode une chaîne JSON en valeur PHP.
	 *
	 * @param string $pJSON Chaîne JSON.
	 * @param boolean $pAssoc Si vrai, crée des tableaux associatifs plutôt que des objets.
	 * @return mixed Valeur analysée
	 * @throws CopixJSONDecoderException Une erreur s'est produite.
	 */
	static public function decode ($pJSON, $pAssoc = false) {
		
		// Info sur les données
		$offset = 0;
		$length = strlen ($pJSON);
		
		// Pile de valeur
		$valueStack = array ();
		$valueIndex = -1;
		
		// Pile d'état
		$stateStack = array ();
		$stateIndex = -1;

		// Etat courant
		$state = self::S_START;
		
		// Référence sur la table d'execution.
		$machineTable =& self::$_machineTable;
		
		while (true) {
			
			// Récupère le prochain token
			$currentOffset = $offset;
			if ($offset < $length) {				
				if (!preg_match (self::TOKEN_REGEX, $pJSON, $parts, null, $offset)) {
					throw new CopixJSONDecoderException (_i18n (
						'copix:copix.error.json.decoder.syntaxError',
						array ($currentOffset, substr ($pJSON, $currentOffset, 50))
					));
				}
				$offset += strlen ($parts[0]);
				list ($token, $value) = each (array_filter (array_slice ($parts, 1)));
				
			} else {
				// Fin de la chaîne
				$token = self::T_EOS;
				$value = null;
			}
			
			// Récupère le triplet (action, empiler, état suivant) depuis la table
			if (!isset ($machineTable[$state][$token])) {
				throw new CopixJSONDecoderException (_i18n (
					'copix:copix.error.json.decoder.unexpectedToken',
					array ($currentOffset, $value)
				));
			}			
			list ($action, $pushState, $nextState) = $machineTable[$state][$token];
			
			// Transforme les scalaires JSON en scalaires PHP
			switch ($token) {
				case self::T_BOOLEAN: $value = (strtolower ($value) == 'true'); break;
				case self::T_NULL:    $value = null;                            break;
				case self::T_INTEGER: $value = intval ( $value);                break;
				case self::T_FLOAT:   $value = floatval ($value);               break;
					
				case self::T_STRING:
					$value = preg_replace (
						'/\x5Cu([0-9a-f]{4})/ei',
						'chr(hexdec(\'$1\'))',
						str_replace (
							array ('\\\\', '\t', '\r', '\n', '\b',   '\f',   '\"', '\/'),
							array ('\\',   "\t", "\r", "\n", "\x08", "\x0C", '"',  '/' ),
							substr ($value,1,-1)
						)
					);
					break;
			}

			// Execute l'action
			switch ($action) {
				// Empile la valeur
				case self::A_PUSHVALUE:
					$valueStack[++$valueIndex] = $value;
					break;
					
				// Empile un nouvel objet
				case self::A_NEWOBJECT:
					$valueStack[++$valueIndex] = $pAssoc ? array () : new stdClass ();
					break;
					
				// Dépile une clef et une valeur puis assigne la propriété à l'objet au sommet de la pile
				case self::A_SETPROP:
					$key = $valueStack[$valueIndex-1];
					if ($pAssoc) {
						$valueStack[$valueIndex-2][$key] = $valueStack[$valueIndex];
					} else {
						$valueStack[$valueIndex-2]->$key = $valueStack[$valueIndex];
					}
					$valueIndex -= 2;
					break;
					
				// Empile un nouveau tableau
				case self::A_NEWARRAY:
					$valueStack[++$valueIndex] = array ();
					break;
					
				// Dépile une valeur et l'ajoute à la fin du tableau au sommet de la pile
				case self::A_ADDITEM:
					$valueStack[$valueIndex-1][] = $valueStack[$valueIndex];
					$valueIndex--;
					break;
					
				// Retourne le haut de la pile (on a terminé donc)
				case self::A_RETURN:
					return $valueStack[$valueIndex];
			}
			
			// Empile $pushState s'il est non-nul
			if ($pushState !== null) {
				$stateStack[++$stateIndex] = $pushState;
			}
			
			// Passe à l'état suivant (ou dépile l'état si $nextState est null)
			$state = $nextState !== null ? $nextState : $stateStack[$stateIndex--];
		}
		
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

/**
 * Wrapper pour les fonctions json_encode et json_decode.
 *
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
	static public function encode ($pValue) {
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
	static public function decode ($pJSON, $pAssoc = false) {
		return json_decode ($pJSON, $pAssoc);
		//return CopixJSONEncoder::decode($pValue);
	}

}

?>
