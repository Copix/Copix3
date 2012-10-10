<?php
/**
 * @package copix
 * @subpackage log
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Informations sur une ligne de log
 * 
 * @package copix
 * @subpackage log
 */
class CopixLogData {
	/**
	 * Nom du profil de log qui a généré ce log
	 *
	 * @var string
	 */
	private $_profile = null;
	
	/**
	 * Type du log
	 *
	 * @var string
	 */
	private $_type = null;
	
	/**
	 * Niveau de log, utiliser les constantes de CopixLog
	 *
	 * @var int
	 */
	private $_level = null;
	
	/**
	 * Date du log, format timestamp
	 *
	 * @var string
	 */
	private $_date = null;
	
	/**
	 * Message
	 *
	 * @var string
	 */
	private $_message = null;
	
	/**
	 * Informations supplémentaires
	 *
	 * @var array
	 */
	private $_extras = null;
	
	/**
	 * Constructeur
	 *
	 * @param string $pProfile Nom du profil
	 * @param string $pType Type de log
	 * @param int $pLevel Niveau de log, utiliser les constantes de CopixLog
	 * @param string $pDate Date et heure du log, format yyyymmddhhmiiss
	 * @param string $pMessage Message à loger
	 * @param array $pExtras Informations supplémentaires
	 */
	public function __construct ($pProfile, $pType, $pLevel, $pDate, $pMessage, $pExtras = array ()) {
		$this->_profile = $pProfile;
		$this->_type = $pType;
		$this->_level = $pLevel;
		$this->_date = CopixDateTime::yyyymmddhhiissToTimeStamp ($pDate);
		$this->_message = $pMessage;
		$this->_extras = $pExtras;
	}
	
	/**
	 * Retourne le nom du profil qui a géréné ce log
	 *
	 * @return string
	 */
	public function getProfile () {
		return $this->_profile;
	}
	
	/**
	 * Retourne le type
	 *
	 * @return string
	 */
	public function getType () {
		return $this->_type;
	}
	
	/**
	 * Retourne le niveau du log
	 *
	 * @return int
	 */
	public function getLevel () {
		return $this->_level;
	}
	
	/**
	 * Retourne la date
	 *
	 * @param string $pFormat Format de la date à retourner, null pour le format de la langue courante
	 * @return string
	 */
	public function getDate ($pFormat = null) {
		if ($pFormat == null) {
			$pFormat = CopixI18N::getDateTimeFormat ();
		}
		return date ($pFormat, $this->_date);
	}
	
	/**
	 * Retourne le message du log
	 *
	 * @return string
	 */
	public function getMessage () {
		return $this->_message;
	}
	
	/**
	 * Retourne toutes les informations supplémentaires
	 *
	 * @return array
	 */
	public function getExtras () {
		return $this->_extras;
	}
	
	/**
	 * Retourne la valeur de l'information supplémentaire demandée
	 *
	 * @param string $pKey Clef de l'extra
	 * @param mixed $pDefaultValue Valeur par défaut
	 */
	public function getExtra ($pKey, $pDefaultValue = null) {
		return (array_key_exists ($pKey, $this->_extras)) ? $this->_extras[$pKey] : $pDefaultValue;
	}
}