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
 * Classe de manipulation des dates
 * @package copix
 * @subpackage utils
 */
class CopixDateTime {
	/**
	 * Renvoie la date du premier jour de la semaine.
	 * @param string $separator le séparateur utilisé dans $date pour séparer les éléments entre eux.
	 * @return string date au format DD-MM-YYYY. Le premier jour de la semaine
	 */
	public static function firstDayOfWeek ($separator = '/') {
		return self::timestampToDate (strtotime('last Monday'), $separator);
	}

	/**
	 * Renvoie la date du dernier jour de la semaine.
	 * @param string $separator le séparateur utilisé dans $date pour séparer les éléments entre eux.
	 * @return string date au format DD-MM-YYYY. Le dernier jour de la semaine
	 */
	public static function lastDayOfWeek ($separator = '/') {
		return self::timestampToDate (strtotime('next Sunday'), $separator);
	}

	/**
	 * Transforme la date au format YYYYMMDD
	 * @param string $date la date à modifier, au format DD/MM/YYYY (en fonction de la langue, voir CopixI18N::getDateFormat)
	 * @param string $separator le séparateur utilisé dans $date pour séparer les éléments entre eux.
	 * @return string la date au format YYYYMMDD. Null si aucune date donnée. False si le format est incorrect
	 */
	public static function dateToYYYYMMDD ($pDate, $pSeparator = '/'){
		//On utilise CopixDateTimeParser pour avoir une info facilement utilisable
		$arDate = CopixDateTimeParser::parseDate ($pDate, $pSeparator);
		if ($arDate === null || $arDate === false){
			return $arDate;
		}
		//La date formattée en YYYYMMDD
		return $arDate['year'].$arDate['month'].$arDate['day'];
	}

	/**
	 * Transforme une date YYYYMMDD en date formattée en fonction du pays (exemple DD/MM/YYYY pour la france)
	 * @param string $yyyymmdd la date au format YYYYMMDD
	 * @param string $separator le séparateur à utiliser pour transformer la date
	 * @return string la date transformée. null si aucune yyyymmd est donnée. False si la date donnée n'est pas correcte
	 */
	public static function yyyymmddToDate ($pYYYYMMDD, $pSeparator='/'){
		//On utilise CopixDateTimeParser pour avoir une info facilement utilisable
		$arDate = CopixDateTimeParser::parseYYYYMMDD ($pYYYYMMDD);
		if ($arDate === null || $arDate === false){
			return $arDate;
		}
		//On retourne la date formattée
		return date (CopixI18N::getDateFormat ($pSeparator), strtotime ($arDate['year'].$arDate['month'].$arDate['day']));
	}

	/**
	 * Transforme une date de format yyyymmdd en texte lisible en fonction de la langue courante
	 * @param string $yyyymmdd la date à transformer
	 * @return string the date. null if no yyyymmdd is given. False is the yyyymmdd is incorrect
	 */
	public static function yyyymmddToText ($pYYYYMMDD){
		//On utilise CopixDateTimeParser pour avoir une info facilement utilisable
		$arDate = CopixDateTimeParser::parseYYYYMMDD ($pYYYYMMDD);
		if ($arDate === null || $arDate === false){
			return $arDate;
		}
		$yyyymmdd = strtotime ($arDate['year'].$arDate['month'].$arDate['day']);
		if (CopixI18N::getLang () == "fr"){
			//Format français
			$toReturn = CopixI18N::get ("copix:datetime.day.".date("w",$yyyymmdd))." ".date("d",$yyyymmdd)." ".CopixI18N::get("copix:datetime.month.".date("m",$yyyymmdd))." ".date("Y",$yyyymmdd);
		}else{
			//format anglais
			$toReturn = date('l dS \of F Y', $yyyymmdd);
		}
		return $toReturn;
	}

