<?php
/**
 * @package  	copix
 * @subpackage	utils
 * @author		Steevan BARBOYON, Guillaume Perréal
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Offre des possibilités pour débuguer
 * 
 * @package		copix
 * @subpackage	utils
 */
class CopixDebug {
	
	/**
	 * Constantes pour le formatage de _getFormatted
	 *
	 * @var integer
	 */
	const FR_SECTION_PUBLIC = 0;
	const FR_SECTION_PRIVATE = 1;
	const FR_SECTION_PROTECTED = 2;
	const FR_FILENAME = 3;
	const FR_KEYWORD = 4;
	const FR_COMMENT = 5;
	const FR_TYPE = 6;
	const FR_CONST = 7;
	const FR_VAR = 8;
	const FR_STRING = 9;
	const FR_VARNAME = 10;
	const FR_VALUE = 11;
	const FR_FUNCTIONNAME = 12;
	const FR_INT = 13;
	
	/**
	 * Instance de CopixDebug
	 * 
	 * @var CopixDebug
	 */
	static private $_instance = null;
	
	/**
	 * Indique si la méthode appelée voulait formatter le retour
	 *
	 * @var boolean
	 */
	static private $_formatReturn = true;
	
	/**
	 * Indique si la méthode appelée voulait un retour ou un affichage
	 *
	 * @var boolean
	 */
	static private $_return = false;
	
	/**
	 * Indique si c'est le 1er dump appelé
	 *
	 * @var boolean
	 */
	static private $_isFirstDump = true;
	
	/**
	 * Nombre d'espaces pour la declaration en cours
	 *
	 * @var int
	 */
	static private $_declarationSpaces = 0;
	
	/**
	 * Nombre d'espaces pour les dumps en cours
	 *
	 * @var int
	 */
	static private $_dumpSpaces = 0;
	
	/**
	 * Nombre d'espaces pour les sections en cours
	 *
	 * @var int
	 */
	static private $_sectionSpaces = 0;
	
	/**
	 * Retourne le singleton
	 *
	 * @return CopixDebug
	 */
	static public function instance () {
		if (is_null (self::$_instance)) {
			self::$_instance = new CopixDebug ();
		}
		return self::$_instance;
	}
	
	/**
	 * Indique qu'on va commencer un nouveau dump
	 *
	 */
	static private function _newDump () {
		if (!self::$_isFirstDump) {
			self::$_declarationSpaces += 4;
			self::$_sectionSpaces += 4;			
		}
		self::$_dumpSpaces += 4;
		self::$_isFirstDump = false;
	}
	
	/**
	 * Retourne les espaces à afficher en fonction du formattage du retour demandé, et des espaces courants pour le dump
	 *
	 * @return string
	 */
	static private function _getSpaces ($pNbrSpaces) {
		$str = (self::$_formatReturn) ? '&nbsp;' : ' ';
		$toReturn = '';
		for ($boucle = 0; $boucle < $pNbrSpaces; $boucle++) {
			$toReturn .= $str;
		}
		return $toReturn;
	}
	
	/**
	 * Retourne une chaine contenant le retour à la ligne, en fonction du formattage du retour demandé
	 *
	 * @return string
	 */
	static private function _getEndLine () {
		return (self::$_formatReturn) ? '<br />' : "\n";
	}
	
