<?php
/**
 * @package		standard
 * @subpackage 	plugin_utf8
 * @author		Guillaume Perréal
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Plugin d'encodage de la sortie en UTF8
 * @package standard
 * @subpackage	plugin_utf8
 */
class PluginUTF8 extends CopixPlugin implements ICopixBeforeDisplayPlugin {
	public function getCaption () {
		return 'Encode l\'HTML en UTF-8 si ce n\'est pas fait';
	}

	public function getDescription () {
		return null;
	}
	
	/**
	 * Table de correspondances pour les caractères non LATIN-1 que l'on veut quand même traiter
	 * proprement.
	 *
	 * Les deux encodages très proches et donc souvent confondu avec le LATIN1 sont :
	 * - le LATIN-9, qui est le LATIN-1 plus quelques symboles dont l'Euro,
	 * - le CP1252, qui provient de Windows et que IE envoie en prétendant que c'est du LATIN-1 ...
	 *
	 * N.B.: à droite se trouve la representation en UTF8 prête à être décodée par _decode.
	 *
	 * @var array
	 */
	private $_charmap = array(
		// LATIN-9 => UTF8
		"\xA4" => '~e282ac~', // € (Symbole de l'Euro)
		"\xBC" => '~c592~', // Œ (OE liés)
		"\xBD" => '~c593~', // œ (oe liés)
		// CP-1252 => UTF8
		"\x80" => '~e282ac~', // € (Symbole de l'Euro)
		"\x8C" => '~c592~', // Œ (OE liés)
		"\x92" => '~e28099~', // ’ (apostrophe)
		"\x96" => '-', // – (tiret court)
		"\x97" => '--', // — (tiret long)
		"\x9C" => '~c593~', // œ (oe liés)
	);
	
	/**
	 * Encodage en UTF8
	 */
	public function beforeDisplay (& $display){
		
		// Si on a mb_string, on l'utilise pour court-circuiter le traitement si le contenu est déjà en UTF-8
		if(function_exists('mb_check_encoding') && mb_check_encoding($display, 'UTF-8')) {
			return;
		}
		 
		// En un seul appel:
		// 1) encode l'UTF-8 valide (et les '~') dans un format maison en ASCII,
		// 2) remplace certains caractères LATIN-9 et CP1252 (windows) en LATIN-1
		// 3) encode en UTF-8 tout ce qui reste,
		// 3) décode l'UTF-8 encodé en 1)
		$display = preg_replace_callback(
			'/~([0-9a-f]+)~/i',
			array($this, '_decode'),
			utf8_encode(
				strtr(
					preg_replace_callback(
						'/(?:~|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3})+/',
						array($this, '_encode'),
						$display
					),
					$this->_charmap
				)
			)
		);
	}
	
	/**
	 * Callback pour encoder l'UTF-8.
	 *
	 * Le chaîne est convertie en héxadécimal puis placée entre tildes (~).
	 * Exemple : "é" (UTF-8) => "~c3a9~" (ASCII).
	 *
	 * @param array $match Résultat de la regex. L'index 0 contient la chaîne à encoder.
	 * @return string La chaîne encodée.
	 */
	private function _encode($match) {
		return '~'.bin2hex($match[0]).'~';
	}
	
	/**
	 * Callback pour redonner à l'UTF-8 sa forme initiale.
	 *
	 * @param array $match Résultat de la regex. L'index 1 contient la chaîne à décoder.
	 * @return string Le chaîne originale.
	 */
	private function _decode($match) {
		return pack('H*', $match[1]);
	}
}