<?php
/**
 * Catégorie
 */
class SyndicationCategory {
	/**
	 * Identifiant
	 *
	 * @var string
	 */
	public $id = null;
	
	/**
	 * Nom
	 *
	 * @var string
	 */
	public $name = null;
	
	/**
	 * Lien spécifique à la catégorie
	 *
	 * @var SyndicationLink
	 */
	public $link = null;
	
	/**
	 * Constructeur
	 */
	public function __construct () {
		$this->link = new SyndicationLink ();
	}
}