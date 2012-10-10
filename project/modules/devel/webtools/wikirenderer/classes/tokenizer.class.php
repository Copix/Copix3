<?php
/**
 * @package		webtools
 * @subpackage	wikirenderer
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author		Julien SALLEYRON
 * @link		http://www.copix.org
 */


_classInclude ('wikirenderer|abstracttokenizercomponent');
_classInclude ('wikirenderer|tokenfactory');


/**
 * Transforme un text en arbre de token un utisant les composants passés en paramètre
 * 
 * @package		webtools
 * @subpackage	wikirenderer
 */
class tokenizer {
	
	private $_trunk = null;

	private $_cache = false;
	
	private $_string = null;

	private $_buffer = array ();
	
	private $_components = null;
	private $_componentsToParse = null;
	
	private $_arPos = array ();

	private $_currentToken = null;
	
	private $_position = 0;
	private $_bPosition = 0;
	
	private $_report = null;
	
	private $_errors = array ();
	
	public function __construct () {
		$this->_errors = new CopixErrorObject();
	}
	
	public function isFromCache () {
		return $this->_cache;
	}
	
	public function getErrors () {
		return $this->_errors;
	}
	
	/**
	 * Tokenize le text
	 *
	 * @param string $this->_string Text a tokenizé
	 * @param array $arComponents Tableau de composants tokenizant
	 * @return Token l'arbre de token
	 */
	public function getTokens ($pString, $arComponents, $pUseCache = true) {
		if (CopixCache::exists(array ($pString, $arComponents)) && $pUseCache) {
			if (CopixCache::exists(array ($pString, $arComponents, 'errors'))) {
				$this->_errors = CopixCache::read(array ($pString, $arComponents, 'errors'));
			}
			_classInclude ('wikirenderer|token');
			$this->_cache = true;
			return CopixCache::read(array ($pString, $arComponents));
		}
		
		$this->_string = $pString;
		$this->_components = $arComponents;
		$this->_componentsToParse = array ();
		
		$this->_arPos = array ();
		
		//On crée un tableau qui contiendra les démarrages de composants si ils possédait un getStartTagsPosition
		foreach ($this->_components as $component) {
			if (($arPos = $component->getStartTagsPosition ($this->_string)) !== false) {
				foreach ($arPos as $pos=>$tag) {
					if (!isset ($this->_arPos[$pos]) || strlen ($tag) > strlen ($this->_arPos[$pos]->tag)) {
						$temp = new StdClass ();
						$temp->tag = $tag;
						$temp->component = $component;
						$this->_arPos[$pos] = $temp;
					}
				}
			} else {
				$this->_componentsToParse[] = $component;
			}
		}
		
		//Initialisation du buffer
		$this->_buffer = array ();
		
		//Création de la racine de l'arbre, un document
		$document = TokenFactory::create (null, 'document');
		//La racine devient le token courant
		$this->_currentToken = $document;
		
		//Debut du parsing du texte
		$this->_length = strlen($this->_string);
		$this->_position = 0;
		while ($this->_position < $this->_length) {
			//Fermeture de token ?
			if ($this->_findEndTag()) {
				//La recherche a mise a jour _position pour continuer le parsing
				continue;
			}
			
			//Ouverture de token ?
			if ($this->_findStartTag()) {
				//La recherche a mise a jour _position pour continuer le parsing
				continue;
			}
			
			//Si on as rien trouvé, on rempli le buffer et parse la lettre suivante
			$this->_position++;
		}
		
		//On mets la fin du buffer a la fin du noeud courant
		$this->_bufferFlush();
		//Ecriture des caches
		CopixCache::write (array ($pString, $arComponents), $document);
		CopixCache::write (array ($pString, $arComponents, 'errors'), $this->_errors);
		return $document;
	}

	/**
	 * Parse la chaine courante à la recherche de la fermeture du token courant
	 *
	 */
	private function _waitForCloseTag () {
		//*
		if (($endTag = $this->_currentToken->getComponent ()->getEndTag()) != null) {
			$temp = explode ($endTag, substr($this->_string, $this->_position), 2);
			$this->_position += strlen ($temp[0]);
			$this->_bufferFlush();
			$this->_position += strlen ($endTag);
			$this->_bPosition = $this->_position;
			$this->_currentToken = $this->_currentToken->getParent ();
			return;
		}
		//*/
		while ($this->_position < $this->_length) {
			if (($data = $this->_currentToken->getComponent()->getEndingTag (substr ($this->_string,$this->_position), $this->_currentToken)) !== false) {
				$this->_bufferFlush();
				$this->_currentToken->setEndTag ($data);
				$this->_position += $this->_currentToken->getComponent()->getEndTagLength($data);
				$this->_bPosition = $this->_position;
				$this->_currentToken = $this->_currentToken->getParent ();
				return ;
			}
			$this->_position++;
		}
	}
	
