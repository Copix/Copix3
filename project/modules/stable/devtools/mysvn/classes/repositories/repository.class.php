<?php
/**
 * Informations sur un dépot
 * Documentation SVN : http://svnbook.red-bean.com/nightly/fr/svn.tour.history.html
 */
class RepositoriesRepository {
	/**
	 * Identifiant
	 * 
	 * @var int
	 */
	private $_id = null;

	private $_uuid = null;

	private $_revision = null;

	private $_lastAuthor = null;

	private $_lastChangeDate = null;

	/**
	 * Libellé
	 * 
	 * @var string
	 */
	private $_caption = null;

	/**
	 * Adresse
	 * 
	 * @var string
	 */
	private $_url = null;

	/**
	 * Lazy loading des informations sur le dépot
	 *
	 * @param boolean $pRefresh Indique si on veut forcer le rafraichissement des informations
	 */
	private function _setInfos ($pRefresh = false) {
		if ($this->_uuid == null || $pRefresh) {
			$infos = array ();
			exec ('svn info "' . $this->getUrl () . '"', $infos);
			foreach ($infos as $info) {
				$pos = strpos ($info, ':');
				$id = trim (substr ($info, 0, $pos));
				$value = trim (substr ($info, $pos + 1));
				switch ($id) {
					case 'Repository UUID' : $this->_uuid = $value; break;
					case 'Revision' : $this->_revision = intval ($value); break;
					case 'Last Changed Author' : $this->_lastAuthor = $value; break;
					case 'Last Changed Date' : $this->_lastChangeDate = $value; break;
				}
			}
		}
	}

	/**
	 * Définit la valeur de Identifiant
	 * 
	 * @param int $pValue Valeur
	 */
	public function setId ($pValue) {
		$this->_id = $pValue;
	}

	/**
	 * Retourne la valeur de Identifiant
	 * 
	 * @return int
	 */
	public function getId () {
		return $this->_id;
	}

	/**
	 * Définit la valeur de Libellé
	 * 
	 * @param string $pValue Valeur
	 */
	public function setCaption ($pValue) {
		$this->_caption = $pValue;
	}

	/**
	 * Retourne la valeur de Libellé
	 * 
	 * @return string
	 */
	public function getCaption () {
		return $this->_caption;
	}

	/**
	 * Définit la valeur de Adresse
	 * 
	 * @param string $pValue Valeur
	 */
	public function setUrl ($pValue) {
		$this->_url = $pValue;
	}

	/**
	 * Retourne la valeur de Adresse
	 * 
	 * @return string
	 */
	public function getUrl () {
		return $this->_url;
	}

	/**
	 * Retourne la liste des commits
	 *
	 * @param boolean $pRefresh Si on veut rafraichir les données au lieu de les lire en cache
	 * @param boolean $pStart Index du premier commit à retourner
	 * @param boolean $pCount Nombre de commits à retourner
	 * @return RepositoriesCommit[]
	 */
	public function getCommits ($pStart = 0, $pCount = 2) {
		$revision = $this->getRevision ();
		$toReturn = array ();
		while (count ($toReturn) < $pCount && $revision >= 0) {
			$infos = array ();
			exec ('svn log "' . $this->getUrl () . '" --verbose  -r ' . $revision, $infos);
			// si on a un retour vide, c'est que le commit ne concerne pas le dépot demandé
			if (count ($infos) > 2) {
				$commit = new RepositoriesCommit ();
				$commit->setMessage ($infos[count ($infos) - 2]);
				_dump ($infos, $commit);
				$toReturn[] = $commit;
			}
			$revision--;
		}
	}

	public function getUUID () {
		$this->_setInfos ();
		return $this->_uuid;
	}

	public function getRevision () {
		$this->_setInfos ();
		return $this->_revision;
	}

	public function getLastAuthor () {
		$this->_setInfos ();
		return $this->_lastAuthor;
	}

	public function getLastChangeDate ($pFormat = 'd/m/Y h:i:s') {
		$this->_setInfos ();
		return date ($this->_lastChangeDate, $pFormat);
	}

	public function refresh () {
		$this->_setInfos (true);
	}

	/**
	 * Indique si l'objet est valide
	 * 
	 * @return mixed
	 */
	public function isValid () {
		return _validator ('mysvn|RepositoriesValidator')->check ($this);
	}
}