<?php
/**
 * @package   copix
 * @subpackage core
 * @author    Guillaume Perr�al
 * @copyright CopixTeam
 * @link      http://copix.org
 * @license   http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de base pour les exceptions de linéarisation XML.
 * @package		copix
 * @subpackage	core
 */
class CopixXMLException extends CopixException {}

/**
 * Classe abstraite de base de linéarisateur.
 *
 * Cette classe contient tout le code de base ainsi qu'une série de méthodes abstraites pour gérer la surcharge
 * des fonctions str* par mbstring.
 *
 * CopixXMLSerializer permet de linéariser une valeur PHP en document XML, dans un format
 * approprié pour reconstituer la valeur PHP ultérieurement.
 *
 * CopixXMLSerializer utilise en interne la fonction PHP serialize(). La linéarisation est donc soumise
 * aux mêmes règles pour que pour cette fonction.
 *
 * @package		copix
 * @subpackage	core
 */
abstract class AbstractCopixXMLSerializer {

	/**
	 * Version de la classe, utilisé pour désérialiser des versions précédentes.
	 *
	 */
 	const VERSION = 1;
 
	/**
	 * Chaîne sérialisée représentant les données.
	 *
	 * @var string
	 */
	private $serialized;
	
	/**
	 * Version de la classe utilisée pour générer le document XML.
	 */
	private $docVersion = self::VERSION;

	/**
	 * Position courante dans la chaîne sérialisée.
	 *
	 * @var integer
	 */
	private $pos;

	/**
	 * Document XML en cours de construction.
	 *
	 * @var DOMDocument
	 */
	private $doc;

	/**
	 * Noeuds créés correspondant aux valeurs.
	 * Utilisé pour prendre en charge les références.
	 *
	 * @var array
	 */
	private $nodes;

	/**
	 * Nombre de valeurs ajoutés dans la Chaîne. Sert à recréer les références.
	 *
	 * @var integer
	 */
	private $valueCount;

	/**
	 * Genère un document XML à partir des données passées en paramètres.
	 *
	 * Initialise les données interne.
	 *
	 * @param string $serialized La chaîne sérialisée.
	 * @return string Document XML généré par la serialisation.
	 */
	public function serializedToXML ($serialized) {
		$this->pos = 0;
		$this->nodes = array (null);
		$this->serialized = $serialized;

		$this->doc = new DOMDocument ('1.0', 'UTF-8');

		$node = $this->doc->appendChild ($this->doc->createElement('data'));
		$node->setAttribute ('phpversion', phpversion ());
		$node->setAttribute ('version', $this->docVersion);
		//$node->appendChild($this->doc->createComment($this->serialized));

		$this->fillNode ($node);

		$xml = $this->doc->saveXML ();

		unset ($this->nodes);
		unset ($this->doc);

		return $xml;
	}

	/**
	 * Complète un noeud XML à partir des données à la position courante de la chaîne sérialisée.
	 * Cette fonction constitue le coeur de la transformation.
	 *
	 * @param DOMElement $node Noeud à compléter.
	 */
	protected function fillNode (DOMElement &$node) {
		$this->nodes[] = $node;

		if(false !== ($matches = $this->eat ('N;'))) {
			_log ('NULL', 'xml');
			$node->setAttribute ('type', 'null');

		} elseif(false !== ($matches = $this->eat ('a:'))) {
			_log ('Array', 'xml');
			$node->setAttribute ('type', 'array');
			$this->fetchArray ($node, 'entry', 'key', 'keyType');

		} elseif (false !== ($matches = $this->eat('O:'))) {
			$class = $this->fetchString ();
			_log ('Object: '.$class, 'xml');

			$node->setAttribute ('type', 'object');
			$node->setAttribute ('class', $class);
			$this->fetchArray ($node, 'property', 'name');

		} elseif(false !== ($matches = $this->eat ('[rR]:(\d+);'))) {
			$refPos = intval($matches[1]);
			$refNode =& $this->nodes[$refPos];
			if(!$refNode->hasAttribute ('id')) {
				$refNode->setAttribute ('id',$refPos);
			}
			$node->setAttribute ('ref', $refPos);

		} else {
			$scalar = $this->fetchScalar (&$type, &$encoding);
			$node->setAttribute ('type', $type);
			if(!empty($encoding)) {
				$node->setAttribute ('sourceEncoding', $encoding);
			}
			if($type == 'boolean') {
				$scalarNode = $this->doc->createTextNode ($scalar ? "true" : "false");
			} else {
				$scalarNode = $this->doc->createTextNode ($scalar);
			}
			$node->appendChild ($scalarNode);
		}

	}
	
