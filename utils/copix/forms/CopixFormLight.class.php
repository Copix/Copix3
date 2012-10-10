<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Nicolas Bastien
 */

/**
 * CopixForm version light
 * @package		copix
 * @subpackage	forms
 */
class CopixFormLight {
	
	/**
	 * identifiant du formulaire (utilisé dans le code html généré)
	 * @var string
	 */
	protected $_id = null;
	
	/**
	 * Liste des champs du formulaire
	 * @var array
	 */
	protected $_fields = array ();
	
	protected $_isMultipart = false;
	
	/**
	 * Url du formulaire (pour le réaffichage en cas d'erreur)
	 * @var string
	 */
	protected $_formUrl = null;
	
	/**
	 * Titre du formulaire
	 * @var string
	 */
	protected $_formTitle = null;
	
	/**
	 * Méthode de soumission du formulaire
	 * @var 
	 */
	protected $_method = 'POST';
	
	/**
	 * Url pour le traitement du formulaire
	 * @var string au format copixurl
	 */
	protected $_submitUrl = null;
	
	/**
	 * Liste des éventuelles erreurs
	 * @var array
	 */
	protected $_formErrors = null;
	
	/**
	 * Renderer gérant l'affichage du formulaire
	 * @var unknown_type
	 */
	protected $_renderer = null;
	
	/**
	 * Template pour le rendu du corps du formulaire
	 * @var string
	 */
	protected $_bodyTpl = 'copix:templates/form/form.body.tpl';
	
	/**
	 * Template pour le rendu d'un ligne (cas ou le champs n'a pas de template associé)
	 * @var string
	 */
	protected $_rowTpl = 'copix:templates/form/form.row.tpl';
	
	/**
	 * Template pour le bouton submit
	 * @var string
	 */
	protected $_submitTpl = 'copix:templates/form/form.submit.tpl';
	
	
	protected $_contentTpl = 'copix:templates/form/form.content.tpl';
	
	protected $_mailTpl = 'copix:templates/form/form.mail.tpl';
	
	protected $_mailConfirmTpl = 'copix:templates/form/form.mail.confirm.tpl';
	
	/**
	 * Légende du formulaire
	 * @var string
	 */
	protected $_formLegend = null;
	
	/**
	 * Tableau stockant les extra des champs du formulaire en fonction du type
	 * permet de tunner les templates
	 * @var array
	 */
	protected $_fieldExtra = array();
	
	/**
	 * Extra par défaut
	 * @var array
	 */
	protected $_defaultFieldExtra = array();
	
    /**
     * code javascript pour la gestion du formulaire
     * @var string
     */
    protected $_jsCode = '';

	/**
	 * Constructeur qui stock l'id du formulaire
	 *
	 * @param string $pId L'id mis en session pour le formulaire
	 */
	public function __construct ($pId = null) {
		$this->_id = $pId;
		$this->configure();
	}
	
	/**
	 * Configuration du formulaire
	 * Permet de créer des objets formulaires
	 * @return void
	 */
	protected function configure() {}
	
	/**
	 * Retourne l'identifiant du formulaire
	 * @return int
	 */
	public function getId () {
		return $this->_id;
	}	
	
	/**
	 * Retourne la méthode d'envoi du formulaire
	 * @return string
	 */
	public function getMethod() {
		return $this->_method;
	}
	
	/**
	 * Setter pour la méthode d'envoie
	 * @param $pMethod
	 * @return CopixFormLight
	 */
	public function setMethod($pMethod) {
		$pMethod = strtoupper($pMethod);
		//TODO améliorer, possibilité de faire de l'ajax ?
		if (in_array($pMethod, array('POST', 'GET'))) {
			$this->_method = $pMethod;
		}
		return $this;
	}
	
	/**
	 * Récupère l'url de traitement du formulaire
	 *
	 * @return string
	 */
	public function getSubmitUrl () {
		return $this->_submitUrl;
	}
	
	/**
	 * Attribut l'url de traitement du formulaire
	 *
	 * @param string $pUrl
	 */
	public function setSubmitUrl ($pUrl) {
	    if ($pUrl == null) {
	        $pUrl = '#';
	    }
		$this->_submitUrl = _url ($pUrl);
		return $this;
	}
	
	public function isMultipart() {
		return $this->_isMultipart;
	}
	
	public function setBodyTemplate($pTemplate) {
		$this->_bodyTpl = $pTemplate;
	}
	
	public function getBodyTemplate() {
		return $this->_bodyTpl;
	}
	
