<?php
class SyndicationRSS10 {
	// encodage
	private $_encoding = 'UTF-8';
	
	// caractère de fin de ligne
	private $_endLine = "\r\n";
	
	// format de la date
	private $_dateFormat = 'D, j M Y H:i:\0\0 \G\M\T';
	
	// nombre de tabulation pour la node en cours
	private $_nbrTab = 0;
	
	// protocoles d'url valides
	private $_protocoles = array ('http://', 'https://', 'ftp://');
	
	// nombre de caractères minimum pour une url valide
	private $_urlMinChar = 10;

	// tous les attributs rdf:about doivent être uniques, sauvegarde de ceux qu'on a écrit
	private $_rdfAbout = array ();
	
	// compresse l'HTML (pas de retour à la ligne et pas de tabulations)
	public $compress = false;
	
	/**
	 * Retourne le contenu de type RSS 2.0
	 * 
	 * @return string
	 */
	public function getContent ($pSyndication) {
		$content = '';
		
		if ($this->compress) {
			$this->_endLine = '';
			$this->_tab = '';
		} else {
			$this->_endLine = "\r\n";
			$this->_tab = "\t";
		}
		
		// header 
		$content .= '<?xml version="1.0" encoding="' . $this->_encoding . '" ?>' . $this->_endLine;
		$content .= '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://purl.org/rss/1.0/" >' . $this->_endLine;
		$this->_nbrTab++;
		
		// verification des infos obligatoires
		if (is_null ($pSyndication->title)) {
			throw new CopixException (_i18n ('errors.badParamValue', array ('NULL', 'title')));
		}
		$this->_assertLink ($pSyndication->link);
		if (is_null ($pSyndication->description)) {
			throw new CopixException (_i18n ('errors.badParamValue', array ('NULL', 'description')));
		}
		if ($pSyndication->itemsCount () == 0) {
			throw new CopixException (_i18n ('errors.noItems'));
		}
		
		// channel est une node qui contient les infos générales sur le flux
		$content .= $this->_getXmlNode ('channel', null, array ('rdf:about' => $pSyndication->link), false);
		$this->_nbrTab++;
		$content .= $this->_getXmlNode ('title', $pSyndication->title);
		$content .= $this->_getXmlNode ('link', $pSyndication->link);
		$content .= $this->_getXmlNode ('description', $pSyndication->description);		
		// image affichée que si on a l'url, le titre, et le lien
		if (!is_null ($pSyndication->image->url) && !is_null ($pSyndication->image->title) && !is_null ($pSyndication->image->link)) {
			$content .= $this->_getXmlNode ('image', null, array ('rdf:resource' => $pSyndication->image->url));
		}

		// table des matières des items du flux
		$content .= $this->_getXmlNode ('items', '', null, false);
		$this->_nbrTab++;
		$content .= $this->_getXmlNode ('rdf:Seq', '', null, false);
		$this->_nbrTab++;
		for ($boucle = 0; $boucle < $pSyndication->itemsCount (); $boucle++) {
			$item = $pSyndication->getItem ($boucle);
			$this->_assertLink ($item->link, true);
			$content .= $this->_getXmlNode ('rdf:li', null, array ('resource' => $item->link));
		}
		$this->_nbrTab--;
    	$content .= $this->_closeNode ('rdf:Seq');
    	$this->_nbrTab--;
    	$content .= $this->_closeNode ('items');
    	
    	$this->_nbrTab--;
    	$content .= $this->_closeNode ('channel');
    	
    	// on re affiche un lien vers l'image avec plus d'infos, si on a l'url, le titre, et le lien
		if (!is_null ($pSyndication->image->url) && !is_null ($pSyndication->image->title) && !is_null ($pSyndication->image->link)) {
			$content .= $this->_getXmlNode ('image', '', null, false);
			$this->_nbrTab++;
			$content .= $this->_getXmlNode ('title', $pSyndication->image->title);
			$content .= $this->_getXmlNode ('link', $pSyndication->image->link);
			$content .= $this->_getXmlNode ('url', $pSyndication->image->url);
			$this->_nbrTab--;
			$content .= $this->_closeNode ('image');
		}
				
		// écriture des items
		for ($boucle = 0; $boucle < $pSyndication->itemsCount (); $boucle++) {
			$item = $pSyndication->getItem ($boucle);
			$pubDate = (!is_null ($item->pubDate)) ? date ($this->_dateFormat, $item->pubDate) : null;
			
			$content .= $this->_getXmlNode ('item', null, array ('rdf:about' => $item->link), false);
			$this->_nbrTab++;
			$content .= $this->_getXmlNode ('title', $item->title);
			$content .= $this->_getXmlNode ('link', $item->link);
    		$content .= $this->_getXmlNode ('description', $item->description);
    		$this->_nbrTab--;
			$content .= $this->_closeNode ('item');
		}
		
		$this->_nbrTab--;
		$content .= $this->_closeNode ('rdf:RDF');
		
		return $content;
	}
	
