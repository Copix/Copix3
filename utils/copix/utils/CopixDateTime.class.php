<?php
/**
* @package   copix
* @subpackage utils
* @author    Gérald Croës
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license   http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Gestion des exceptions pour CopixDateTime 
 * 
 * @package		copix
 * @subpackage	utils
 */
class CopixDateTimeException extends CopixException {
	
}

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
     * @param string $date la date à modifier, au format DD/MM/YYYY (en fonction de la langue, voir CopixI18N::getDateForamt)
     * @param string $separator le séparateur utilisé dans $date pour séparer les éléments entre eux.
     * @return string la date au format YYYYMMDD. Null si aucune date donnée. False si le format est incorrect
     */
	public static function dateToYYYYMMDD ($date, $separator = '/'){
		//si la date donnée est nulle ou vide, on retourne null
		if (($date === null) || (strlen ($date = trim ($date)) === 0)){
			return null;
		}

		//On vérifie que la date dispose de exactement 3 éléments
		if (count ($tmp = explode ($separator, $date)) !== 3){
			return false;
		}
		foreach ($tmp as $key=>$value){
			$tmp[$key] = $value;
		}

		//récupération du format de date de la langue courante
		$format = CopixI18N::getDateFormat ($separator);

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

		if (! self::checkDate ($tmp[$positions['m']], $tmp[$positions['d']], $tmp[$positions['Y']])){
			return false;
		}

		//La date formattée en YYYYMMDD
		return sprintf ("%04d", $tmp[$positions['Y']]).sprintf("%02d", $tmp[$positions['m']]).sprintf( "%02d", $tmp[$positions['d']]);
	}

	/**
	 * Transforme une date YYYYMMDD en date formattée en fonction du pays (exemple DD/MM/YYYY pour la france)
     * @param string $yyyymmdd la date au format YYYYMMDD
     * @param string $separator le séparateur à utiliser pour transformer la date
     * @return string la date transformée. null si aucune yyyymmd est donnée. False si la date donnée n'est pas correcte
     */
	public static function yyyymmddToDate ($yyyymmdd, $separator='/'){
        // Substitution des caractères autres que numérique
        // $yyyymmdd = CopixFilter::getAlphaNum($yyyymmdd); 

	    //On vérifie que la date donnée est remplie
		if (($yyyymmdd !== false) && (($yyyymmdd === null) || (strlen ($yyyymmdd = trim ($yyyymmdd)) === 0))){
			return null;
		}

		//On vérifie que la date donnée est correcte
		if ((strlen ($yyyymmdd) !== 8) ||
		(! @checkdate (substr ($yyyymmdd, 4, 2), substr ($yyyymmdd, 6, 2), substr ($yyyymmdd, 0, 4))) ||
		(($yyyymmdd = strtotime ($yyyymmdd)) === -1)){
			return false;
		}

		//On retourne la date formattée
		return date (CopixI18N::getDateFormat ($separator), $yyyymmdd);
	}

	/**
	 * Transforme une date de format yyyymmdd en texte lisible en fonction de la langue courante
	 * @param string $yyyymmdd la date à transformer
	 * @return string the date. null if no yyyymmdd is given. False is the yyyymmdd is incorrect
	 */
	public static function yyyymmddToText ($yyyymmdd){
	    //Aucune date donnée ?
	    if (($yyyymmdd !== false) && (($yyyymmdd === null) || (strlen ($yyyymmdd = trim ($yyyymmdd)) === 0))){
	        return null;
	    }

	    //Vérification du format
	    if ((strlen ($yyyymmdd) !== 8) ||
	    (! @checkdate (substr ($yyyymmdd, 4, 2), substr ($yyyymmdd, 6, 2), substr ($yyyymmdd, 0, 4))) ||
	    (($yyyymmdd = strtotime ($yyyymmdd)) === -1)){
	        return false;
	    }

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
	public static function hhiissToTime ($hhiiss, $separator=':'){
	    if (($hhiiss !== false) && (($hhiiss === null) || (strlen ($hhiiss = trim ($hhiiss)) === 0))){
	        return null;
	    }

	    $arTime=array();
	    switch (strlen($hhiiss)) {
	        case 6:
	            $arTime[2]=substr($hhiiss, 4, 2);
	            if ($arTime[2] > 59) return false;

	        case 4:
	            $arTime[1]=substr($hhiiss, 2, 2);
	            if ($arTime[1] > 59) return false;
	            	
	        case 2:
	            $arTime[0]=substr($hhiiss, 0, 2);
	            if ($arTime[0] > 23) return false;
	            break;

	        default:
	            return false;
	    }
	    ksort ($arTime);
	    if (count($arTime) > 0) {
	        return implode(':',$arTime);
	    }else{
	        return false;
	    }
	}

	/**
	 * Transforme une heure au foramt (hh:mm:ss) en HHIISS
	 * @param string $time l'heure à transformer 
	 * @return string l'heure au format HHIISS. Null si non donné. False si l'heure est incorrecte
	 */
	public static function timeTohhiiss ($time, $separator=':'){
	    if (($time !== false) && (($time === null) || (strlen ($time = trim ($time)) === 0))){
	        return null;
	    }

	    $time = explode ($separator, $time);
	    $seconds = 0;
	    $minutes = 0;
	    $hour = 0;
	    switch (count ($time)){
	        case 3:
	            $seconds = $time[2];
	        case 2:
	            $minutes = $time[1];
	        case 1:
	            $hour = $time[0];
	            break;

	        default:
	            return false;
	    }
	    if (! (is_numeric ($seconds) && is_numeric ($minutes) && is_numeric ($hour))){
	        return false;
	    }
	    if (! ((0 <= $seconds) && ($seconds < 60))){
	        return false;
	    }
	    if (! ((0 <= $minutes) && ($minutes < 60))){
	        return false;
	    }
	    if ($hour == 24){
	        $hour = 0;
	    }
	    if (! ((0 <= $hour) && ($hour < 24))){
	        return false;
	    }
	    return sprintf("%02d",$hour).sprintf("%02d",$minutes).sprintf("%02d",$seconds);
	}

	/**
	 * Tranformation d'une date YYYYMMDD en timestamp.
	 * @param string $date date au format YYYYMMDD
	 * @return timestamp en fonction de la date
	 */
	public static function yyyymmddToTimestamp ($yyyymmdd){
		if (($yyyymmdd !== false) && (($yyyymmdd === null) || (strlen ($yyyymmdd = trim ($yyyymmdd)) === 0))){
			return null;
		}		
	    return mktime (0, 0, 0, substr($yyyymmdd, 4, 2), substr($yyyymmdd, 6, 2), substr($yyyymmdd, 0, 4));
	}

	/**
	 * Transformation d'un timestamp en une Date (format courant)
	 *
	 * @param int $timestamp le timestamp
	 * @param string $separator separator de date a passer au format
	 * @return date au format spécifié
	 */
	public static function timestampToDate($timestamp, $separator='/') {
		if ($timestamp === null){
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
		if ($timestamp === null){
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
	    if (($pParam !== false) && (($pParam === null) || (strlen ($pParam = trim ($pParam)) === 0))){
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
	 * @param string $separator Separateur (par défaut /)
	 * @return DateTime
	 */
	public static function yyyymmddhhiissToDateTime ($pParam, $separator='/') {
	    //On vérifie que la date donnée est remplie
	    if (($pParam !== false) && (($pParam === null) || (strlen ($pParam = trim ($pParam)) === 0))){
			return null;
		}
		
		if (strlen ($pParam) != 14){
			return false;
		}
		
		if ($date = self::yyyymmddToDate (substr ($pParam, 0, 8), $separator)){
			if ($time = self::hhiissToTime (substr ($pParam, 8, 6))){
				return $date.' '.$time;
			}
		}
	}
	
	/**
	 * Convertit yyyymmddhhiiss en texte
	 *
	 * @param string $pParam Date au format yyyymmddhhiiss
	 * @param string $separator Separateur (par défaut /)
	 * @return DateTime
	 */
	public static function yyyymmddhhiissToText ($pParam, $separator='/') {
	    //On vérifie que la date donnée est remplie
	    if (($pParam !== false) && (($pParam === null) || (strlen ($pParam = trim ($pParam)) === 0))){
			return null;
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
		if ($timestamp === null){
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
	    //On vérifie que la date donnée est remplie
		if (($pParam !== false) && (($pParam === null) || (strlen ($pParam = trim ($pParam)) === 0))){
			return null;
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
	    return mktime (substr($pParam, 8, 2),substr($pParam, 10, 2),substr($pParam, 12, 2), substr($pParam, 4, 2), substr($pParam, 6, 2), substr($pParam, 0, 4));
	}
	
	/**
	 * Converti une heure au format HHIISS en timestamp
	 *
	 * @param string $pParam	l'heure à convertir
	 * @return	string / false en cas d'erreur
	 */
	public static function hhiissToTimeStamp ($pParam){
	    //On vérifie que la date donnée est remplie
		if (($pParam !== false) && (($pParam === null) || (strlen ($pParam = trim ($pParam)) === 0))){
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

		//on retourne au jour 0	
	    return mktime (substr($pParam, 0, 2), substr($pParam, 2, 2), substr($pParam, 4, 2), 
			    0, 0, 0);
	}
	
	/**
	 * Convertit DateTime en yyyymmddhhiiss
	 * 
	 * @param string $DateTime le DateTime
	 * @param string $separator Separateur (par défaut /)
	 * @return yyyymmddhhiiss
	 */
	public static function DateTimeToyyyymmddhhiiss ($DateTime, $separator='/') {
		if ($DateTime === null){
			return null;
		}
	    $arMask = CopixI18N::getDateTimeMask ($separator);
	    $tmpArray = sscanf($DateTime,$arMask->mask);
	    $arDate=array();
	    foreach($tmpArray as $key=>$donnee) {
	        $arDate[$arMask->format[$key]]=$donnee;
	    }
        if (!isset($arDate['H'])) {
            $arDate['H']=(isset ($arDate['p']) && ($arDate['p'] =='am')) ? $arDate['h'] : $arDate['h']+12; 
        }
	    return sprintf("%04d",$arDate['y']).sprintf("%02d",$arDate['m']).sprintf("%02d",$arDate['d']).sprintf("%02d",$arDate['H']).sprintf("%02d",$arDate['i']).sprintf("%02d",$arDate['s']);
	}
	//TODO Tests unitaires
    //TODO Function datetime ISO8601

    /**
     * Permet de convertir Datetime ISO 8601 (YYYY-MM-DD hh:ii:ss ou YYYY-MM-DDThh:ii:ssZ) en DateTime local
     *  eg (dd/mm/yyyy) 
     * @param	string	$pIsoDateTime	la date au format ISO 8601 à convertir
     * @param	string	$pSeparator		le séparateur que l'on va utiliser pour générer la date finale.	  
     */
	public static function ISODateTimeToDateTime ($pIsoDateTime, $pSeparator='/') {
	    //On vérifie que la date donnée est remplie
	    if (($pIsoDateTime !== false) && (($pIsoDateTime === null) || (strlen ($pIsoDateTime = trim ($pIsoDateTime)) === 0))){
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
		if ($pYYYYMMDD === null){
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
		if ($pYYYYMMDDHHIISS === null){
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
		if ($pHHIISS === null){
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
	 * @param int $pMonth Mois à tester
	 * @param int $pDay Jour à tester
	 * @param int $pYear Année à tester
	 * @return boolean
	 */
	public static function checkDate ($pMonth, $pDay, $pYear) {
		if (!preg_match ('/^[0-9]{1,2}$/', $pMonth) || !preg_match ('/^[0-9]{1,2}$/', $pDay) || !preg_match ('/^[0-9]{1,4}$/', $pYear)) {
			return false;
		}
		return checkdate ($pMonth, $pDay, $pYear);
	}
}
?>