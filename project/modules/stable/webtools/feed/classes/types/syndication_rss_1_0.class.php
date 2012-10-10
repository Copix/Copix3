<?php
/**
 * Création d'une syndication au format RSS 1.0
 * Norme respectée : http://web.resource.org/rss/1.0/spec (fr : http://www.scriptol.fr/rss/RSS-1.0.html)
 */
class SyndicationRSS10 extends SyndicationType {
	/**
	 * Format de la date
	 *
	 * @var string
	 */
	private $_dateFormat = 'D, j M Y H:i:\0\0 \G\M\T';

	/**
	 * Protocoles d'url valides
	 *
	 * @var array
	 */
	private $_protocoles = array ('http://', 'https://', 'ftp://');
	
	/**
	 * nombr ede caractères minimum pour une url valide
	 *
	 * @var int
	 */
	private $_urlMinChar = 10;

	/**
	 * Tous les attributs rdf:about doivent être uniques, sauvegarde de ceux qu'on a écrit
	 *
	 * @var array
	 */
	private $_rdfAbout = array ();
	
	/**
	 * Retourne le contenu de type RSS 1.0
	 * 
	 * @return string
	 */
	public function getContent ($pSyndication) {
		// verification des infos obligatoires
		if ($pSyndication->title == null) {
			throw new SyndicationException (_i18n ('feed|errors.badParamValue', array ('NULL', 'title')));
		}
		$this->_assertLink ($pSyndication->link->uri);
		if ($pSyndication->description == null) {
			throw new SyndicationException (_i18n ('feed|errors.badParamValue', array ('NULL', 'description')));
		}
		if ($pSyndication->countItems () == 0) {
			throw new SyndicationException (_i18n ('feed|errors.noItems'));
		}
		
		$xml = new SyndicationXML ($pSyndication->compress);
		
		// header
		$xml->openNode ('rdf:RDF', array (
			'xmlns:rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
			'xmlns' => 'http://purl.org/rss/1.0/',
			'xmlns:content' => 'http://purl.org/rss/1.0/modules/content/'
		));
		
		// channel est une node qui contient les infos générales sur le flux
		$xml->openNode ('channel', array ('rdf:about' => $pSyndication->link->uri));
		$xml->addNode ('title', '<![CDATA[' .$pSyndication->title. ']]>');
		$xml->addNode ('link', $pSyndication->link->uri);
		$xml->addNode ('description', '<![CDATA[' .$pSyndication->description. ']]>');		
		if ($pSyndication->logo->src->uri != null) {
			$xml->addNode ('image', null, array ('rdf:resource' => $pSyndication->logo->src->uri));
		}

		// table des matières des items du flux
		$xml->openNode ('items');
		$xml->openNode ('rdf:Seq');
		for ($boucle = 0; $boucle < $pSyndication->countItems (); $boucle++) {
			$item = $pSyndication->getItem ($boucle);
			$this->_assertLink ($item->link->uri, true);
			$xml->addNode ('rdf:li', null, array ('resource' => $item->link->uri));
		}
    	$xml->closeNode ('rdf:Seq');
    	$xml->closeNode ('items');
    	
    	$xml->closeNode ('channel');
    	
    	// on re affiche un lien vers l'image avec plus d'infos, si on a l'url, le titre, et le lien
		if ($pSyndication->logo->src->uri != null && $pSyndication->logo->title != null && $pSyndication->logo->link->uri != null) {
			$xml->openNode ('image', array ('rdf:about' => $pSyndication->logo->url->uri));
			$xml->addNode ('title', '<![CDATA[' .$pSyndication->logo->title. ']]>');
			$xml->addNode ('link', $pSyndication->logo->link->uri);
			$xml->addNode ('url', $pSyndication->logo->src->uri);
			$xml->closeNode ('image');
		}
				
		// écriture des items
		for ($boucle = 0; $boucle < $pSyndication->countItems (); $boucle++) {
			$item = $pSyndication->getItem ($boucle);
			$pubDate = ($item->publishDate != null) ? date ($this->_dateFormat, $item->publishDate) : null;
			
			$xml->openNode ('item', array ('rdf:about' => $item->link->uri));
			$xml->addNode ('title', '<![CDATA[' .$item->title . ']]>');
			$xml->addNode ('link', $item->link->uri);
    		$xml->addNode ('description', '<![CDATA[' . $item->content->value . ']]>');
			$xml->closeNode ('item');
		}
		
		$xml->closeNode ('rdf:RDF');
		
		return $xml->getContent ();
	}
	
	/**
	 * Vérifie la validité d'un lien, en levant une exception si il n'est pas valide
	 * 
	 * @param string $pLink Lien à vérifier
	 * @param boolean $pIsRdfAbout Si c'est un attribut rdf:about, on vérifie que l'on ne l'a pas déja ajouté
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
			throw new SyndicationException (_i18n ('feed|errors.badLinkValueRss10', array ($pLink, $this->_urlMinChar, implode (', ', $this->_protocoles))));
		}
		
		// si c'est un attribut rdf:about, on vérifie qu'on ne l'a pas déja ajouté
		if ($pIsRdfAbout) {
			// lien déja ajouté
			if (in_array ($pLink, $this->_rdfAbout)) {
				throw new SyndicationException (_i18n ('feed|errors.rdfAboutLinkExists', array ($pLink)));
			} else {
				$this->_rdfAbout[] = $pLink;
			}			
		}
	}
}