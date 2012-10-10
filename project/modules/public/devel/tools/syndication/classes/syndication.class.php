<?php
/**
 * Permet de créer une syndication pour un contenu, et de retourner le contenu ou de l'écrire dans un fichier
 */
class Syndication {
	
	// types de syndication possibles
	const RSS_1_0 = 'rss10';
	const RSS_2_0 = 'rss20';
	const ATOM_1_0 = 'atom10';
	
	// SyndicationId. Identifiant de la syndication.
	public $id = null;
	
	// titre
	public $title = null;
	
	// lien
	public $link = null;
	
	// description
	public $description = null;
	
	// Le langage dans lequel est écrit le canal. Ceci permet aux aggrégateurs de regrouper tous les sites de langue italienne, par exemple, sur une même page.
	// Valeurs possibles : http://www.w3.org/TR/REC-html40/struct/dirlang.html#langcodes
	public $language = null;
	
	// SyndicationText. License pour le contenu du canal
	public $copyright = null;
	
	// Adresse email de la personne responsable du contenu éditorial
	public $managingEditor = null;

	// SyndicationPerson. personne responsable des problèmes techniques relatifs au canal
	public $webMaster = null;
	
	// La date de publication du contenu du canal, format timestamp
	public $pubDate = null;
	
	// La dernière date où le contenu du canal a changé, format timestamp
	public $lastBuildDate = null;

	// SyndicationGenerator. Générateur de la syndication
	public $generator = null;
	
	// Une URL pointant sur la documentation du format utilisé pour le fichier RSS
	public $docs = null;
	
	// Permet aux processus  d'être notifiés des mises à jour du canal, pour enregistrer en nuage, en implémentant un protocole de flux RSS publier-souscrire léger.
	// http://www.scriptol.fr/rss/RSS-2.0.html#ltcloudgtSubelementOfLtchannelgt
	// http://cyber.law.harvard.edu/rss/soapMeetsRss.html#rsscloudInterface
	public $cloud = null;
	
	// Nombre de minutes qui indique combien de temps un canal peut être gardé en mémoire cache avant rafraîchissement à la source
	public $timeToLeave = null;
	
	// Logo de la syndication
	public $logo = null;
	
	// Icone de la syndication
	public $icon = null;
	
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
	
	// array SyndicationPerson. Liste des auteurs de la syndication.
	private $_authors = array ();
	
	// array SyndicationPerson. Liste des contributeurs de la syndication.
	private $_contributors = array ();
	
	// array SyndicationCategory. Catégories de la syndication.
	private $_categories = array ();
	
	/**
	 * Constructeur
	 */
	public function __construct () {
		$this->id = new SyndicationId ();
		$this->logo = new SyndicationImage ();
		$this->icon = new SyndicationImage ();
		$this->cloud = new SyndicationCloud ();
		$this->link = new SyndicationLink ();
		$this->webMaster = new SyndicationPerson ();
		$this->generator = new SyndicationGenerator ();
		$this->copyright = new SyndicationText ();
	}
	
	/**
	 * Ajoute un item
	*
	 * @return SyndicationItem
	 */
	public function addItem () {
		return $this->_items[] = new SyndicationItem (); 
	}
	
	/**
	 * Retourne un item
	 * 
	 * @param int $pIndex Index de l'item
	 * @return SyndicationItem
	 */
	public function getItem ($pIndex) {
		return (isset ($this->_items[$pIndex])) ? $this->_items[$pIndex] : null;
	}
	
	/**
	 * Nombre d'items
	 * 
	 * @return int
	 */
	public function itemsCount () {
		return count ($this->_items);
	}
	
	/**
	 * Ajoute un auteur
	 * 
	 * @return SyndicationPerson
	 */
	public function addAuthor () {
		return $this->_authors[] = new SyndicationPerson ();
	}
	
	/**
	 * Retourne un auteur
	 * 
	 * @return SyndicationPerson
	 */	
	public function getAuthor ($pIndex) {
		return (isset ($this->_authors[$pIndex])) ? $this->_authors[$pIndex] : null;
	}
	
	/**
	 * Retourne le nombre d'auteurs
	 * 
	 * @return int
	 */
	public function authorsCount () {
		return count ($this->_authors);
	}
	
