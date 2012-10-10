<?php
/**
 * Type de fichier de log erreurs PHP
 */
class LogReaderTypePHP {
	/**
	 * Retourne le nom du type
	 *
	 * @return string
	 */
	public static function getCaption () {
		return 'Erreurs PHP';
	}

	/**
	 * Parse une ligne de log
	 *
	 * @param int $pIndex Numéro de la ligne
	 * @param string $pLine Texte de la ligne
	 */
	public static function parse ($pIndex, $pLine) {
		// recherche de la date
		$posEndDate = strpos ($pLine, ']');
		$fullDate = substr ($pLine, 1, $posEndDate - 1);
		list ($date, $hours) = explode (' ', $fullDate);
		list ($day, $month, $year) = explode ('-', $date);
		switch (strtolower ($month)) {
			case 'jan' : $month = 1; break;
			case 'feb' : $month = 2; break;
			case 'mar' : $month = 3; break;
			case 'apr' : $month = 4; break;
			case 'may' : $month = 5; break;
			case 'jun' : $month = 6; break;
			case 'jul' : $month = 7; break;
			case 'aug' : $month = 8; break;
			case 'sep' : $month = 9; break;
			case 'oct' : $month = 10; break;
			case 'nov' : $month = 11; break;
			case 'dec' : $month = 12; break;
		}
		list ($hour, $min, $sec) = explode (':', $hours);
		$date = mktime ($hour, $min, $sec, $month, $day, $year);

		// recherche du type
		$posEndType = strpos ($pLine, ':', $posEndDate + 5);
		$type = substr ($pLine, $posEndDate + 5, $posEndType - ($posEndDate + 5));

		// recherche du texte court
		$shortText = substr ($pLine, $posEndType + 3);

		return new LogReaderLine ($pIndex, $pLine, $shortText, $date, $type);
	}
}