<?php
/**
 * @package    copix
 * @subpackage utils
 * @author     Gérald Croës
 * @copyright  CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe capable de transformer les diverses représentations standard des dates dans 
 * copix en un tableau year, month, day, hour, min, sec
 * @package copix
 * @subpackage core
 */
class CopixDateTimeParser {
	/**
	 * Converti une date au format local en tableau associatif
	 * @param string $pDate      la date a parser (au format local)
	 * @param string $pSeparator le séparateur utilisé
	 * @return array
	 */
	public static function parseDate ($pDate, $pSeparator){
		if (self::_isNull ($pDate)){
			return null;
		}

		//On vérifie que la date dispose de exactement 3 éléments
		if (count ($tmp = explode ($pSeparator, $pDate)) !== 3){
			return false;
		}
		foreach ($tmp as $key=>$value){
			$tmp[$key] = intval ($value);
		}

		//récupération du format de date de la langue courante
		$format = CopixI18N::getDateFormat ($pSeparator);

		//On crée un tableau intermédiaire ($positions) pour indiquer à quelles positions sont les 
		//mois, jours et année dans $tmp 
		$positions = array ('d'=>strpos ($format, 'd'),
							'm'=>strpos ($format, 'm'),
							'Y'=>strpos ($format, 'Y'));

		//we know the first match will be 0 (at least we start with d m or Y)
		switch (array_search (0, $positions)){
			case 'd':
				if ($positions['m'] > $positions['Y']){
					$positions['m'] = 2;
					$positions['Y'] = 1;
				}else{
					$positions['m'] = 1;
					$positions['Y'] = 2;
				}
				break;
			case 'm':
				if ($positions['d'] > $positions['Y']){
					$positions['d'] = 2;
					$positions['Y'] = 1;
				}else{
					$positions['d'] = 1;
					$positions['Y'] = 2;
				}
				break;
			case 'Y':
				if ($positions['d'] > $positions['m']){
					$positions['d'] = 2;
					$positions['m'] = 1;
				}else{
					$positions['d'] = 1;
					$positions['m'] = 2;
				}
				break;
		}

		//Création du tableau de date
		$arDate = array ('year'=>$tmp[$positions['Y']], 'month'=>$tmp[$positions['m']], 'day'=>$tmp[$positions['d']]);

		//On retourne le tableau si ok, false sinon
		return self::_checkArray (self::_fillArray ($arDate));
	}

	/**
	 * Parse un "datetime" au format local
	 *
	 * @param string $pDate           le date time au format local
	 * @param string $pDateSeparator  le séparateur de date à considérer
	 * @param string $pTimeSeparator  le séparateur d'heure à considérer
	 * @return array
	 */
	public static function parseDateTime ($pDate, $pDateSeparator, $pTimeSeparator){
		if (self::_isNull ($pDate)){
			return null;
		}
		
		if (($datePart = self::parseDate (substr ($pDate, 0, 10), $pDateSeparator)) === false){
			return false;
		}
		
		if (($hourPart = self::parseTime (substr ($pDate, -8), $pTimeSeparator)) === false){
			return false;
		}
	
		foreach (array ('hour', 'min', 'sec') as $part){
			$datePart[$part] = $hourPart[$part];
		}
		return $datePart;
	}

	public static function parseTimeStamp ($pDate){
		if (self::_isNull ($pDate)){
			return null;
		}
		return date ($separator, $timestamp);
	}

	public static function parseYYYYMMDD ($pDate){
		if (self::_isNull ($pDate)){
			return null;
		}
		if (strlen ($pDate) !== 8){
			return false;
		}

		//création du tableau
		$arDate = array ('year'=>substr ($pDate, 0, 4),
						 'month'=>substr ($pDate, 4, 2), 
					 	 'day'=>substr ($pDate, 6, 2));

		//On retourne le tableau si ok, false sinon
		return self::_checkArray (self::_fillArray ($arDate));
	}

	/**
	 * Parse une date/heure exprimée en YYYYMMDDHHIISS
	 * 
	 * @param string $pDate la date heure à parser
	 *
	 * @return array
	 */
	public static function parseYYYYMMDDHHIISS ($pDate){
		if (self::_isNull ($pDate)){
			return null;
		}
		if (strlen ($pDate) !== 14){
			return false;
		}
		if (($datePart = self::parseYYYYMMDD (substr ($pDate, 0, 8))) === false){
			return false;
		}
		if (($hourPart = self::parseTime (substr ($pDate, -6))) === false){
			return false;
		}
		foreach (array ('hour', 'min', 'sec') as $part){
			$datePart[$part] = $hourPart[$part];
		}
		return $datePart;
	}

