<?php 
/**
 * @package tools
 * @subpackage documentation
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */

/**
 * Génère un code pour le module wiki via les commentaires PHP d'une classe
 * @package tools
 * @subpackage documentation
 */
class ActionGroupClasstowiki extends CopixActionGroup {

	/**
	 * Demande de la classe dont on veut le code wiki
	 */
	public function processDefault () {
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('classtowiki.title.default');
		
		
		
		return _arPPO ($ppo, 'classtowiki.form.tpl');
	}

	/**
	 * Traduit les commentaires de la classe en code wiki
	 */
	public function processToWiki () {
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('classtowiki.title.toWiki');
		
		$hwnd = fopen ('E:\Sites internet\Copix3\utils\copix\core\CopixUrl.class.php', 'r');
		$lines = array ();
		$classFound = false;
		$functions = array ();
		$comFirstIndex = 0;
		while (!feof ($hwnd)) {
			$line = fgets ($hwnd);
			$lineIndex = count ($lines);
			$lines[] = $line;

			// recherche du début de la classe concernée
			if (!$classFound) {
				if (strpos ($line, 'class') !== false && stripos ($line, 'CopixUrl ') !== false) {
					$classFound = true;
				}
			// si on a déja trouvé la classe, on est dans ses méthodes
			} else {
				// si on est à la fin de la classe
				if ($line == '}') {
					break;
				}
				
				// si on a trouvé une ouverture de commentaire
				if (strpos ($line, '/**') !== false) {
					$comFirstIndex = $lineIndex;
				
				// si on a trouvé une méthode public
				} else if (strpos ($line, 'public') !== false && strpos ($line, 'function') !== false) {
					
					// recherche du nom de la fonction, avec ses paramètres
					$pos = strpos ($line, 'function') + strlen ('function');
					$posEnd = strpos ($line, '{');
					$functionIndex = count ($functions);
					$functions[$functionIndex]['name'] = trim (substr ($line, $pos, $posEnd - $pos));					
					//echo '[<font color="red">' . $funcName . '</font>]<br />';
					
					// recherche de la description
					$inDesc = true;
					$arDesc = array ();
					$arParams = array ();
					$arCode = array ();
					$return = '';
					for ($boucle = $comFirstIndex + 1; $boucle <= $lineIndex - 2; $boucle++) {
						$parsedLine = str_replace ("\t", ' ', $lines[$boucle]);
						$parsedLine = trim (substr (trim ($parsedLine), 2));
						 
						//echo '[$parsedLine] [' . $parsedLine . ']<br />';
						//echo '[_array_strpos=' . $this->_array_strpos (array ('@param', '@return', '@todo', '<code>'), $parsedLine) . ']<br />';
						// on regarde si on est encore dans la description
						$inDesc = ($inDesc && !$this->_array_strpos (array ('@param', '@return', '@todo', '<code>'), $parsedLine));
						
						// si on est dans la description
						if ($inDesc) {							
							$arDesc[] = $parsedLine;
							
						// si on a des infos sur un paramètre
						} else if (strpos ($parsedLine, '@param') !== false) {
							$paramIndex = count ($arParams);
							$paramLine = substr ($parsedLine, strpos ($parsedLine, '@param') + strlen ('@param') + 1);
							$paramType = trim (substr ($paramLine, 0, strpos ($paramLine, '$') - 1));
							$posParamName = strpos ($paramLine, '$');
							$posEndParamName = strpos ($paramLine, ' ', $posParamName);
							$paramName = trim (substr ($paramLine, $posParamName, $posEndParamName - $posParamName));
							$posParamDesc = $posEndParamName + 1;
							$paramDesc = substr ($paramLine, $posParamDesc);
							$arParams[$paramIndex]['type'] = $paramType;
							$arParams[$paramIndex]['name'] = $paramName;
							$arParams[$paramIndex]['desc'] = $paramDesc;
						
						// si on a trouvé un exemple de code
						} else if (strpos ($parsedLine, '<code>') !== false) {
							for ($boucle2 = $boucle + 1; $boucle2 <= $lineIndex - 2; $boucle2++) {
								if (strpos ($lines[$boucle2], '</code>') === false) {
									$arCode[] = trim (substr (trim ($lines[$boucle2]), 2));
								} else {
									break;
								}
							}
						}
					}
					$functions[$functionIndex]['arDesc'] = $arDesc;
					$functions[$functionIndex]['arParams'] = $arParams;					
					$functions[$functionIndex]['arCode'] = $arCode;
					
					//$desc = implode ("\n", $arDesc);
					//echo '[<b>' . $desc . '</b>]<br /><div align="left"><pre>';
					//print_r ($arCode);
					//echo '<br /><br />';
				}
			}
		}
		fclose ($hwnd);
		
		$wiki  = '===== Méthodes de CopixUrl =====' . "\n";
		foreach ($functions as $funcInfos) {
			$wiki .= '==== ' . $funcInfos['name'] . ' ====' . "\n";
			$wiki .= implode ("\n", $funcInfos['arDesc']) . "\n\n";
			foreach ($funcInfos['arParams'] as $param) {
				//$wiki .= '<paramName>' . $param['name'] . '</paramName> (<paramType>' . $param['type'] . '</paramType>) : ' . $param['desc'] . "\n";
				$wiki .= $param['name'] . ' (' . $param['type'] . ') : ' . $param['desc'] . "\n\n";
			}
			if (count ($funcInfos['arCode']) > 0) {
				$wiki .= '<code php>' . "\n" . implode ("\n", $funcInfos['arCode']) . "\n" . '</code>' . "\n\n";
			}
		}
		
		$ppo->wiki = htmlentities (utf8_decode ($wiki));
		return _arPPO ($ppo, 'classtowiki.result.tpl');
	}
	
	/**
	 * Vérifie si $pStr existe dans au moins une des valeurs de $pArray
	 */
	private function _array_strpos ($pArray, $pStr) {
		foreach ($pArray as $value) {
			if (strpos ($pStr, $value) !== false) {				
				return true;
			}
		}
		return false;
	}

}
 ?>