	public function setRowTemplate($pTemplate) {
		$this->_rowTpl = $pTemplate;
		return $this;
	}
	
	public function setSubmitTemplate($pTemplate) {
		$this->_submitTpl = $pTemplate;
		return $this;
	}
	
	public function getMailTemplate() {
		return $this->_mailTpl;
	}
	
	public function setMailTemplate($pTemplate) {
		$this->_mailTpl = $pTemplate;
		return $this;
	}
	
	public function getContentTemplate() {
		return $this->_contentTpl;
	}
	
	public function setContentTemplate($pTemplate) {
		$this->_contentTpl = $pTemplate;
		return $this;
	}
	
	public function getMailConfirmTemplate(){
		return $this->_mailConfirmTpl;	
	}
	
	public function setMailConfirmTemplate($pTemplate) {
		$this->_mailConfirmTpl = $pTemplate;
		return $this;
	}
	
	public function setTitle($pTitle) {
		$this->_formTitle = $pTitle;
		return $this;
	}
	
	public function getTitle() {
		return $this->_formTitle;
	}
	
	public function setLegend($pLegend) {
		$this->_formLegend = $pLegend;
		return $this;
	}
	
	public function getLegend() {
		return $this->_formLegend;
	}

    public function addJSCode($pJSCode) {
        $this->_jsCode .= $pJSCode;
    }

    public function getJSCode() {
        return $this->_jsCode;
    }
	
	/**
	 * Renvoit l'extra d'un champs en fonction de son type
	 * @return array
	 */
	public function getFieldExtra($pTypeElement) {
		if (isset($this->_fieldExtra[$pTypeElement])) {
			return $this->_fieldExtra[$pTypeElement];
		}
		return $this->_defaultFieldExtra;
	}
	
	/* *** Gestion des erreurs *** */
	
	/**
	 * Ajoute des messages des erreurs sur le formulaire ou sur les champs qui le compose
	 *
	 * @param mixed $pErrors soit une chaine (sera attribué au formulaire) 
	 *                       soit un tableau clé=>valeur 
	 *                       la clé etant un champs
	 *                       la valeur etant le message
	 *                       si le champs n'existe pas l'erreur est ajouté 
	 *                       pour le formulaire
	 */
	public function setErrors ($pErrors) {
		$this->_formErrors = new CopixErrorObject();
		foreach ($pErrors as $key=>$error) {
			if (($field = $this->getField ($key)) !== false) {
				$field->addError ($error);
			} else {
				$this->_formErrors->addErrors($error);
			}
		}
	}
	
	/**
	 * Renvoit la liste des erreurs
	 * @return array
	 */
	public function getFormErrors () {
		return $this->_formErrors;
	}
	
	
	/**
	 * Attache un champ au formulaire
	 *
	 * @param    string       $pName Nom du champs
	 * @param    ICopixField  $pField un type de champ
	 * @param    array        $pParams liste de paramètre du champ
	 * @return   CopixForm
	 */
	public function attachField ($pName, ICopixField $pField, $pParams = array ()) {
		if (!isset ($this->_fields[$pName])) {
			$this->_fields[$pName] = new CopixFieldContainer($pName, $pParams);
		}
		$this->_fields[$pName]->setParams ($pParams);
		$this->_fields[$pName]->setField ($pField);
		
		if (isset($pParams['rowTpl'])) {
			$this->_fields[$pName]->setRowTemplate ($pParams['rowTpl']);
		} elseif ($pField->getType() == 'submit') {
			$this->_fields[$pName]->setRowTemplate ($this->_submitTpl);
		}else {
			$this->_fields[$pName]->setRowTemplate ($this->_rowTpl);
		}
		
		return $this;
	}

    /**
     * Ajoute un champ provenant d'un autre formulaire (utilisé pour merger des formulaires par exemple dans la route dynamique)
     * @param CopixFieldContainer $field
     */
    public function addField(CopixFieldContainer $field) {
        $name = $field->getName();
        $this->_fields[$name] = $field;
        return $this;
    }
	
	/**
	 * Ajout d'un bloc
	 * Un bloc est en fait un sous formulaire
	 * 
	 * @param object $pBloc
	 * @param object $pParams[optional]
	 * @return   CopixForm
	 */
	public function attachBloc(CopixFormLight $pBloc, $pParams = array()) {
		$this->_fields[$pBloc->getId()] = $pBloc;
	}
	
