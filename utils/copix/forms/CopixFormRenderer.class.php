<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Croës Gérald, Salleyron Julien
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */

/**
 * Classe de rendu pour les formulaires
 *
 * @package copix
 * @subpackage forms
 */
class CopixFormRenderer {
	/**
	 * Le formulaire associé
	 *
	 * @var unknown_type
	 */
	private $_form = null;

	/**
	 * Construction du formulaire
	 *
	 * @param unknown_type $pForm
	 */
	public function __construct (CopixForm $pForm) {
		$this->_form = $pForm;
	}

	/**
	 * Appel autoamtique de all () lorsque l'on souhaite manipuler le renderer en tant que chaine de caractère
	 *
	 * @return string
	 */
	public function __toString () {
		return $this->all ();
	}

	/**
	 * Récupère la contenu HTML de l'ensemble du formulaire
	 *
	 * @return string
	 */
	public function all () {
		return $this->header ().$this->errors ().$this->body ().$this->footer ();
	}

	/**
	 * Retourne le code html pour l'en tête du formulaire
	 *
	 * @param array $pParams 
	 * @return string
	 */
	public function header ($pParams = array ()) {
		if (!isset ($pParams['action'])) {
			$pParams['action'] = 'generictools|copixforms|newForm';
		}
		$more = '';
		if (isset ($pParams['uploadedfile']) && $pParams['uploadedfile']) {
			$more = ' enctype="multipart/form-data" ';
		}
                if (isset($pParams['more'])) {
                    $more .= ' '.$pParams['more'];
                }
		return '<form name="'.$this->_form->getId ().'" id="'.$this->_form->getId ().'" '.$more.' method="POST" action="'._url ($pParams['action'], array ('currentForm'=>$this->_form->getId ())).'" >
                        <input type="hidden" name="form_'.$this->_form->getId ().'" id="form_'.$this->_form->getId ().'" value="1" />';
	}

	/**
	 * Récupération du "pied de page" du formulaire
	 *
	 * @return string
	 */
	public function footer () {
		return '</form>';
	}

	/**
	 * Récupération du corp du formulaire
	 *
	 * @param string $pTemplate le template a utiliser (null par défaut, ce qui fera pointer sur 'copix:templates/copixform.tpl') 
	 * @return string
	 */
	public function body ($pTemplate = null) {
		$tpl = new CopixTpl ();
		$tpl->assign ('fields', $this->_form->getFields());
		return $tpl->fetch ($pTemplate === null ? 'copix:templates/copixform.tpl' : $pTemplate);
	}

	
	/**
	 * Rendu d'un champ en particulier
	 *
	 * @param string $pName   le nom du champ dont on souhaite avoir le contenu HTML
	 * @param array  $pParams tableau d'options
	 * @return string
	 */
	public function field ($pName, $pParams = array ()) {
		$toReturn = array ();
		if (is_string ($pParams)) {
			$pParams['kind'] = array ($pParams);
		}
		if (!isset($pParams['kind'])) {
			$pParams['kind'] = array ('all');
		}

		if (!is_array ($pParams['kind'])) {
			$pParams['kind'] = array ($pParams['kind']);
		}

		$field = $this->_form->getField ($pName);
		if ($field != null) {
			foreach ($pParams['kind'] as $kind) {
				if ($kind == 'all' || $kind == 'label') {
					$toReturn[] = $field->getLabel ();
				}
				if ($kind == 'all' || $kind == 'input') {
					$toReturn[] = $field->getHTML ();
				}
				if ($kind == 'all' || $kind == 'errors') {
					if ($field->getErrors () != null) {
						$toReturn[] = '<span class="fieldError">'.$field->getErrors ().'</span>';
					}
				}
				if ($kind == 'value') {
					$toReturn[] = $field->getValue();
				}
			}
		}
		return implode (' ', $toReturn);
	}

	/**
	 * Récupération de la partie erreurs du formulaire
	 *
	 * @return string
	 */
	public function errors () {
		$toReturn = '';
		$errors = $this->_form->getFormErrors ();
		if ($errors != null && count ($errors) > 0) {
			$toReturn = '<div class="errorMessage"><ul><li>'.implode ('</li><li>', $errors->asArray ()).'</li></ul></div>';
		}
		return $toReturn;
	}
}