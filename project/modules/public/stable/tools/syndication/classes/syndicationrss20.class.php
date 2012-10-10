<?php
class SyndicationRSS20 {
	// encodage
	private $_encoding = 'UTF-8';
	
	// caractère de fin de ligne
	private $_endLine = "\r\n";
	
	// caractère tabulation
	private $_tab = "\t";
	
	// format de la date
	private $_dateFormat = 'D, j M Y H:i:\0\0 \G\M\T';
	
	// nombre de tabulation pour la node en cours
	private $_nbrTab = 0;
	
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
		$content .= '<rss version="2.0">' . $this->_endLine;
		$this->_nbrTab++;
		$content .= $this->_getXmlNode ('channel', '', null, false);
		$this->_nbrTab++;
		
		// verification des infos obligatoires pour la partie "globale"
		if (is_null ($pSyndication->title)) {
			throw new CopixException (_i18n ('errors.badParamValue', array ('NULL', 'title')));
		}
		if (is_null ($pSyndication->link)) {
			throw new CopixException (_i18n ('errors.badParamValue', array ('NULL', 'link')));
		}
		if (is_null ($pSyndication->description)) {
			throw new CopixException (_i18n ('errors.badParamValue', array ('NULL', 'description')));
		}
		
		// écriture de la partie "globale"
		$pubDate = (!is_null ($item->pubDate)) ? date ($this->_dateFormat, $item->pubDate) : null;
		$lastBuildDate = (!is_null ($item->pubDate)) ? date ($this->_dateFormat, $item->pubDate) : null;
		
		$content .= $this->_getXmlNode ('title', $pSyndication->title);
		$content .= $this->_getXmlNode ('link', $pSyndication->link);
		$content .= $this->_getXmlNode ('description', $pSyndication->description);
		$content .= $this->_getXmlNode ('language', $pSyndication->language);
		$content .= $this->_getXmlNode ('copyright', $pSyndication->copyright);
		$content .= $this->_getXmlNode ('managingEditor', $pSyndication->managingEditor);
		$content .= $this->_getXmlNode ('webMaster', $pSyndication->webMaster);
		$content .= $this->_getXmlNode ('pubDate', $pubDate);
		$content .= $this->_getXmlNode ('lastBuildDate', $lastBuildDate);
		$content .= $this->_getXmlNode ('generator', $pSyndication->generator);
		$content .= $this->_getXmlNode ('docs', $pSyndication->docs);
		$content .= $this->_getXmlNode ('ttl', $pSyndication->timeToLeave);
		$content .= $this->_getXmlNode ('rating', $pSyndication->rating);
		$content .= $this->_getXmlNode ('skipHours', $pSyndication->skipHours);
		$content .= $this->_getXmlNode ('skipDays', $pSyndication->skipDays);
		// cloud, notification de mise à jour du canal
		if (!is_null ($pSyndication->cloud->domain) && !is_null ($pSyndication->cloud->port) && !is_null ($pSyndication->cloud->registerProcedure) && !is_null ($pSyndication->cloud->protocol)) {
			$content .= $this->_getXmlNode ('cloud', null, array ('domain' => $item->cloud->domain, 'port' => $item->cloud->port, 'registerProcedure' => $item->cloud->registerProcedure, 'protocol' => $item->cloud->protocol));
		}
		// image pour le canal
		if (!is_null ($pSyndication->image->url) && !is_null ($pSyndication->image->title) && !is_null ($pSyndication->image->link)) {
			$content .= $this->_getXmlNode ('image', '', null, false);
			$this->_nbrTab++;
			$content .= $this->_getXmlNode ('url', $pSyndication->image->url);
			$content .= $this->_getXmlNode ('title', $pSyndication->image->title);
			$content .= $this->_getXmlNode ('link', $pSyndication->image->link);
			$content .= $this->_getXmlNode ('width', $pSyndication->image->width);
			$content .= $this->_getXmlNode ('height', $pSyndication->image->height);
			$content .= $this->_getXmlNode ('description', $pSyndication->image->description);
			$this->_nbrTab--;
			$content .= $this->_closeNode ('image');
		}
				
		// écriture des items
		for ($boucle = 0; $boucle < $pSyndication->itemsCount (); $boucle++) {
			$item = $pSyndication->getItem ($boucle);
			$pubDate = (!is_null ($item->pubDate)) ? date ($this->_dateFormat, $item->pubDate) : null;
			
			$content .= $this->_getXmlNode ('item', '', null, false);
			$this->_nbrTab++;
			$content .= $this->_getXmlNode ('title', $item->title);
			$content .= $this->_getXmlNode ('link', $item->link);
			$content .= $this->_getXmlNode ('description', $item->description);
			$content .= $this->_getXmlNode ('pubDate', $pubDate);
			if (!is_null ($item->guid->value) || !is_null ($item->guid->isPermaLink)) {
				$content .= $this->_getXmlNode ('guid', $item->guid->value, array ('isPermaLink' => ($item->guid->isPermaLink) ? 'true' : null));
			}
			if (!is_null ($item->category->name) || !is_null ($item->category->domain)) {
				$content .= $this->_getXmlNode ('category', $item->category->name, array ('domain' => $item->category->domain));
			}
			$content .= $this->_getXmlNode ('comments', $item->comments);
			$content .= $this->_getXmlNode ('author', $item->author);
			if (!is_null ($item->source->title) || !is_null ($item->source->url)) {
				$content .= $this->_getXmlNode ('source', $item->source->title, array ('url' => $item->source->url));
			}
			
			// enclosure, attachement d'un média à l'item
			if (!is_null ($item->enclosure->url) && !is_null ($item->enclosure->length) && !is_null ($item->enclosure->type)) {
				$content .= $this->_getXmlNode ('enclosure', null, array ('url' => $item->enclosure->url, 'length' => $item->enclosure->length, 'type' => $item->enclosure->type));
			}
			
			$this->_nbrTab--;
			$content .= $this->_closeNode ('item');
		}
		
		$this->_nbrTab--;
		$content .= $this->_closeNode ('channel');
		$this->_nbrTab--;
		$content .= $this->_closeNode ('rss');
		
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
}
?>