	/**
	 * Prépare une chaîne à être stockée dans le XML.
	 *
	 * @param string $string La chaîne à stocker.
	 * @param string &$encoding L'encoding à preciser dans le XML.
	 * @return La chaîne encodée.
	 */
	protected function quoteString($string, &$encoding) {
		$toReturn = $string;
		if(preg_match('/^(?:['."\r\t\n".'x20-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3})*$/', $string)) {
			// UTF8 ou 7bits, ok
			$encoding = null;
		} elseif(preg_match('/^['."\r\t\n".'x20-\xFF]*$/', $string)) {
			// 8 bits quelconque => on suppose ISO-8859-1
			$toReturn = utf8_encode($string);
			$encoding = 'ISO-8859-1';
		} else {
			// On encode en base64
			$toReturn = base64_encode($string);
			$encoding = 'binary';
		}
		return $toReturn;
	}
	
	/**
	 * Retransforme une chaîne stockée en XML dans 
	 *
	 * @param string $string La chaîne encodée.
	 * @param string &$encoding L'encoding indiqué dans le XML.
	 * @return string La chaîne décodée.
	 */
	protected function unquoteString($string, &$encoding) {
		if(empty($encoding)) {
			return $string;
		}
		switch(strtolower(str_replace('-', '', $encoding))) {
			case 'base64':
			case 'binary':
				return base64_decode($string);
				
			case 'iso885915':
			case 'iso88591':
			case 'latin1':
				return utf8_decode($string);
				
			case 'utf8':
			case 'usascii':
			case 'ascii':
			case '7bit':
			case '7bits':
				return $string;
				
			default:
				if(extension_loaded('iconv')) {
					return iconv('UTF-8', $encoding, $string);
				} elseif(extension_loaded('mbstring')) {
					return mb_convert_encoding($string, $encoding, 'UTF-8');
				} else {
					$this->error("Cannot convert from UTF-8 to ".$encoding);			
				}
		}
	}	

	/**
	 * Extrait un scalaire (entier, flottant ou booléen) de la chaîne sérialisée.
	 *
	 * @param string &$type Le type de scalaire.
	 * @param string $&encoding L'encoding du scalaire (pour les chaînes).
	 * @return mixed Le scalaire extrait, sous la forme d'une chaine.
	 */
	protected function fetchScalar (&$type, &$encoding) {
		if(false !== ($matches = $this->eat ('i:([^;]+);'))) {
			_log ('Integer: '.$matches[1]);
			$type = 'integer';
			return $matches[1];

		} elseif(false !== ($matches = $this->eat ('d:([^;]+);'))) {
			_log ('Double: '.$matches[1]);
			$type = 'double';
			return $matches[1];

		} elseif(false !== ($matches = $this->eat ('b:([01]);'))) {
			_log ('Boolean: '.$matches[1]);
			$type = 'boolean';
			return intval ($matches[1]) == 1 ? true : false;

		} elseif(false !== ($matches = $this->eat('s:'))) {
			$type = 'string';
			return $this->quoteString($this->fetchString (), &$encoding);
			
		} else {
			$this->error ('Scalar expected');
		}
	}

	/**
	 * Extrait une chaîne de caractères ("string") de la chaîne sérialisée.
	 *
	 * @return La chaîne extraite.
	 */
	protected function fetchString () {
		if(false !== ($matches = $this->eat ('(\d+):"'))) {
			$length = intval ($matches[1]);
			$string = $this->substr ($this->serialized, $this->pos, $length);
			// VERSION 0 : 	$string = utf8_encode ($string); // Evite des problèmes dans le XML, même si on "sur-encode"
			$this->pos += $length + 2;
			_log ('String: '.$string);
			return $string;
		} else {
			$this->error ('String expected');
		}
	}