	/**
	 * Transforme une chaine hhiiss dans une chaine représentant une heure en fonction de la langue donnée (HH:MM:SS en français)
	 * @param string $hhiiss l'heure
	 * @return l'heure formattée, null si aucune heure donnée, false si l'heure est incorrecte
	 */
	public static function hhiissToTime ($pHHIISS, $pSeparator=':'){
		$arDate = CopixDateTimeParser::parseHHIISS ($pHHIISS);
		if ($arDate === null || $arDate === false){
			return $arDate;
		}
		return $arDate['hour'].$pSeparator.$arDate['min'].$pSeparator.$arDate['sec'];
	}

	/**
	 * Transforme une heure au foramt (hh:mm:ss) en HHIISS
	 * @param string $time l'heure à transformer 
	 * @return string l'heure au format HHIISS. Null si non donné. False si l'heure est incorrecte
	 */
	public static function timeTohhiiss ($pTime, $pSeparator=':'){
		$arDate = CopixDateTimeParser::parseTime ($pTime, $pSeparator);
		if ($arDate === null || $arDate === false){
			return $arDate;
		}

		//retour du hhiiss
		return $arDate['hour'].$arDate['min'].$arDate['sec'];
	}

	/**
	 * Tranformation d'une date YYYYMMDD en timestamp.
	 * @param string $date date au format YYYYMMDD
	 * @return timestamp en fonction de la date
	 */
	public static function yyyymmddToTimestamp ($pYYYYMMDD){
		//On utilise CopixDateTimeParser pour avoir une info facilement utilisable
		$arDate = CopixDateTimeParser::parseYYYYMMDD ($pYYYYMMDD);
		if ($arDate === null || $arDate === false){
			return $arDate;
		}
		return self::_parsedArrayToTimeStamp ($arDate);
	}

	/**
	 * Transformation d'un timestamp en une Date (format courant)
	 *
	 * @param int $timestamp le timestamp
	 * @param string $separator separator de date a passer au format
	 * @return date au format spécifié
	 */
	public static function timestampToDate ($pTimestamp, $pSeparator='/') {
		if (self::_isNull ($timestamp)){
			return null;
		}

		return date (CopixI18N::getDateFormat ($separator), $timestamp);
	}

	/**
	 * Transformation d'un timestamp en date yyyymmdd
	 * @param int $timestamp le timestamp
	 * @return string Date au format yyyymmdd
	 */
	public static function timestampToyyyymmdd ($timestamp) {
		if (self::_isNull ($timestamp)){
			return null;
		}
		return strftime ('%Y%m%d', $timestamp);
	}

	/**
	 * Transformation d'une date en timestamp
	 * @param date $date Une date
	 * @param string $separator Separateur
	 * @return int Un timestamp
	 */
	public static function dateToTimestamp ($pParam, $separator='/') {
		if (self::_isNull ($pParam)){
			return null;
		}
		if (($yyyymmdd = self::dateToYYYYMMDD ($pParam, $separator)) === false){
			return false;
		}
		return self::yyyymmddToTimestamp ($yyyymmdd);
	}

	/**
	 * Convertit yyyymmddhhiiss en DateTime
	 *
	 * @param string $pParam Date au format yyyymmddhhiiss
	 * @param string $pDateSeparator Separateur (par défaut /)
	 * @param string $pTimeSeparator Separateur (par défaut :)
	 * @return DateTime
	 */
	public static function yyyymmddhhiissToDateTime ($pParam, $pDateSeparator='/', $pTimeSeparator=':') {
		//On vérifie que la date donnée est remplie
		if (self::_isNull ($pParam)){
			return null;
		}
		// prise en compte des yyyymmddhh
		if (strlen ($pParam) == 8){
			$pParam = str_pad($pParam, 14, '0', STR_PAD_RIGHT);
		}

		if (strlen ($pParam) != 14){
			return false;
		}

		if ($date = self::yyyymmddToDate (substr ($pParam, 0, 8), $pDateSeparator)){
			if ($time = self::hhiissToTime (substr ($pParam, 8, 6), $pTimeSeparator)){
				return $date.' '.$time;
			}
		}

		return false;
	}

