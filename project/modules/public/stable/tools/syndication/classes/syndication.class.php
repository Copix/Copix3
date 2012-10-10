<?php
/**
 * Permet de créer une syndication pour un contenu, et de retourner le contenu ou de l'écrire dans un fichier
 */
class Syndication {
	
	// types de syndication possibles
	const RSS_1_0 = 'rss10';
	const RSS_2_0 = 'rss20';
	const ATOM_1_0 = 'atom10';
	
	// titre
	public $title = null;
	
	// lien
	public $link = null;
	
	// description (contenu de l'element)
	public $description = null;
	
	// Le langage dans lequel est écrit le canal. Ceci permet aux aggrégateurs de regrouper tous les sites de langue italienne, par exemple, sur une même page.
	// Valeurs possibles : http://www.w3.org/TR/REC-html40/struct/dirlang.html#langcodes
	public $language = null;
	
	// license pour le contenu du canal
	public $copyright = null;
	
	// Adresse email de la personne responsable du contenu éditorial
	public $managingEditor = null;

	// Adresse email de la personne responsable des problèmes techniques relatifs au canal
	public $webMaster = null;
	
	// La date de publication du contenu du canal, format timestamp
	public $pubDate = null;
	
	// La dernière date où le contenu du canal a changé, format timestamp
	public $lastBuildDate = null;
	
	// Spécifie une catégorie ou plusieurs auxquelles correspond le canal.
	public $category = null;

	// Une chaîne indiquant le programme utilisé pour générer le canal
	public $generator = 'Copix (http://www.copix.org)';
	
	// Une URL pointant sur la documentation du format utilisé pour le fichier RSS
	public $docs = 'http://cyber.law.harvard.edu/tech/rss';
	
	// Permet aux processus  d'être notifiés des mises à jour du canal, pour enregistrer en nuage, en implémentant un protocole de flux RSS publier-souscrire léger.
	// http://www.scriptol.fr/rss/RSS-2.0.html#ltcloudgtSubelementOfLtchannelgt
	// http://cyber.law.harvard.edu/rss/soapMeetsRss.html#rsscloudInterface
	public $cloud = null;
	
	// Nombre de minutes qui indique combien de temps un canal peut être gardé en mémoire cache avant rafraîchissement à la source
	// http://www.scriptol.fr/rss/RSS-2.0.html#ltttlgtSubelementOfLtchannelgt
	public $timeToLeave = null;
	
	// Spécifie une image GIF, JPEG ou PNG qui ne peut pas être affichée avec le canal
	// http://www.scriptol.fr/rss/RSS-2.0.html#ltimagegtSubelementOfLtchannelgt
	public $image = null;
	
	// La côte PICS pour le canal
	// http://www.scriptol.fr/rss/RSS-2.0.html#lttextinputgtSubelementOfLtchannelgt
	public $rating = null; 
	
	// Un indice pour les aggrégateurs leur indiquant combien d'heures peuvent être sautées
	// http://cyber.law.harvard.edu/rss/skipHoursDays.html#skiphours
	public $skipHours = null;
	
	// Un indice pour les aggrégateurs leur indiquant combien de jours peuvent être sautés
	// http://blogs.law.harvard.edu/tech/skipHoursDays#skipdays
	public $skipDays = null;
	
	// elements du canal
	private $_items = array ();
	
	// compresse le code HTML
	public $compress = false;
	
	/**
	 * Constructeur
	 */
	public function __construct () {
		$this->image = new SyndicationImage ();
		$this->cloud = new SyndicationCloud ();
	}
	
	/**
	 * Ajoute un item
	 * @param string $pTitle Titre
	 * @param string $pLink Lien vers le contenu
	 * @param string $pDescription Contenu de l'élément
	 * @return SyndicationItem
	 */
	public function addItem ($pTitle = null, $pLink = null, $pDescription = null) {
		return $this->_items[] = new SyndicationItem ($pTitle, $pLink, $pDescription); 
	}
	
	/**
	 * Retourne un item
	 * 
	 * @param int $pIndex Index de l'item
	 * @return SyndicationItem
	 */
	public function getItem ($pIndex) {
		if (isset ($this->_items[$pIndex])) {
			return $this->_items[$pIndex];
		} else {
			return null;
		}
	}
	
	/**
	 * Nombre d'items
	 */
	public function itemsCount () {
		return count ($this->_items);
	}
	
	/**
	 * Retourne le contenu de la syndication
	 * 
	 * @param string $pSyndicType Type de syndication (rss1.0, rss2.0, atom, etc)
	 */	
	public function getContent ($pSyndicType = null) {
		if (is_null ($pSyndicType)) {
			switch (CopixConfig::get ('syndication|defaultType')) {
				case 'RSS_1_0' : $pSyndicType = self::RSS_1_0; break;
				case 'RSS_2_0' : $pSyndicType = self::RSS_2_0; break;
				case 'ATOM_1_0' : $pSyndicType = self::ATOM_1_0; break;
				default : $pSyndicType = self::RSS_2_0; break;
			}
		}	
		$syndication = _class ('syndication|syndication' . $pSyndicType);
		$syndication->compress = $this->compress;
		return $syndication->getContent ($this);
	}
	