	/**
	 * Ajoute une categorie
	 * 
	 * @return SyndicationCategory
	 */
	public function addCategory () {
		return $this->_categories[] = new SyndicationCategory ();
	}
	
	/**
	 * Retourne un auteur
	 * 
	 * @return SyndicationPerson
	 */	
	public function getCategory ($pIndex) {
		return (isset ($this->_categories[$pIndex])) ? $this->_categories[$pIndex] : null;
	}
	
	/**
	 * Retourne le nombre d'auteurs
	 * 
	 * @return int
	 */
	public function categoriesCount () {
		return count ($this->_categories);
	}
	
	/**
	 * Ajoute un contributeur
	 * 
	 * @return SyndicationPerson
	 */
	public function addContributor () {
		return $this->_contributors[] = new SyndicationPerson ();
	}
	
	/**
	 * Retourne un contributeur
	 * 
	 * @return SyndicationPerson
	 */	
	public function getContributor ($pIndex) {
		return (isset ($this->_contributors[$pIndex])) ? $this->_contributors[$pIndex] : null;
	}
	
	/**
	 * Retourne le nombre de contributeurs
	 * 
	 * @return int
	 */
	public function contributorsCount () {
		return count ($this->_contributors);
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
		return $syndication->getContent ($this);
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
	// SyndicationLink. URL d'une image qui représente le canal
	public $src = null;
	
	// décrit l'image, il est utilisé par l'attribut ALT de la balise HTML <img> quand le canal est rendu en HTML.
	public $title = null;
	
	// SyndicationLink. URL du site. quand le canal est affiché, l'image est un lien sur le site.
	public $link = null;
	
	// largeur de l'image
	public $width = null;
	
	// hauteur de l'image
	public $height = null;
	
	// texte inclut dans l'attribut TITLE du lien formé autour de l'image dans le rendu HTML
	public $description = null;
	
	public function __construct () {
		$this->src = new SyndicationLink ();
		$this->link = new SyndicationLink ();
	}
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
	
	// SyndicationLink
	public $link = null;
	
	// SyndicationText. Contenu de l'élément
	public $content = null;
	
	// SyndicationText. Résumé de l'élément
	public $summary = null;

	// array SyndicationPerson. Liste des auteurs de cet item.
	private $_authors = array ();
	
	// array SyndicationContributor. Liste des contributeurs de cet item
	private $_contributors = array ();
	
	// array SyndicationCategory. Catégories de l'item
	private $_categories = array ();
	
	// URL de la page de commentaires concernant l'item
	// http://www.scriptol.fr/rss/RSS-2.0.html#ltcommentsgtSubelementOfLtitemgt
	public $comments = null;
	
	// SyndicationLink. décrit un objet média attaché à l'item
	// http://www.scriptol.fr/rss/RSS-2.0.html#ltenclosuregtSubelementOfLtitemgt 	
	public $enclosure = null;

	// SyndicationId. Une chaîne qui identifie l'item de façon unique
	public $id = null;
	
	// date de publication de l'item, format timestamp
	public $publishDate = null;
	
	// date de la dernière mise à jour de l'item, format timestamp
	public $updateDate = null;
	
	// SyndicationItem. Source de l'item
	public $source = null;
	
	// SyndicationText. License pour l'item
	public $copyright = null;
	
	/**
	 * Constructeur
	 * 
	 * @param bool $pHaveSource Si cet item peut avoir une source
	 */
	public function __construct ($pHaveSource = true) {
		$this->enclosure = new SyndicationLink ();
		$this->id = new SyndicationId ();
		$this->link = new SyndicationLink ();
		$this->content = new SyndicationText ();
		$this->summary = new SyndicationText ();
		$this->copyright = new SyndicationText ();
		// $pHaveSource à false car on ne peut avoir qu'une seule source
		// si on laissait true tout le temps, on aurait une boucle infinie (item qui créé source, qui créé item, qui créé source, etc)
		if ($pHaveSource) {
			$this->source = new SyndicationItem (false);
		}
	}
	
	/**
	 * Ajoute un auteur
	 * 
	 * @return SyndicationPerson
	 */
	public function addAuthor () {
		return $this->_authors[] = new SyndicationPerson ();
	}
	
	/**
	 * Retourne un auteur
	 * 
	 * @return SyndicationPerson
	 */	
	public function getAuthor ($pIndex) {
		return (isset ($this->_authors[$pIndex])) ? $this->_authors[$pIndex] : null;
	}
	
	/**
	 * Retourne le nombre d'auteurs
	 * 
	 * @return int
	 */
	public function authorsCount () {
		return count ($this->_authors);
	}
	
	/**
	 * Ajoute un contributeur
	 * 
	 * @return SyndicationPerson
	 */
	public function addContributor () {
		return $this->_contributors[] = new SyndicationPerson ();
	}
	
	/**
	 * Retourne un contributeur
	 * 
	 * @return SyndicationPerson
	 */	
	public function getContributor ($pIndex) {
		return (isset ($this->_contributors[$pIndex])) ? $this->_contributors[$pIndex] : null;
	}
	
	/**
	 * Retourne le nombre de contributeurs
	 * 
	 * @return int
	 */
	public function contributorsCount () {
		return count ($this->_contributors);
	}
	
		/**
	 * Ajoute une categorie
	 * 
	 * @return SyndicationCategory
	 */
	public function addCategory () {
		return $this->_categories[] = new SyndicationCategory ();
	}
	
	/**
	 * Retourne un auteur
	 * 
	 * @return SyndicationPerson
	 */	
	public function getCategory ($pIndex) {
		return (isset ($this->_categories[$pIndex])) ? $this->_categories[$pIndex] : null;
	}
	
	/**
	 * Retourne le nombre d'auteurs
	 * 
	 * @return int
	 */
	public function categoriesCount () {
		return count ($this->_categories);
	}
}

/**
 * Indentifiant unique
 */
class SyndicationId {
	// valeur unique
	public $value = null;
	