	/**
	 * Parse des lignes de commentaire type PHPDoc. Peut être un retour de Reflection->getDocComment ()
	 *
	 * @param string $pDocComment
	 * @return array
	 */
	static public function parseDocComment ($pDocComment) {
		$arComments = explode ("\n", $pDocComment);
		$toReturn = array ();
		foreach ($arComments as $comment) {
			
			// commentaire d'ouverture du block
			if (strpos ($comment, '/**') !== false) {
				continue;
			
			// commentaires "au milieu" du block
			} else if (strpos ($comment, '*') !== false) {
				$commentTrimed = trim (substr ($comment, strpos ($comment, '*') + 1));
				$commentTrimed = str_replace ("\t", ' ', $commentTrimed);
				$commentTrimed = str_replace ('  ', ' ', $commentTrimed);
				
				// commentaire de type : @param type $pVarName Mon explication sur la variable
				if (substr ($commentTrimed, 0, 6) == '@param') {
					$posVar = strpos ($commentTrimed, ' ', 7) + 1;
					
					// si on a bien le nom de la variable après le type
					if ($posVar > 1) {
						
						// si on a une explication sur le paramètre
						if (strpos ($commentTrimed, ' ', $posVar) !== false) {
							$posText = strpos ($commentTrimed, ' ', $posVar) + 1;
							$text = substr ($commentTrimed, $posText);
						// si on n'a pas d'explication sur le paramètre
						} else {
							$posText = null;
							$text = null;
						}
						$posEndVar = (!is_null ($posText)) ? $posText - $posVar : strlen ($commentTrimed);
						$var = trim (substr ($commentTrimed, $posVar, $posEndVar));
						
						$toReturn['param'][$var]['type'] = trim (substr ($commentTrimed, 7, $posVar - 8));
						$toReturn['param'][$var]['text'] = $text;
					}
				
				// commentaire de type : @return type Mon explication sur le retour
				} if (substr ($commentTrimed, 0, 7) == '@return') {
					$posType = strpos ($commentTrimed, ' ') + 1;
					// si on a bien le type après @return
					if ($posType > 1) {
						$posText = strpos ($commentTrimed, ' ', $posType) + 1;
						// si on a un text après le type
						if ($posText > 1) {
							$toReturn['return']['type'] = substr ($commentTrimed, $posType, $posText - $posType);
							$toReturn['return']['text'] = substr ($commentTrimed, $posText);
						} else {
							$toReturn['return']['type'] = substr ($commentTrimed, $posType);
						}
					}
				
				// tout autre commentaire qui ne commence pas par @
				} else if (strlen ($commentTrimed) > 1 && substr ($commentTrimed, 0, 1) != '@') {
					$toReturn['text'][] = $commentTrimed;
				}
			}
		}
		
		return $toReturn;
	}
	
	/**
	 * Commence une section, avec la possibilité de l'afficher / cacher
	 *
	 * @param string $pTitle
	 * @param int $pType
	 * @param boolean $pDefaultOpened
	 * @return string
	 */
	static private function _beginSection ($pTitle, $pType, $pDefaultOpened = true) {
		if (self::$_formatReturn) {
			$imgUp = _resource ('themes/default/img/tools/moveup.png');
			$imgDown = _resource ('themes/default/img/tools/movedown.png');
			$img = ($pDefaultOpened) ? $imgUp : $imgDown;
			$sectionId = uniqid ();
			$divDisplay = ($pDefaultOpened) ? "''" : "none";
			
			$toReturn = '<span style="cursor: pointer" onclick="';
			$toReturn .= 'var copixdebug_div = document.getElementById (\'div_' . $sectionId . '\');';
			$toReturn .= 'var copixdebug_img = document.getElementById (\'img_' . $sectionId . '\');';
			$toReturn .= 'if (copixdebug_div.style.display == \'\') {';
			$toReturn .= 'copixdebug_div.style.display = \'none\';';
			$toReturn .= 'copixdebug_img.src = \'' . $imgDown . '\';';
			$toReturn .= '} else {';
			$toReturn .= 'copixdebug_div.style.display = \'\';';
			$toReturn .= 'copixdebug_img.src = \'' . $imgUp . '\';';
			$toReturn .= '}';
			$toReturn .= '"><img id="img_' . $sectionId . '" src="' . $img . '" /> ';
			$toReturn .= self::_getFormatted ($pTitle, $pType);
			$toReturn .= '</span><div style="display:' . $divDisplay . '" id="div_' . $sectionId . '">';
		} else {
			$toReturn = $pTitle;
		}
		
		return $toReturn;
	}
	
	/**
	 * Termine la section en cours
	 * 
	 * @return string
	 */
	static private function _endSection () {
		return (self::$_formatReturn) ? '</div>' : null;
	}
	