	/**
	 * Méthode qui cherche si la chaine courante commence par un tag de fermeture
	 *
	 * @return boolean Retourne si oui ou non elle commence par un tag de fermeture
	 */
	private function _findEndTag () {
			//Boucle pour tester les fermetures
			$openTagParsing = $this->_currentToken;
			//Tableau temporaire pour fermer et rouvrir les tags imbriqués bizarrement
			$arTemp = array ();
			//On démarre la boucle pour remonter les tags ouverts non fermés
			$string = substr ($this->_string, $this->_position);
			while ($openTagParsing->getParent () != null) {
				//Si c'est le bon tag on voit pour le fermer
				if (($openTagParsing->getComponent() != null 
				&& ($data = $openTagParsing->getComponent()->getEndingTag ($string, $openTagParsing)) !== false)) {
					$this->_bufferFlush();
					
					$openTagParsing->setEndTag ($data);
					$this->_position += $openTagParsing->getComponent()->getEndTagLength ($data);
					$this->_bPosition = $this->_position;
					$name = get_class($openTagParsing->getComponent());
					$this->_currentToken = $openTagParsing->getParent ();
					//On parcours les tags non fermé (en erreur)
					foreach (array_reverse ($arTemp) as $tag) {
						$childName = get_class($tag->getComponent());
						$this->_errors->addError($this->_errors->countErrors(), "Un tag [$name] a été fermé avant la fermeture d'un de ses enfants[$childName] au caractère ".$this->_position.'['.substr($this->_string, $this->_position, 10).']');
						$start = TokenFactory::create ($this->_currentToken, $tag->getComponent());
						$start->setStartTag ($tag->getStartTag());
						$this->_currentToken->addChild ($start);
						$this->_currentToken = $start;
					}
					return true;
				//Ce n'est pas le bon on test le précédent
				} else {
					//On stock les tags qui auraient du etre fermé
					$arTemp [] = $openTagParsing;
					$openTagParsing = $openTagParsing->getParent();
				}
			}
			return false;
		
	}
	
	
	private function _putStartTag ($pData, $pComponent) {
		$this->_bufferFlush();
		$this->_position += $pComponent->getStartTagLength ($pData);
		$this->_bPosition = $this->_position;
		$start = TokenFactory::create ($this->_currentToken, $pComponent);
		$start->setStartTag ($pData);
		
		$this->_currentToken->addChild ($start);
		
		if ($pComponent->isEscapeComponent ()) {
			return $this->_tryToEscape ($start);
		}
		
		if (!$pComponent->isContainerComponent()) {
			return true;
		}
		
		$this->_currentToken = $start;
		if (!$pComponent->contentMustBeParse()) {
			$this->_waitForCloseTag ();
			return true;
		}
		return true;
	}
	
	/**
	 * Méthode qui cherche si la chaine courante commence par un tag d'ouverture
	 *
	 * @return boolean Retourne si oui ou non elle commence par un tag d'ouverture
	 */
	private function _findStartTag () {
		//Boucle pour tester les ouvertures
		    //Si cette position est marqué comme une position de départ deja trouvé
			if (isset ($this->_arPos[$this->_position])) {
				$component = $this->_arPos[$this->_position]->component;
				$data = $this->_arPos[$this->_position]->tag;
				return $this->_putStartTag ($data, $component);
			}
		
			$string = substr ($this->_string, $this->_position);
			foreach ($this->_componentsToParse as $component) {
				if (($data = $component->getStartingTag ($string)) != false) {
					$this->_putStartTag ($data, $component);
					return $this->_putStartTag ($data, $component);
				}
			}
			return false;
		
	}

	
	private function _bufferFlush() {
		if ($this->_position > $this->_bPosition ) {
			$this->_currentToken->addChild (TokenFactory::create ($this->_currentToken, 'text', $buffer = substr ($this->_string, $this->_bPosition, $this->_position - $this->_bPosition)));
			return $buffer;
		}
		return null;
		
	}
	
	private function _tryToEscape ($pStart) {
		$string = substr ($this->_string, $this->_position);
		foreach ($this->_components as $componentToEscape) {
			if (($data = $componentToEscape->getStartingTag ($string)) !== false) {
				$this->_currentToken->addChild (TokenFactory::create ($this->_currentToken, 'text', $data));
				$this->_position += $componentToEscape->getEndTagLength ($data);
				return true;
			} else if ($componentToEscape->isContainerComponent() && ($data = $componentToEscape->getEndingTag ($string, null)) !== false) {
				$this->_currentToken->addChild (TokenFactory::create ($this->_currentToken, 'text', $data));
				$this->_position += $componentToEscape->getEndTagLength ($data);
				return true;
			}
			
		}
		$this->_currentToken->addChild (TokenFactory::create ($this->_currentToken, 'text', $pStart->getStartTag()));
		return true;
	}
}

?>