	// est-ce un lien permanent
	public $isPermaLink = null;
	
	/**
	 * Génère un identifiant unique, le met dans value, et le retourne
	 * 
	 * @param string $pPrefix Préfixe à appliquer à l'identifiant généré
	 * @return string
	 */
	public function generate ($pPrefix = null) {
		$this->value = uniqid ($pPrefix);
		return $this->value;
	}
}

/**
 * Lien
 */
class SyndicationLink {
	// uri pointée
	public $uri = null;
	
	// type de relation (alternate, enclosure, related, self, via)
	// http://www.atomenabled.org/developers/syndication/#link
	public $rel = null;
	
	// type de resource pointée
	public $type = null;
	
	// langue de la resource pointée
	public $urilang = null;
	
	// titre du lien, sera généralement affiché par les agrégateurs
	public $title = null;
	
	// taille en octets de la resource pointée
	public $resourceLength = null;
	
	// type mime du lien pointé
	public $mimeType = null;
}

/**
 * Informations sur une personne
 */
class SyndicationPerson {
	// nom
	public $name = null;
	
	// e-mail
	public $email = null;
	
	// SyndicationLink. Adresse du site de la personne
	public $webSite = null;
	
	/**
	 * Constructeur
	 */
	public function __construct () {
		$this->webSite = new SyndicationLink ();
	}
}

/**
 * Catégorie
 */
class SyndicationCategory {
	// identifiant unique
	public $id = null;
	
	// nom
	public $name = null;
	
	// SyndicationLink. lien spécifique à la catégorie
	public $link = null;
	
	public function __construct () {
		$this->link = new SyndicationLink ();
	}
}

/**
 * Générateur de la syndication
 */
class SyndicationGenerator {
	// nom
	public $name = 'Copix, module syndication';
	
	// version
	public $version = null;
	
	// SyndicationLink. Adresse du générateur
	public $link = null;
	
	public function __construct () {
		$this->link = new SyndicationLink ();
		$this->link->uri = 'http://www.copix.org';
		$infos = CopixModule::getInformations ('syndication');
		$this->version = $infos->version;
	}
}

class SyndicationText {
	// valeur
	public $value = null;
	
	// type du contenu (text, html, xhtml, text+xml, html+xml, xhtml+xml)
	public $type = 'text';
	
	// SyndicationLink. Source où trouver le texte.
	public $src = null;
	
	/**
	 * Constructeur
	 */
	public function __construct () {
		$this->src = new SyndicationLink ();
	}
}
?>