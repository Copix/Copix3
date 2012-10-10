<?php
/**
 * Permet de créer une syndication pour un contenu, et de retourner le contenu ou de l'écrire dans un fichier
 */
class Syndication {
	/**
	 * Types de syndication possibles
	 */
	const RSS_1_0 = 'rss10';
	const RSS_2_0 = 'rss20';
	const ATOM_1_0 = 'atom10';
	
	/**
	 * Identifiant
	 *
	 * @var SyndicationId
	 */
	public $id = null;
	
	/**
	 * Titre
	 *
	 * @var string
	 */
	public $title = null;
	
	/**
	 * Adresse liée
	 *
	 * @var SyndicationLink
	 */
	public $link = null;
	
	/**
	 * Description
	 *
	 * @var string
	 */
	public $description = null;
	
	/**
	 * Le langage dans lequel est écrit le canal. Ceci permet aux aggrégateurs de regrouper tous les sites de langue italienne, par exemple, sur une même page
	 * Valeurs possibles : http://www.w3.org/TR/REC-html40/struct/dirlang.html#langcodes
	 *
	 * @var string
	 */
	public $language = null;
	
	/**
	 * License pour le contenu du canal
	 *
	 * @var SyndicationText
	 */
	public $copyright = null;
	
	/**
	 * Adresse email de la personne responsable du contenu éditorial
	 *
	 * @var string
	 */
	public $managingEditor = null;

	/**
	 * Personne responsable des problèmes techniques relatifs au canal
	 *
	 * @var SyndicationPerson
	 */
	public $webMaster = null;
	
	/**
	 * La date de publication du contenu du canal, format timestamp
	 *
	 * @var int
	 */
	public $pubDate = null;
	
	/**
	 * La dernière date où le contenu du canal a changé, format timestamp
	 *
	 * @var int
	 */
	public $lastBuildDate = null;

	/**
	 * Générateur de la syndication
	 *
	 * @var SyndicationGenerator
	 */
	public $generator = null;
	
	/**
	 * URL pointant sur la documentation du format utilisé pour le fichier RSS
	 *
	 * @var string
	 */
	public $docs = null;
	
	/**
	 * Permet aux processus d'être notifiés des mises à jour du canal, pour enregistrer en nuage, en implémentant un protocole de flux RSS publier-souscrire léger
	 * http://www.scriptol.fr/rss/RSS-2.0.html#ltcloudgtSubelementOfLtchannelgt
	 * http://cyber.law.harvard.edu/rss/soapMeetsRss.html#rsscloudInterface
	 *
	 * @var SyndicationCloud
	 */
	public $cloud = null;
	
	/**
	 * Nombre de minutes qui indique combien de temps un canal peut être gardé en mémoire cache avant rafraîchissement à la source
	 *
	 * @var int
	 */
	public $timeToLeave = null;
	
	/**
	 * URL du logo de la syndication
	 *
	 * @ ar string
	 */
	public $logo = null;
	
	/**
	 * URL de l'icone de la syndication
	 *
	 * @var string
	 */
	public $icon = null;
	
	/**
	 * La côte PICS pour le canal
	 * http://www.scriptol.fr/rss/RSS-2.0.html#lttextinputgtSubelementOfLtchannelgt
	 *
	 * @var 
	 */
	public $rating = null;
	
	/**
	 * Un indice pour les aggrégateurs leur indiquant combien d'heures peuvent être sautées
	 * http://cyber.law.harvard.edu/rss/skipHoursDays.html#skiphours
	 *
	 * @var int
	 */
	public $skipHours = null;
	
	/**
	 * Un indice pour les aggrégateurs leur indiquant combien de jours peuvent être sautés
	 * http://blogs.law.harvard.edu/tech/skipHoursDays#skipdays
	 *
	 * @var int
	 */
	public $skipDays = null;
	
	/**
	 * Elements du canal
	 *
	 * @var SyndicationItem[]
	 */
	private $_items = array ();
	
	/**
	 * Indique si on veut compresser la sortie, c'est à dire ne pas avoir de tabulations, retours à la ligne, etc
	 *
	 * @var boolean
	 */
	public $compress = false;
	
	/**
	 * Auteurs de la syndication
	 * 
	 * @var SyndicationPerson[].
	 */
	private $_authors = array ();
	
	/**
	 * Contributeurs de la syndication
	 *
	 * @var SyndicationPerson[]
	 */
	private $_contributors = array ();
	
	/**
	 * Catégories de la syndication
	 *
	 * @var SyndicationCategory[]
	 */
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
	public function countItems () {
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
	public function countAuthors () {
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
	public function countCategories () {
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
	public function countContributors () {
		return count ($this->_contributors);
	}
	
	/**
	 * Retourne le contenu de la syndication
	 * 
	 * @param string $pSyndicType Type de syndication (rss1.0, rss2.0, atom, etc)
	 */	
	public function getContent ($pSyndicType = null) {
		if ($pSyndicType == null) {
			switch (CopixConfig::get ('feed|defaultType')) {
				case 'RSS_1_0' : $pSyndicType = self::RSS_1_0; break;
				case 'RSS_2_0' : $pSyndicType = self::RSS_2_0; break;
				case 'ATOM_1_0' : $pSyndicType = self::ATOM_1_0; break;
				default : $pSyndicType = self::RSS_2_0; break;
			}
		}
		$syndication = _class ('feed|types/Syndication' . $pSyndicType);
		return $syndication->getContent ($this);
	}
}