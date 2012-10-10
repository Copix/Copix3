<?php
/**
* @package		copix
* @subpackage	utils
* @author		Gérald Croës
* @copyright	CopixTeam
* @link 		http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe permettant de formatter correctement un élément de données
 * Toutes les méthodes retournent une structure de données formattée de façon normalisée, 
 *  et génèrent une exception si l'élément n'est pas correct
 */
class CopixFormatter {
	/**
	 * Formatte le nom d'une personne
	 * @return string
	 */
	public static function getNom ($pNom){
		return strtoupper ($pNom);
	}

	/**
	 * récupération d'un prénom formatté correctement
	 * @return string
	 */
	public static  function getPrenom ($pPrenom){
		$result = self::capitalize ($pPrenom);
        return strtoupper (substr ($result, 0, 1)).strtolower (substr ($result, 1));
	}

	/**
	 * Retourne une expression "capitalisée" (premières lettres de chaque mot en majuscule et le reste en minuscule.)
	 * Pronoms en minuscules, les tirets et espaces sont considérés comme étant des séparateurs
	 * @param string $pExpressions l'expression à capitaliser
	 */
	public  static  function capitalize ($pExpression){
		$final = array ();
		$spacesPart = explode (' ', $pExpression);
		foreach ($spacesPart as $spacePart){
			$tiretParts = explode ('-', $spacePart);
			$finalTiret = array ();
			foreach ($tiretParts as $tiretPart){
				$finalTiret[] = self::_capitalizeString ($tiretPart);				
			}
			$final[] = implode ('-', $finalTiret);
		}
		return implode (' ', $final);
	}

	/**
	 * Capitalisation d'une chaine sans tenir compte des espaces ou autres élements de séparation
	 * @param string $pPart l'élément a capitaliser
	 * @return string
	 */
	public  static  function _capitalizeString ($pPart){
		$minuscules = array ('à', 'a', 'du', 'de', 'des', 'le', 'la', 'les');
		if (in_array ($pPart, $minuscules)){
			return strtolower ($pPart);
		}
	    return strtoupper (substr ($pPart, 0, 1)).strtolower (substr ($pPart, 1));
	}
		
	/**
	 * Formattage d'un numéro de sécurité sociale
	 * @return string $pNum si cela représente bien un numéro de sécurité sociale.
	 */
	public  static function getNumeroSecuriteSociale ($pNum, $pCle){
		$v_numero=$pNum;
		$v_final=0;
	
		if ($pCle< 1 ||  $pCle> 97) {
			throw new CopixException (_i18n ('copix:copixformatter.error.incorrectkey'));
		}
		if (substr($pNum,5,2)=='2A'){
			$v_numero = substr($pNum,0,6)+'19'+ substr($pNum,8,4);
		} else {
			if (substr($pNum,5,2)=='2A'){
				$v_numero = substr($pNum,1,6)+'19'+substr($pNum,8,4);
			} else {
				$v_numero = $pNum;
			}
		}
	
		$v_final = $v_numero + $pCle;
		$div = floatval ($v_final) / floatval (97);
		if (strpos($div, '.')) {
			throw new CopixException (_i18n ('copix:copixformatter.error.incorrectsecu'));
		}
		return $pNum.$pCle;
	}
	
	/**
	 * Formattage d'un rib
	 * @param string $pRibString la chaine qui représente un RIB. 
	 */
	public static function getRib ($pRibString){
		$pRibString = trim($pRibString);
		$pRibString = str_replace (array (' ', '-', '/', '\\', '|', '.'), '/', $pRibString);
		$pRibString = strtolower ($pRibString);
		$parts = explode ('/', $pRibString);
		if (count ($parts) !== 4 && count ($parts) !== 1){
			throw new CopixException (_i18n ('copix:copixformatter.error.rib.nbelement', $pRibString));
		}
		if (count($parts)=== 1){
			if (strlen ($pRibString) !== 23){
			   throw new CopixException (_i18n ('copix:copixformatter.error.rib.badlenght', $pRibString));
			}
		}
		if (count ($parts) === 4){
			if ((strlen ($parts[0]) != 5) || 
			    (strlen ($parts[1]) != 5) || 
			    (strlen ($parts[2]) != 11) || 
			    (strlen ($parts[3]) != 2)){
               throw new CopixException (_i18n ('copix:copixformatter.error.rib.badelementlenght', $pRibString));
			}            
		}

		if (count ($parts) === 4){
			$banqueRib  = $parts[0];
			$guichetRib = $parts[1];
			$compteRib  = $parts[2];
			$clefRib    = $parts[3]; 
		}else{
			$banqueRib  = substr ($pRibString, 0, 5);
			$guichetRib = substr ($pRibString, 5, 5);
			$compteRib  = substr ($pRibString, 10, 11);
			$clefRib    = substr ($pRibString, -2); 
		}

        $ribCheckString = $banqueRib.$guichetRib.$compteRib.$clefRib; 
        $ribCheckString = strtr($ribCheckString, "abcdefghijklmnopqrstuvwxyz",
                                            "12345678912345678923456789"); 


        $Coef=array(62,34,3);
        $s=0;
        for ($i=0, $s=0; $i<3; $i++){
           $Code=substr($ribCheckString, 7*$i, 7);
           $s += (0+$Code) * $Coef[$i];
        }
        $clef = 97-($s%97);
        
       if ($clef != $clefRib){
       		throw new CopixException (_i18n ('copix:copixformatter.error.rib.badelementlenght2', array($clefRib, $clef)));
       }
       return $banqueRib.' '.$guichetRib.' '.$compteRib.' '.$clefRib;
    }