	/**
	 * Convertit yyyymmddhhiiss en texte
	 *
	 * @param string $pParam Date au format yyyymmddhhiiss
	 * @param string $separator Separateur (par défaut /)
	 * @return DateTime
	 */
	public static function yyyymmddhhiissToText ($pParam, $separator='/') {
		if (self::_isNull ($pParam)){
			return null;
		}
		// prise en compte des yyyymmddhh
		if (strlen ($pParam) == 8){
			$pParam = str_pad($pParam, 14, '0', STR_PAD_RIGHT);
		}
		
		if (strlen ($pParam) != 14){
			return false;
		}

		if ($date = self::yyyymmddToText (substr ($pParam, 0, 8), $separator)){
			if ($time = self::hhiissToTime (substr ($pParam, 8, 6))){
				return $date.' '.$time;
			}
		}
	}

	/**
	 * Convertir timestamp en yyyymmddhhiiss
	 *
	 * @param string $timestamp TimeStamp
	 * @return yyyymmddhhiiss
	 */
	public static function timeStampToyyyymmddhhiiss ($timestamp) {
		if (self::_isNull ($timestamp)){
			return null;
		}
		return strftime ('%Y%m%d%H%M%S', $timestamp);
	}

	/**
	 * Converti un timestamp en DateTime
	 * @param	int	$pTimestamp	le timestamp à convertir
	 * @param	int	$pSeparator	le séparateur à utiliser	
	 * @return string
	 */
	public static function timestampToDateTime ($pTimestamp, $pSeparator = '/'){
		return self::yyyymmddhhiissToDateTime (self::timeStampToyyyymmddhhiiss ($pTimestamp), $pSeparator);
	}

	/**
	 * Convertit yyyymmddhhiiss en timestamp
	 *
	 * @param string $pParam yyyymmddhhiiss
	 * @return timestamp
	 */
	public static function yyyymmddhhiissToTimeStamp ($pParam) {
		if (self::_isNull ($pParam)){
			return null;
		}
		// prise en compte des yyyymmddhh
		if (strlen ($pParam) == 8){
			$pParam = str_pad($pParam, 14, '0', STR_PAD_RIGHT);
		}

		//On vérifie que la date donnée est correcte
		if ((substr ($pParam, 8, 2)<0 || substr ($pParam, 8, 2)>24)) {
			return false;
		}

		if ((substr ($pParam, 10, 2)<0 || substr ($pParam, 10, 2)>59)) {
			return false;
		}

		if ((substr ($pParam, 12, 2)<0 || substr ($pParam, 12, 2)>59)) {
			return false;
		}

		if ((strlen ($pParam) !== 14) ||
		(! @checkdate (substr ($pParam, 4, 2), substr ($pParam, 6, 2), substr ($pParam, 0, 4)))) {
			return false;
		}
		return mktime (substr($pParam, 8, 2), substr($pParam, 10, 2), substr($pParam, 12, 2), substr($pParam, 4, 2), substr($pParam, 6, 2), substr($pParam, 0, 4));
	}

	/**
	 * Converti une heure au format HHIISS en timestamp
	 *
	 * @param string $pParam	l'heure à convertir
	 * @return	string / false en cas d'erreur
	 */
	public static function hhiissToTimeStamp ($pParam){
		if (self::_isNull ($pParam)){
			return null;
		}
		//On vérifie que la date donnée est correcte
		if ((substr ($pParam, 0, 2)<0 || substr ($pParam, 8, 2)>24)) {
			return false;
		}

		if ((substr ($pParam, 2, 2)<0 || substr ($pParam, 10, 2)>59)) {
			return false;
		}

		if ((substr ($pParam, 4, 2)<0 || substr ($pParam, 12, 2)>59)) {
			return false;
		}
		
		if ((int)$pParam == 0) {
			return 0;
		}
		
		//on retourne au jour 0
		return mktime (substr($pParam, 0, 2), substr($pParam, 2, 2), substr($pParam, 4, 2), 0, 0, 0);
	}

