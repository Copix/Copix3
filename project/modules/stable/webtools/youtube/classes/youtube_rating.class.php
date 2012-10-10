<?php
/**
 * Informations sur les notes d'une vidéos
 */
class YoutubeRating {
	/**
	 * Moyenne de toutes les notes
	 *
	 * @var float
	 */
	private $_average = null;

	/**
	 * Note minimum qui peut être donnée
	 *
	 * @var int
	 */
	private $_min = null;

	/**
	 * Note maximale qui peut être donnée
	 *
	 * @var int
	 */
	private $_max = null;

	/**
	 * Nombre de notes données
	 *
	 * @var int
	 */
	private $_count = null;

	/**
	 * Constructeur
	 *
	 * @param SimpleXMLNode $pXML Node contenant les infos sur les votes
	 */
	public function __construct ($pXML) {
		$attributes = $pXML->attributes ();
		$this->_average = (string)$attributes['average'];
		$this->_min = (int)$attributes['min'];
		$this->_max = (int)$attributes['max'];
		$this->_count = (int)$attributes['count'];
	}

	/**
	 * Retourne la moyenne de toutes les notes
	 *
	 * @return float
	 */
	public function getAverage () {
		return $this->_average;
	}

	/**
	 * Retourne la note minimum qui peut être donnée
	 *
	 * @return int
	 */
	public function getMin () {
		return $this->_min;
	}

	/**
	 * Retourne la note maximale qui peut être donnée
	 *
	 * @return int
	 */
	public function getMax () {
		return $this->_max;
	}

	/**
	 * Nombre de notes données
	 *
	 * @return int
	 */
	public function count () {
		return $this->_count;
	}
}