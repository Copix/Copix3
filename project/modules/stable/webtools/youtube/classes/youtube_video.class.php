<?php
/**
 * Informations une vidéo Youtube
 */
class YoutubeVideo {
	/**
	 * Identifiant
	 *
	 * @var string
	 */
	private $_id = null;

	/**
	 * Date de publication, format timestamp
	 *
	 * @var int
	 */
	private $_publishedDate = null;

	/**
	 * Date de dernière modification, format timestamp
	 *
	 * @var int
	 */
	private $_updatedDate = null;

	/**
	 * Titre
	 *
	 * @var string
	 */
	private $_title = null;

	/**
	 * Description
	 *
	 * @var string
	 */
	private $_description = null;

	/**
	 * Miniatures
	 *
	 * @var YoutubeThumbnail[]
	 */
	private $_thumbnails = array ();

	/**
	 * Durée de la vidéo, en secondes
	 *
	 * @var int
	 */
	private $_duration = null;

	/**
	 * Votes
	 *
	 * @var YoutubeRating
	 */
	private $_rating = null;

	/**
	 * Nombre de visualisations
	 *
	 * @var int
	 */
	private $_viewCount = null;

	/**
	 * Nombre de mise en favori
	 *
	 * @var int
	 */
	private $_favoriteCount = null;

	/**
	 * Constructeur
	 *
	 * @param SimpleXMLNode $pXML Node contenant les informations de la vidéo
	 */
	public function __construct ($pXML) {
		$this->_id = substr ((string)$pXML->id, strrpos ((string)$pXML->id, '/') + 1);
		$this->_publishedDate = strtotime ((string)$pXML->published);
		$this->_updatedDate = strtotime ((string)$pXML->updated);
		$this->_title = (string)$pXML->title;
		$this->_description = (string)$pXML->content;
		$this->_author = (string)$pXML->author->name;
		foreach ($pXML->children ('media', true)->group->children ('media', true)->thumbnail as $thumbnail) {
			$this->_thumbnails[] = new YoutubeThumbnail ($thumbnail);
		}
		$attributes = $pXML->children ('media', true)->group->children ('yt', true)->duration->attributes ();
		$this->_duration = (int)$attributes['seconds'];
		if (isset ($pXML->children ('gd', true)->rating)) {
			$this->_rating = new YoutubeRating ($pXML->children ('gd', true)->rating);
		}

		if (isset ($pXML->children ('yt', true)->statistics)) {
			$attributes = $pXML->children ('yt', true)->statistics->attributes ();
			$this->_viewCount = (int)$attributes['viewCount'];
			$this->_favoriteCount = (int)$attributes['favoriteCount'];
		}
	}

	/**
	 * Retourne l'identifiant
	 *
	 * @return string
	 */
	public function getId () {
		return $this->_id;
	}

	/**
	 * Retourne la date de publication
	 *
	 * @param string $pFormat Voir la fonction date () PHP, ou null pour avoir le timestamp
	 * @return mixed
	 */
	public function getPublishedDate ($pFormat = null) {
		return ($pFormat === null) ? $this->_publishedDate : date ($pFormat, $this->_publishedDate);
	}

	/**
	 * Retourne la date de dernière modification
	 *
	 * @param string $pFormat Voir la fonction date () PHP, ou null pour avoir le timestamp
	 * @return mixed
	 */
	public function getUpdateddDate ($pFormat = null) {
		return ($pFormat === null) ? $this->_updatedDate : date ($pFormat, $this->_updatedDate);
	}

	/**
	 * Retourne le titre
	 *
	 * @return string
	 */
	public function getTitle () {
		return $this->_title;
	}

	/**
	 * Retourne la description
	 *
	 * @return string
	 */
	public function getDescription () {
		return $this->_description;
	}

	/**
	 * Retourne l'adresse du player flash
	 *
	 * @return string
	 */
	public function getURL () {
		return 'http://www.youtube.com/watch?v=' . $this->getId ();
	}

	/**
	 * Retourne l'auteur
	 *
	 * @return string
	 */
	public function getAuthor () {
		return $this->_author;
	}

	/**
	 * Retourne toutes les miniatures disponibles
	 *
	 * @return YoutubeThumbnail[]
	 */
	public function getThumbnails () {
		return $this->_thumbnails;
	}

	/**
	 * Retourne la miniature demandée
	 *
	 * @param int $pIndex Numéro de la miniature
	 * @return YoutubeThumbnail
	 */
	public function getThumbnail ($pIndex = 0) {
		return (array_key_exists ($this->_thumbnails, $pIndex)) ? $this->_thumbnails[$pIndex] : null;
	}

	/**
	 * Retourne le nombre de miniatures disponibles
	 *
	 * @return int
	 */
	public function thumbnailsCount () {
		return count ($this->_thumbnails);
	}

	/**
	 * Retourne la durée de la vidéo, en secondes
	 *
	 * @return int
	 */
	public function getDuration () {
		return $this->_duration;
	}

	/**
	 * Indique si il existe des informations de vote
	 *
	 * @return boolean
	 */
	public function haveRating () {
		return $this->_rating !== null;
	}

	/**
	 * Retourne les informations de vote
	 *
	 * @return YoutubeRating
	 */
	public function getRating () {
		return $this->_rating;
	}

	/**
	 * Retourne le nombre de visualisation
	 *
	 * @return int
	 */
	public function viewCount () {
		return $this->_viewCount;
	}

	/**
	 * Retourne le nombre de mise en favori
	 *
	 * @return int
	 */
	public function favoriteCount () {
		return $this->_favoriteCount;
	}
}