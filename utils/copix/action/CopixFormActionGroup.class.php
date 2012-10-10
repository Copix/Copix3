<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * ActionGroup qui gère les formulaires
 * 
 * @package		copix
 * @subpackage	core
 */
 class CopixFormActionGroup extends CopixActionGroup {
	
	/**
	 * Formulaire en cours de gestion
	 * 
	 * @var object
	 */
	protected $_form;
	
	/**
	 * Surcharge du beforeAction qui instancie le _form et qui met dans le CopixRequest tout les champs du form (form|champs)
	 */
	public function _beforeAction ($pActionName) {
		if (_request ('currentForm') != null) {
			$this->_form = _form (_request ('currentForm'));
			$this->_form->fillFromRequest ();
			if ($this->_form->check () !== true) {
				return _arRedirect ($this->_form->getFormUrl (array ('error_'._request('currentForm')=>'true')));
			}
		}
        return $this->beforeAction ($pActionName);
	}

	/**
	 * Catch les exceptions de check de formulaire générées dans le process de l'actiongroup
	 *
	 * @param object $pException Exception
	 * @return object CopixActionReturn
	 */
	public function _catchActionExceptions ($pException) {
		if ($pException instanceof CopixFormCheckException) {
			$this->_form->setErrors ($pException->getErrors ());
			return _arRedirect ($this->_form->getFormUrl (array ('error_'._request('currentForm')=>'true')));
		} else {
			return $this->catchActionExceptions ($pException);
		}
	}
}