	/**
	 * Retourne une chaine formatée entre $pBegin et $pEnd, si self::$_formatReturn est à true
	 *
	 * @param string $pStr Chaine à formatter ou non
	 * @param int $pType Type de formattage à effectuer, utiliser les constantes self::FR_
	 * @return string
	 */
	static private function _getFormatted ($pStr, $pType) {
		if (!self::$_formatReturn) {
			return $pStr;
		}
		
		switch ($pType) {
			case self::FR_SECTION_PUBLIC : return '<b><font color="green">' . $pStr . '</font></b>'; break;
			case self::FR_SECTION_PRIVATE : return '<b><font color="red">' . $pStr . '</font></b>'; break;
			case self::FR_SECTION_PROTECTED : return '<b><font color="red">' . $pStr . '</font></b>'; break;
			case self::FR_FILENAME : return '<i>' . $pStr . '</i>'; break;
			case self::FR_KEYWORD : return  '<i>' . $pStr . '</i>'; break;
			case self::FR_COMMENT : return '<font color="#808080">' . $pStr . '</font>'; break;
			case self::FR_TYPE : return '<i>' . $pStr . '</i>'; break;
			case self::FR_CONST : return '<b>' . $pStr . '</b>'; break;
			case self::FR_VAR : return '<font color="#663300">' . $pStr . '</font>'; break;
			case self::FR_INT : return '<font color="green">' . $pStr . '</font>'; break;
			case self::FR_STRING : return '<font color="blue">\'' . $pStr . '\'</font>'; break;
			case self::FR_VARNAME : return '<b>' . $pStr . '</b>'; break;
			case self::FR_VALUE : return '<font color="green">' . $pStr . '</font>'; break;
			case self::FR_FUNCTIONNAME : return '<b>' . $pStr . '</b>'; break;
		}
	}
	
	/**
	 * Effectue un dump sur un array
	 *
	 * @param object $pObject Objet à dumper
	 * @param boolean $pReturn true : renvoie le résultat sous forme de chaine, false : affiche le résultat avec echo
	 * @param boolean $pFormatReturn Formater le résultat retourné, ou retourner un texte brut
	 * @return string
	 */
	static private function _arrayDump ($pObject, $pReturn = false, $pFormatReturn = true) {
		self::_newDump ();
		$toReturn = array ();
		
		foreach ($pObject as $key => $value) {			
			switch (gettype ($key)) {
				case 'string' :
					$toReturnStr = self::_getFormatted ($key, self::FR_STRING);
					break;
				case 'int' :
				case 'integer' :
					$toReturnStr = self::_getFormatted ($key, self::FR_INT);
					break;
				default :
					$toReturnStr = self::_getFormatted ($key, self::FR_VALUE);
					break;
			}
			
			$toReturnStr .= ' => ';
			
			switch (gettype ($value)) {
				case 'string' :
					$toReturnStr .= self::_getFormatted ($value, self::FR_STRING);
					break;
				case 'int' :
				case 'integer' :
					$toReturnStr .= self::_getFormatted ($value, self::FR_INT);
					break;
				default :
					$toReturnStr .= self::_getFormatted ($value, self::FR_VALUE);
					break;
			}
			
			$toReturn[] = $toReturnStr;
		}
		
		if ($pReturn) {
			return implode (self::_getEndLine (), $toReturn);
		} else {
			self::_outputFormatted (implode (self::_getEndLine (), $toReturn));
		}
	}
	
