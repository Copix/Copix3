<?php
/**
 * @package		CodeSniffer
 * @subpackage  Copix
 * @author		Nicolas Bastien
 * @copyright	CopixTeam
 * @link		http://copix.org,
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
	throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');
}

/**
 * Copix Coding Standard.
 *
 * Classe décrivant les standards de codage pour Copix
 * Les normes de dévelopement sont décrites en détails ici : {@link http://www.copix.org/index.php/wiki/Normes_de_developpement/fr}
 *
 * @package  CodeSniffer
 * @subpackage   Copix
 */
class PHP_CodeSniffer_Standards_Copix_CopixCodingStandard  extends PHP_CodeSniffer_Standards_CodingStandard {

    /**
     * Renvoit la liste des sniffs externes à utiliser
     *
     * @return array
     */
    public function getIncludedSniffs() {
        return array(
			'Generic/Sniffs/PHP/DisallowShortOpenTagSniff.php',
			'Generic/Sniffs/Classes/DuplicateClassNameSniff.php',
			'Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff.php',
			'Generic/Sniffs/PHP/LowerCaseConstantSniff.php',

			'MySource/Sniffs/Debug/FirebugConsoleSniff.php',

			'PEAR/Sniffs/NamingConventions',
			'PEAR/Sniffs/Commenting/FunctionCommentSniff.php',
			'PEAR/Sniffs/Commenting/InlineCommentSniff.php',
			'PEAR/Sniffs/ControlStructures/ControlSignatureSniff.php',
			'PEAR/Sniffs/Files/LineEndingsSniff.php',
			'PEAR/Sniffs/Functions/ValidDefaultValueSniff.php',
			'PEAR/Sniffs/WhiteSpace/ScopeClosingBraceSniff.php',

			'Squiz/Sniffs/Functions/GlobalFunctionSniff.php',
        );

    }//end getIncludedSniffs()


}//end class
