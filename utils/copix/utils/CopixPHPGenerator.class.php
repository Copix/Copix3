<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Gérald Croës
 * @see			http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */
 
/**
 * Classe qui contient un certain nombre de fonction pour faciliter la génération de PHP
 * tel que l'on peut en trouve à de multiples endroit dans Copix
 * @package copix
 * @subpackage utils
 */ 
class CopixPHPGenerator {
   /**
    * Ajoute les tags PHP en début et fin de chaine
    * @param string $pString la chaine PHP que l'on souhaite intégrer dans les balises PHP
    * @return string la chaine PHP 
    */
   public function getPHPTags ($pString){
      return '<?php '.$pString.' ?>';
   }
   
   /**
    * Code PHP permettant de déclarer une variable avec une valeur
    * @param string $pVariableName le nom de la variable
    * @param mixed $pValue la valeur de la variable
    * @return string le code PHP correspondant
    */
   public function getVariableDeclaration ($pVariableName, $pValue){
      return "$pVariableName = ".var_export ($pValue, true).';';
   }
}
?>