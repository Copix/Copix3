<?php
/**
 * Création d'une syndication au format RSS 2.0
 * Norme respectée : http://www.rssboard.org/rss-specification (fr : http://www.scriptol.fr/rss/RSS-2.0.html)
 */
class SyndicationRSS20 extends SyndicationType {
	/**
	 * Format de la date
	 *
	 * @var string
	 */
	private $_dateFormat = 'r';
	
	/**
	 *  URL pointant sur la doc utilisée pour générer ce format
	 *
	 * @var string
	 */
	private $_docs = 'http://www.rssboard.org/rss-specification';
	
	/**
	 * Retourne le contenu de type RSS 2.0
	 * 
	 * @param syndication $pSyndication Objet de type syndication qui contient les informations
	 * @return string
	 */
	public function getContent ($pSyndication) {
		// verification des infos obligatoires pour la partie "globale"
		if ($pSyndication->title == null) {
			throw new SyndicationException (_i18n ('feed|errors.badParamValue', array ('NULL', 'title')));
		}
		if ($pSyndication->link->uri == null) {
			throw new SyndicationException (_i18n ('feed|errors.badParamValue', array ('NULL', 'link')));
		}
		if ($pSyndication->description == null) {
			throw new SyndicationException (_i18n ('feed|errors.badParamValue', array ('NULL', 'description')));
		}
		
		$xml = new SyndicationXML ($pSyndication->compress);
		$pSyndication->docs = $this->_docs;
		
		// header 
		$xml->openNode ('rss', array ('version' => '2.0','xmlns:content'=>"http://purl.org/rss/1.0/modules/content/"));
		$xml->openNode ('channel');
		
		// écriture de la partie "globale"
		$pubDate = ($pSyndication->pubDate != null && strlen ($pSyndication->pubDate) == 10) ? date ($this->_dateFormat, $pSyndication->pubDate) : null;
		if ($pubDate == null) {
			//so format is probably YYYYMMDDHHiiss
			$pubDate = CopixDateTime::yyyymmddToTimestamp ($pSyndication->pubDate);
		} else {
			//that was the last chance...
			$pubDate = null;
		}
		
		$lastBuildDate = null;
		if ($pSyndication->lastBuildDate != null) {
			$lastBuildDate = date ($this->_dateFormat, $pSyndication->lastBuildDate);
		} else if ($pubDate != null) {
			$lastBuildDate = $pubDate;
		}
		
		$xml->addNode ('title', '<![CDATA[' .$pSyndication->title. ']]>');
		$xml->addNode ('link', $pSyndication->link->uri);
		$xml->addNode ('description', '<![CDATA[' .$pSyndication->description. ']]>');
		$xml->addNode ('language', $pSyndication->language);
		$xml->addNode ('copyright', $pSyndication->copyright->value);
		$xml->addNode ('managingEditor', $pSyndication->managingEditor);
		$xml->addNode ('webMaster', $pSyndication->webMaster->email);
		$xml->addNode ('pubDate', $pubDate);
		$xml->addNode ('lastBuildDate', $lastBuildDate);
		$generator = $pSyndication->generator->name;
		if ($pSyndication->generator->link->uri != null) {
			$generator .= ' (' . $pSyndication->generator->link->uri . ')';
		}
		$xml->addNode ('generator', $generator);
		$xml->addNode ('docs', $pSyndication->docs);
		$xml->addNode ('ttl', $pSyndication->timeToLeave);
		$xml->addNode ('rating', $pSyndication->rating);
		$xml->addNode ('skipHours', $pSyndication->skipHours);
		$xml->addNode ('skipDays', $pSyndication->skipDays);
		// cloud, notification de mise à jour du canal
		if ($pSyndication->cloud->domain != null && $pSyndication->cloud->port != null && $pSyndication->cloud->registerProcedure != null && $pSyndication->cloud->protocol != null) {
			$xml->addNode ('cloud', null, array ('domain' => $item->cloud->domain, 'port' => $item->cloud->port, 'registerProcedure' => $item->cloud->registerProcedure, 'protocol' => $item->cloud->protocol));
		}
		// image pour le canal
		if ($pSyndication->logo->src->uri != null && $pSyndication->logo->title != null&& $pSyndication->logo->link->uri != null) {
			$xml->openNode ('image');
			$xml->addNode ('url', $pSyndication->logo->src->uri);
			$xml->addNode ('title', '<![CDATA[' .$pSyndication->logo->title. ']]>');
			$xml->addNode ('link', $pSyndication->logo->link->uri);
			$xml->addNode ('width', $pSyndication->logo->width);
			$xml->addNode ('height', $pSyndication->logo->height);
			$xml->addNode ('description', '<![CDATA[' .$pSyndication->logo->description. ']]>');
			$xml->closeNode ('image');
		}
				
		// écriture des items
		for ($boucle = 0; $boucle < $pSyndication->countItems (); $boucle++) {
			$item = $pSyndication->getItem ($boucle);
			$pubDate = ($item->pubDate != null) ? date ($this->_dateFormat, $item->pubDate) : null;
			
			$xml->addNode ('item', '', null, false);
			$xml->addNode ('title', '<![CDATA[' .$item->title.']]>');
			$xml->addNode ('link', $item->link->uri);
			$xml->addNode ('description', '<![CDATA[' . $item->content->value . ']]>');
			//en cas de content:encoded
			if ($item->content->encoded != null){
				$xml->addNode ('content:encoded', $item->content->encoded);
			}
			
			$xml->addNode ('pubDate', $pubDate);
			// identifint guid
			if ($item->id->value != null) {
				$attributes = ($item->id->isPermaLink != null) ? array ('isPermaLink' => $item->id->isPermaLink) : null;
				$xml->addNode ('guid', $item->id->value, $attributes);
			}
			// catégorie
			if ($item->countCategories () > 0) {
				$categorie = $item->getCategory (0);
				if ($categorie->name != null || $categorie->link->uri != null) {
					$xml->addNode ('category', $categorie->name, array ('domain' => $categorie->link->uri));
				}
			}
			$xml->addNode ('comments', $item->comments);
			if ($item->countAuthors () > 0) {
				$author = $item->getAuthor (0);
				if ($author->email != null) {
					$email = ($author->name != null) ? $author->email . ' (' . $author->name . ')'  : $author->email;
					$xml->addNode ('author', $email);
				}
			}
			if ($item->source->title != null || $item->source->link->uri != null) {
				$xml->addNode ('source', $item->source->title, array ('url' => $item->source->link->uri));
			}
			
			// enclosure, attachement d'un média à l'item
			if ($item->enclosure->uri != null && $item->enclosure->length != null && $item->enclosure->mimeType != null) {
				$xml->addNode ('enclosure', null, array ('url' => $item->enclosure->uri, 'length' => $item->enclosure->length, 'type' => $item->enclosure->mimeType));
			}
			$xml->closeNode ('item');
		}
		
		$xml->closeNode ('channel');
		$xml->closeNode ('rss');
		
		return $xml->getContent ();
	}
}