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
 * Récupération d'une chaine débarrassée des caractères qui ne doivent pas y apparaître
 * 
 * @package		copix
 * @subpackage	filter
 */
class CopixFilterRemove extends CopixAbstractFilter {
	/**
	 * Récupération d'une chaine débarrassée des caractères qui ne doivent pas y apparaître (les 2 paramètres peuvent être utilisés ensemble)
	 *   
	 * @param for String Pour préciser le type de balise (textarea, input, inputSimpleQuote, inputDoubleQuote)
	 * @param charList String Liste de caractères à supprimer
	 */
	public function get ($pValue){
		$toReplace = array();
		if( $this->getParam ('for') ){
			switch( $this->getParam ('for') ){
				case 'textarea':
					$toReplace = array('<', '>');
					break;
				case 'input': // Supprime les 'simple quote' et "double quote"
					$toReplace = array('<', '>', '"', "'");
					break;
				case 'inputSimpleQuote': // Supprime les 'simple quote'
					$toReplace = array('<', '>', "'");
					break;
				case 'inputDoubleQuote': // Supprime les "double quote"
					$toReplace = array('<', '>', '"');
					break;
				default:
					break;
			}
		}
		if( $this->getParam ('charList') ){
			$charList = str_split ($this->getParam ('charList'));
			$toReplace = array_merge ($toReplace , $charList);
		}
		return str_replace ($toReplace, '', _toString ($pValue));
	}
}