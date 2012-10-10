<?php
/**
 * Générateur de la syndication
 */
class SyndicationGenerator {
	/**
	 * Nom
	 *
	 * @var string
	 */
	public $name = 'Copix, module feed';
	
	/**
	 * Version
	 *
	 * @var string
	 */
	public $version = null;
	
	/**
	 * Adresse du générateur
	 *
	 * @var SyndicationLink
	 */
	public $link = null;

	/**
	 * Constructeur
	 */
	public function __construct () {
		$this->link = new SyndicationLink ();
		$this->link->uri = 'http://www.copix.org';
		$infos = CopixModule::getInformations ('feed');
		$this->version = $infos->version;
	}
}