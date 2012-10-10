<?php
/**
 * @package copix
 * @subpackage log
 * @author Landry Benguigui, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Log dans un fichier
 *
 * @package copix
 * @subpackage log
 */
class CopixLogFileStrategy extends CopixLogAbstractStrategy {
	/**
	 * Séparateur entre les éléments
	 *
	 * @var string
	 */
	private $_separator = "\n";

	/**
	 * Le profil en cours de lecture
	 *
	 * @var string
	 */
	private $_profile = null;

    /**
     * Nombre d'éléments
     * @var int
     */
    private $_count = null;
	
	/**
	 * Indique si on peut lire le contenu du profil de log indiqué
	 *
	 * @param string $pProfile Nom du profil
	 * @return boolean
	 */
	public function isReadable ($pProfile) {
		$file = $this->_getFileName ($pProfile);
		return (file_exists ($file)) ? is_readable ($file) : true;
	}
	
	/**
	 * Indique si on peut écrire dans le profil de log indiqué
	 *
	 * @param string $pProfile Nom du profil
	 * @return boolean
	 */
	public function isWritable ($pProfile) {
		$file = $this->_getFileName ($pProfile);
		return (file_exists ($file)) ? is_writable ($file) : @touch ($file);
	}

	/**
	 * Effectue un log
	 *
	 * @param string $pProfile Nom du profil
	 * @param string $pType Type de log
	 * @param int $pLevel Niveau de log, utiliser les constantes de CopixLog
	 * @param string $pDate Date et heure du log, format YmdHis
	 * @param string $pMessage Message à loger
	 * @param array $pExtras Informations supplémentaires
	 */
	public function log ($pProfile, $pType, $pLevel, $pDate, $pMessage, $pExtras) {
		$csvLogFile = new CopixCsv ($this->_getFileName ($pProfile));
		return $csvLogFile->addLine ($this->_getArInfosLog ($pType, $pDate, $pMessage, $pLevel, $pExtras));
	}
	
	/**
	 * Supprime le contenu du log pour le profil demandé
	 *
	 * @param string $pProfile Nom du profil
	 */
	public function delete ($pProfile) {
		if ($this->isWritable ($pProfile)) {
			unlink ($this->_getFileName ($pProfile));
		}
	}

	/**
	 * Retourne un objet CSV
	 *
	 * @param string $pProfile Nom du profil de log
	 * @return CopixCsv
	 */
	private function _getCSV ($pProfile) {
		$fileName = $this->_getFileName ($pProfile);
		if (is_readable ($fileName)) {
			return new CopixCsv ($fileName);
		}
		throw new CopixLogException (_i18n ('copix:copixfile.error.fileNotFound', $fileName));
	}
	
	/**
	 * Retourne les éléments qui correspondent aux paramètres de recherche indiqués
	 *
	 * @param string $pProfile Nom du profil
	 * @param int $pStart Index du premier élément à retourner
	 * @param int $pCount Nombre d'éléments à retourner, null pour tous
	 * @return CopixLogData[]
	 */
	public function get ($pProfile, $pStart = 0, $pCount = null) {
		if ($pCount == null) {
			$pCount = 9999;
		}

		try {
			$csvLog = $this->_getCSV ($pProfile);
		} catch (Exception $e) {
			return new ArrayObject ();
		}
			
		// Récupération de l'itérateur et compte du nombre de ligne
		$csvLines = $csvLog->getIterator ();
		$this->_count = $csvLines->count();

		// Calcul de la position et des offset
		$pPosition = $this->_count - $pStart - $pCount;

		// Calcul de la position de départ pour parcourir la portion du fichier à afficher
		if ($pPosition < 0) {
			$pOffset = $pCount + $pPosition;
			$pPosition = 0;
		} else {
			$pOffset = $pCount;
		}

		$csvLines->seek ($pPosition);
		$content = array ();
		for ($i = 0 ; $i < $pOffset ; $i++) {
			$content[] = $csvLines->current ();
			$csvLines->next ();
		}

		$content = array_reverse ($content);

		$arrayObject = new ArrayObject (array_map (array ($this, '_toObject'), $content));
		return $arrayObject->getIterator ();
	}

	/**
	 * Annule la recherche du nombre d'éléments, trop long à faire pour cette stratégie
	 *
	 * @param string $pProfile Nom du profil
	 * @return null
	 */
	public function count ($pProfile) {
		try {
			return $this->_getCSV ($pProfile)->getIterator ()->count ();
		} catch (Exception $e) {
			return 0;
		}
	}

	/**
	 * Retourne la taille prise par les éléments logés
	 *
	 * @param string $pProfile Nom du profil
	 * @return int
	 */
	public function getSize ($pProfile) {
		$fileName = $this->_getFileName ($pProfile);
		if (is_readable ($fileName)) {
			return filesize ($this->_getFileName ($pProfile));
		}
		return 0;
	}
	
	/**
	 * Formate le message à sauvegarder
	 *
	 * @param string $pType Type de log
	 * @param string $pDate Date et heure du log, format YmdHis
	 * @param string $pMessage Message à loger
	 * @param int $pLevel Niveau de log, utiliser les constantes de CopixLog
	 * @param array $pExtras Informations supplémentaires
	 * @return array
	 */
	private function _getArInfosLog ($pType, $pDate, $pMessage, $pLevel, $pExtras) {
		$date = $pDate;
		$classe = (isset ($pExtras['classname'])) ? $pExtras['classname'] : null;
		$line = (isset ($pExtras['line'])) ? $pExtras['line'] : null;
		$file = (isset ($pExtras['file'])) ? $pExtras['file'] : null;
		$function = (isset ($pExtras['functionname'])) ? $pExtras['functionname'] : null;
		$user = (isset ($pExtras['user'])) ? $pExtras['user'] : null;
		
		return array ($pType, $date, $pLevel, $classe, $line, $file, $function, $user, str_replace ($this->_separator, ' ', $pMessage));
	}

	/**
	 * Conversion d'un tableau en CopixLogData
	 *
	 * @return CopixLogData
	 */
	private function _toObject ($pInfos) {
		$extras = array ('classname' => $pInfos[3], 'line' => $pInfos[4], 'file' => $pInfos[5], 'functionname' => $pInfos[6], 'user' => $pInfos[7]);
		$toReturn = new CopixLogData ($this->_profile, $pInfos[0], $pInfos[2], $pInfos[1], $pInfos[8], $extras);
		return $toReturn;
	}
	
	/**
	 * Retourne le nom du fichier de log
	 *
	 * @param string $pProfile Nom du profil
	 * @return string
	 */
	private function _getFileName ($pProfile) {
		return COPIX_LOG_PATH . $pProfile . '.log';
	}
}