	/**
	 * Effectue un dump sur un objet
	 *
	 * @param object $pObject Objet à dumper
	 * @param boolean $pReturn true : renvoie le résultat sous forme de chaine, false : affiche le résultat avec echo
	 * @param boolean $pFormatReturn Formater le résultat retourné, ou retourner un texte brut
	 * @return string
	 */
	static private function _objectDump ($pObject) {
		self::_newDump ();
				
		$sectionSpaces = self::_getSpaces (self::$_sectionSpaces);
		$dumpSpaces = self::_getSpaces (self::$_dumpSpaces);
		$toReturn = array ();
		$class = get_class ($pObject);
		$reflection = new ReflectionClass ($class);
		$parent = $reflection->getParentClass ();
		$endLine = self::_getEndLine ();
		//$endLine = "\n";
		
		// ------------------------------------------------------------------
		// recherche du nom de la classe, de l'extends et du fichier déclarant la classe
		// ------------------------------------------------------------------
		
		$file = $reflection->getFileName ();
		$arFileDirs = explode ('/', $file);
		$countFileDirs = count ($arFileDirs);
		if ($countFileDirs > 4) {
			 $fileDir =
			 	'(...)/' .
			 	$arFileDirs[$countFileDirs - 4] . '/' .
			 	$arFileDirs[$countFileDirs - 3] . '/' .
			 	$arFileDirs[$countFileDirs - 2] . '/' .
			 	$arFileDirs[$countFileDirs - 1];
		} else {
			$fileDir = $file;
		}
		$declaration = ($reflection->isFinal ()) ? self::_getFormatted ('final', self::FR_KEYWORD) . ' ' : null;
		$declaration .= ($reflection->isAbstract ()) ? self::_getFormatted ('abstract', self::FR_KEYWORD) . ' ' : null;
		$declaration .= ($reflection->isInterface ()) ? self::_getFormatted ('interface', self::FR_KEYWORD) . ' ' : self::_getFormatted ('object', self::FR_KEYWORD) . ' ';
		$declaration .= self::_getFormatted ($class, self::FR_VARNAME);
		if ($parent) {
			$declaration .= ' ' . self::_getFormatted ('extends', self::FR_KEYWORD) . ' ' . self::_getFormatted ($parent->name, self::FR_VARNAME);
		}
		$toReturn[0] .= $declaration . ' (' . self::_getFormatted ($fileDir, self::FR_FILENAME) . ')';
		
		// ------------------------------------------------------------------
		// recherche des constantes
		// ------------------------------------------------------------------
		
		$constantes = $reflection->getConstants ();
		$arConstSort = array ();
		foreach ($constantes as $constName => $constValue) {
			$arConstSort[] = $constName;
		}
		sort ($arConstSort);
		
		if (count ($arConstSort) > 0) {
			$toReturn[] = $sectionSpaces . self::_beginSection ('Constantes', self::FR_SECTION_PUBLIC);
			foreach ($arConstSort as $constName) {
				$constValue = $reflection->getConstant ($constName);
				$constType = gettype ($constValue);
				
				if ($constType == 'boolean') {
					$constValue = ($constValue) ? 'true' : 'false';
				}
				$constValueStr = self::_getFormatted ($constValue, self::FR_VALUE);				
				
				if ($constType == 'string') {
					$constTypeStr = self::_getFormatted ($constType . '(' . strlen ($constValue) . ')', self::FR_TYPE);
					$constValueStr = self::_getFormatted ($constValue, self::FR_STRING);
				} else {
					$constTypeStr = self::_getFormatted ($constType, self::FR_TYPE);
				}
				
				$constNameStr = self::_getFormatted ($constName, self::FR_VAR);
				
				$toReturn[] = $dumpSpaces . $constTypeStr . ' ' . $constNameStr . ' = ' . $constValueStr;
			}
			$toReturn[count ($toReturn) - 1] .= self::_endSection ();
		}
		
		// ------------------------------------------------------------------
		// recherche des propriétés
		// ------------------------------------------------------------------
		
		$properties = $reflection->getProperties ();
		$arPropPrivateSort = array ();
		$arPropProtectedSort = array ();
		$arPropPublicSort = array ();
		foreach ($properties as $propIndex => $propReflec) {
			if ($propReflec->isPrivate ()) {
				$arPropPrivateSort[] = $propReflec->name;
			} else if ($propReflec->isProtected ()) {
				$arPropProtectedSort[] = $propReflec->name;
			} else {
				$arPropPublicSort[] = $propReflec->name;
			}
		}
		sort ($arPropPrivateSort);
		sort ($arPropProtectedSort);
		sort ($arPropPublicSort);		
		
		if (count ($arPropPublicSort)) {
			$toReturn[] = $sectionSpaces . self::_beginSection ('Propriétés publiques', self::FR_SECTION_PUBLIC);
			foreach ($arPropPublicSort as $propName) {
				$propNameStr = self::_getFormatted ('$' . $propName, self::FR_VAR);
				$isStatic = (!isset ($pObject->$propName));
				$propValue = ($isStatic) ? $reflection->getStaticPropertyValue ($propName) : $pObject->$propName;
				$propType = gettype ($propValue);
				
				if ($propType == 'string') {
					$propTypeStr = self::_getFormatted ('string(' . strlen ($propValue) . ')', self::FR_TYPE);
					$propValueStr = self::_getFormatted ($propValue, self::FR_STRING);
				} else if ($propType == 'array') {
					self::_arrayDump ($propValue);
				} else {
					$propTypeStr = self::_getFormatted ($propType, self::FR_TYPE);
					$propValueStr = self::_getFormatted ($propValue, self::FR_VALUE);
				}
				$accessStr = ($isStatic) ? self::_getFormatted ('static', self::FR_KEYWORD) . ' ' : null;
				
				$toReturn[] = $dumpSpaces . $propTypeStr . ' ' . $accessStr . $propNameStr . ' = ' . $propValueStr;
			}
			$toReturn[] = self::_endSection ();
		}
		if (count ($arPropProtectedSort)) {
			$toReturn[] = $sectionSpaces . self::_beginSection ('Propriétés protégées', self::FR_SECTION_PROTECTED, false);
			foreach ($arPropProtectedSort as $propName) {
				$toReturn[] = $dumpSpaces . self::_getFormatted ('$' . $propName, self::FR_VAR);
			}
			$toReturn[] = self::_endSection ();
		}
		if (count ($arPropPrivateSort)) {
			$toReturn[] = $sectionSpaces . self::_beginSection ('Propriétés privées', self::FR_SECTION_PRIVATE, false);
			foreach ($arPropPrivateSort as $propName) {
				$toReturn[] = $dumpSpaces . self::_getFormatted ('$' . $propName, self::FR_VAR);
			}
			$toReturn[] = self::_endSection ();
		}
		
		// ------------------------------------------------------------------
		// recherche des méthodes
		// ------------------------------------------------------------------
		
		$methods = $reflection->getMethods ();
		$arMethodPrivateSort = array ();
		$arMethodProtectedSort = array ();
		$arMethodPublicSort = array ();
		$arMethodInfos = array ();
		foreach ($methods as $methodIndex => $reflectMethod) {
			$arMethodInfos[$reflectMethod->name]['comment'] = self::parseDocComment ($reflectMethod->getDocComment ());
			$methodComment = null;
			$methodParamsType = array ();
			
			// recherche de l'accès à la méthode (private, protected, public)
			if ($reflectMethod->isPrivate ()) {
				$arMethod = &$arMethodPrivateSort;
			} else if ($reflectMethod->isProtected ()) {
				$arMethod = &$arMethodProtectedSort;
			} else {
				$arMethod = &$arMethodPublicSort;
			}
			
			// recherche d'informations dans les commentaires de type PHPDoc
			if (strlen ($comments) > 0) {
				$arComments = explode ("\n", $comments);
				foreach ($arComments as $commentIndex => $comment) {
					if ($commentIndex == 1) {
						$methodComment = trim (substr ($comment, strpos ($comment, '*') + 1));
					} else {
						$posParam = strpos ($comment, '@param');
						if ($posParam !== false) {
							$str = trim (substr ($comment, $posParam + 6));
							$arParamStr = explode (' ', $str);
							if (count ($arParamStr) >= 2) {
								$methodParamsType[substr ($arParamStr[1], 1)] = $arParamStr[0];
							}
						}
					}
				}
			}
			
			// recherche des paramètres d'appels de la méthode
			$parameters = $reflectMethod->getParameters ();
			$nbrRequiredParams = $reflectMethod->getNumberOfRequiredParameters ();
			$requiredParams = array_slice ($methodParams, 0, $nbrRequiredParams);
			$optionalParams = array_slice ($methodParams, $nbrRequiredParams);
			
			$arMethodInfos[$reflectMethod->name]['call'] = self::_getFormatted ($reflectMethod->name, self::FR_FUNCTIONNAME) . ' (';
			$isFirst = true;
			$endStrCall = null;
			for ($boucle = 0; $boucle < $reflectMethod->getNumberOfParameters (); $boucle++) {
				$name = $parameters[$boucle]->name;
				$nameStr = self::_getFormatted ('$' . $name, self::FR_VAR);
				$type = (isset ($methodParamsType[$name])) ? $methodParamsType[$name] : null;
				$typeStr = (!is_null ($type)) ? self::_getFormatted ($type, self::FR_TYPE) . ' ' : null;
				
				// si c'est un paramètre obligatoire
				if ($boucle < $nbrRequiredParams) {
					$arMethodInfos[$reflectMethod->name]['call'] .= ($isFirst) ? $typeStr . $nameStr : ', ' . $typeStr . $nameStr;
					
				// si c'est un paramètre facultatif
				} else {
					$value = $parameters[$boucle]->getDefaultValue ();
					
					// valeur de type boolean
					if (in_array ($type, array ('bool', 'boolean')) && in_array ($value, array (0, 1))) {
						$valueStr = ($valueStr == true) ? ' = ' . self::_getFormatted ('true', self::FR_VALUE) : ' = ' . self::_getFormatted ('false', self::FR_VALUE);
						
					// valeur nulle
					} else if (is_null ($value)) {
						$valueStr = ' = ' . self::_getFormatted ('null', self::FR_VALUE);
						
					// valeur de type string
					} else if ($type == 'string') {
						$valueStr = ' = ' . self::_getFormatted ($value, self::FR_STRING);
						
					// toutes les autres valeurs
					} else {
						$valueStr = ' = ' . self::_getFormatted ($value, self::FR_VALUE);
					}
					$arMethodInfos[$reflectMethod->name]['call'] .= ($isFirst) ? '[' . $typeStr . $nameStr . $valueStr : ', [' . $typeStr . $nameStr . $valueStr;
					$endStrCall .= ']';
				}
				$isFirst = false;
			}
			$arMethodInfos[$reflectMethod->name]['call'] .= $endStrCall . ')';
			
			$arMethodInfos[$reflectMethod->name]['isStatic'] = $reflectMethod->isStatic ();			
			$arMethod[] = $reflectMethod->name;
		}
		sort ($arMethodPrivateSort);
		sort ($arMethodProtectedSort);
		sort ($arMethodPublicSort);
		
		if (count ($arMethodPublicSort)) {
			$toReturn[] = $sectionSpaces . self::_beginSection ('Méthodes publiques', self::FR_SECTION_PUBLIC);
			foreach ($arMethodPublicSort as $methodName) {
				if (isset ($arMethodInfos[$methodName]['comment']['text'][0])) {
					$toReturn[] = $dumpSpaces . self::_getFormatted ('// ' . $arMethodInfos[$methodName]['comment']['text'][0], self::FR_COMMENT);
				}
				$static = ($arMethodInfos[$methodName]['isStatic']) ? self::_getFormatted ('static', self::FR_KEYWORD) . ' ' : null;
				$toReturn[] = $dumpSpaces . $static . $arMethodInfos[$methodName]['call'];
			}
			$toReturn[] = self::_endSection ();
		}
		if (count ($arMethodProtectedSort)) {
			$toReturn[] = $sectionSpaces . self::_beginSection ('Méthodes protégées', self::FR_SECTION_PROTECTED, false);
			foreach ($arMethodProtectedSort as $methodName) {
				if (isset ($arMethodInfos[$methodName]['comment']['text'][0])) {
					$toReturn[] = $dumpSpaces . self::_getFormatted ('// ' . $arMethodInfos[$methodName]['comment']['text'][0], self::FR_COMMENT);
				}
				$static = ($arMethodInfos[$methodName]['isStatic']) ? self::_getFormatted ('static', self::FR_KEYWORD) . ' ' : null;
				$toReturn[] = $dumpSpaces . $static . $arMethodInfos[$methodName]['call'];
			}
			$toReturn[] = self::_endSection ();
		}
		if (count ($arMethodPrivateSort)) {
			$toReturn[] = $sectionSpaces . self::_beginSection ('Méthodes privées', self::FR_SECTION_PRIVATE, false);
			foreach ($arMethodPrivateSort as $methodName) {
				if (isset ($arMethodInfos[$methodName]['comment']['text'][0])) {
					$toReturn[] = $dumpSpaces . self::_getFormatted ('// ' . $arMethodInfos[$methodName]['comment']['text'][0], self::FR_COMMENT);
				}
				$static = ($arMethodInfos[$methodName]['isStatic']) ? self::_getFormatted ('static', self::FR_KEYWORD) . ' ' : null;
				$toReturn[] = $dumpSpaces . $static . $arMethodInfos[$methodName]['call'];
			}
			$toReturn[] = self::_endSection ();
		}
		
		if (self::$_return) {
			return implode ($endLine, $toReturn);
		} else {
			self::_outputFormatted (implode ($endLine, $toReturn));
		}
	}
	
