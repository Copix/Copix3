<?php
/**
 * Création d'une syndication au format ATOM 1.0
 * Norme respectée : http://www.atomenabled.org/
 */
class SyndicationATOM10 extends SyndicationType {
	/**
	 * Format de la date
	 *
	 * @var string
	 */
	private $_dateFormat = 'Y-m-d\TH:i:s\Z';
	
	/**
	 * Objet pour générer le XML, évite de le passer en parmaètre à toutes les fonctions internes
	 *
	 * @var SyndicationXML
	 */
	private $_xml = null;
	
	/**
	 * Retourne le contenu de type RSS 2.0
	 * 
	 * @param syndication $pSyndication Objet de type syndication qui contient les informations
	 * @return string
	 */
	public function getContent ($pSyndication) {
		// verification des infos obligatoires
		if ($pSyndication->title == null) {
			throw new SyndicationException (_i18n ('feed|errors.badParamValue', array ('NULL', 'title')));
		}
		
		$xml = new SyndicationXML ($pSyndication->compress);
		$this->_xml = $xml;
		
		// header 
		$xml->openNode ('feed', array ('xmlns' => 'http://www.w3.org/2005/Atom', 'xmlns:content' => 'http://purl.org/rss/1.0/modules/content/'));
		
		// id unique
		if ($pSyndication->id->value != null) {
			$id = 'urn:uuid:' . $pSyndication->id->value;
		} else if ($pSyndication->link->uri != null) {
			$id = $this->_getUrlWithSlash ($pSyndication->link->uri);
		} else {
			$id = $pSyndication->id->generate ('urn:uuid:');
		}		
		// identifiant
		$xml->addNode ('id', $id);
		// titre
		$xml->addNode ('title', $pSyndication->title);
		// sous titre
		$xml->addNode ('subtitle', $pSyndication->description);
		// date de dernière modification
		if ($pSyndication->lastBuildDate != null) {
			$updated = date ($this->_dateFormat, $pSyndication->lastBuildDate);
		} else if ($pSyndication->pubDate != null) {
			$updated = date ($this->_dateFormat, $pSyndication->pubDate);
		} else {
			$updated = date ($this->_dateFormat, time ());
		}
		$xml->addNode ('updated', $updated);
		// auteurs
		for ($boucle = 0; $boucle < $pSyndication->countAuthors (); $boucle++) {
			$this->_addPerson ($pSyndication->getAuthor ($boucle), 'author');
		}
		// contributeurs
		for ($boucle = 0; $boucle < $pSyndication->countContributors (); $boucle++) {
			$this->_addPerson ($pSyndication->getContributor ($boucle), 'contributor');
		}
		// lien
		$this->_addLink ($pSyndication->link);
		// categories
		$this->_addCategories ($pSyndication);
		// générateur
		if ($pSyndication->generator->name != null) {
			$attributes = array ();
			if ($pSyndication->generator->link->uri != null) {
				$attributes['uri'] = $pSyndication->generator->link->uri;
			}
			if ($pSyndication->generator->version != null) {
				$attributes['version'] = $pSyndication->generator->version;
			}
			$xml->addNode ('generator', $pSyndication->generator->name, $attributes);
		}
		// icone
		$xml->addNode ('icon', $pSyndication->icon->src->uri);
		// logo
		$xml->addNode ('logo', $pSyndication->logo->src->uri);
		// license
		$xml->addNode ('rights', $pSyndication->copyright->value);
		
		// entrées (entry)
		for ($boucle = 0; $boucle < $pSyndication->countItems (); $boucle++) {
			$this->_addItem ($pSyndication->getItem ($boucle), 'entry');
		}
		
		$xml->closeNode ('feed');
		
		return $xml->getContent ();
	}
	
	/**
	 * Ajoute une node de type lien
	 * 
	 * @param SyndicationLink $pSyndicationLink Informations sur le lien
	 * @param string $pNodeName Nom de la node
	 */
	private function _addLink ($pSyndicationLink, $pNodeName = 'link') {
		if ($pSyndicationLink->uri != null) {
			$attributes = array ('href' => $pSyndicationLink->uri);
			if ($pSyndicationLink->rel != null) {
				$attributes['rel'] = $pSyndicationLink->rel;
			}
			if ($pSyndicationLink->type != null) {
				$attributes['type'] = $pSyndicationLink->type;
			}
			if ($pSyndicationLink->urilang != null) {
				$attributes['hreflang'] = $pSyndicationLink->urilang;
			}
			if ($pSyndicationLink->title != null) {
				$attributes['title'] = $pSyndicationLink->title;
			}
			if ($pSyndicationLink->resourceLength != null) {
				$attributes['length'] = $pSyndicationLink->length;
			}
			$this->_xml->addNode ($pNodeName, null, $attributes);
		}
	}
	
