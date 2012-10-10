<?php
/**
 * Informations sur une miniature de vidéo
 */
class YoutubeThumbnail {
	/**
	 * Adresse de l'image
	 *
	 * @var string
	 */
	private $_url = null;

	/**
	 * Hauteur, en pixels
	 *
	 * @var int
	 */
	private $_height = null;

	/**
	 * Largeur, en pixels
	 *
	 * @var int
	 */
	private $_width = null;

	/**
	 * Temps de la vidéo où la capture a été faite
	 *
	 * @var string
	 */
	private $_time = null;

	/**
	 * Constructeur
	 *
	 * @param SimpleXMLNode $pXML Node contenant les informations sur la miniature
	 */
	public function __construct ($pXML) {
		$attributes = $pXML->attributes ();
		$this->_url = (string)$attributes['url'];
		$this->_height = (int)$attributes['height'];
		$this->_width = (int)$attributes['width'];
		$this->_time = (string)$attributes['time'];
	}

	/**
	 * Retourne l'adresse de l'image
	 *
	 * @return string
	 */
	public function getURL () {
		return $this->_url;
	}

	/**
	 * Retourne la hauteur de l'image, en pixels
	 *
	 * @return int
	 */
	public function getHeight () {
		return $this->_height;
	}

	/**
	 * Retourne la largeur de l'image, en pixels
	 *
	 * @return int
	 */
	public function getWidth () {
		return $this->_width;
	}

	/**
	 * Retourne le temps de la vidéo où la capture a été faite
	 *
	 * @return string
	 */
	public function getTime () {
		return $this->_time;
	}
}