	/**
	 * Extrait tous les éléments d'un collection (entrées d'un tableau ou propriétés d'un objet) et
	 * les ajoute au noeud à compléter.
	 *
	 * @param DOMElement $node Noeud à compléter.
	 * @param string $valueTag Nom des éléments à ajouter au noeud.
	 * @param string $keyAttribute Nom de l'attribut de clef.
	 * @param string $keyTypeAttribute Nom de l'attribut indiquant le type de la clef.
	 */
	protected function fetchArray (DOMElement &$node, $valueTag, $keyAttribute, $keyTypeAttribute = NULL) {
		if(false !== ($matches = $this->eat ('(\d+):{'))) {
			$keyEncodingAttribute = $keyAttribute.'SourceEncoding';
			$size = intval ($matches[1]);
			for ($i = 0; $i < $size; $i++) {
				$key = $this->fetchScalar ($type, $encoding);
				$entryNode = $node->appendChild ($this->doc->createElement ($valueTag));
				$entryNode->setAttribute ($keyAttribute, $key);
				if ($keyTypeAttribute) {
					$entryNode->setAttribute ($keyTypeAttribute, $type);
				}
				if(!empty($encoding)) {
					$entryNode->setAttribute ($keyEncodingAttribute, $encoding);
				}				
				$this->fillNode ($entryNode);
			}
			if(!$this->eat ('}')) {
				$this->error ("} expected");
			}
		} else {
			$this->error ('List of values expected');
		}
	}

	/**
	 * Teste une expression régulière sur la chaîne sérialisée à la position courante.
	 * Si l'expression fonctionne, la position courante est avancée de la taille de la capture.
	 *
	 * @param String $regex Expression régulière à tester.
	 * @return Array les captures de l'expression régulière ou false s'il n'y pas de correspondance..
	 */
	protected function eat ($regex) {
		if (preg_match('/^'.$regex.'/', $this->substr ($this->serialized,  $this->pos), $matches) == 1) {
			$this->pos += $this->strlen ($matches[0]);
			return $matches;
		} else {
			return false;
		}
	}

	/**
	 * Convertit un document XML en chaîne sérialisée.
	 *
	 * @param string $xml Document XML.
	 * @return string Chaîne sérialisée.
	 */
	public function &serializedFromXML ($xml) {
		$this->nodes = array ();
		$this->serialized = '';
		$this->valueCount = 0;

		$this->doc = new DOMDocument ('1.0', 'UTF-8');
		$this->doc->loadXML ($xml);

		if(!$this->doc->firstChild) {
			$this->error("XML parsing error");
		}
			
		// Récupère la version du document
		$this->docVersion = intval($this->doc->firstChild->getAttribute ('version'));
		if($this->docVersion > self::VERSION) {
			$this->error("Cannot unserialize data serialized with class version ".$this->docVersion);
		}
		
		$this->parseNode ($this->doc->firstChild);

		unset ($this->nodes);
		unset ($this->doc);

		return $this->serialized;
	}

	/**
	 * Ajoute des données en fin de Chaîne sérialisée.
	 *
	 * @param string $str Chaîne à ajouter.
	 */
	protected function append ($str) {
		if (func_num_args () > 1) {
			$args = array_slice (func_get_args (), 1);
			$str = vsprintf ($str, $args);
		}
		$this->serialized .= $str;
	}

	/**
	 * Ajoute une description de chaîne dan la chaîne sérialisée.
	 *
	 * @param string $str Chaîne à "sérialiser".
	 * @param string $sep Séparateur optionnel à ajouter après la chaîne.
	 */
	protected function appendString ($str, $sep='') {
		$this->append ('%d:"%s"%s', $this->strlen ($str), $str, $sep);
	}