	/**
	 * Parse une heure exprimée en HH II SS
	 *
	 * @param string $pHHIISS l'heure a interpréter
	 * @return array
	 */
	public static function parseHHIISS ($pHHIISS){
		if (self::_isNull ($pHHIISS)){
			return null;
		}

		$arTime = array();
		switch (strlen($pHHIISS)) {
			case 6:
				$arTime['sec'] = substr($pHHIISS, 4, 2);
			case 4:
				$arTime['min'] = substr($pHHIISS, 2, 2);
			case 2:
				$arTime['hour'] = substr($pHHIISS, 0, 2);
		}

		//On retourne le tableau si ok, false sinon
		return self::_checkArray (self::_fillArray ($arTime), true);
	}

	/**
	 * Parse une heure exprimée au format local
	 *
	 * @param string $pTime      l'heure à parser
	 * @param string $pSeparator le séparateur a considérer
	 * @return array
	 */
	public static function parseTime ($pTime, $pSeparator){
		if (self::_isNull ($pTime)){
			return null;
		}

		$arTime = array ();
		switch (count ($time = explode ($pSeparator, $pTime))){
			case 3:
				$arTime['sec'] = $time[2];
			case 2:
				$arTime['min'] = $time[1];
			case 1:
				$arTime['hour'] = $time[0];
		}

		//On retourne le tableau si ok, false sinon
		return self::_checkArray (self::_fillArray ($arTime), true);
	}

	/**
	 * Vérifie que le tableau passé en paramètre corresponds à une date / heure valide.
	 * 
	 * @param array $pArray un tableau associatif qui représente la date
	 * @return null / false / le tableau inchangé
	 */
	public static function _checkArray ($pArray, $pTimeOnly = false){
		//Null ou False, on retourne tel quel
		if ($pArray === null || $pArray === false){
			return $pArray;
		}
		//Si on ne souhaites vérifier que la composante heure
		if ($pTimeOnly === false){
			//On vérifie que la date soit ok
			if (! checkdate ($pArray['month'], $pArray['day'], $pArray['year'])){
				return false;
			}
		}

		//On vérifie que la partie heure soit ok
		if (!self::_checkTime ($pArray['hour'], $pArray['min'], $pArray['sec'])){
			return false;
		}

		return $pArray;
	}

	/**
	 * Cette fonction remplis les éléments manquants dans la représentation d'une date
	 *
	 * @param	array	$pArray	Le tableau a compléter s'il manque des élémnets
	 * @return 	array / null si null donné
	 */
	public static function _fillArray ($pArray){
		$toCheck = array ('year'=>"%04d", 'day'=>"%02d", 'month'=>"%02d", 'hour'=>"%02d", 'min'=>"%02d", 'sec'=>"%02d");
		foreach ($toCheck as $element=>$length){
			if (!isset ($pArray[$element])){
				$pArray[$element] = 0;
			}
			$vNumeric = new CopixValidatorNumeric ();
			if ($vNumeric->check ($pArray[$element]) !== true){
				return false;
			}
			$pArray[$element] = sprintf ($length, $pArray[$element]);
		}
		return $pArray;
	}

	/**
	 * Vérification de l'heure pasée en paramètre, si pas bon, retourne false
	 * 
	 * @param int $pHour l'heure
	 * @param int $pMin  les minutes
	 * @param int $pSec  les secondes
	 * @return boolean 
	 */
	protected static function _checkTime ($pHour, $pMin, $pSec){
		$v0To23 = new CopixValidatorNumeric (array ('min'=>0, 'max'=>23));
		$v0To59 = new CopixValidatorNumeric (array ('min'=>0, 'max'=>59));
		return $v0To23->check ($pHour) === true 
		       && $v0To59->check ($pMin) === true
		       && $v0To59->check ($pSec) === true;
	}

	/**
	 * Vérifie que la valeur en entrée n'est pas une date nulle
	 *
	 * @param string $pDate la représentation de la date a vérifier, dans un format inconnu
	 * @return boolean
	 */
	private static function _isNull (& $pDate){
		$pDate = trim ($pDate);
		//si la date donnée est nulle ou vide, on retourne null
		if (($pDate === null) || (strlen ($pDate = trim ($pDate)) === 0)){
			return true;
		}
		return false;
	}
}