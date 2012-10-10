<?php
/**
* @package   copix
* @subpackage core
* @author    Guillaume Perr�al
* @copyright CopixTeam
* @link      http://copix.org
* @license   http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file

*

/**
 * Classe de base pour les exceptions de linéarisation XML. 
 * @package		copix
 * @subpackage	core
 */
class CopixXMLException extends CopixException {}

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
	
	/**
	 * Sérialise des données au format XML.
	 *
	 * @param mixed $data Données à sérialiser en XML.
	 * @return string Document XML généré par la serialisation.
	 */
	static public function serialize(&$data) {
		$serializer = new CopixXMLSerializer();
		return $serializer->serializedToXML(serialize($data));
	}

	/**
	 * Désérialise des données au format XML.
	 *
	 * @param string $xml Document XML représentant les données.
	 * @return mixed Données désérialisées.
	 */
	static public function &unserialize($xml) {
		$unserializer = new CopixXMLserializer();
		$data =& unserialize($unserializer->serializedFromXML($xml));
		return $data;
	}
	
	/**
	 * Chaîne sérialisée représentant les données.
	 *
	 * @var string
	 */
	private $serialized;
	
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
	protected function serializedToXML($serialized) {
		$this->pos = 0;
		$this->nodes = array(null);
		$this->serialized = $serialized;
		
		$this->doc =& new DOMDocument('1.0', 'UTF-8');

		$node =& $this->doc->appendChild($this->doc->createElement('data'));
		$node->setAttribute('phpversion', phpversion());
		//$node->appendChild($this->doc->createComment($this->serialized));
		
		$this->fillNode($node);
		
		$xml = $this->doc->saveXML();
		
		unset($this->nodes);
		unset($this->doc);

		return $xml;
	}
	
	/**
	 * Complète un noeud XML à partir des données à la position courante de la chaîne sérialisée.
	 * Cette fonction constitue le coeur de la transformation.
	 *
	 * @param DOMElement $node Noeud à compléter.
	 */
	protected function fillNode(DOMElement &$node) {
		$this->nodes[] = $node;
		
		if(FALSE !== ($matches = $this->eat('N;'))) {
			$this->debug('NULL');
			$node->setAttribute('type', 'null');
			
		} elseif(FALSE !== ($matches = $this->eat('a:'))) {
			$this->debug('Array');
			$node->setAttribute('type', 'array');				
			$this->fetchArray($node, 'entry', 'key', 'keyType');
			
		} elseif(FALSE !== ($matches = $this->eat('O:'))) {
			$class = $this->fetchString();
			$this->debug('Object: '.$class);
						
			$node->setAttribute('type', 'object');
			$node->setAttribute('class', $class);
			$this->fetchArray($node, 'property', 'name');

		} elseif(FALSE !== ($matches = $this->eat('[rR]:(\d+);'))) {		
			$refPos = intval($matches[1]);
			$refNode =& $this->nodes[$refPos];
			if(!$refNode->hasAttribute('id')) {
				$refNode->setAttribute('id',$refPos);
			}
			$node->setAttribute('ref', $refPos);
					
		} else {
			$scalar = $this->fetchScalar(&$type);
			$node->setAttribute('type', $type);
			switch($type) {
				case 'string':
					if(strpos($scalar, "\0") !== FALSE) {
						// Les \0 passent mal en XML => on encode en base64
						$scalar = base64_encode($scalar);
						$node->setAttribute('encoding', 'base64');
					}
					$scalarNode = $this->doc->createTextNode($scalar);
					break;

				case 'boolean':
					$scalarNode = $this->doc->createTextNode($scalar ? "true" : "false");
					break;
				
				default:
					$scalarNode = $this->doc->createTextNode($scalar);
			}
			$node->appendChild($scalarNode);
		}

	}
	
	/**
	 * Extrait un scalaire (entier, flottant ou booléen) de la Chaîne sérialisée. 
	 *
	 * @param string &$type Le type de scalaire.
	 * @return mixed Le scalaire extrait, sous la forme d'une chaine.
	 */
	protected function fetchScalar(&$type) {
		if(FALSE !== ($matches = $this->eat('i:([^;]+);'))) {
			$this->debug('Integer: '.$matches[1]);
			$type = 'integer';
			return $matches[1];
			
		} elseif(FALSE !== ($matches = $this->eat('d:([^;]+);'))) {
			$this->debug('Double: '.$matches[1]);
			$type = 'double';
			return $matches[1];
			
		} elseif(FALSE !== ($matches = $this->eat('b:([01]);'))) {
			$this->debug('Boolean: '.$matches[1]);
			$type = 'boolean';
			return intval($matches[1]) == 1 ? true : false;

		} elseif(FALSE !== ($matches = $this->eat('s:'))) {
			$type = 'string';
			return $this->fetchString();
			
		} else {
			$this->error('Scalar expected');
		}
	}

	/**
	 * Extrait une chaîne de caractères ("string") de la chaîne sérialisée.
	 *
	 * @return La chaîne extraite.
	 */
	protected function fetchString() {
		if(FALSE !== ($matches = $this->eat('(\d+):"'))) {
			$length = intval($matches[1]);
			$string = substr($this->serialized, $this->pos, $length);
			$string = utf8_encode($string); // Evite des problèmes dans le XML, même si on "sur-encode"
			$this->pos += $length + 2;
			$this->debug('String: '.$string);
			return $string;
		} else {
			$this->error('String expected');
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
	protected function fetchArray(DOMElement &$node, $valueTag, $keyAttribute, $keyTypeAttribute = NULL) {
		if(FALSE !== ($matches = $this->eat('(\d+):{'))) {	
			$size = intval($matches[1]);
			for($i = 0; $i < $size; $i++) {
				$key = $this->fetchScalar($type);
				$entryNode = $node->appendChild($this->doc->createElement($valueTag));
				$entryNode->setAttribute($keyAttribute, $key);
				if($keyTypeAttribute) {
					$entryNode->setAttribute($keyTypeAttribute, $type);
				}
				$this->fillNode($entryNode);
			}
			if(!$this->eat('}')) {
				$this->error("} expected");
			}
		} else {
			$this->error('List of values expected');
		}
	}
	
	/**
	 * Teste une expression régulière sur la chaîne sérialisée à la position courante.
	 * Si l'expression fonctionne, la position courante est avancée de la taille de la capture. 
	 *
	 * @param String $regex Expression régulière à tester.
	 * @return Array les captures de l'expression régulière ou FALSE s'il n'y pas de correspondance..
	 */
	protected function eat($regex) {
		if(preg_match('/^'.$regex.'/', substr($this->serialized,  $this->pos), $matches) == 1) {
			$this->pos += strlen($matches[0]);
			return $matches;
		} else {
			return FALSE;
		}
	}

	/**
	 * Convertit un document XML en chaîne sérialisée.
	 *
	 * @param string $xml Document XML.
	 * @return string Chaîne sérialisée.
	 */
	protected function &serializedFromXML($xml) {	
		$this->nodes = array();
		$this->serialized = '';
		$this->valueCount = 0;
		
		$this->doc = new DOMDocument('1.0', 'UTF-8');
		$this->doc->loadXML($xml);
		$this->parseNode($this->doc->firstChild);
		
		unset($this->nodes);
		unset($this->doc);
		
		return $this->serialized;		
	}
	
	/**
	 * Ajoute des données en fin de Chaîne sérialisée.
	 *
	 * @param string $str Chaîne à ajouter.
	 */
	protected function append($str) {
		if(func_num_args() > 1) {
			$args = array_slice(func_get_args(), 1);
			$str = vsprintf($str, $args);
		}
		$this->serialized .= $str;
		//var_dump($this->serialized);
	}
	
	/**
	 * Ajoute une description de chaîne dan la chaîne sérialisée.
	 *
	 * @param string $str Chaîne à "sérialiser".
	 * @param string $sep Séparateur optionnel à ajouter après la chaîne.
	 */
	protected function appendString($str, $sep='') {
		$this->append('%d:"%s"%s', strlen($str), $str, $sep);
	}
	
	/**
	 * Convertit un noeud XML en chaîne sérialisée.
	 *
	 * @param DOMElement $node Le noued à analyser.
	 */
	protected function parseNode(DOMElement &$node) {
		$this->valueCount++;
		
		$type = $node->getAttribute('type');
		
		if($node->hasAttribute('id')) {
			$refPos = $this->valueCount;
			$id = $node->getAttribute('id');
			if($type == 'array') {
				$this->nodes[$id] = sprintf('R:%d;', $refPos);
			} else {
				$this->nodes[$id] = sprintf('r:%d;', $refPos);
			}			
		}
		
		if($node->hasAttribute('ref')) {
			$id = intval($node->getAttribute('ref'));
			if(isset($this->nodes[$id])) {
				$this->append($this->nodes[$id]);
			} else {
				$this->error("No element with id ".$id." found");
			}
			return;
		}
		
		switch($type) {
			case 'object':
				$this->append('O:');
				$class = $node->getAttribute('class');
				$this->appendString($class, ':');
				$this->parseArray($node->childNodes, 'property', 'name');
				break;
				
			case 'array':
				$this->append('a:');
				$this->parseArray($node->childNodes, 'entry', 'key', 'keyType');
				break;
				
			case 'null';
				$this->append('N;');
				break;
				
			case 'string':
				$enc = $node->getAttribute('encoding');
				if($enc == 'base64') {
					$this->appendScalar(base64_decode($node->nodeValue), 'string');
					break;					
				}

			default:
				$this->appendScalar($node->nodeValue, $type);
		}
		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $scalar
	 * @param unknown_type $type
	 */
	protected function appendScalar($scalar, $type) {
		switch($type) {
			case 'string': 
				$this->append('s:');
				$scalar = utf8_decode($scalar);
				$this->appendString($scalar, ';');
				break;
				
			case 'integer':
				$this->append('i:%s;', $scalar);
				break;

			case 'double':
				$this->append('d:%s;', $scalar);
				break;

			case 'boolean':
				$this->append('b:%d;', (strtolower($scalar) == 'true') ? 1 : 0);
				break;	
				
			default:
				$this->error('Unhandled scalar type: '.$type);
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
	protected function parseArray(DOMNodeList &$nodes, $childName, $keyAttribute, $keyTypeAttribute = NULL) {
		$this->append('%d:{', $nodes->length);
		for($i = 0; $i < $nodes->length; $i++) {
			$child = $nodes->item($i);
			$keyType = $keyTypeAttribute ? $child->getAttribute($keyTypeAttribute) : 'string';
			$this->appendScalar($child->getAttribute($keyAttribute), $keyType);
			$this->parseNode($child);
		}
		$this->append('}');
	}
		
	/**
	 * Fais remonter une erreur.
	 *
	 * @param string $msg Message d'erreur.
	 */
	protected function error($msg) {
		throw new CopixXMLException(sprintf("%s: position #%d, content: %s", $msg, $this->pos, substr($this->serialized, $this->pos)));
	}
	
	/**
	 * Affiche une Chaîne de déboggage.
	 *
	 * @param string $msg Chaîne à afficher.
	 */
	protected function debug($msg) {
		//printf("<pre>%s</pre>", $msg);
	}
	
}

?>