	/**
	 * Affiche le contenu de la syndication
	 */
	public function arDirectContent ($pTitlePage, $pSyndicType = null) {
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = $pTitlePage;
		
		$ppo->content = $this->getContent ($pSyndicType);
		
		return _arDirectPPO ($ppo, 'content.tpl');
	}
	
	/**
	 * Ecrit le contenu de la syndication dans un fichier
	 * 
	 * @param string $pSyndicType Type de syndication (rss1.0, rss2.0, atom, etc)
	 * @param string $pFileName Nom du fichier généré (écrasé si existant) 
	 */
	public function writeToFile ($pFileName, $pSyndicType = null) {
		file_put_contents ($pFileName, $this->getContent ($pSyndicType));
	}
}

/**
 * Image d'une syndication
 */
class SyndicationImage {
	// URL d'une image GIF, JPEG ou PNG qui représente la canal
	public $url = null;
	
	// décrit l'image, il est utilisé par l'attribut ALT de la balise HTML <img> quand le canal est rendu en HTML.
	public $title = null;
	
	// URL du site. quand le canal est affiché, l'image est un lien sur le site.
	public $link = null;
	
	// largeur de l'image
	public $width = null;
	
	// hauteur de l'image
	public $height = null;
	
	// texte inclut dans l'attribut TITLE du lien formé autour de l'image dans le rendu HTML
	public $description = null;
}

/**
 * Notification de mises à jour du flux
 * http://cyber.law.harvard.edu/rss/soapMeetsRss.html#rsscloudInterface
 */
class SyndicationCloud {
	// nom de domaine (ex : test.com) 
	public $domain = null;
	
	// port (80)
	public $port = 80;
	
	// répertoire de la procedure (ex : /myDir)
	public $path = null;
	
	// nom de la procedure (ex : rssNotify)
	public $registerProcedure = null;
	
	// protocole à utiliser (ex : xml-rpc)
	public $protocol = 'xml-rpc';
}

/**
 * Element d'une syndication
 */
class SyndicationItem {
	
	// titre
	public $title = null;
	
	// lien
	public $link = null;
	
	// texte
	public $description = null;

	// adresse e-mail de l'auteur
	public $author = null;
	
	// place l'item dans une ou plusieurs catégories
	// http://www.scriptol.fr/rss/RSS-2.0.html#ltcategorygtSubelementOfLtitemgt
	public $category = null;
	
	// URL de la page de commentaires concernant l'item
	// http://www.scriptol.fr/rss/RSS-2.0.html#ltcommentsgtSubelementOfLtitemgt
	public $comments = null;
	
	// décrit un objet média attaché à l'item
	// http://www.scriptol.fr/rss/RSS-2.0.html#ltenclosuregtSubelementOfLtitemgt 	
	public $enclosure = null;

	// Une chaîne qui identifie l'item de façon unique
	// http://www.scriptol.fr/rss/RSS-2.0.html#ltguidgtSubelementOfLtitemgt
	public $guid = null;
	
	// date de publication de l'item, format timestamp
	public $pubDate = null;
	
	// le canal RSS d'ou vient l'item
	// http://www.scriptol.fr/rss/RSS-2.0.html#ltsourcegtSubelementOfLtitemgt
	public $source = null;
	
	
	/**
	 * Constructeur
	 * 
	 * @param string $pTitle Titre
	 * @param string $pLink Lien vers le contenu
	 * @param string $pDescription Contenu de l'élément
	 */
	public function __construct ($pTitle = null, $pLink = null, $pDescription = null) {
		$this->title = $pTitle;
		$this->link = $pLink;
		$this->description = $pDescription;
		
		$this->category = new SyndicationItemCategory ();
		$this->enclosure = new SyndicationItemEnclosure ();
		$this->guid = new SyndicationItemGuid ();
		$this->source = new SyndicationItemSource ();
	}
}

/**
 * Catégorie d'un item
 */
class SyndicationItemCategory {
	// domaine
	public $domain = null;
	
	// titre
	public $name = null;
}

/**
 * Objet média attaché à un item
 */
class SyndicationItemEnclosure {
	// adresse du média
	public $url = null;
	
	// taille en octet du média
	public $length = null;
	
	// type mime
	public $type = null;
}

/**
 * Indentifiant unique d'un item
 */
class SyndicationItemGuid {
	// valeur unique
	public $value = null;
	
	// est-ce un lien permanent
	public $isPermaLink = null;
}

/**
 * Source d'un item
 */
class SyndicationItemSource {
	// lien
	public $url = null;
	
	// titre
	public $title = null;
}
?>