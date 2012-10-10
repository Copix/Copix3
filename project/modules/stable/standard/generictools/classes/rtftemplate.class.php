<?php
/**
* @package		standard 
 * @subpackage	generictools
* @author	Croes Gérald
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Classe permettant de générer un RTF à partir d'un "template RTF"
* @package		standard 
 * @subpackage	generictools
*/
class RTFTemplate {
	/**
	* Les données actuellements assignées
	*/
	var $_data = array ();

	/**
	* Assignation d'une donnée
	* @param string $pName nom de la variable telle qu'elle figure dans le document RTF
	* @param string $pValue le contenu de la variable
	* @param boolean $pIsHtml si la variable est de type HTML ou non
	*/
	function assign ($pName, $pValue, $pIsHTML = false){
       $this->_data[$pName] = $pIsHTML ? $this->_html2rtf ($this->_stripBraces ($pValue)) : $this->_stripBraces ($pValue);
	}
	
	/**
	* Récupère dans une chaine de caractère le contenu du RTF
	* @param $pRTFTemplateFileName Le fichier RTF modèle. 
	*    Si le sélecteur donné est de type module, on considère que le fichier rtf est dans "templates"
	* @return string le fichier RTF transformé
	*/
	function fetch ($pRTFTemplateFileName){
		$selector = CopixSelectorFactory::create ($pRTFTemplateFileName);
		if (($content = file_get_contents ($selector->getPath ($selector->type == 'module' ? COPIX_TEMPLATES_DIR : '').$selector->fileName)) !== false){
  		    preg_match_all('{\$(\w+)}', $content, $out);
		    foreach ($out[1] as $nomVariable){
		       $content = str_replace ('\{$'.$nomVariable.'\}', (isset ($this->_data[$nomVariable]) ? $this->_data[$nomVariable] : ''), $content);			    	
		    }
            return $content;
		}else{
			return false;
		}
	}

	/**
	* Converti un morceau de code HTML en RTF
	* @param string $pHtml
	*/
	function _html2rtf ( $pHTML ) {
		$startpos = strpos($pHTML,'<');
		$rtf = '';
		while ( $startpos !== false ) {
			// get tag
			$endpos = strpos($pHTML,'>',$startpos);
			if ($endpos !== false) {
				$tag  = substr ($pHTML, $startpos+1, $endpos - $startpos - 1);
				$rtf .= substr ($pHTML, 0, $startpos);
				switch (strtoupper ($tag)) {
					case 'P':
					break;

					case '/P':
					$rtf .= ' \par ';
					break;
					
					case 'BR':
                    case 'BR/':
                    case 'BR /':
                    case 'H1':
                    case 'H2':
                    case 'H3':
                    case 'H4':
                    case '/H1':
                    case '/H2':
                    case '/H3':
                    case '/H4':
					$rtf .= ' \line ';
					break;
					
					case 'B':
					$rtf .= '{\b ';
					break;
					
					case '/B':
					$rtf .= '}';
					break;
					
					case 'STRONG':
					$rtf .= '{\b ';
					break;
					
					case '/STRONG':
					$rtf .= '} ';
					break;
					
					case 'I':
					$rtf .= '{\i ';
					break;
					
					case '/I':
					$rtf .= '}';
					break;
					
					case 'EM':
					$rtf .= '{\i ';
					break;
					
					case '/EM':
					$rtf .= '}';
					break;
					
					case 'U':
					$rtf .= '{\ul ';
					break;
					
					case '/U':
					$rtf .= '}';
					break;
					
					case 'OL':
					$listnumber = 1;
					$listtype = 'number';
					break;
					
					case '/OL':
					break;
					
					case 'UL':
					$listtype = 'bullet';
 					break;
					
					case '/UL':
					break;
					
					case 'LI':
					switch ($listtype) {
						case 'number':
						$bullet = "$listnumber.";
						$listnumber++;
						break;
						default:
						$bullet = '\bullet ';
						break;
					}
					$rtf .= ' \fi-400\li400{'. $bullet .'\tab ';
					break;
					
					case '/LI':
					$rtf .= '}\par ';
					break;
					
					case '/TD':
					$rtf .= ' ';
					break;
					
					case '/TR':
					$rtf .= ' \par ';
					break;
					
					case 'HR': 
					$rtf .= '\page ';
					break;
				}
				$pHTML    = substr($pHTML, $endpos + 1);
				$startpos = strpos($pHTML, '<');
			} else {
				$startpos = false;
			}
		}

		// append remaining text
		$rtf .= $pHTML;
		return $rtf;

	}

	/**
	* Remplace les accolades spéciales RTF
	* @param string $pToReplace le texte à remplacer
	* @return string 
	*/
	function _stripBraces ($pToReplace){
		return str_replace (array ('{', '}'), array ('\\{', '\\}'), $pToReplace);
	}
}
?>