	/**
	 * Convertit DateTime en yyyymmddhhiiss
	 *
	 * @param string $DateTime le DateTime
	 * @param string $pDateSeparator Separateur (par défaut /)
	 * @param string $pTimeSeparator Separateur (par défaut :)
	 * @return yyyymmddhhiiss
	 */
	public static function DateTimeToyyyymmddhhiiss ($pDateTime, $pDateSeparator = '/', $pTimeSeparator = ':') {
		$parsedArray = CopixDateTimeParser::parseDateTime ($pDateTime, $pDateSeparator, $pTimeSeparator);
		if ($parsedArray === false || $parsedArray === null){
			return $parsedArray;
		}
		return self::timeStampToyyyymmddhhiiss (self::_parsedArrayToTimeStamp ($parsedArray));
	}

	/**
	 * Permet de convertir Datetime ISO 8601 (YYYY-MM-DD hh:ii:ss ou YYYY-MM-DDThh:ii:ssZ) en DateTime local
	 *  eg (dd/mm/yyyy)
	 * @param	string	$pIsoDateTime	la date au format ISO 8601 à convertir
	 * @param	string	$pSeparator		le séparateur que l'on va utiliser pour générer la date finale.	  
	 */
	public static function ISODateTimeToDateTime ($pIsoDateTime, $pSeparator='/') {
		//On vérifie que la date donnée est remplie
		if (self::_isNull ($pIsoDateTime)){
			return null;
		}

		if (strpos ($pIsoDateTime, "T") !== false ) {
			$delimiter = "T";
		} else if (strpos ($pIsoDateTime, " ")) {
			$delimiter = " ";
		} else {
			return false;
		}
		list ($date, $time) = explode ($delimiter, $pIsoDateTime);

		//On vérifie que l'heure donnée est correcte
		if ((substr ($time, 0, 2)<0 || substr ($time, 0, 2)>24)) {
			return false;
		}

		if ((substr ($time, 3, 2)<0 || substr ($pIsoDateTime, 3, 2)>59)) {
			return false;
		}

		if ((substr ($pIsoDateTime, 6, 2)<0 || substr ($pIsoDateTime, 6, 2)>59)) {
			return false;
		}

		if ((strlen ($date) !== 10) ||
		(! @checkdate (substr ($date, 5, 2), substr ($pIsoDateTime, 8, 2), substr ($pIsoDateTime, 0, 4))) ||
		(($pIsoDateTime = strtotime ($pIsoDateTime)) === -1)){
			return false;
		}

		//On retourne la date formattée
		return date (CopixI18N::getDateTimeFormat ($pSeparator), $pIsoDateTime);
	}

	/**
	 * Transforme une date au format YYYYMMDD au format donné en paramètre
	 *
	 * @param string $pYYYYMMDD	la date à convertir
	 * @param string $format	le format désiré ()
	 * @return string
	 */
	public static function yyyymmddToFormat ($pYYYYMMDD, $pFormat){
		if (self::_isNull ($pYYYYMMDD)){
			return null;
		}

		if (($timeStamp = self::yyyymmddToTimeStamp ($pYYYYMMDD)) !== false){
			return date ($pFormat, $timeStamp);
		}

		return false;
	}

	/**
	 * Récupération de la date au format ISO8601
	 *
	 * @param string $pDate
	 */
	public static function getIsoDateTime ($pDate) {
		if (self::ISODateTimeToDateTime ($pDate) !== false){
			return $pDate;
		}		
		
		if (($timestamp = self::yyyymmddhhiissToTimeStamp ($pDate))!== false) {
			return strftime ('%Y-%m-%d %H:%M:%S', $timestamp);
		}
	}