	/**
	 * Formattage d'une adresse e mail.
	 */
	public  static function getMail ($pMail){
       if (!ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.
       '@'.
       '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.
       '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',
       $pMail)){
       		throw new CopixException (_i18n ('copix:copixformatter.error.badmail', $pMail));    	       	
       }
       return strtolower ($pMail);
	}

	/**
	 * Formattage d'un numero permettant de communiquer (téléphone, fax, portable...)
	 * Cette fonction est destinée à être appelée par une autre
	 *
	 * @param string $pNumber
	 * @param string $pSeparator
	 * @return chaine de caractère avec le numéro formatté correctement avec separator entre les couples de chiffres
	 */
	public static function getCommunicationNumber($pNumber, $pSeparator = ' ') {
		$pNumber = str_replace ($pSeparator, '', $pNumber);
		if (!preg_match("#^(0|\+33)[1-9]\d{8}$#", $pNumber)){
			throw new CopixException (_i18n ('copix:copixformatter.error.badphonenumber', $pNumber));
		}else{
		    if (preg_match("#^\+33#",$pNumber)) {
		        $intNbBegin = 4;
		    } else {
		        $intNbBegin = 2;
		    }
		    return substr ($pNumber, 0, $intNbBegin).$pSeparator.substr ($pNumber, $intNbBegin+0, 2).$pSeparator.substr ($pNumber, $intNbBegin+2, 2).$pSeparator.substr ($pNumber, $intNbBegin+4, 2).$pSeparator.substr ($pNumber, $intNbBegin+6, 2);
		}		
	}
	
	/**
	 * Formattage d'un numéro de téléphone
	 * @param string $pTelephone le téléphone à vérifier
	 * @param string $pSeparator séparateur
	 * @return chaine de caractère avec le téléphone formatté correctement avec separator entre les couples de chiffres 
	 */
	public  static function getTelephone ($pTelephone, $pSeparator = ' '){
	    try {
		    $pTelephone = self::getCommunicationNumber($pTelephone,$pSeparator);
		    return $pTelephone;
	    } catch (Exception $e) {
	        if ($e->getMessage() == _i18n ('copix:copixformatter.error.badphonenumber', $pTelephone)) {
	            throw new CopixException (_i18n ('copix:copixformatter.error.badtel', $pTelephone));
	        } else {
	            throw $e;
	        }
	    }
	}
	
	/**
	 * Formattage d'un numéro de fax
	 * @param string $pFax le fax à vérifier.
	 * @param string $pSeparator séparateur
	 * @return chaine de caractère avec le fax formatté correctement avec separateur entre les couples de chiffres
	 */
	public  static function getFax ($pFax, $pSeparator = ' '){
	    try {
		    $pFax = self::getCommunicationNumber($pFax,$pSeparator);
		    return $pFax;
	    } catch (Exception $e) {
	        if ($e->getMessage() == _i18n ('copix:copixformatter.error.badphonenumber', $pFax)) {
	            throw new CopixException (_i18n ('copix:copixformatter.error.badfax', $pFax));
	        } else {
	            throw $e;
	        }
	    }
	}

	
	/**
	 * Compresse un mot à X caractères
	 *
	 * @param string	$pVarContent	Contenu de la variable
	 * @param int		$pMaxChars		Nombre maximum de caractères autorisés
	 * 	 * @return unknown
	 */
	static public function getReduced ($pVarContent, $pMaxChars = 30){
		//Tableau contenant la liste des séparateurs à supprimer de la chaine
		static $separators = array ("-", "_", '|', '@', '.');

		if (strlen ($pVarContent) <= $pMaxChars){
			return $pVarContent;
		}

		//Supression des séparateurs et transfo de la chaine en capitalisée.
		$pVarContent = str_replace ($separators, '-', $pVarContent);
		$varParts = array ();
		foreach (explode ('-', $pVarContent) as $element){
			foreach (self::explodeCapitalized ($element) as $subElement){
				$varParts[] = self::capitalize ($subElement);
			}
		}
		$pVarContent = implode ('', $varParts);

		//Parties finales à traiter
		while (count ($varParts) > 1){
			$varParts = self::_removeSmallest ($varParts);
			if (self::_strlenTab ($varParts) < $pMaxChars){
				return implode ('', $varParts); 
			}
		}

		return substr (implode ('', $varParts), self::_strlenTab ($varParts) - $pMaxChars);		
	}
	
	/**
	 * Supprime la plus petite chaine du tableau. A taille équivalente supprime la première
	 * @param  array	$pParts	le tableau de chaines de caractères
	 * @return array 		
	 */
	static private function _removeSmallest ($pParts){
		$smallest = null;
		foreach ($pParts as $key=>$element){
			if ($smallest === null ||  (strlen ($element) < strlen ($pParts[$smallest]))){
				$smallest = $key;
			}
		}
		
		unset ($pParts[$smallest]);
		return $pParts;
	} 
	
	/**
	 * Cette fonction compte le nombre de caractères contenus dans le tableau. (n'est pas récursif)
	 * @param	array	$pArToCalc	Le tableau qui contient les chaines de caractères
	 * @return int
	 */
	static private function _strlenTab ($pArToCalc){
		return strlen (implode ('', (array) $pArToCalc));
	}

	/**
	 * Explose une chaine capitalisée en un tableau.
	 *
	 * @param string $pCapitalizedString	la chaine à exploser
	 * @return array
	 */
	static public function explodeCapitalized ($pCapitalizedString){
		if (strlen ($pCapitalizedString) == 0){
			return array ();
		}

		$stack = '';
		$parts = array ();
		
		$stateCount = 0;
		$stateUpper = ctype_upper ($pCapitalizedString[0]);

		//parcours de la chaine pour trouver les éléments de rupture 
		// et les mettre dans un tableau
		for ($i = 0; $i<strlen ($pCapitalizedString); $i++){
			if (ctype_alpha ($pCapitalizedString[$i])){
				$newStateUpper = ctype_upper ($pCapitalizedString[$i]);
				//Si c'est une minuscule, et que le dernier état est une minuscule, on stack sans se poser de questions.
				if (! $newStateUpper && !$stateUpper){
					$stack .= $pCapitalizedString[$i];
					$stateCount++;
				}elseif (!$newStateUpper && $stateUpper){
					//Si c'est une minuscule et que le dernier état une majuscule, alors on procède à une rupture
					if (strlen (substr ($stack, 0, -1))){
						$parts[] = substr ($stack, 0, -1);
					}
					$stack = substr ($stack, -1).$pCapitalizedString[$i];
					$stateCount = 1;
				}elseif ($newStateUpper && $stateUpper){
					//Si c'est une majuscule et que le dernier était une majuscule, on stack une majuscule
					$stack .= $pCapitalizedString[$i];
					$stateCount++;
				}elseif ($newStateUpper && !$stateUpper){
					//Si c'est une majuscule et que le dernier était une minuscule, rupture
					if (strlen ($stack)){
						$parts[] = $stack;
					}
					$stack = $pCapitalizedString[$i];
				}
				$stateUpper = $newStateUpper;
			}else{
				//ce n'est pas une lettre, on ajoute à la pile courante
				$stateCount++;
				$stack .= $pCapitalizedString[$i];
			}
		}

		$parts[] = $stack;
		return $parts;
	}

	/**
	 * Retourne un booléen formatté en chaine de caractère
	 *
	 * @param boolean	$value	le booléen à convertir 	
	 * @return string
	 */
	static public function getBool ($value){
		return $value ? _i18n ('copix:common.type.boolean.true') : _i18n ('copix:common.type.boolean.false');
	}
}
?>