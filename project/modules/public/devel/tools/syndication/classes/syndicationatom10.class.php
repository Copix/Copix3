<?php
/**
 * Création d'une syndication au format ATOM 1.0
 * Norme respectée : http://www.atomenabled.org/
 */
class SyndicationATOM10 {
	// format de la date
	private $_dateFormat = 'Y-m-d\TH:i:s\Z';
	
	// objet de type xmlfunctions
	private $_xml = null;
	
	/**
	 * Retourne le contenu de type RSS 2.0
	 * 
	 * @param syndication $pSyndication Objet de type syndication qui contient les informations
	 * @return string
	 */
	public function getContent ($pSyndication) {
		// verification des infos obligatoires
		if (is_null ($pSyndication->title)) {
			throw new CopixException (_i18n ('errors.badParamValue', array ('NULL', 'title')));
		}
		
		$xml = _class ('xmlfunctions', array ($pSyndication->compress));
		$this->_xml = $xml;
		
		// header 
		$xml->openNode ('feed', array ('xmlns' => 'http://www.w3.org/2005/Atom'));
		
		// id unique
		if (!is_null ($pSyndication->id->value)) {
			$id = 'urn:uuid:' . $pSyndication->id->value;
		} else if (!is_null ($pSyndication->link->uri)) {
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
		if (!is_null ($pSyndication->lastBuildDate)) {
			$updated = date ($this->_dateFormat, $pSyndication->lastBuildDate);
		} else if (!is_null ($pSyndication->pubDate)) {
			$updated = date ($this->_dateFormat, $pSyndication->pubDate);
		} else {
			$updated = date ($this->_dateFormat, mktime ());
		}
		$xml->addNode ('updated', $updated);
		// auteurs
		if ($pSyndication->authorsCount () > 0) {
			for ($boucle = 0; $boucle < $pSyndication->authorsCount (); $boucle++) {
				$this->_addPerson ($pSyndication->getAuthor ($boucle), 'author');
			}
		}
		// contributeurs
		if ($pSyndication->contributorsCount () > 0) {
			for ($boucle = 0; $boucle < $pSyndication->contributorsCount (); $boucle++) {
				$this->_addPerson ($pSyndication->getContributor ($boucle), 'contributor');
			}
		}
		// lien
		$this->_addLink ($pSyndication->link);
		// categories
		$this->_addCategories ($pSyndication);
		// générateur
		if (!is_null ($pSyndication->generator->name)) {
			$attributes = array ();
			if (!is_null ($pSyndication->generator->link->uri)) {
				$attributes['uri'] = $pSyndication->generator->link->uri;
			}
			if (!is_null ($pSyndication->generator->version)) {
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
		for ($boucle = 0; $boucle < $pSyndication->itemsCount (); $boucle++) {
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
		if (!is_null ($pSyndicationLink->uri)) {
			$attributes = array ('href' => $pSyndicationLink->uri);
			if (!is_null ($pSyndicationLink->rel)) {
				$attributes['rel'] = $pSyndicationLink->rel;
			}
			if (!is_null ($pSyndicationLink->type)) {
				$attributes['type'] = $pSyndicationLink->type;
			}
			if (!is_null ($pSyndicationLink->urilang)) {
				$attributes['hreflang'] = $pSyndicationLink->urilang;
			}
			if (!is_null ($pSyndicationLink->title)) {
				$attributes['title'] = $pSyndicationLink->title;
			}
			if (!is_null ($pSyndicationLink->resourceLength)) {
				$attributes['length'] = $pSyndicationLink->length;
			}
			$this->_xml->addNode ($pNodeName, null, $attributes);
		}
	}
	
	/**
	 * Ajoute une node de type text
	 * @param SyndicationText $pSyndicationText Texte
	 * @param string $pNodeName Nom de la node
	 * @param bool $pSrcExists L'attribut src existe-t-il dans la node à ajouter
	 */
	private function _addText ($pSyndicationText, $pNodeName, $pSrcExists = true) {
		if ($pSrcExists && !is_null ($pSyndicationText->src->uri)) {
			$this->_xml->addNode ($pNodeName, null, array ('src' => $pSyndicationText->src->uri));
		} else if (!is_null ($pSyndicationText->value)) {
			$attributes = (!is_null ($pSyndicationText->type)) ? array ('type' => $pSyndicationText->type) : null;
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
		if ($pObject->categoriesCount () > 0) {
			for ($boucle = 0; $boucle < $pObject->categoriesCount (); $boucle++) {
				$category = $pObject->getCategory ($boucle);
				if (is_null ($category->id)) {
					throw new CopixException (_i18n ('errors.badParamValue', array ('NULL', 'SyndicationCategory->id')));
				}
				$attributes = array ('term' => $category->id);
				if (!is_null ($category->name)) {
					$attributes['label'] = $category->name;
				}
				if (!is_null ($category->link->uri)) {
					$attributes['scheme'] = $category->link->uri;
				}
				$this->_xml->addNode ($pNodeName, null, $attributes);
			}
		}
	}
	
	/**
	 * Ajoute une personne
	 * 
	 * @param SyndicationPerson $pSyndicationPerson Personne
	 * @param string $pNodeName Nom de la node
	 */
	private function _addPerson ($pSyndicationPerson, $pNodeName) {
		if (!is_null ($pSyndicationPerson->name)) {
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
	 */
	private function _addItem ($pSyndicationItem, $pNodeName, $pAddSource = true) {
		$this->_xml->openNode ($pNodeName);
		// identifiant
		if (!is_null ($pSyndicationItem->id->value)) {
			$id = $pSyndicationItem->id->value;
		} else if (!is_null ($pSyndicationItem->link->uri)) {
			$id = $this->_getUrlWithSlash ($pSyndicationItem->link->uri);
		} else {
			$id = $pSyndicationItem->id->generate ();
		}
		$this->_xml->addNode ('id', $id);
		// titre
		if (is_null ($pSyndicationItem->title)) {
			throw new CopixException (_i18n ('errors.badParamValue', array ('NULL', 'SyndicationItem->title')));
		}
		$this->_xml->addNode ('title', $pSyndicationItem->title);
		// date de modification
		if (!is_null ($pSyndicationItem->updateDate)) {
			$updated = date ($this->_dateFormat, $pSyndicationItem->updateDate);
		} else if (!is_null ($pSyndicationItem->pubDate)) {
			$updated = date ($this->_dateFormat, $pSyndicationItem->pubDate);
		} else {
			throw new CopixException (_i18n ('errors.badParamValue', array ('NULL', 'SyndicationItem->updateDate')));
		}
		$this->_xml->addNode ('updated', $updated);
		// auteurs
		if ($pSyndicationItem->authorsCount () > 0) {
			for ($boucle2 = 0; $boucle2 < $pSyndicationItem->authorsCount (); $boucle2++) {
				$this->_addPerson ($pSyndicationItem->getAuthor ($boucle2), 'author');
				
			}
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
		if ($pSyndicationItem->contributorsCount() > 0) {
			for ($boucle2 = 0; $boucle2 < $pSyndicationItem->contributorsCount (); $boucle2++) {
				$this->_addPerson ($pSyndicationItem->getContributor ($boucle2), 'contributor');
				
			}
		}
		// date de la 1ère publication
		$published = (!is_null ($pSyndicationItem->publishDate)) ? date ($this->_dateFormat, $pSyndicationItem->publishDate) : null;
		$this->_xml->addNode ('published', $published);
		// source
		if ($pAddSource && !is_null ($pSyndicationItem->source->title)) {
			$this->_addItem ($pSyndicationItem->source, 'source', false);
		}
		// license
		$this->_addText ($pSyndicationItem->copyright, 'rights', false);
		
		$this->_xml->closeNode ($pNodeName);
	}
	
	/**
	 * Retourne une url avec le / de fin
	 * 
	 * @param string $pUrl Url
	 * @return string
	 */
	private function _getUrlWithSlash ($pUrl) {
		return (substr ($pUrl, strlen ($pUrl) - 1) == '/') ? $pUrl : $pUrl . '/';
	}
}
?>