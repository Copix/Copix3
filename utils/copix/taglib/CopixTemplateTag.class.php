<?php
/**
* @package		copix
* @subpackage	taglib
* @author		Gérald Croës
* @copyright	2000-2006 CopixTeam
* @link			http://www.copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Classe de base pour gérer les exceptions dans les template
* @package		copix
* @subpackage	taglib
*/
class CopixTemplateTagException extends CopixException {}

/**
* Objet parent des balises développées dans Copix pour CopixTpl
* @package		copix
* @subpackage	taglib
*/
abstract class CopixTemplateTag extends CopixParameterHandler {
	
	/**
	 * Nom du tag.
	 *
	 * @var string
	 */
	private $_tagName;
	
	/**
	 * Initialise le tag.
	 *
	 * @param string $pTagName Nom du tag.
	 */
	public function __construct($pTagName) {
		$this->_tagName = $pTagName;
	}
	
    /**
     * Fonction qui sera en charge de créer le template
     * @param array $pParams la liste des paramètres envoyés au plugin
     * @return string le contenu de la fonction
     */
    abstract public function process ($pParams);
    
    /**
     * Lance une CopixTemplateTagException.
     * 
     * @param array $pErrors les erreurs, @see CopixParameterHandler::_errors.
     */
    protected function _reportErrors($pErrors) {
    	$errors = array();
    	if(isset($pErrors['missing'])) {
    		$errors[] = _i18n('copix:copix.error.tag.missingParameters', implode(",", array_keys($pErrors['missing'])));
    	}
    	if(isset($pErrors['unknown'])) {
    		$errors[] = _i18n('copix:copix.error.tag.unknownParameters', implode(",", array_keys($pErrors['unknown'])));
    	}
    	if(isset($pErrors['invalid'])) {
    		$errors[] = _i18n('copix:copix.error.tag.invalidValues', implode(",", array_keys($pErrors['invalid'])));
    	}
    	throw new CopixTemplateTagException('[tag '.$this->_tagName.']: '.implode("; ", $errors));
    }
    
}

?>