	/**
	 * Log un var_dump de $pVar en type 'debug', en supprimant l'overload de xdebug si il est installé.
	 * 
	 * @param mixed $pVar variabl à logguer
	 */
	static public function log ($pVar) {
		// On ne veut pas formater l'affichage 
		if (extension_loaded ('Xdebug')) {
			ini_set ('xdebug.overload_var_dump', 0);
		}
		ob_start ();
		var_dump ($pVar);
		$content = ob_get_contents ();
		ob_end_clean ();
		
		_log ($content, 'debug', CopixLog::INFORMATION);
	}
	
	/**
	 * Affiche un contenu dans un div et avec des infos sur le fichier appelant
	 * 
	 * @param string $pContent Contenu à afficher.
	 */
	static private function _outputFormatted (&$pContent) {
		header ("Content-Type: text/html;charset=UTF-8");
		echo '<div style="border: solid red 1px; padding: 5px; text-align: left; cursor: default; align: left; background-color: white; whitespace: preserve; overflow: auto">';
		//echo '<pre>';
		$caller = self::getCaller ();
		list ($prefix, $file) = CopixFile::getCopixPathPrefix ($caller['file']);
		if ($prefix) {
			$file = $prefix . DIRECTORY_SEPARATOR . $file;
		}
		echo '<div style="width:100%; background-color:red; color: white; padding-top: 5px; padding-bottom: 5px; margin-bottom: 5px; font-weight: bold; text-align: center;">';
		printf ("From %s, line %d :", htmlentities ($file), $caller['line']);
		echo '</div>';
		echo $pContent;
		//echo '</pre>';
		echo '</div>';
	}
	