	/**
	 * Retourne une node
	 * 
	 * @param string $pNodeName Nom de la node
	 * @param string $pValue Valeur de la node
	 */
	private function _getXmlNode ($pNodeName, $pValue, $pAttributes = null, $closeNode = true) {
		// si on a indiqué une valeur, ou des attributs
		if (!is_null ($pValue) || (is_array ($pAttributes) && count ($pAttributes) > 0)) {
			$functions = _class ('syndication|xmlfunctions');
			
			// création de la chaine des attributs
			$attStr = '';
			if (!is_null ($pAttributes)) {
				foreach ($pAttributes as $attName => $attValue) {
					if (!is_null ($attValue)) {
						$hasAtt = true;
						$attStr .= ' ' . $attName . '="' . $functions->xmlValue ($attValue) . '"';
					}
				}
			}
			
			// si on a une valeur
			if (strlen ($pValue) > 0) {
				return $this->_getTabStr () . '<' . $pNodeName . $attStr . '>' . $functions->xmlValue ($pValue) . '</' . $pNodeName . '>' . $this->_endLine;
				
			// si on n'a pas de valeur
			} else {
				$endStr = ($closeNode) ? ' />' : '>';
				return $this->_getTabStr () . '<' . $pNodeName . $attStr . $endStr . $this->_endLine;
			}
			
		} else {
			return '';
		}
	}
	
	/**
	 * Ferme une node
	 * 
	 * @param string $pNodeName Nom de la node
	 */
	private function _closeNode ($pNodeName) {
		return $this->_getTabStr () . '</' . $pNodeName . '>' . $this->_endLine;
	}
	
	/**
	 * Retourne une chaine avec le nombre de tabulations nécessaires
	 * 
	 * @return string
	 */
	private function _getTabStr () {
		$tabStr = '';
		for ($boucle = 0; $boucle < $this->_nbrTab; $boucle++) {
			$tabStr .= $this->_tab;
		}
		return $tabStr;
	}
	
	/**
	 * Vérifie la validité d'un lien
	 * 
	 * @param string $pLink Lien à vérifier
	 */
	private function _assertLink ($pLink, $pIsRdfAbout = false) {
		$isValid = false;
		
		// longueur du lien trop courte
		if (strlen ($pLink) < $this->_urlMinChar) {
			$isValid = false;
			
		// longueur du lien ok
		} else {
			// vérification du protocole du lien
			foreach ($this->_protocoles as $protIndex => $protValue) {
				// si ce protocole est celui du lien
				if (substr ($pLink, 0, strlen ($protValue)) == $protValue) {
					$isValid = true;
				}
			}
		}
		
		// lien non valide, on génère une exception
		if (!$isValid) {
			throw new CopixException (_i18n ('errors.badLinkValueRss10', array ($pLink, $this->_urlMinChar, implode (', ', $this->_protocoles))));
		}
		
		// si c'est un attribut rdf:about, on vérifie qu'on ne l'a pas déja ajouté
		if ($pIsRdfAbout) {
			// lien déja ajouté
			if (in_array ($pLink, $this->_rdfAbout)) {
				throw new CopixException (_i18n ('errors.rdfAboutLinkExists', array ($pLink)));
			} else {
				$this->_rdfAbout[] = $pLink;
			}			
		}
	}
}
?>