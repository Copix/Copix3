<?php
/**
 * @package copix
 * @subpackage taglib
 * @author Gérald Croës
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Objet parent des balises développées dans Copix pour CopixTpl
 * 
 * @package copix
 * @subpackage taglib
 */
abstract class CopixTemplateTag {
	protected $_params;

	/**
	 * Initialise le tag
	 *
	 * @param string $pTagName Nom du tag
	 */
	public function __construct ($pParams) {
		$this->_params = new CopixParameterHandler ($pParams);
		$this->_params->setParams ($pParams);
	}

    /**
     * Retourne le code HTML généré par le tag
     * 
     * @param array $pParams Paramètres envoyés au tag
     * @return string
     */
    abstract public function process ($pContent = null);
    
	public function getParam ($pName, $pDefault = null, $pType = null, $pDefaultIfNotValidate = false){
		return $this->_params->getParam ($pName, $pDefault, $pType, $pDefaultIfNotValidate);
	}
	public function requireParam ($pName, $pType = null){
		return $this->_params->requireParam ($pName, $pType);
	}
	public function assertParams (){
		$params = func_get_args ();
		return call_user_func_array (array ($this->_params, 'assertParams'), $params);
	}
	public function getParams (){
		return $this->_params->getParams ();
	}
	public function setParams ($pParams){
		return $this->_params->setParams ($pParams);
	}    
	public function getExtraParams(){
		return $this->_params->getExtraParams ();
	}
}