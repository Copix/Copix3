<?php
/**
 * @package copix
 * @subpackage forms
 * @author Nicolas Bastien
 */

/**
 * Classe de rendu pour les CopixFormLight
 *
 * @package copix
 * @subpackage forms
 */
class CopixFormLightRenderer {
	
	/**
	 * Le formulaire associé
	 *
	 * @var CopixFromLight
	 */
	protected $_form = null;
	
	/**
	 * Construction du renderer
	 *
	 * @param CopixFormLight $pForm
	 */
	public function __construct (CopixFormLight $pForm) {
		$this->_form = $pForm;
	}
	
	/**
	 * Retourne le contenu HTML de l'ensemble du formulaire
	 *
	 * @return string
	 */
	public function all () {
		return $this->header ().$this->errors ().$this->body ().$this->footer ();
	}

	/**
	 * Retourne le contenu HTML d'une partie du formulaire
	 * 
	 * @param $pFields array un tableau contenant les noms des champs à afficher
	 * @return string
	 */
	public function partial($pFields) {
		$tpl = new CopixTpl ();
		
		$strIdPrefix = 'cfc_' . str_replace('cms_form_', '', $this->_form->getId()) . '_';
	
		$fields = array();
		foreach ($this->_form->getFields() as $field) {
			if (in_array(str_replace($strIdPrefix, '', $field->getName()), $pFields)) {
				$fields[] = $field;
			}
		}
		
		$tpl->assign ('fields', $fields);
		
		return $tpl->fetch ($this->_form->getBodyTemplate());
	}
	
	/**
	 * Retourne le code html pour l'en tête du formulaire
	 *
	 * @param string $pExtras Chaine à ajouter à la balise form (class...) 
	 * @return string
	 */
	public function header ($pExtras = '') {
		$formExtra = $this->_form->getFieldExtra('form');
		if (!empty($formExtra) && array_key_exists('extra', $formExtra)){
			$pExtras .= " " . $formExtra['extra'];
		}
		
		if ($this->_form->isMultipart()) {
			$pExtras = $pExtras . ' enctype="multipart/form-data" ';
		}
		
		return '<form name="' . $this->_form->getId () . '" id="' . $this->_form->getId ()
				. '" method="' . $this->_form->getMethod() . '" action="' . _url ($this->_form->getSubmitUrl()).'" ' .$pExtras.' >';
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
	 * Récupération de la partie erreurs du formulaire
	 *
	 * @return string
	 */
	public function errors () {
		$toReturn = '';
		$errors = $this->_form->getFormErrors ();
		if ($errors != null && count ($errors) > 0) {
			$toReturn = '<div class="errorMessage"><ul><li>'.implode ('</li><li>', $errors).'</li></ul></div>';
		}
		return $toReturn;
	}
	
	/**
	 * Récupération du corp du formulaire
	 *
	 * @param string $pTemplate le template a utiliser (null par défaut, ce qui fera pointer sur 'copix:templates/copixform.tpl') 
	 * @return string
	 */
	public function body () {
		$tpl = new CopixTpl ();
		$tpl->assign ('title', $this->_form->getTitle());
		$tpl->assign ('hiddenFields', $this->_form->getHiddenFields());
		$tpl->assign ('fields', $this->_form->getFields());
		$tpl->assign ('legend', $this->_form->getLegend());
        $tpl->assign ('jsCode', $this->_form->getJSCode());
		return $tpl->fetch ($this->_form->getBodyTemplate());
	}
	
}