	/**
	 * Converti une heure au format demandé.
	 *
	 * @param string $pYYYYMMDDHHIISS la date/heure à convertir
	 * @param unknown_type $pFormat	le format désiré
	 * @return string
	 */
	public static function yyyymmddhhiissToFormat ($pYYYYMMDDHHIISS, $pFormat){
		if (self::_isNull ($pYYYYMMDDHHIISS)){
			return null;
		}

		if (($timeStamp = self::yyyymmddhhiissToTimeStamp ($pYYYYMMDDHHIISS)) !== false){
			return date ($pFormat, $timeStamp);
		}
		return false;
	}

	/**
	 * Converti une heure au format demandé.
	 *
	 * @param string $pHHIISS l'heure à convertir
	 * @param unknown_type $pFormat	le format désiré
	 * @return string
	 */
	public static function hhiissToFormat ($pHHIISS, $pFormat){
		if (self::_isNull ($pHHIISS)){
			return null;
		}
		if (($timeStamp = self::hhiissToTimeStamp ($pHHIISS)) !== false){
			return date ($pFormat, $timeStamp);
		}
		return false;
	}

	/**
	 * Conversion d'une heure en heure affichage
	 *
	 * @param string $hhiiss	heure à convertir
	 * @param string $separator	caractère de séparation à utiliser
	 * @return string
	 * @deprecated
	 */
	public static function hhmmsstoTime ($pHHIISS, $pSeparator=':'){
		return self::hhiissToTime ($pHHIISS, $pSeparator);
	}

	/**
	 * Conversion d'une heure en heure affichage
	 *
	 * @param string $hhiiss	heure à convertir
	 * @param string $separator	caractère de séparation à utiliser
	 * @return string
	 * @deprecated
	 */
	public static function timeToHHMMSS ($pHHIISS, $pSeparator=':'){
		return self::timeToHHIISS ($pHHIISS, $pSeparator);
	}

	/**
	 * Retourne la différence entre $pBaseDate et $pToDate
	 * Cette méthode n'utilisant pas de timestamp, on peut avoir des dates antérieures au 01/01/1970
	 *
	 * @param string $pBaseDate Date de départ, format yyyymmdd
	 * @param string $pToDate Date d'arrivée, format yyyymmdd (date du jour par défaut ou si = null)
	 * @param boolean $pAbsolute Indique si on veut des valeurs absolues, ou positives si $pBaseDate <= $pToDate et négatives si $pBaseDate > $pToDate
	 * @return stdclass
	 */
	public static function getDiff ($pBaseDate, $pToDate = null, $pAbsolute = false) {
		// si $pToTimestamp est null, on prend la date du jour
		if (is_null ($pToDate)) {
			$pToDate = date ('Ymd');
		}

		if (!preg_match ('/^[0-9]{8}$/', $pBaseDate)) {
			throw new CopixDateTimeException (_i18n ('copix:copixdatetime.getdiff.invalidBaseDate', $pBaseDate));
		}
		if (!preg_match ('/^[0-9]{8}$/', $pToDate)) {
			throw new CopixDateTimeException (_i18n ('copix:copixdatetime.getdiff.invalidToDate', $pToDate));
		}

		$sign = null;
		if ($pToDate < $pBaseDate) {
			$tmpDate = $pToDate;
			$pToDate = $pBaseDate;
			$pBaseDate = $tmpDate;
			$sign = ($pAbsolute) ? null : '-';
		}

		$baseYear = substr ($pBaseDate, 0, 4);
		$baseMonth = substr ($pBaseDate, 4, 2);
		$baseDay = substr ($pBaseDate, 6, 2);

		$toYear = substr ($pToDate, 0, 4);
		$toMonth = substr ($pToDate, 4, 2);
		$toDay = substr ($pToDate, 6, 2);

		if (!checkdate ($baseMonth, $baseDay, $baseYear)) {
			throw new CopixDateTimeException (_i18n ('copix:copixdatetime.getdiff.invalidBaseDate', $pBaseDate));
		}
		if (!checkdate ($toMonth, $toDay, $toYear)) {
			throw new CopixDateTimeException (_i18n ('copix:copixdatetime.getdiff.invalidToDate', $pToDate));
		}

		// calcul du nombre d'années
		$diffYear = $toYear - $baseYear;

		// calcul du nombre de mois
		if ($baseMonth > $toMonth) {
			$diffYear = ($diffYear > 0) ? $diffYear - 1 : $diffYear;
			$diffMonth = (12 + $toMonth) - $baseMonth;
		} else {
			$diffMonth = $toMonth - $baseMonth;
		}

		// calcul du nombre de jours
		if ($baseDay > $toDay) {
			if ($diffMonth > 0) {
				$diffMonth--;
			} else {
				$diffMonth = 11;
				$diffYear--;
			}
			$jourFinMois = date ("d", mktime (0, 0, 0, $toMonth, 0, $toYear));
			$diffDay = $jourFinMois - $baseDay + $toDay;
		} else {
			$diffDay = $toDay - $baseDay;
		}

		$toReturn = new stdclass ();
		$toReturn->year = ($sign == '-') ? ($diffYear - $diffYear * 2) : $diffYear;
		$toReturn->month = ($sign == '-') ? ($diffMonth - $diffMonth * 2) : $diffMonth;
		$toReturn->day = ($sign == '-') ? ($diffDay - $diffDay * 2) : $diffDay;
		return $toReturn;
	}