	/**
	 * Affiche un var_dump
	 * 
	 * @param mixed $pVar Variable
	 * @param boolean $pReturn true : renvoie le résultat sous forme de chaine, false : affiche le résultat avec echo
	 * @param boolean $pFormatReturn Formater le résultat retourné, ou retourner un texte brut
	 * @return string
	 */
	static public function var_dump ($pVar, $pReturn = false, $pFormatReturn = true) {
		self::$_return = $pReturn;
		self::$_formatReturn = $pFormatReturn;
		
		// si $pVar est un objet, on lance la bonne méthode, le but étant d'avoir une méthode par "type" de variable possible
		
		if (is_object ($pVar)) {
			return self::_objectDump ($pVar, $pReturn, $pFormatReturn);
		} else if (is_array ($pVar)) {
			return self::_arrayDump ($pVar, $pReturn, $pFormatReturn);			
		}
		
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
			// Remplace les indentations de 2 espaces par des indentations de 4 espaces
			$content = preg_replace ('/^((?: {2})+)/m', '$1$1', $content);
		}
		
		// si on veut retourner le contenu
		if ($pReturn) {
			return $content;
			
		// si on veut afficher le contenu directement
		} else {
			self::_outputFormatted ($content);
		}
	}
	
	/**
	 * Affiche un debug_backtrace en formattant le retour
	 * 
	 * @param int $pLevel Niveau d'appelant du contexte recherché, 1 = appelant direct
	 * @param array $pIgnorePaths Fichiers et chemins du debug_backtrace à ignorer 
	 * @param boolean $pReturn true : renvoie le résultat sous forme de chaine, false : affiche le résultat avec echo
	 * @param boolean $pFormatReturn Formater le résultat retourné, ou retourner un texte brut
	 * @return string
	 */
	static public function debug_backtrace ($pLevel = 0, $pIgnorePaths = null, $pReturn = false, $pFormatReturn = true) {
		$debug = self::_filtered_debug_backtrace ($pIgnorePaths, $pLevel);
		
		// si on veut retourner le contenu
		if ($pReturn) {
			return $debug;
			
		// si on veut afficher le contenu directement
		} else {
			
			// si on ne veut pas formater l'affichage
			if (!$pFormatReturn) {
				echo $debug;
				
			// si on veut formater l'affichage
			} else {
				echo '<table class="CopixTable">';
				echo '<tr>';
				//echo '<th>' . _i18n ('copix:copixdebug.th.class') . '</th>';
				echo '<th>' . _i18n ('copix:copixdebug.th.functions') . '</th>';
				echo '<th>' . _i18n ('copix:copixdebug.th.args') . '</th>';
				echo '</tr>';
				
				$alternate = '';
				foreach ($debug as $index => $infos) {				
					echo '<tr ' . $alternate . ' valign="top">';
					echo '<td><span title="' . htmlspecialchars ($infos['file'] . ':' . $infos['line']) . '">';
					if (isset ($infos['class'])) {
						echo $infos['class'] . '::';
					}
					echo '<strong>' . $infos['function'] . ' ()</strong>';
					echo '</span></td>';
					echo '<td style="max-height: 200">';
					self::var_dump ($infos['args']);
					echo '</td>';
					echo '</tr>';
					
					$alternate = ($alternate == '') ? 'class="alternate"' : '';
				}
				echo '</table><br />';
			}
		}
	}
	
	/**
	 * Retourne les informations de contexte de l'un des appelants.
	 *
	 * @param int $pLevel Niveau d'appelant du contexte recherché, 1 = appelant direct.
	 * @param array $pIgnorePaths Fichiers et chemins du debug_backtrace à ignorer
	 * @return array Niveau d'appelant voulu
	 */
	static public function getCaller ($pLevel = 0, $pIgnorePaths = null) {
		$backtrace = self::debug_backtrace ($pLevel + 1, $pIgnorePaths, true, false);
		return reset ($backtrace);
	}
	
	/**
	 * Retourne le debug_backtrace purgé des références à certains fichiers (et/ou chemins).
	 *
	 * @param array $pIgnorePaths Chemins à ignorer
	 * @param integer $pLevel Nombre de niveau d'appels à ignorer.
	 * @return array debug_backtrace
	 */
	static private function _filtered_debug_backtrace ($pIgnorePaths = null, $pLevel = 0) {
		static $recurse = false;
		if ($recurse) return array ();
		$recurse = true;
		try {
					
			// Chemins à ignorer
			$ignorePaths = array (
				__FILE__, 
				COPIX_CORE_PATH . 'shortcuts.lib.php',
				COPIX_TEMP_PATH
			);
			if (is_array ($pIgnorePaths)) {
				$ignorePaths = array_merge ($ignorePaths, $pIgnorePaths);
			}
			
			// Construit la regex pour vérifier les chemins
			$regex = array ();
			foreach ($ignorePaths as $path) {
				$regex[] = preg_quote (CopixConfig::getRealPath ($path), '/');
			}			
			$pathRegex = '/^(' . implode ('|', $regex) . ')/i';
			
			// Filtre la pile d'appel
			$backtrace = array_slice (debug_backtrace (), $pLevel + 2);			
			foreach ($backtrace as $k => $step) {			
				if (isset ($step['file']) && preg_match ($pathRegex, $step['file'])) {
					unset ($backtrace[$k]);
				}
			}
		
			$recurse = false;
			return array_values ($backtrace);
			
		} catch (Exception $e) {
			$recurse = false;
			throw $e;
		}
	}
}
?>