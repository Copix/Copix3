<?php
/**
 * Type de fichier de log par défaut
 */
class LogReaderTypeDefault {
	/**
	 * Retourne le nom du type
	 *
	 * @return string
	 */
	public static function getCaption () {
		return 'Défaut';
	}

	/**
	 * Parse une ligne de log
	 *
	 * @param int $pIndex Numéro de la ligne
	 * @param string $pLine Texte de la ligne
	 */
	public static function parse ($pIndex, $pLine) {
		return new LogReaderLine ($pIndex, $pLine);
	}
}