	/**
	 * Indique si l'année est bissextile
	 *
	 * @param int $pYear Année sur 4 chiffres, null pour l'année actuelle
	 */
	public static function isLeapYear ($pYear = null) {
		if (is_null ($pYear)) {
			$pYear = date ('Y');
		}
		// 1. Les années divisibles par 4 sont bissextiles, pas les autres.
		// 2. Exception : les années divisibles par 100 ne sont pas bissextiles.
		// 3. Exception à l'exception (!) : les années divisibles par 400 sont bissextiles.
		return ($pYear % 4 == 0 && $pYear % 400 == 0);
	}

	/**
	 * Vérifie que $pMonth, $pDay et $pYear sont des chiffres uniquement, et effectue un checkdate PHP
	 *
	 * Si un seul paramètre est passé à la fonction, on considère que c'est la date complète passée dans la 
	 * représentation CopixDateTime::getDateFormat
	 *
	 * @param int $pMonth le mois à vérifier. 
	 * @param int $pDay   le jour a vérifier	
	 * @param int $pYear  l'année a vérifier
	 * @return boolean
	 */
	public static function checkDate () {
		//pour garder la compatibilité avec l'existant Copix 3.0.3
		$args = func_get_args ();
		if (count ($args) == 3) {
			if (!preg_match ('/^[0-9]{1,2}$/', $args[0]) || !preg_match ('/^[0-9]{1,2}$/', $args[1]) || !preg_match ('/^[0-9]{1,4}$/', $args[2])) {
				return false;
			}
			return checkdate ($args[0], $args[1], $args[2]);
		} else if (count ($args) == 1) {
			//On vérifie si la date est correcte en considérant que sa représentation suit CopixDateTime::getDateFormat
			return CopixDateTime::checkDate (substr ($args[0], 3, 2), substr ($args[0], 0, 2), substr ($args[0], -4));
		} else {
			return false;//pas le bon nombre d'argument, on considère la date comme mauvaise.
		}
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

	/**
	 * Converti un tableau parsé avec CopixDateTimeParser en timestamp
	 *
	 * @param array $pArray
	 * @return int
	 */
	private static function _parsedArrayToTimeStamp ($pArray){
		return mktime ($pArray['hour'], $pArray['min'], $pArray['sec'],
		$pArray['month'], $pArray['day'], $pArray['year']);
	}

	/**
	 * Transforme une date de format GMT (Friday 25th of August 2006 10:23:17 AM) en date yyyymmdd
	 * @param string $pDateGMT la date à transformer
	 * @return string la date
	 */
	public static function GMTToyyyymmdd ($pDateGMT){
        return date('Ymd', strtotime($pDateGMT));
	}
}