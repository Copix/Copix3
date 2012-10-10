<?php
/**
 * @package  	copix
 * @subpackage	coding
 * @author		Steevan BARBOYON, Guillaume Perréal
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Offre des possibilités pour débuguer
 * /!\ CopixDebug est prévu pour fonctionner au maximum "tout seul", c'est pour ça qu'il n'y a pas de i18N ou d'autoload /!\
 * /!\ Sinon, on ne peut pas l'utiliser dans certaines parties du framework en lui même (boucles infinies) /!\
 *
 * @package		copix
 * @subpackage	coding
 */
class CopixDebug {
	/**
	 * Section publique, pour le formatage de _getFormated
	 */
	const FR_SECTION_PUBLIC = 0;
	
	/**
	 * Section privée, pour le formatage de _getFormated
	 */
	const FR_SECTION_PRIVATE = 1;
	
	/**
	 * Section protégée, pour le formatage de _getFormated
	 */
	const FR_SECTION_PROTECTED = 2;
	
	/**
	 * Nom d'un fichier, pour le formatage de _getFormated
	 */
	const FR_FILENAME = 3;
	
	/**
	 * Mot clef, pour le formatage de _getFormated
	 */
	const FR_KEYWORD = 4;
	
	/**
	 * Commentaire, pour le formatage de _getFormated
	 */
	const FR_COMMENT = 5;
	
	/**
	 * Type de variable, pour le formatage de _getFormated
	 */
	const FR_TYPE = 6;
	
	/**
	 * Nom de constante, pour le formatage de _getFormated
	 */
	const FR_CONST = 7;
	
	/**
	 * Nom de variable, pour le formatage de _getFormated
	 */
	const FR_VAR = 8;
	
	/**
	 * Chaine de caractères, pour le formatage de _getFormated
	 */
	const FR_STRING = 9;
	
	/**
	 * Noms des éléments dans la déclaration, pour le formatage de _getFormated
	 */
	const FR_DECLARATION = 10;
	
	/**
	 * Value de variable / constante / propriété, pour le formatage de _getFormated
	 */
	const FR_VALUE = 11;
	
	/**
	 * Nom d'une fonction / méthode, pour le formatage de _getFormated
	 */
	const FR_FUNCTIONNAME = 12;
	
	/**
	 * Type de variable integer, pour le formatage de _getFormated
	 */
	const FR_INT = 13;
	
	/**
	 * Type de variable boolean, pour le formatage de _getFormated
	 */
	const FR_BOOLEAN = 15;
	
	/**
	 * Type de variable null, pour le formatage de _getFormated
	 */
	const FR_NULL = 16;
	
	/**
	 * Type de variable erreur, pour le formatage de _getFormated
	 */
	const FR_ERROR = 17;
	
	/**
	 * Indique si on veut formater les retours des méthodes
	 *
	 * @var boolean
	 */
	public static $formatReturn = true;
	
	/**
	 * Indique de combien de sous-niveaux var_dump va descendre
	 *
	 * @var int
	 */
	public static $maxDumpLevels = 5;
	
	/**
	 * Nombre d'espaces pour la declaration en cours
	 *
	 * @var int
	 */
	private static $_declarationSpaces = 0;
	
	/**
	 * Nombre d'espaces pour les dumps en cours
	 *
	 * @var int
	 */
	private static $_dumpSpaces = 0;
	
	/**
	 * Nombre d'espaces pour les sections en cours
	 *
	 * @var int
	 */
	private static $_sectionSpaces = 0;
	
	/**
	 * Indique dans quel niveau de dump on est
	 *
	 * @var int
	 */
	private static $_dumpIndex = 0;
	
	/**
	 * Indique si on vient de la méthode reflect ou var_dump
	 *
	 * @var boolean
	 */
	private static $_isReflect = false;
	
