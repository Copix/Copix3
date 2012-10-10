<?php
/**
 * Informations sur une personne
 */
class SyndicationPerson {
	/**
	 * Nom
	 *
	 * @var string
	 */
	public $name = null;
	
	/**
	 * E-mail
	 *
	 * @var string
	 */
	public $email = null;
	
	/**
	 * Adresse du site de la personne
	 *
	 * @var SyndicationLink[]
	 */
	public $webSite = null;
	
	/**
	 * Constructeur
	 */
	public function __construct () {
		$this->webSite = new SyndicationLink ();
	}
}