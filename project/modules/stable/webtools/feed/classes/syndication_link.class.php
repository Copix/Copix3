<?php
/**
 * Lien
 */
class SyndicationLink {
	/**
	 * uri pointée
	 *
	 * @var string
	 */
	public $uri = null;
	
	/**
	 * Type de relation (alternate, enclosure, related, self, via)
	 * http://www.atomenabled.org/developers/syndication/#link
	 *
	 * @var string
	 */
	public $rel = null;
	
	/**
	 * Type de resource pointée
	 *
	 * @var string
	 */
	public $type = null;
	
	/**
	 * Langue de la resource pointée
	 *
	 * @var string
	 */
	public $urilang = null;
	
	/**
	 * Titre du lien, sera généralement affiché par les agrégateurs
	 *
	 * @var string
	 */
	public $title = null;
	
	/**
	 * Taille en octets de la resource pointée
	 *
	 * @var int
	 */
	public $resourceLength = null;
	
	/**
	 * Type mime du lien pointé
	 *
	 * @var string
	 */
	public $mimeType = null;
}