<?php
/**
* @package  	copix
* @subpackage	utils
* @author		Steevan BARBOYON
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Offre des possibilités pour débuguer
* @package copix
* @subpackage utils
*/
class CopixDebug {
	
	/**
	 * Affiche un var_dump
	 * 
	 * @param var $pVar Variable
	 * @param bool $pReturn False : affiche le résultat avec echo, true : renvoie le résultat sous forme de chaine
	 * @param bool $pFormatReturn Formater le résultat retourné, avec des couleurs et un affichage moins "lourd"
	 */
	public static function var_dump ($pVar, $pReturn = false, $pFormatReturn = true) {
		// récupération du var_dump		
		ob_start ();
		var_dump ($pVar);
		$content = ob_get_contents ();
		ob_end_clean ();
		
		// si on veut formater l'affichage
		if ($pFormatReturn) {
			// ["nom"]
			$content = preg_replace ('/\[\"([A-z]*)\"\]/', '<strong>$1</strong> ', $content);
			// ["nom:public"]
			$content = preg_replace ('/\[\"([A-z]*)[:]([A-z]*)\"\]/', '<strong>$1</strong> ($2) ', $content);
			// [0]
			$content = preg_replace ('/\[([0-9]*)\]/', '<strong><font color="green">[$1]</font></strong> ', $content);
			// supprime le retour à la ligne après =>
			$content = preg_replace ('/=>\n[ ]*/', '=> ', $content);
			// string(4) "test"
			$content = preg_replace ('/(string\([0-9]*\)) "(.*?)"/', '$1 "<font color="blue">$2</font>"', $content);
			// int(2)
			$content = preg_replace ('/int\(([0-9]*)\)/', 'int <font color="green">$1</font>', $content);
			// bool(true)
			$content = preg_replace ('/bool\(([true|false]*)\)/', 'bool <font color="green">$1</font>', $content);
			
			// pour remplacer les doubles espaces en guise de tabulation par 4 espaces, pour que ce soit plus lisible
			// si quelqu'un sait comment faire ça en expressions rationnelles ...
			$arContent = explode ("\n", $content);
			foreach ($arContent as $index => $line) {
				$spaces = '';
				while (substr ($arContent[$index], 0, 2) == '  ') {
					$spaces .= "    ";
					$arContent[$index] = substr ($arContent[$index], 2);
				}
				$arContent[$index] = $spaces . $arContent[$index];
			}
			$content = implode ("\n", $arContent);
		}
		
		// si on veut retourner le contenu
		if ($pReturn) {
			return $content;
			
		// si on veut afficher le contenu directement
		} else {
			echo '<div style="align: left; background-color: white"><pre>';
			echo $content;
			echo '</pre></div>';
		}
	}
	
	/**
	 * Affiche un print_r
	 * 
	 * @param var $pVar Variable
	 * @param bool $pReturn False : affiche le résultat avec echo, true : renvoie le résultat sous forme de chaine
	 * @param bool $pFormatReturn Formater le résultat retourné, avec des couleurs et un affichage moins "lourd"
	 */
	public static function print_r ($pVar, $pReturn = false, $pFormatReturn = true) {
		$content = print_r ($pVar, true);
		
		// si on veut formater l'affichage
		if ($pFormatReturn) {
			// [nom]
			$content = preg_replace ('/\[([A-z]*)\]/', '<strong>$1</strong>', $content);
			// [nom:public]
			$content = preg_replace ('/\[([A-z]*)[:]([A-z]*)\]/', '<strong>$1</strong> ($2) ', $content);
			// [0]
			$content = preg_replace ('/\[([0-9]*)\]/', '<strong><font color="green">[$1]</font></strong> ', $content);
			
			// recherche des lignes qui contiennent une valeur, pour la mettre en couleur
			$arContent = explode ("\n", $content);
			foreach ($arContent as $index => $line) {
				if (strpos ($line, ' => ') !== false && trim ($arContent[$index + 1]) != '(') {
					$arLine = explode (' => ', $line);
					$arContent[$index] = $arLine[0] . ' => <font color="blue">' . $arLine[1] . '</font>';
				}
			}
			$content = implode ("\n", $arContent);
		}
		
		// si on veut retourner le contenu
		if ($pReturn) {
			return $content;
			
		// si on veut afficher le contenu directement
		} else {
			echo '<div style="align: left; background-color: white"><pre>';
			echo $content;
			echo '</pre></div>';
		}
	}
	
	/**
	 * Affiche un debug_backtrace en formattant le retour
	 * 
	 * @param bool $pReturn False : affiche le résultat avec echo, true : renvoie le résultat sous forme de chaine
	 * @param bool $pFormatReturn Formater le résultat retourné dans un tableau HTML
	 */
	public static function debug_backtrace ($pReturn = false, $pFormatReturn = true) {
		$debug = debug_backtrace ();
		
		// si on veut retourner le contenu
		if ($pReturn) {
			return $debug;
			
		// si on veut afficher le contenu directement
		} else {
			
			// si on ne veut pas formater l'affichage
			if (!$pFormatReturn) {
				self::var_dump ($debug);
			} else {
				echo '<table class="CopixTable">';
				echo '<tr>';
				echo '<th>' . _i18n ('copix:copixdebug.th.class') . '</th>';
				echo '<th>' . _i18n ('copix:copixdebug.th.functions') . '</th>';
				echo '<th>' . _i18n ('copix:copixdebug.th.args') . '</th>';
				echo '</tr>';
				
				$alternate = '';
				foreach ($debug as $index => $infos) {				
					echo '<tr ' . $alternate . '>';
					echo '<td>';
					if (isset ($infos['class'])) {
						echo $infos['class'];
					}
					echo '</td>';
					echo '<td>';
					echo '<strong>' . $infos['function'] . '</strong>';
					echo '</td>';
					echo '<td>';
					self::var_dump ($infos['args']);
					echo '</td>';
					echo '</tr>';
					
					$alternate = ($alternate == '') ? 'class="alternate"' : '';
				}
				echo '</table><br />';
			}
		}
	}
}
?>