	/**
	 * Ajoute une node de type text
	 * 
	 * @param SyndicationText $pSyndicationText Texte
	 * @param string $pNodeName Nom de la node
	 * @param bool $pSrcExists L'attribut src existe-t-il dans la node à ajouter
	 */
	private function _addText ($pSyndicationText, $pNodeName, $pSrcExists = true) {
		if ($pSrcExists && $pSyndicationText->src->uri  != null) {
			$this->_xml->addNode ($pNodeName, null, array ('src' => $pSyndicationText->src->uri));
		} else if ($pSyndicationText->value != null) {
			$attributes = ($pSyndicationText->type != null) ? array ('type' => $pSyndicationText->type) : null;
			$this->_xml->addNode ($pNodeName, $pSyndicationText->value, $attributes);
		}
	}
	
	/**
	 * Ajoute des catégories
	 * 
	 * @param stdclass $pObject Catégorie
	 * @param string $pNodeName Nom de la node
	 */
	private function _addCategories ($pObject, $pNodeName = 'category') {
		for ($boucle = 0; $boucle < $pObject->countCategories (); $boucle++) {
			$category = $pObject->getCategory ($boucle);
			if ($category->id == null) {
				throw new SyndicationException (_i18n ('feed|errors.badParamValue', array ('NULL', 'SyndicationCategory->id')));
			}
			$attributes = array ('term' => $category->id);
			if ($category->name != null) {
				$attributes['label'] = $category->name;
			}
			if ($category->link->uri != null) {
				$attributes['scheme'] = $category->link->uri;
			}
			$this->_xml->addNode ($pNodeName, null, $attributes);
		}
	}
	
	/**
	 * Ajoute une personne
	 * 
	 * @param SyndicationPerson $pSyndicationPerson Personne
	 * @param string $pNodeName Nom de la node
	 */
	private function _addPerson ($pSyndicationPerson, $pNodeName) {
		if ($pSyndicationPerson->name != null) {
			$this->_xml->openNode ($pNodeName);
			$this->_xml->addNode ('name', $pSyndicationPerson->name);
			$this->_xml->addNode ('email', $pSyndicationPerson->email);
			$this->_xml->addNode ('uri', $pSyndicationPerson->webSite->uri);
			$this->_xml->closeNode ($pNodeName);
		}
	}
	
	/**
	 * Ajoute un item
	 * 
	 * @param SyndicationItem $pSyndicationItem Item
	 * @param string $pNodeName Nom de la node
	 * @param boolean Indique si on veut ajouter la source
	 */
	private function _addItem ($pSyndicationItem, $pNodeName, $pAddSource = true) {
		$this->_xml->openNode ($pNodeName);
		// identifiant
		if ($pSyndicationItem->id->value != null) {
			$id = $pSyndicationItem->id->value;
		} else if ($pSyndicationItem->link->uri != null) {
			$id = $this->_getUrlWithSlash ($pSyndicationItem->link->uri);
		} else {
			$id = $pSyndicationItem->id->generate ();
		}
		$this->_xml->addNode ('id', $id);
		// titre
		if ($pSyndicationItem->title == null) {
			throw new SyndicationException (_i18n ('feed|errors.badParamValue', array ('NULL', 'SyndicationItem->title')));
		}
		$this->_xml->addNode ('title', $pSyndicationItem->title);
		// date de modification
		if ($pSyndicationItem->updateDate != null) {
			$updated = date ($this->_dateFormat, $pSyndicationItem->updateDate);
		} else if ($pSyndicationItem->pubDate != null) {
			$updated = date ($this->_dateFormat, $pSyndicationItem->pubDate);
		} else {
			throw new SyndicationException (_i18n ('feed|errors.badParamValue', array ('NULL', 'SyndicationItem->updateDate')));
		}
		$this->_xml->addNode ('updated', $updated);
		// auteurs
		for ($boucle2 = 0; $boucle2 < $pSyndicationItem->countAuthors (); $boucle2++) {
			$this->_addPerson ($pSyndicationItem->getAuthor ($boucle2), 'author');
		}
		// contenu
		$this->_addText ($pSyndicationItem->content, 'content');
		// lien
		$this->_addLink ($pSyndicationItem->link);
		// résumé
		$this->_addText ($pSyndicationItem->summary, 'summary', false);
		// categories
		$this->_addCategories ($pSyndicationItem);
		// contributeurs
		for ($boucle2 = 0; $boucle2 < $pSyndicationItem->countContributors (); $boucle2++) {
			$this->_addPerson ($pSyndicationItem->getContributor ($boucle2), 'contributor');
		}
		// date de la 1ère publication
		$published = ($pSyndicationItem->publishDate != null) ? date ($this->_dateFormat, $pSyndicationItem->publishDate) : null;
		$this->_xml->addNode ('published', $published);
		// source
		if ($pAddSource && $pSyndicationItem->source->title != null) {
			$this->_addItem ($pSyndicationItem->source, 'source', false);
		}
		// license
		$this->_addText ($pSyndicationItem->copyright, 'rights', false);
		
		$this->_xml->closeNode ($pNodeName);
	}
}