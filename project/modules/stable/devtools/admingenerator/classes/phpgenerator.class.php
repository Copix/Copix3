<?php
/**
 * @package devtools
 * @subpackage admingenerator
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Facilite la génération de code PHP
 *
 * @package devtools
 * @subpackage admingenerator
 */
class PHPGenerator {
	const TYPE_BOOLEAN = 'boolean';
	const TYPE_DATE = 'date';
	const TYPE_TIME = 'time';
	const TYPE_DATETIME = 'datetime';

	/**
	 * Cache du générateur PHP
	 *
	 * @var CopixPHPGenerator
	 */
	private $_php = null;

	/**
	 * Constructeur
	 */
	public function __construct () {
		$this->_php = new CopixPHPGenerator ();
	}

	/**
	 * Retourne la liste des types de champs spéciaux
	 *
	 * @return array
	 */
	public function getTypes () {
		return array (
			self::TYPE_BOOLEAN => 'Boolean',
			self::TYPE_DATE => 'Date',
			self::TYPE_TIME => 'Heure',
			self::TYPE_DATETIME => 'Date et heure'
		);
	}

	/**
	 * Retourne le code PHP pour un setteur
	 *
	 * @param string $pMethod Nom de la méthode
	 * @param string $pType Type de la propriété
	 * @param string $pProperty Nom de la propriété
	 * @param string $pCaption Libellé
	 * @return string
	 */
	public function getPHP4Settor ($pMethod, $pType, $pProperty, $pCaption) {
		switch ($pType) {
			case self::TYPE_BOOLEAN :
				$param = '$pValue';
				$comment = $this->_php->getPHPDoc (array ('Définition de ' . $pCaption, null, '@param boolean ' . $param . ' Valeur'), 1);
				$set = $this->_php->getLine ('$this->_' . $pProperty . ' = _filter (\'boolean\')->get (' . $param . ');', 2);
				break;
			
			case self::TYPE_DATE :
				$param = '$pDate';
				$set = $this->_php->getLine ('$this->_' . $pProperty . ' = CopixDateTime::yyyymmddToTimestamp (' . $param . ');', 2);
				$comment = $this->_php->getPHPDoc (array ('Définition de ' . $pCaption, null, '@param string ' . $param . ' Date au format yyyymmdd'), 1);
				break;
			
			case self::TYPE_TIME :
				$param = '$pTime';
				$comment = $this->_php->getPHPDoc (array ('Définition de ' . $pCaption, null, '@param string' . $param . ' Heure au format hhiiss'), 1);
				$set = $this->_php->getLine ('$this->_' . $pProperty . ' = CopixDateTime::hhiissToTimestamp (' . $param . ');', 2);
				break;

			case self::TYPE_DATETIME :
				$param = '$pDateTime';
				$comment = $this->_php->getPHPDoc (array ('Définition de ' . $pCaption, null, '@param string ' . $param . ' Date et heure au format yyyymmddhhiiss'), 1);
				$set = $this->_php->getLine ('$this->_' . $pProperty . ' = CopixDateTime::yyyymmddhhiissToTimestamp (' . $param . ');', 2);
				break;

			default :
				$param = '$pValue';
				$comment = $this->_php->getPHPDoc (array ('Définition de ' . $pCaption, null, '@param ' . $pType . ' ' . $param . ' Valeur'), 1);
				$set = $this->_php->getLine ('$this->_' . $pProperty . ' = ' . $param . ';', 2);
				break;
		}

		$toReturn = $comment;
		$toReturn .= $this->_php->getLine ('public function ' . $pMethod . ' (' . $param . ') {', 1);
		$toReturn .= $set;
		$toReturn .= $this->_php->getLine ('}', 1);
		return $toReturn;
	}

	public function getPHP4Gettor ($pMethod, $pType, $pProperty, $pCaption) {
		$toReturn = null;

		// date, time et datetime
		if (in_array ($pType, array (self::TYPE_DATE, self::TYPE_TIME, self::TYPE_DATETIME))) {
			$toReturn .= $this->_php->getPHPDoc (array ('Retourne la valeur de ' . $pCaption, null, '@param string $pFormat Format de retour, null pour le format de la langue courante', '@return string'), 1);
			$toReturn .= $this->_php->getLine ('public function ' . $pMethod . ' ($pFormat = null) {', 1);
			$toReturn .= $this->_php->getLine ('if ($this->_' . $pProperty . ' == null) {', 2);
			$toReturn .= $this->_php->getLine ('return null;', 3);
			$toReturn .= $this->_php->getLine ('}', 2);
			$toReturn .= $this->_php->getLine ('if ($pFormat == null) {', 2);
			if ($pType == 'date') {
				$toReturn .= $this->_php->getLine ('$pFormat = CopixI18N::getDateFormat ();', 3);
			} else if ($pType == 'datetime') {
				$toReturn .= $this->_php->getLine ('$pFormat = CopixI18N::getDateTimeFormat ();', 3);
			} else {
				$toReturn .= $this->_php->getLine ('$pFormat = \'H:i:s\';', 3);
			}
			$toReturn .= $this->_php->getLine ('}', 2);
			$toReturn .= $this->_php->getLine ('return date ($pFormat, $this->_' . $pProperty . ');', 2);
			$toReturn .= $this->_php->getLine ('}', 1, 2);
		} else {
			$toReturn .= $this->_php->getPHPDoc (array ('Retourne la valeur de ' . $pCaption, null, '@return ' . $pType), 1);
			$toReturn .= $this->_php->getLine ('public function ' . $pMethod . ' () {', 1);
			$toReturn .= $this->_php->getLine ('return $this->_' . $pProperty . ';', 2);
			$toReturn .= $this->_php->getLine ('}', 1);
		}

		return $toReturn;
	}
}