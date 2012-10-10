<?php
/**
* @package   copix
* @subpackage utils
* @author   Gérald Croës
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license   http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
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
    public static function firstDayOfWeek ($separator = '-') {
       return self::timestampToDate(strtotime('last Monday'), $separator);
    }
    
    /**
     * Renvoie la date du dernier jour de la semaine.
     * @param string $separator le séparateur utilisé dans $date pour séparer les éléments entre eux.
     * @return string date au format DD-MM-YYYY. Le dernier jour de la semaine
     */
    public static function lastDayOfWeek ($separator = '-') {
       return self::timestampToDate(strtotime('next Sunday'), $separator);
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
			$tmp[$key] = intval ($value);
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

		if (! @checkdate ($tmp[$positions['m']], $tmp[$positions['d']], $tmp[$positions['Y']])){
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
    * Transforme une chaine hhmmss dans une chaine représentant une heure en fonction de la langue donnée (HH:MM:SS en français)
    * @param string $hhiiss l'heure
    * @return l'heure formattée, null si aucune heure donnée, false si l'heure est incorrecte
    */
	public static function hhmmssToTime ($hhmmss, $separator=':'){
	    if (($hhmmss !== false) && (($hhmmss === null) || (strlen ($hhmmss = trim ($hhmmss)) === 0))){
	        return null;
	    }

	    $arTime=array();
	    switch (strlen($hhmmss)) {
	        case 6:
	            $arTime[2]=substr($hhmmss, 4, 2);
	            if ($arTime[2] > 59) return false;

	        case 4:
	            $arTime[1]=substr($hhmmss, 2, 2);
	            if ($arTime[1] > 59) return false;
	            	
	        case 2:
	            $arTime[0]=substr($hhmmss, 0, 2);
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
	public static function timeToHHMMSS ($time, $separator=':'){
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
	    return mktime (0, 0, 0, substr($yyyymmdd, 4, 2), substr($yyyymmdd, 6, 2), substr($yyyymmdd, 0, 4));
	}

	/**
	 * Transformation d'un timestamp en une Date (format courant)
	 *
	 * @param int $timestamp le timestamp
	 * @param string $separator separator de date a passer au format
	 * @return date au format spécifier
	 */
	public static function timestampToDate($timestamp,$separator='/') {
	    return date(CopixI18N::getDateFormat ($separator),$timestamp);
	}

	/**
	 * Transformation d'un timestamp en date yyyymmdd
	 * @param int $timestamp le timestamp
	 * @return string Date au format yyyymmdd
	 */
	public static function timestampToyyyymmdd($timestamp) {
	    return strftime('%Y%m%d',$timestamp);
	}
	
	/**
	 * Transformation d'une date en timestamp
	 * @param date $date Une date
	 * @param string $separator Separateur
	 * @return int Un timestamp
	 */
	public static function dateTotimestamp($date,$separator='/') {
	    $yyyymmdd = self::dateToYYYYMMDD ($date,$separator);
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
			if ($time = self::hhmmssToTime (substr ($pParam, 8, 6))){
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
			if ($time = self::hhmmssToTime (substr ($pParam, 8, 6))){
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
	public static function timeStampToyyyymmddhhiiss($timestamp) {
        return strftime ('%Y%m%d%H%M%S', $timestamp);
    }
	
    /**
     * Convertit yyyymmddhhiiss en timestamp
     *
     * @param string $pParam yyyymmddhhiiss
     * @return timestamp
     */
    public static function yyyymmddhhiissToTimeStamp($pParam) {
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
	 * Convertit DateTime en yyyymmddhhiiss
	 * 
	 * @param string $DateTime le DateTime
	 * @param string $separator Separateur (par défaut /)
	 * @return yyyymmddhhiiss
	 */
	public static function DateTimeToyyyymmddhhiiss($DateTime,$separator='/') {
	    $arMask = CopixI18N::getDateTimeMask($separator);
	    $tmpArray = sscanf($DateTime,$arMask->mask);
	    $arDate=array();
	    foreach($tmpArray as $key=>$donnee) {
	        $arDate[$arMask->format[$key]]=$donnee;
	    }
        if (!isset($arDate['H'])) {
            $arDate['H']=($arDate['p']=='am') ? $arDate['h'] : $arDate['h']+12; 
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
}
?>