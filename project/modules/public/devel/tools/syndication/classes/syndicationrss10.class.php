<?php
/**
 * Création d'une syndication au format RSS 1.0
 * Norme respectée : http://web.resource.org/rss/1.0/spec (fr : http://www.scriptol.fr/rss/RSS-1.0.html)
 */
class SyndicationRSS10 {
	// format de la date
	private $_dateFormat = 'D, j M Y H:i:\0\0 \G\M\T';
	
	// protocoles d'url valides
	private $_protocoles = array ('http://', 'https://', 'ftp://');
	
	// nombre de caractères minimum pour une url valide
	private $_urlMinChar = 10;

	// tous les attributs rdf:about doivent être uniques, sauvegarde de ceux qu'on a écrit
	private $_rdfAbout = array ();
	
	/**
	 * Retourne le contenu de type RSS 2.0
	 * 
	 * @return string
	 */
	public function getContent ($pSyndication) {
		// verification des infos obligatoires
		if (is_null ($pSyndication->title)) {
			throw new CopixException (_i18n ('errors.badParamValue', array ('NULL', 'title')));
		}
		$this->_assertLink ($pSyndication->link->uri);
		if (is_null ($pSyndication->description)) {
			throw new CopixException (_i18n ('errors.badParamValue', array ('NULL', 'description')));
		}
		if ($pSyndication->itemsCount () == 0) {
			throw new CopixException (_i18n ('errors.noItems'));
		}
		
		$xml = _class ('xmlfunctions', array ($pSyndication->compress));
		
		// header
		$xml->openNode ('rdf:RDF', array ('xmlns:rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'xmlns' => 'http://purl.org/rss/1.0/'));
		
		// channel est une node qui contient les infos générales sur le flux
		$xml->openNode ('channel', array ('rdf:about' => $pSyndication->link->uri));
		$xml->addNode ('title', $pSyndication->title);
		$xml->addNode ('link', $pSyndication->link->uri);
		$xml->addNode ('description', $pSyndication->description);		
		if (!is_null ($pSyndication->logo->src->uri)) {
			$xml->addNode ('image', null, array ('rdf:resource' => $pSyndication->logo->src->uri));
		}

		// table des matières des items du flux
		$xml->openNode ('items');
		$xml->openNode ('rdf:Seq');
		for ($boucle = 0; $boucle < $pSyndication->itemsCount (); $boucle++) {
			$item = $pSyndication->getItem ($boucle);
			$this->_assertLink ($item->link->uri, true);
			$xml->addNode ('rdf:li', null, array ('resource' => $item->link->uri));
		}
    	$xml->closeNode ('rdf:Seq');
    	$xml->closeNode ('items');
    	
    	$xml->closeNode ('channel');
    	
    	// on re affiche un lien vers l'image avec plus d'infos, si on a l'url, le titre, et le lien
		if (!is_null ($pSyndication->logo->src->uri) && !is_null ($pSyndication->logo->title) && !is_null ($pSyndication->logo->link->uri)) {
			$xml->openNode ('image', array ('rdf:about' => $pSyndication->logo->url->uri));
			$xml->addNode ('title', $pSyndication->logo->title);
			$xml->addNode ('link', $pSyndication->logo->link->uri);
			$xml->addNode ('url', $pSyndication->logo->src->uri);
			$xml->closeNode ('image');
		}
				
		// écriture des items
		for ($boucle = 0; $boucle < $pSyndication->itemsCount (); $boucle++) {
			$item = $pSyndication->getItem ($boucle);
			$pubDate = (!is_null ($item->publishDate)) ? date ($this->_dateFormat, $item->publishDate) : null;
			
			$xml->openNode ('item', array ('rdf:about' => $item->link->uri));
			$xml->addNode ('title', $item->title);
			$xml->addNode ('link', $item->link->uri);
    		$xml->addNode ('description', $item->content->value);
			$xml->closeNode ('item');
		}
		
		$xml->closeNode ('rdf:RDF');
		
		return $xml->getContent ();
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