<?php
/**
 * Image d'une syndication
 */
class SyndicationImage {
	/**
	 * URL d'une image qui représente le canal
	 *
	 * @var SyndicationLink
	 */
	public $src = null;
	
	/**
	 * Décrit l'image, il est utilisé par l'attribut alt de la balise HTML <img> quand le canal est rendu en HTML
	 *
	 * @var string
	 */
	public $title = null;
	
	/**
	 * URL du site. quand le canal est affiché, l'image est un lien sur le site
	 *
	 * @var SyndicationLink
	 */
	public $link = null;
	
	/**
	 * Llargeur de l'image
	 *
	 * @var int
	 */
	public $width = null;
	
	/**
	 * Hauteur de l'image
	 *
	 * @var int
	 */
	public $height = null;
	
	/**
	 * Texte inclut dans l'attribut title du lien formé autour de l'image dans le rendu HTML
	 *
	 * @var int
	 */
	public $description = null;

	/**
	 * Constructeur
	 */
	public function __construct () {
		$this->src = new SyndicationLink ();
		$this->link = new SyndicationLink ();
	}
}