<?php
/**
 * @package		copix
 * @subpackage	filter
 * @author		Champenois Goulven
 * @copyright	CopixTeam
 * @link 		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Supprime les caractères accentués d'une chaîne de caractères
 * 
 * @package		copix
 * @subpackage	filter
 */
class CopixFilterRemoveAccents extends CopixAbstractFilter {
	/**
	 * Remplace les caractères accentués d'une chaîne de caractères par le caractère non accentué
	 */
	public function get ($pValue){
		// Vérification du paramètre charset
		if ( $this->getParam ('charset')) {
			// Liste des encodages acceptés par htmlentities et html_entity_decode (selon la doc PHP)
			$charsets = array (
				// Europe occidentale
				'ISO-8859-1', 'ISO-8859-15', 'ISO8859-1', 'ISO8859-15',
				// Universel
				'UTF-8',
				// Cyrillique DOS
				'cp866', 'ibm866', '866',
				// Cyrillique Windows
				'cp1251', 'Windows-1251', 'win-1251', '1251',
				// Russe
				'KOI-8R', 'koi8-ru', 'koi8r',
				// Chinois
				'BIG5', '950', 'GB2312', '936', 'BIG5-HKSCS',
				// Japonais
				'Shift_JIS', 'SJIS', '932', 'EUC-JP', 'EUCJP'
			);
			$charset = $this->getParam ('charset');
			if( !in_array( $charset, $charsets ) ){
				throw new CopixException("L'encodage '$charset' doit faire partie de la liste [".implode (', ', $charsets)."]. (Chaîne passée : ".$str.")");
			}
		} else {
			$charset = 'UTF-8';
		}
		// 1 : convertit les accents en entités HTML
		$pValue = htmlentities($pValue, ENT_NOQUOTES, $charset);
		// 2 : prend la première lettre de l'entité (en utilisant le fait que les entités sont de forme &-lettre-accent-;)
		$pValue = preg_replace('/&(.)(acute|caron|cedil|circ|grave|ring|slash|tilde|uml);/', '$1', $pValue);
		// 3 : reconvertit les entités qui n'ont pas été remplacées
		$pValue = html_entity_decode($pValue, ENT_NOQUOTES, $charset);
		return $pValue;
	}
}