	/**
	 * Mise à jour des champs du formulaires avec les champs passé en paramètres
	 * @param $arValues
	 * @return void
	 */
	public function populate($arValues = array()) {
        if (!is_array($arValues)) {
            $arValues = get_object_vars($arValues);
        }

        foreach ($this->_fields as $fieldName => $field) {
			if ($field instanceof CopixFormLight) {
				//Gestion des blocs
				$field->populate($arValues);
			} else {
				if (isset($arValues[$fieldName])) {
					$this->_fields[$fieldName]->setValue($arValues[$fieldName]);
				}
			}
		}
	}
	
	/* *** Affichage *** */
	
	public function getRenderer () {
		//Instanciation du renderer
		if ($this->_renderer == null) {
			$this->_renderer = new CopixFormLightRenderer ($this);
		}
		return $this->_renderer;
	}
	
	/**
	 * Retourne le HTML de tout les champs
	 *
	 * @param string $pTemplate un template ou afficher les fields
	 * @return string le HTML
	 */
	public function getAllHTML () {
		return $this->getRenderer()->all();
	}
	
	public function getPartialHTML($pFields) {
		return $this->getRenderer()->partial($pFields);
	}
	
	/**
	 * Retourne un champs (type CopixFieldContainer)
	 *
	 * @param string $pName le nom du champs
	 * @return CopixFieldContainer 
	 */
	public function getField ($pName) {
		if (isset ($this->_fields[$pName])) {
			return $this->_fields[$pName];
		}
		return false;
	}
	
	/**
	 * Retourne la dernière url ou a été affiché le formulaire
	 *
	 * @param array $pParams paramètre a rajouter a l'url
	 * @return string la dernière url du formulaire avec les paramètres passé
	 */
	public function getFormUrl ($pParams = array ()) {
		return _url ($this->_formUrl, $pParams);
	}
	
	
	/**
	 * Renvoi le header du formulaire
	 *
	 * @return string le HTML
	 */
	public function getHeader () {
		return $this->getRenderer()->header();
	}
	
	/**
	 * Renvoi le footer du formulaire
	 *
	 * @return string le HTML
	 */
	public function getFooter () {
		return $this->getRenderer()->footer();
	}
	
	/**
	 * Lance les différents check du formulaire
	 *
	 * @return bool true ou false
	 */
	public function check () {
		$toReturn = true;
		foreach ($this->_fields as $field) {
			$checkResult = $field->check ();
			if ($checkResult !== true) {
				$toReturn = false;
				$this->_formErrors[] = $field->getLabel() . ' ' .$checkResult->asString();
			}
		}
		return $toReturn;
	}
	
	/**
	 * Renvoit les valeurs des champs du formulaire
	 * @return array
	 */
	public function getValues() {
		$toReturn = array();
		foreach ($this->_fields as $field) {
			if ($field instanceof CopixFormLight) {
				//Gestion des blocs
				$toReturn = array_merge($toReturn, $field->getValues());
			} else {
				$toReturn[$field->getName()] = $field->getValue();
			}	
		}
		return $toReturn;
	}
	
	/**
	 * Renvoit les labels des champs du formulaire
	 * @return array
	 */
	public function getLabels() {
		$toReturn = array();
		foreach ($this->_fields as $field) {
			if ($field instanceof CopixFormLight) {
				//Gestion des blocs
				$toReturn = array_merge($toReturn, $field->getLabels());
			} else {
				$toReturn[$field->getName()] = $field->getLabel();
			}
		}
		return $toReturn;
	}
	
	/**
	 * Renvoit la liste des champs du formulaire
	 * @return array
	 */
	public function getFields () {
		$toReturn = array();
		foreach ($this->_fields as $field) {
			if ($field->getField()->getType() != 'hidden') {
				$toReturn[] = $field;
			}
		}
		return $toReturn;
	}
	
	/**
	 * Renvoit la liste des champs cachés du formulaire
	 * @return array
	 */
	public function getHiddenFields () {
		$toReturn = array();
		foreach ($this->_fields as $field) {
			if ($field->getField()->getType() == 'hidden') {
				$toReturn[] = $field;
			}
		}
		return $toReturn;
	}
	
	/**
	 * Renvoit un string de séparation pour l'affichage des éléments à plusieures valuers (radio, checkbox)
	 * @param $pOrientation
	 * @return string
	 */
	public function getValueSeparator($pOrientation) {
		if ($pOrientation == 1) {
			return '<br/>';
		}
		return '';
	}
	
	/**
	 * Affichage d'un bloc
	 */
	public function getRow() {
		return $this->getRenderer()->partial($this->getFields);
	}
	
}