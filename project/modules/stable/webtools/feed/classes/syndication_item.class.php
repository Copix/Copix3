<?php
/**
 * Element d'une syndication
 */
class SyndicationItem {
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
	 * Lien vers l'élément
	 *
	 * @var SyndicationLink
	 */
	public $link = null;
	
	/**
	 * Contenu
	 *
	 * @var SyndicationText
	 */
	public $content = null;
	
	/**
	 * Résumé
	 *
	 * @var SyndicationText
	 */
	public $summary = null;

	/**
	 * Auteurs
	 *
	 * SyndicationPerson[]
	 */
	private $_authors = array ();
	
	/**
	 * Contributeurs
	 *
	 * @var SyndicationContributor[]
	 */
	private $_contributors = array ();
	
	/**
	 * Catégories
	 *
	 * @var SyndicationCategory[]
	 */
	private $_categories = array ();
	
	/**
	 * URL de la page de commentaires concernant cet élément
	 * http://www.scriptol.fr/rss/RSS-2.0.html#ltcommentsgtSubelementOfLtitemgt
	 *
	 * @var string
	 */
	public $comments = null;
	
	/**
	 * Décrit un objet média attaché à l'item
	 * http://www.scriptol.fr/rss/RSS-2.0.html#ltenclosuregtSubelementOfLtitemgt
	 *
	 * @var SyndicationLink
	 */
	public $enclosure = null;
	
	/**
	 * Date de publication de l'item, format timestamp
	 *
	 * @var int
	 */
	public $pubDate = null;
	
	/**
	 * Date de la dernière mise à jour de l'item, format timestamp
	 *
	 * @var int
	 */
	public $updateDate = null;
	
	/**
	 * Elément source
	 *
	 * @var SyndicationItem
	 */
	public $source = null;
	
	/**
	 * License
	 *
	 * @var SyndicationText
	 */
	public $copyright = null;
	
	/**
	 * Constructeur
	 * 
	 * @param boolean $pHaveSource Si cet élément peut avoir une source
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
	public function getAuthor ($pIndex = 0) {
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
	public function getContributor ($pIndex = 0) {
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
	public function getCategory ($pIndex = 0) {
		return (isset ($this->_categories[$pIndex])) ? $this->_categories[$pIndex] : null;
	}
	
	/**
	 * Retourne le nombre de catégories
	 * 
	 * @return int
	 */
	public function countCategories () {
		return count ($this->_categories);
	}
}