	/**
	 * Retourne les espaces à afficher en fonction du formattage du retour demandé, et des espaces courants pour le dump
	 *
	 * @return string
	 */
	private static function _getSpaces ($pNbrSpaces) {
		$str = (self::$formatReturn) ? '&nbsp;' : ' ';
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
	private static function _getEndLine () {
		return (self::$formatReturn) ? '<br />' : "\n";
	}
	
	/**
	 * Commence une section, avec la possibilité de l'afficher / cacher
	 *
	 * @param string $pTitle
	 * @param int $pType
	 * @param boolean $pDefaultOpened
	 * @return string
	 */
	private static function _beginSection ($pTitle, $pType, $pDefaultOpened = true) {
		if (self::$formatReturn) {
			$imgUp = _resource ('img/tools/moveup.png');
			$imgDown = _resource ('img/tools/movedown.png');
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
			$toReturn .= self::_getFormated ($pTitle, $pType);
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
	private static function _endSection () {
		return (self::$formatReturn) ? '</div>' : null;
	}
	
	/**
	 * Retourne une chaine formatée en fonction de la configuration du dump
	 *
	 * @param string $pStr Chaine à formater ou non
	 * @param int $pType Type de formattage à effectuer, utiliser les constantes self::FR_
	 * @return string
	 */
	private static function _getFormated ($pStr, $pType) {
		if (!self::$formatReturn) {
			return ($pType == self::FR_STRING) ? "'" . $pStr . "'" : $pStr;
		}
		$isUtf8 = (utf8_encode(utf8_decode($pStr)) == $pStr);
		if(!$isUtf8){
			$pStr = utf8_encode($pStr);
		}
		
		$str = htmlentities ($pStr, ENT_COMPAT, 'UTF-8');
		switch ($pType) {
			case self::FR_SECTION_PUBLIC : return '<b><font color="green">' . $str . '</font></b>'; break;
			case self::FR_SECTION_PRIVATE : return '<b><font color="red">' . $str . '</font></b>'; break;
			case self::FR_SECTION_PROTECTED : return '<b><font color="red">' . $str . '</font></b>'; break;
			case self::FR_FILENAME : return '<i>' . $str . '</i>'; break;
			case self::FR_KEYWORD : return  '<b>' . $str . '</b>'; break;
			case self::FR_COMMENT : return '<font color="#808080">' . $str . '</font>'; break;
			case self::FR_TYPE : return '<font color="#808080">' . $str . '</font>'; break;
			case self::FR_CONST : return '<b>' . $str . '</b>'; break;
			case self::FR_VAR : return '<font color="#663300">' . $str . '</font>'; break;
			case self::FR_INT : return '<font color="green">' . $str . '</font>'; break;
			case self::FR_STRING : return '<font color="blue">\'' . $str . '\'</font>'; break;
			case self::FR_DECLARATION : return '<b>' . $str . '</b>'; break;
			case self::FR_VALUE : return '<font color="green">' . $str . '</font>'; break;
			case self::FR_FUNCTIONNAME : return '<b>' . $str . '</b>'; break;
			case self::FR_BOOLEAN : return '<b>' . $str . '</b>'; break;
			case self::FR_NULL : return '<b>' . $str . '</b>'; break;
		}
	}
	
	/**
	 * Retourne le type de la variable $pVar
	 *
	 * @param mixed $pVar
	 * @return string
	 */
	private static function _getType ($pVar) {
		$toReturn = gettype ($pVar);
		if ($toReturn == 'string') {
			$toReturn = 'string (' . strlen ($pVar) . ')';
		} else if (is_null ($pVar)) {
			$toReturn = null;
		} else if (is_object ($pVar)) {
			$toReturn = get_class ($pVar);
		}
		
		return $toReturn;
	}
	
	/**
	 * Effectue un dump sur une variable normale
	 *
	 * @param mixed $pVar Variable à dumper
	 * @param boolean $pShowType Indique si on veut voir le type de la valeur, null = regarde self::$_isReflect
	 * @return string
	 */
	private static function _varDump ($pVar, $pShowType = null) {
		
		$endLine = self::_getEndLine ();
		$dump = null;
		$dumpSpaces = self::_getSpaces (self::$_dumpSpaces);
		$declarationSpaces = self::_getSpaces (self::$_declarationSpaces);
		$showType = (($pShowType === null && self::$_isReflect) || $pShowType);
				
		if (is_resource ($pVar)) {
			$dump .= self::_getFormated ('resource ', self::FR_TYPE);
			$dump .= self::_getFormated (get_resource_type ($pVar), self::FR_VALUE);
			
		} else if (is_object ($pVar) || is_array ($pVar)) {
			$dump = self::_dump ($pVar);
			
		} else if (is_string ($pVar)) {
			
			if ($showType) {
				$dump .= self::_getFormated ('string[' . strlen ($pVar) . '] ', self::FR_TYPE);
			}
			$dump .= self::_getFormated ($pVar, self::FR_STRING);
		
		} else if (is_int ($pVar) || is_float ($pVar)) {
			if ($showType) {
				$dump .= self::_getFormated (gettype ($pVar), self::FR_TYPE) . ' ';
			}
			$dump .= self::_getFormated ($pVar, self::FR_INT);
			
		} else if (is_bool ($pVar)) {
			if ($showType) {
				$dump .= self::_getFormated ('bool', self::FR_TYPE) . ' ';
			}
			$dump .= self::_getFormated (($pVar) ? 'true' : 'false', self::FR_BOOLEAN);
			
		} else if ($pVar === null) {
			$dump = self::_getFormated ('null', self::FR_NULL);
			
		} else {
			if ($showType) {
				$dump .= self::_getFormated (gettype ($pVar), self::FR_TYPE) . ' ';
			}
			$dump .= self::_getFormated ($pVar, self::FR_VALUE);
		}
		
		return $dump;
	}
	
	/**
	 * Effectue un dump sur un array
	 *
	 * @param object $pArray Array à dumper
	 * @return string
	 */
	private static function _arrayDump ($pArray, $pIsJustDeclaration) {
		if (!is_array ($pArray)) {
			return self::_getFormated ('Invalid variable type : "array"', self::FR_ERROR);
		}
		
		$endLine = self::_getEndLine ();
		$dump = array ();
		$dumpSpaces = self::_getSpaces (self::$_dumpSpaces);
		$declarationSpaces = self::_getSpaces (self::$_declarationSpaces);
		
		$dump[0] = self::_getFormated ('array', self::FR_DECLARATION) . '[' . count ($pArray) . '] (';
		if ($pIsJustDeclaration) {
			return $dump[0] . '...)';
		} else if (count ($pArray) == 0) {
			return $dump[0] . ')';
		}
			
		foreach ($pArray as $key => $value) {
			$index = count ($dump);
			$type = (is_string ($key)) ? self::FR_STRING : self::FR_INT;
			$dump[$index] = $dumpSpaces . self::_getFormated ($key, $type) . ' = ';
			$dump[$index] .= self::_varDump ($value);
		}
		$dump[] = $declarationSpaces . ')';
		
		return implode ($endLine, $dump);
	}
	
	/**
	 * Retourne un tableau avec toutes les propriétés d'un objet, même les privées, avec leur valeur
	 *
	 * @param object $pObject Objet
	 * @return mixed[]
	 */
	private static function _getObjectVars ($pObject) {
		if (!is_object ($pObject)) {
			return self::_getFormated ('Invalid variable type : "object"', self::FR_ERROR);
		}
		
		$export = var_export ($pObject, true);
		$varsStr = preg_replace ('/([a-zA-Z0-9\\\_]+)::__set_state\(/', 'new CopixObjectVars (\'\\1\', ', $export);
		require_once (CopixFile::extractFilePath (__FILE__) . 'CopixObjectVars.class.php');
		return eval ('return ' . $varsStr . ';');
	}
	
	/**
	 * Effectue un dump sur un objet
	 *
	 * @param object $pObject Objet
	 * @param boolean $pIsJustDeclaration Indique si on ne veut que la déclaration ou le dump complet
	 */
	private static function _objectDump ($pObject, $pIsJustDeclaration) {
		$reflect = ($pObject instanceof CopixObjectVars) ? $pObject : self::_getObjectVars ($pObject);
		$vars = array ();
		$dumpSpaces = self::_getSpaces (self::$_dumpSpaces);
		$declarationSpaces = self::_getSpaces (self::$_declarationSpaces);
		
		// propriétés
		foreach ($reflect->getVars () as $name => $value) {
			$vars[] = $dumpSpaces . self::_getFormated ('$' . $name, self::FR_VAR) . ' = ' . self::_varDump ($value);
		}
		
		sort ($vars);
		$dump = array ();
		$name = ($pObject instanceof CopixObjectVars) ? $pObject->getObjectName () : get_class ($pObject);
		if (count ($vars) > 0) {
			$dump[] = self::_getFormated ('object', self::FR_KEYWORD) . ' ' . $name . ' (';
			$dump = array_merge ($dump, $vars);
			$dump[] = $declarationSpaces . ')';
		} else {
			$dump[] = self::_getFormated ('object', self::FR_KEYWORD) . ' ' . $name . ' ()';
		}
		return implode ("\n", $dump);
	}
	
	/**
	 * Effectue un reflect sur un objet
	 *
	 * @param object $pObject Objet
	 * @param boolean $pIsJustDeclaration Indique si on ne veut que la déclaration ou le reflect complet
	 * @return string
	 * @todo Cas des propriétés protégées et privées statiques non géré
	 */
	private static function _objectReflect ($pObject, $pIsJustDeclaration) {
		if (!is_object ($pObject)) {
			return self::_getFormated ('Invalid variable type : "object"', self::FR_ERROR);
		}
		
		$config = CopixConfig::instance ();
		$sectionSpaces = self::_getSpaces (self::$_sectionSpaces);
		$dumpSpaces = self::_getSpaces (self::$_dumpSpaces);
		$dump = array ();
		$class = get_class ($pObject);
		$reflection = new ReflectionClass ($class);
		$parent = $reflection->getParentClass ();
		$endLine = self::_getEndLine ();
		
		// ------------------------------------------------------------------
		// recherche du nom de la classe, de l'extends et du fichier déclarant la classe
		// ------------------------------------------------------------------
		
		$file = str_replace ('\\', '/', $reflection->getFileName ());
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
		$declaration = ($reflection->isFinal ()) ? self::_getFormated ('final', self::FR_KEYWORD) . ' ' : null;
		$declaration .= ($reflection->isAbstract ()) ? self::_getFormated ('abstract', self::FR_KEYWORD) . ' ' : null;
		$declaration .= ($reflection->isInterface ()) ? self::_getFormated ('interface', self::FR_KEYWORD) . ' ' : self::_getFormated ('object', self::FR_KEYWORD) . ' ';
		$declaration .= self::_getFormated ($class, self::FR_DECLARATION);
		if ($parent) {
			$declaration .= ' ' . self::_getFormated ('extends', self::FR_KEYWORD) . ' ' . self::_getFormated ($parent->name, self::FR_DECLARATION);
		}
		$interfaces = $reflection->getInterfaces ();
		if (count ($interfaces) > 0) {
			$declaration .= ' ' . self::_getFormated ('implements', self::FR_KEYWORD) . ' ' . self::_getFormated (implode (', ', array_keys ($interfaces)), self::FR_DECLARATION);
		}
		$dump[0] = $declaration . ' (' . self::_getFormated ($fileDir, self::FR_FILENAME) . ')';
		
		if ($pIsJustDeclaration) {
			return $dump[0] . ' (...)';
		}
		
		// ------------------------------------------------------------------
		// recherche des constantes
		// ------------------------------------------------------------------
		
		$constantes = $reflection->getConstants ();
		ksort ($constantes);
		
		if (count ($constantes) > 0) {
			$dump[] = $sectionSpaces . self::_beginSection (
				'Constantes (' . count ($constantes) . ')',
				self::FR_SECTION_PUBLIC,
				$config->copixdebug_showConstantes
			);
			foreach ($constantes as $constName => $constValue) {
				$constValueStr = self::_varDump ($constValue);
				$constTypeStr = (is_null ($constValue)) ? null : self::_getFormated (self::_getType ($constValue), self::FR_TYPE);
				$constNameStr = self::_getFormated ($constName, self::FR_VAR);

				$dump[] = $dumpSpaces . trim ($constTypeStr . ' ' . $constNameStr) . ' = ' . $constValueStr;
			}
			$dump[count ($dump) - 1] .= self::_endSection ();
		}
		
		// ------------------------------------------------------------------
		// recherche des propriétés
		// ------------------------------------------------------------------
		
		// getProperties renvoie uniquement les propriétés déclarées dans la classe, et pas celles "ajoutées" après instanciation
		$properties = $reflection->getProperties ();
		$newProperties = array ();
		
		// on recherche les propriétés ajoutées "après instanciation"
		foreach ($pObject as $objectProperty => $value) {
			$isNew = true;
			foreach ($properties as $property) {
				if ($objectProperty == $property->name) {
					$isNew = false;
					break;
				}
			}
			if ($isNew) {
				$newProperties[$objectProperty] = $value;
			}
		}
		ksort ($newProperties);
		
		// tri des méthodes selon leur accessibilité (public, protected et private)
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
		
		// propriétés "ajoutées", qui ne sont pas déclarées dans la classe
		if (count ($newProperties)) {
			$dump[] = $sectionSpaces . self::_beginSection (
				'Propriétés non déclarées dans la classe (' . count ($newProperties) . ')',
				self::FR_SECTION_PUBLIC,
				$config->copixdebug_showNotDeclaredProperties
			);
			
			foreach ($newProperties as $propName => $propValue) {
				$propNameStr = self::_getFormated ('$' . $propName, self::FR_VAR);
				$propType = self::_getType ($propValue);
				$propTypeStr = (is_null ($propType)) ? null : self::_getFormated ($propType, self::FR_TYPE);
				$propValueStr = self::_varDump ($propValue);

				$index = count ($dump);
				$dump[$index] = $dumpSpaces . trim ($propTypeStr . ' ' . $propNameStr);
				if (!is_object ($propValue)) {
					$dump[$index] .= ' = ' . $propValueStr;
				}
			}
			
			$dump[] = self::_endSection ();
		}
		
		// propriétés publiques
		if (count ($arPropPublicSort)) {
			$dump[] = $sectionSpaces . self::_beginSection (
				'Propriétés publiques (' . count ($arPropPublicSort) . ')',
				self::FR_SECTION_PUBLIC,
				$config->copixdebug_showPublicProperties
			);
			foreach ($arPropPublicSort as $propName) {
				if (($phpdoc = $reflection->getProperty ($propName)->getDocComment ()) !== false) {
					$phpdocParsed = CopixPHPDoc::parse ($phpdoc);
					if (isset ($phpdocParsed['comment'])) {
						foreach ($phpdocParsed['comment'] as $comment) {
							$dump[] = $dumpSpaces . self::_getFormated ('// ' . $comment, self::FR_COMMENT);
						}
					}
				}
				$propNameStr = self::_getFormated ('$' . $propName, self::FR_VAR);
				// getStaticProperties renvoie un tableau dont la clef est difficilement utilisable (caractères spéciaux ajoutés suivant l'accès)
				$isStatic = false;
				
				// si la propriété n'est pas statique, et n'est pas nulle (si elle est nulle, isset renverra false)
				if (isset ($pObject->$propName)) {
					$propValue = $pObject->$propName;
				// propriété statique, ou de valeur nulle
				} else {
					// try pour le cas la propriété n'est pas statique mais nulle, getStaticPropertyValue retourne une exception
					try {
						$propValue = $reflection->getStaticPropertyValue ($propName);
						// si on arrive ici, c'est que la propriété était bien statique
						$isStatic = true;
					} catch (Exception $e) {
						// propriété non statique, mais de valeur nulle
						$propValue = null;
					}
				}
				$propValueStr = self::_varDump ($propValue);
				$propType = self::_getType ($propValue);
				$propTypeStr = (is_null ($propType)) ? null : self::_getFormated ($propType, self::FR_TYPE);
				$accessStr = ($isStatic) ? self::_getFormated ('static', self::FR_KEYWORD) . ' ': null;
				$index = count ($dump);
				$dump[$index] = $dumpSpaces . trim ($propTypeStr . ' ' . $accessStr . $propNameStr);
				if (!is_object ($propValue)) {
					$dump[$index] .= ' = ' . $propValueStr;
				}
			}
			$dump[] = self::_endSection ();
		}
		
		// propriétés protégées
		if (count ($arPropProtectedSort)) {
			$dump[] = $sectionSpaces . self::_beginSection (
				'Propriétés protégées (' . count ($arPropProtectedSort) . ')',
				self::FR_SECTION_PROTECTED,
				$config->copixdebug_showProtectedProperties
			);
			foreach ($arPropProtectedSort as $propName) {
				if (($phpdoc = $reflection->getProperty ($propName)->getDocComment ()) !== false) {
					$phpdocParsed = CopixPHPDoc::parse ($phpdoc);
					if (isset ($phpdocParsed['comment'])) {
						foreach ($phpdocParsed['comment'] as $comment) {
							$dump[] = $dumpSpaces . self::_getFormated ('// ' . $comment, self::FR_COMMENT);
						}
					}
				}
				$dump[] = $dumpSpaces . self::_getFormated ('$' . $propName, self::FR_VAR);
			}
			$dump[] = self::_endSection ();
		}
		
		// propriétés privées
		if (count ($arPropPrivateSort)) {
			$dump[] = $sectionSpaces . self::_beginSection (
				'Propriétées privées (' . count ($arPropPrivateSort) . ')',
				self::FR_SECTION_PRIVATE,
				$config->copixdebug_showPrivateProperties
			);
			foreach ($arPropPrivateSort as $propName) {
				if (($phpdoc = $reflection->getProperty ($propName)->getDocComment ()) !== false) {
					$phpdocParsed = CopixPHPDoc::parse ($phpdoc);
					if (isset ($phpdocParsed['comment'])) {
						foreach ($phpdocParsed['comment'] as $comment) {
							$dump[] = $dumpSpaces . self::_getFormated ('// ' . $comment, self::FR_COMMENT);
						}
					}
				}
				$dump[] = $dumpSpaces . self::_getFormated ('$' . $propName, self::FR_VAR);
			}
			$dump[] = self::_endSection ();
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
			if (($phpdoc = $reflectMethod->getDocComment ()) !== false) {
				$parse = CopixPHPDoc::parse ($phpdoc);
				$arMethodInfos[$reflectMethod->name]['comment'] = $parse;
			}
			$methodParamsType = array ();
			
			// recherche de l'accès à la méthode (private, protected, public)
			if ($reflectMethod->isPrivate ()) {
				$arMethod = &$arMethodPrivateSort;
			} else if ($reflectMethod->isProtected ()) {
				$arMethod = &$arMethodProtectedSort;
			} else {
				$arMethod = &$arMethodPublicSort;
			}
			
			// recherche des paramètres d'appels de la méthode
			$parameters = $reflectMethod->getParameters ();
			$nbrRequiredParams = $reflectMethod->getNumberOfRequiredParameters ();
			$requiredParams = array_slice ($parameters, 0, $nbrRequiredParams);
			$optionalParams = array_slice ($parameters, $nbrRequiredParams);
			
			$arMethodInfos[$reflectMethod->name]['call'] = self::_getFormated ($reflectMethod->name, self::FR_FUNCTIONNAME) . ' (';
			$isFirst = true;
			$endStrCall = null;
			for ($boucle = 0; $boucle < $reflectMethod->getNumberOfParameters (); $boucle++) {
				$name = $parameters[$boucle]->name;
				$nameStr = self::_getFormated ('$' . $name, self::FR_VAR);
				$commentParams = (isset ($arMethodInfos[$reflectMethod->name]['comment']['param'][$boucle])) ? $arMethodInfos[$reflectMethod->name]['comment']['param'][$boucle] : null;
				$type = (isset ($commentParams['type'])) ? $commentParams['type'] : null;
				$typeStr = (!is_null ($type)) ? self::_getFormated ($type, self::FR_TYPE) . ' ' : null;
				
				// si c'est un paramètre obligatoire
				if ($boucle < $nbrRequiredParams) {
					$arMethodInfos[$reflectMethod->name]['call'] .= ($isFirst) ? $typeStr . $nameStr : ', ' . $typeStr . $nameStr;
					
				// si c'est un paramètre facultatif
				} else {
					$value = $parameters[$boucle]->getDefaultValue ();
					if (is_null ($type)) {
						$type = gettype ($value);
						$typeStr = self::_getFormated ($type, self::FR_TYPE) . ' ';
					}
					$valueStr = ' = ' . self::_varDump ($value);
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
		
		// méthodes publiques
		if (count ($arMethodPublicSort)) {
			$dump[] = $sectionSpaces . self::_beginSection (
				'M2thodes publiques (' . count ($arMethodPublicSort) . ')',
				self::FR_SECTION_PUBLIC,
				$config->copixdebug_showPublicMethods
			);
			foreach ($arMethodPublicSort as $methodName) {
				if (isset ($arMethodInfos[$methodName]['comment']['comment'])) {
					foreach ($arMethodInfos[$methodName]['comment']['comment'] as $comment) {
						$dump[] = $dumpSpaces . self::_getFormated ('// ' . $comment, self::FR_COMMENT);
					}
				}
				$static = ($arMethodInfos[$methodName]['isStatic']) ? self::_getFormated ('static', self::FR_KEYWORD) . ' ' : null;
				$dump[] = $dumpSpaces . $static . $arMethodInfos[$methodName]['call'];
			}
			$dump[] = self::_endSection ();
		}
		
		// méthodes protégées
		if (count ($arMethodProtectedSort)) {
			$dump[] = $sectionSpaces . self::_beginSection (
				'Méthodes protégées (' . count ($arMethodProtectedSort) . ')',
				self::FR_SECTION_PROTECTED,
				$config->copixdebug_showProtectedMethods
			);
			foreach ($arMethodProtectedSort as $methodName) {
				if (isset ($arMethodInfos[$methodName]['comment']['comment'])) {
					foreach ($arMethodInfos[$methodName]['comment']['comment'] as $comment) {
						$dump[] = $dumpSpaces . self::_getFormated ('// ' . $comment, self::FR_COMMENT);
					}
				}
				$static = ($arMethodInfos[$methodName]['isStatic']) ? self::_getFormated ('static', self::FR_KEYWORD) . ' ' : null;
				$dump[] = $dumpSpaces . $static . $arMethodInfos[$methodName]['call'];
			}
			$dump[] = self::_endSection ();
		}
		
		// méthodes privées
		if (count ($arMethodPrivateSort)) {
			$dump[] = $sectionSpaces . self::_beginSection (
				'Méthodes privées (' . count ($arMethodPrivateSort) . ')',
				self::FR_SECTION_PRIVATE,
				$config->copixdebug_showPrivateMethods
			);
			foreach ($arMethodPrivateSort as $methodName) {
				if (isset ($arMethodInfos[$methodName]['comment']['comment'])) {
				foreach ($arMethodInfos[$methodName]['comment']['comment'] as $comment) {
						$dump[] = $dumpSpaces . self::_getFormated ('// ' . $comment, self::FR_COMMENT);
					}
				}
				$static = ($arMethodInfos[$methodName]['isStatic']) ? self::_getFormated ('static', self::FR_KEYWORD) . ' ' : null;
				$dump[] = $dumpSpaces . $static . $arMethodInfos[$methodName]['call'];
			}
			$dump[] = self::_endSection ();
		}
		
		return implode ($endLine, $dump);
	}
	
	/**
	 * Log un var_dump de $pVar, sans formatage du retour
	 *
	 * @param mixed $pVar Variable à loguer
	 */
	public static function log ($pVar, $pLogType = 'debug') {
		$exFormat = self::$formatReturn;
		self::$formatReturn = false;
		_log (self::getDump ($pVar), $pLogType, CopixLog::INFORMATION);
		self::$formatReturn = $exFormat;
	}
	
	/**
	 * Affiche un contenu dans un div et avec des infos sur le fichier appelant
	 *
	 * @param string $pContent Contenu à afficher.
	 */
	private static function _outputFormatted (&$pContent) {
		if (!headers_sent ()) {
			@header ("Content-Type: text/html; charset=UTF-8");
		}
		echo '<div style="border: solid #CC0000 1px; margin: 5px; color: black; padding: 5px; text-align: left; cursor: default; align: left; background-color: white; overflow: auto">';
		$caller = self::getCaller ();
		list ($prefix, $file) = CopixFile::getCopixPathPrefix ($caller['file']);
		if ($prefix) {
			$file = $prefix . DIRECTORY_SEPARATOR . $file;
		}
		echo '<div style="width:100%; padding: 1px; font-size: 9pt; background-color:#CC0000; color: white; font-weight: bold; text-align: center;">';
		printf ("From %s, line %d :", htmlentities ($file), $caller['line']);
		echo '</div>';
		echo '<pre style="margin-top: 2px; margin-bottom: 0px;">';
		echo "\n" . $pContent . "\n";
		echo '</pre>';
		echo '</div>';
	}
	
	/**
	 * Effectue un dump avec la bonne méthode
	 *
	 * @param mixed $pVar Variable
	 * @return string
	 */
	private static function _dump ($pVar) {
		self::$_dumpIndex++;
		if (self::$_dumpIndex > 0) {
			self::$_declarationSpaces += 4;
			self::$_sectionSpaces += 4;
		}
		self::$_dumpSpaces += 4;
		
		$isJustDeclaration = (self::$_dumpIndex) >= self::$maxDumpLevels;
		if (is_object ($pVar)) {
			$toReturn = self::_objectDump ($pVar, $isJustDeclaration);
		} else if (is_array ($pVar)) {
			$toReturn = self::_arrayDump ($pVar, $isJustDeclaration);
		} else {
			
			$toReturn = self::_varDump ($pVar, $isJustDeclaration);
		}
		
		self::$_dumpIndex--;
		if (self::$_dumpIndex >= 0) {
			self::$_declarationSpaces -= 4;
			self::$_sectionSpaces -= 4;
		}
		self::$_dumpSpaces -= 4;
		
		return $toReturn;
	}
	
	/**
	 * Affiche le contenu d'une variable, si plusieurs variables sont passées plusieurs dump seront affichés
	 *
	 * @param mixed $pVar Variable
	 */
	public static function var_dump ($pVar) {
		
		// on met -1 car self::_dump fera un +1, donc mettra l'index à 0
		self::$_dumpIndex = -1;
		foreach (func_get_args () as $arg) {
			$dump = self::_dump ($arg);
			 
			self::_outputFormatted ($dump);
		}
	}
	
	/**
	 * Retourne un CopixDebug::var_dump de $pVar
	 *
	 * @param mixed $pVar Variable
	 * @return string
	 */
	public static function getDump ($pVar, $pFormatReturn = true) {
		// on met -1 car self::_dump fera un +1, donc mettra l'index à 0
		$exFormat = self::$formatReturn;
		self::$formatReturn = $pFormatReturn;
		self::$_dumpIndex = -1;
		$dump = self::_dump ($pVar);
		if ($pFormatReturn) {
			$dump = '<pre style="margin: 0px; padding: 0px;">' . $dump . '</pre>';
		}
		self::$formatReturn = $exFormat;
		return $dump;
	}
	
	/**
	 * Affiche les propriétés et méthodes d'un objet
	 *
	 * @param mixed $pVar Variable dont on veut les informations
	 * @param boolean $pReturn Indique si on veut retourner le résultat, ou l'afficher directement
	 * @param boolean $pFormat Indique si on veut formater le résultat, ou avoir un texte brut
	 * @return string
	 */
	/*public static function reflect ($pVar, $pReturn = false, $pFormat = true) {
		self::$_isReflect = true;
		self::var_dump ($pVar, $pReturn, $pFormat);
		self::$_isReflect = false;
	}*/
	
	/**
	 * Affiche un debug_backtrace en formattant le retour
	 *
	 * @param int $pLevel Niveau d'appelant du contexte recherché, 0 = appelant direct
	 * @param array $pIgnorePaths Fichiers et chemins du debug_backtrace à ignorer
	 */
	public static function debug_backtrace ($pLevel = 0, $pIgnorePaths = null) {
		$debug = self::_filteredDebugBacktrace ($pIgnorePaths, $pLevel);
		
		// si on ne veut pas formater l'affichage
		if (!self::$formatReturn) {
			echo self::getDump ($debug);
			
		// si on veut formater l'affichage
		} else {
			$output = '<table style="border: solid 1px black;">';
			$output .= '<tr>';
			//echo '<th style="border: solid 1px black; background-color: #F4E3F7">Classe</th>';
			$output .= '<th style="border: solid 1px black; background-color: #F4E3F7">Fonctions</th>';
			$output .= '<th style="border: solid 1px black; background-color: #F4E3F7">Arguments</th>';
			$output .= '</tr>';
			
			$alternate = '';
			foreach ($debug as $index => $infos) {
				$output .= '<tr ' . $alternate . ' valign="top">';
				$output .= '<td style="border: solid 1px black;">';
				$showSpan = (isset ($infos['file']) && isset ($infos['line']));
				if ($showSpan) {
					$output .= '<span title="' . htmlspecialchars ($infos['file'] . ':' . $infos['line']) . '">';
				}
				if (isset ($infos['class'])) {
					$output .= $infos['class'] . '::';
				}
				$output .= '<strong>' . $infos['function'] . ' ()</strong>';
				if ($showSpan) {
					$output .= '</span>';
				}
				$output .= '</td>';
				$output .= '<td style="max-height: 200; border: solid 1px black;"><pre style="margin: 0px; padding: 0px;">';
				$output .= self::getDump ($infos['args']);
				$output .= '</pre></td>';
				$output .= '</tr>';
				
				$alternate = ($alternate == '') ? 'style="background-color: #E3EFF7;"' : '';
			}
			$output .= '</table>';
			self::_outputFormatted ($output);
			
		}
	}
	
	/**
	 * Retourne un CopixDebug::debug_backtrace
	 *
	 * @param int $pLevel Niveau d'appelant du contexte recherché, 0 = appelant direct
	 * @param array $pIgnorePaths Fichiers et chemins du debug_backtrace à ignorer
	 * @return string
	 */
	public static function getDebugBacktrace ($pLevel = 0, $pIgnorePaths = null) {
		// @todo gérer le retour dans un tableau HTML comme debug_backtrace
		return self::_filteredDebugBacktrace ($pIgnorePaths, $pLevel);
	}
	
	/**
	 * Retourne les informations de contexte de l'un des appelants
	 *
	 * @param int $pLevel Niveau d'appelant du contexte recherché, 1 = appelant direct
	 * @param array $pIgnorePaths Fichiers et chemins du debug_backtrace à ignorer
	 * @return array Niveau d'appelant voulu
	 */
	public static function getCaller ($pLevel = 0, $pIgnorePaths = null) {
		$exFormat = self::$formatReturn;
		self::$formatReturn = false;
		$backtrace = self::getDebugBacktrace ($pLevel + 1, $pIgnorePaths);
		self::$formatReturn = $exFormat;
		return reset ($backtrace);
	}
	
	/**
	 * Effectue un _dump des informations de contexte de l'un des appelants
	 *
	 * @param int $pLevel Niveau d'appelant du contexte recherché, 1 = appelant direct
	 * @param array $pIgnorePaths Fichiers et chemins du debug_backtrace à ignorer
	 */
	public static function dumpCaller ($pLevel = 0, $pIgnorePaths = null) {
		self::var_dump (self::getCaller ($pLevel + 1, $pIgnorePaths));
	}
	
	/**
	 * Retourne le debug_backtrace purgé des références à certains fichiers (et/ou chemins).
	 *
	 * @param array $pIgnorePaths Chemins à ignorer
	 * @param integer $pLevel Nombre de niveau d'appels à ignorer.
	 * @return array debug_backtrace
	 */
	private static function _filteredDebugBacktrace ($pIgnorePaths = null, $pLevel = 0) {
		static $recurse = false;
		if ($recurse) {
			return array ();
		}
		$recurse = true;
		try {
			// Chemins à ignorer
			$ignorePaths = array (
				__FILE__,
				COPIX_PATH . 'core/shortcuts.lib.php',
				COPIX_TEMP_PATH
			);
			if (is_array ($pIgnorePaths)) {
				$ignorePaths = array_merge ($ignorePaths, $pIgnorePaths);
			}
			
			// Construit la regex pour vérifier les chemins
			$regex = array ();
			foreach ($ignorePaths as $path) {
				$regex[] = preg_quote (CopixFile::getRealPath ($path), '/');
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