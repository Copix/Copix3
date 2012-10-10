<?php
/**
 * Texte
 */
class SyndicationText {
	/**
	 * Contenu du texte
	 *
	 * @var string
	 */
	public $value = null;
	
	/**
	 * Type du contenu (text, html, xhtml, text+xml, html+xml, xhtml+xml)
	 *
	 * @var string
	 */
	public $type = 'text';
	
	/**
	 * Source où trouver le texte.
	 *
	 * @var SyndicationLink
	 */
	public $src = null;

	/**
	 * Encodage du texte, si besoin
	 *
	 * @var string
	 */
	public $encoded = null;
	
	/**
	 * Constructeur
	 */
	public function __construct () {
		$this->src = new SyndicationLink ();
	}
}