<?php
/**
 * Création d'une syndication au format RSS 1.0
 * Norme respectée : http://www.rssboard.org/rss-specification (fr : http://www.scriptol.fr/rss/RSS-2.0.html)
 */
class SyndicationRSS20 {
	// format de la date
	private $_dateFormat = 'D, d M Y H:i:\0\0 \G\M\T';
	
	// URL pointant sur la doc utilisée pour générer ce format
	private $_docs = 'http://www.rssboard.org/rss-specification';
	
	
	/**
	 * Retourne le contenu de type RSS 2.0
	 * 
	 * @param syndication $pSyndication Objet de type syndication qui contient les informations
	 * @return string
	 */
	public function getContent ($pSyndication) {
		// verification des infos obligatoires pour la partie "globale"
		if (is_null ($pSyndication->title)) {
			throw new CopixException (_i18n ('errors.badParamValue', array ('NULL', 'title')));
		}
		if (is_null ($pSyndication->link->uri)) {
			throw new CopixException (_i18n ('errors.badParamValue', array ('NULL', 'link')));
		}
		if (is_null ($pSyndication->description)) {
			throw new CopixException (_i18n ('errors.badParamValue', array ('NULL', 'description')));
		}
		
		$xml = _class ('xmlfunctions', array ($pSyndication->compress));
		$pSyndication->docs = $this->_docs;
		
		// header 
		$xml->openNode ('rss', array ('version' => '2.0'));
		$xml->openNode ('channel');
		
		// écriture de la partie "globale"
		$pubDate = (!is_null ($pSyndication->pubDate)) ? date ($this->_dateFormat, $pSyndication->pubDate) : null;
		$lastBuildDate = null;
		if (!is_null ($pSyndication->lastBuildDate)) {
			$lastBuildDate = date ($this->_dateFormat, $pSyndication->lastBuildDate);
		} else if (!is_null ($pubDate)) {
			$lastBuildDate = $pubDate;
		}
		
		$xml->addNode ('title', $pSyndication->title);
		$xml->addNode ('link', $pSyndication->link->uri);
		$xml->addNode ('description', $pSyndication->description);
		$xml->addNode ('language', $pSyndication->language);
		$xml->addNode ('copyright', $pSyndication->copyright->value);
		$xml->addNode ('managingEditor', $pSyndication->managingEditor);
		$xml->addNode ('webMaster', $pSyndication->webMaster->email);
		$xml->addNode ('pubDate', $pubDate);
		$xml->addNode ('lastBuildDate', $lastBuildDate);
		$generator = $pSyndication->generator->name;
		if (!is_null ($pSyndication->generator->link->uri)) {
			$generator .= ' (' . $pSyndication->generator->link->uri . ')';
		}
		$xml->addNode ('generator', $generator);
		$xml->addNode ('docs', $pSyndication->docs);
		$xml->addNode ('ttl', $pSyndication->timeToLeave);
		$xml->addNode ('rating', $pSyndication->rating);
		$xml->addNode ('skipHours', $pSyndication->skipHours);
		$xml->addNode ('skipDays', $pSyndication->skipDays);
		// cloud, notification de mise à jour du canal
		if (!is_null ($pSyndication->cloud->domain) && !is_null ($pSyndication->cloud->port) && !is_null ($pSyndication->cloud->registerProcedure) && !is_null ($pSyndication->cloud->protocol)) {
			$xml->addNode ('cloud', null, array ('domain' => $item->cloud->domain, 'port' => $item->cloud->port, 'registerProcedure' => $item->cloud->registerProcedure, 'protocol' => $item->cloud->protocol));
		}
		// image pour le canal
		if (!is_null ($pSyndication->logo->src->uri) && !is_null ($pSyndication->logo->title) && !is_null ($pSyndication->logo->link->uri)) {
			$xml->openNode ('image');
			$xml->addNode ('url', $pSyndication->logo->src->uri);
			$xml->addNode ('title', $pSyndication->logo->title);
			$xml->addNode ('link', $pSyndication->logo->link->uri);
			$xml->addNode ('width', $pSyndication->logo->width);
			$xml->addNode ('height', $pSyndication->logo->height);
			$xml->addNode ('description', $pSyndication->logo->description);
			$xml->closeNode ('image');
		}
				
		// écriture des items
		for ($boucle = 0; $boucle < $pSyndication->itemsCount (); $boucle++) {
			$item = $pSyndication->getItem ($boucle);
			$pubDate = (!is_null ($item->pubDate)) ? date ($this->_dateFormat, $item->pubDate) : null;
			
			$xml->addNode ('item', '', null, false);
			$xml->addNode ('title', $item->title);
			$xml->addNode ('link', $item->link->uri);
			$xml->addNode ('description', $item->content->value);
			$xml->addNode ('pubDate', $pubDate);
			// identifint guid
			if (!is_null ($item->id->value)) {
				$attributes = (!is_null ($item->id->isPermaLink)) ? array ('isPermaLink' => $item->id->isPermaLink) : null;
				$xml->addNode ('guid', $item->id->value, $attributes);
			}
			// catégorie
			if ($item->categoriesCount () > 0) {
				$categorie = $item->getCategory (0);
				if (!is_null ($categorie->name) || !is_null ($categorie->link->uri)) {
					$xml->addNode ('category', $categorie->name, array ('domain' => $categorie->link->uri));
				}
			}
			$xml->addNode ('comments', $item->comments);
			if ($item->authorsCount () > 0) {
				$author = $item->getAuthor (0);
				if (!is_null ($author->email)) {
					$email = (!is_null ($author->name)) ? $author->email . ' (' . $author->name . ')'  : $author->email;
					$xml->addNode ('author', $email);
				}
			}
			if (!is_null ($item->source->title) || !is_null ($item->source->link->uri)) {
				$xml->addNode ('source', $item->source->title, array ('url' => $item->source->link->uri));
			}
			
			// enclosure, attachement d'un média à l'item
			if (!is_null ($item->enclosure->uri) && !is_null ($item->enclosure->length) && !is_null ($item->enclosure->mimeType)) {
				$xml->addNode ('enclosure', null, array ('url' => $item->enclosure->uri, 'length' => $item->enclosure->length, 'type' => $item->enclosure->mimeType));
			}
			
			$xml->closeNode ('item');
		}
		
		$xml->closeNode ('channel');
		$xml->closeNode ('rss');
		
		return $xml->getContent ();
	}
}
?>