	/**
	 * Convertit un noeud XML en chaîne sérialisée.
	 *
	 * @param DOMElement $node Le noued à analyser.
	 */
	protected function parseNode (DOMElement &$node) {
		$this->valueCount++;

		$type = $node->getAttribute ('type');

		if ($node->hasAttribute ('id')) {
			$refPos = $this->valueCount;
			$id = $node->getAttribute ('id');
			if ($type == 'array') {
				$this->nodes[$id] = sprintf ('R:%d;', $refPos);
			} else {
				$this->nodes[$id] = sprintf ('r:%d;', $refPos);
			}
		}

		if ($node->hasAttribute ('ref')) {
			$id = intval ($node->getAttribute ('ref'));
			if (isset ($this->nodes[$id])) {
				$this->append ($this->nodes[$id]);
			} else {
				$this->error ("No element with id ".$id." found");
			}
			return;
		}

		switch ($type) {
			case 'object':
				$this->append ('O:');
				$class = $node->getAttribute ('class');
				$this->appendString ($class, ':');
				$this->parseArray ($node->childNodes, 'property', 'name');
				break;

			case 'array':
				$this->append ('a:');
				$this->parseArray ($node->childNodes, 'entry', 'key', 'keyType');
				break;

			case 'null';
				$this->append ('N;');
				break;

			case 'string':
				if($this->docVersion < 1) {
					$this->appendScalar ($node->nodeValue, 'string', $node->getAttribute ('encoding'));
				} else {					
					$this->appendScalar ($node->nodeValue, 'string', $node->getAttribute ('sourceEncoding'));
				}
				break;

			default:
				$this->appendScalar ($node->nodeValue, $type);
		}

	}

	/**
	 * Enter description here...
	 *
	 * @param mixed $scalar
	 * @param string $type
	 * @param string $encoding
	 */
	protected function appendScalar ($scalar, $type, $encoding = NULL) {
		switch ($type) {
			case 'string':
				$this->append ('s:');
				if($this->docVersion < 1) {
					if($encoding == 'base64') {
						$scalar = base64_decode($scalar);
					} else {
						$scalar = utf8_decode ($scalar);
					}
				} elseif($encoding !== NULL) {
					$scalar = $this->unquoteString($scalar, $encoding);
				}
				$this->appendString ($scalar, ';');
				break;

			case 'integer':
				$this->append ('i:%s;', $scalar);
				break;

			case 'double':
				$this->append ('d:%s;', $scalar);
				break;

			case 'boolean':
				$this->append ('b:%d;', (strtolower($scalar) == 'true') ? 1 : 0);
				break;

			default:
				$this->error ('Unhandled scalar type: '.$type);
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param DOMNodeList $nodes
	 * @param unknown_type $childName
	 * @param unknown_type $keyAttribute
	 * @param unknown_type $keyTypeAttribute
	 */
	protected function parseArray (DOMNodeList &$nodes, $childName, $keyAttribute, $keyTypeAttribute = NULL) {
		$keyEncodingAttribute = $keyAttribute.'SourceEncoding';
		$this->append ('%d:{', $nodes->length);
		for ($i = 0; $i < $nodes->length; $i++) {
			$child = $nodes->item($i);
			$keyType = $keyTypeAttribute ? $child->getAttribute ($keyTypeAttribute) : 'string';
			$this->appendScalar ($child->getAttribute ($keyAttribute), $keyType, $child->getAttribute ($keyEncodingAttribute));
			$this->parseNode ($child);
		}
		$this->append ('}');
	}

	/**
	 * Fais remonter une erreur.
	 *
	 * @param string $msg Message d'erreur.
	 */
	protected function error ($msg) {
		throw new CopixXMLException (sprintf ("%s: position #%d, content: %s", $msg, $this->pos, $this->substr($this->serialized, $this->pos)));
	}
	
	/**
	 * Retourne la taille de la chaîne en octets.
	 *
	 * @param string $string La chaîne en question.
	 * @return int Taille de la chaîne en octets.
	 */
	abstract public function strlen($string);
	
	/**
	 * Retourne une portion d'une chaîne.
	 *
	 * @param string $string Chaîne.
	 * @param int $begin Position du début en octets.
	 * @param int $length Taille de la sous-chaîne en octets.
	 */
	abstract public function substr($string, $begin, $length = NULL);

	/**
	 * Recherche une chaîne dans une autre.
	 * 
	 * @param string $string La chaîne à observer.
	 * @param string $pattern La sous-chaîne à retrouver.
	 * @param int $offset Décalage initial.
	 * @return int Position de la sous-chaîne dans la chaîne, en octets, ou FALSE si elle n'a pas été trouvée.
	 */
	abstract public function strpos($string, $pattern, $offset = 0);
	
}

/**
 * Implémentation de AbstractCopixXMLSerializer supportant la surcharge des fonctions standard par mbstring.
 *
 * @package		copix
 * @subpackage	core
 */
class CopixXMLSerializer_with_mb_overload extends AbstractCopixXMLSerializer {
	/**
	 * Retourne la taille de la chaîne en octets.
	 *
	 * @param string $string La chaîne en question.
	 * @return int Taille de la chaîne en octets.
	 */
	public function strlen($string) {
		return mb_strlen($string, '8bit');
	}
	
	/**
	 * Retourne une portion d'une chaîne.
	 *
	 * @param string $string Chaîne.
	 * @param int $begin Position du début en octets.
	 * @param int $length Taille de la sous-chaîne en octets.
	 */
	public function substr($string, $begin, $length = NULL) {
		if($length === NULL) {
			$length = $this->strlen($string) - $begin;
		}
		return mb_substr($string, $begin, $length, '8bit');
	}

	/**
	 * Recherche une chaîne dans une autre.
	 * 
	 * @param string $string La chaîne à observer.
	 * @param string $pattern La sous-chaîne à retrouver.
	 * @param int $offset Décalage initial.* 
	 * @return int Position de la sous-chaîne dans la chaîne, en octets, ou FALSE si elle n'a pas été trouvée.
	 */
	public function strpos($string, $pattern, $offset = 0) {
		return mb_strpos($string, $pattern, $offset, '8bit');
	}
}

/**
 * Implémentation de AbstractCopixXMLSerializer fonctionnant sans la surcharge des fonctions standard par mbstring.
 *
 * @package		copix
 * @subpackage	core
 */

class CopixXMLSerializer_without_mb_overload extends AbstractCopixXMLSerializer {
	/**
	 * Retourne la taille de la chaîne en octets.
	 *
	 * @param string $string La chaîne en question.
	 * @return int Taille de la chaîne en octets.
	 */
	public function strlen($string) {
		return strlen($string);
	}
	
	/**
	 * Retourne une portion d'une chaîne.
	 *
	 * @param string $string Chaîne.
	 * @param int $begin Position du début en octets.
	 * @param int $length Taille de la sous-chaîne en octets.
	 */
	public function substr($string, $begin, $length = NULL) {
		if($length === NULL) {
			$length = $this->strlen($string) - $begin;
		}
		return substr($string, $begin, $length);
	}

	/**
	 * Recherche une chaîne dans une autre.
	 * 
	 * @param string $string La chaîne à observer.
	 * @param string $pattern La sous-chaîne à retrouver.
	 * @pram int $offset Décalage initial.
	 * @return int Position de la sous-chaîne dans la chaîne, en octets, ou FALSE si elle n'a pas été trouvée.
	 */
	public function strpos($string, $pattern, $offset = 0) {
		return strpos($string, $pattern, $offset);
	}
}

/**
 * CopixXMLSerializer permet de linéariser une valeur PHP en document XML, dans un format
 * approprié pour reconstituer la valeur PHP ultérieurement.
 *
 * CopixXMLSerializer utilise en interne la fonction PHP serialize(). La linéarisation est donc soumise
 * aux mêmes règles pour que pour cette fonction.

 * @package		copix
 * @subpackage	core
 */
class CopixXMLSerializer {

	static public function getInstance() {
		if(extension_loaded("mbstring") && ((ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING) == MB_OVERLOAD_STRING)) {
			$instance = new CopixXMLSerializer_with_mb_overload ();
		} else {
			$instance = new CopixXMLSerializer_without_mb_overload ();
		}
		return $instance;
	}

	/**
	 * Sérialise des données au format XML.
	 *
	 * @param mixed $data Données à sérialiser en XML.
	 * @return string Document XML généré par la serialisation.
	 */
	static public function serialize (&$data) {
		$serializer = self::getInstance ();
		return $serializer->serializedToXML (serialize ($data));
	}

	/**
	 * Désérialise des données au format XML.
	 *
	 * @param string $xml Document XML représentant les données.
	 * @return mixed Données désérialisées.
	 */
	static public function &unserialize ($xml) {
		$unserializer = & self::getInstance ();
		$data = & unserialize ($unserializer->serializedFromXML ($xml));
		return $data;
	}

}
?>