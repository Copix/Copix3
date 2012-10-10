<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Nicolas Bastien
 */

/**
 * Route_Mail envoit les données saisies par mail à l'adresse saisie en paramètre
 * 
 * @package copix
 * @subpackage forms
 * @author Nicolas Bastien
 */
class Route_Mail extends CopixAbstractFormRoute {
	
	protected $_mailTitle;
	protected $_mailTemplate;
	protected $_mailConfirmTemplate;
	
	protected $_mailEnvoiConfirmation = '';
	
	
	public function __construct($pForm) {
		parent::__construct($pForm);
		
		$this->_mailTitle = $pForm->getTitle();
		$this->_formContentTemplate = $pForm->getContentTemplate();
		$this->_mailTemplate = $pForm->getMailTemplate();
		$this->_mailConfirmTemplate = $pForm->getMailConfirmTemplate();
		
		//Récupération du mail de confirmation
		$idForm = $pForm->getField('cf_id')->getValue();
        if (isset($this->_params['route_confirm_id'])) {
            $field = $pForm->getField('cfc_' . $idForm . '_' . $this->_params['route_confirm_id']);
            if ($field != false) {
                $this->_mailEnvoiConfirmation = $field->getValue();
            }
        }
	}
	
	/**
	 * (non-PHPdoc)
	 * @see forms/ICopixFormRoute#getFormParams()
	 */
	public static function getFormParams($arCfRouteParams = array()) {
		
		$form = new CopixFormLight('form_route_params');
		$service = new Form_Service();
		
		$form->setTitle('Paramètre de l\'envoie d\'email');
		
		$form->attachField ('route_class', _field ('hidden'), array ())
			 ->attachField ('editId', _field ('hidden'), array ())
			 ->attachField ('route_to', _field ('varchar', array('extra'=>'style="width:100%"')), 
							array ('label'=>'Adresse d\'envoi :', 'require'=>true))
			 ->attachField ('route_cc', _field ('varchar', array('extra'=>'style="width:100%"')), 
							array ('label'=>'Copie :'))
			 ->attachField ('route_cci', _field ('varchar', array('extra'=>'style="width:100%"')), 
							array ('label'=>'Copie cachée :'))
			 ->attachField ('route_confirm_id', _field ('select', array('values'=>$service->getFormFieldByType('email'),'extra'=>'style="width:100%"')), 
							array ('label'=>'Envoie de la confirmation :'));

        $helpImageSrc = _resource('form|img/help.png');

$legend = <<<STR_LEGEND
<p class="copix_help">
<img src="$helpImageSrc" class="p_icon"/>
&nbsp;&nbsp;Envoit des données saisie aux adresses indiquées.<br/><br/>
Le dernier paramètre "Envoie de la confirmation" sert à déterminer sur quel champs le programme doit se baser pour envoyer un mail de confirmation à l'utilisateur.<br/>
Pour le remplir vous devez d'abord ajouter un champ de type "email" à votre formulaire, ce champ apparaitra ensuite dans la liste déroulante.	
</p>
STR_LEGEND;
		
		$form->setLegend($legend);					
							
		$form->populate(array('route_class'=>'Route_Mail', 'editId'=>_request('editId')));
		$form->populate($arCfRouteParams);

		return $form;
	}
	
	
	public static function formatParams() {
		$arParams = array(
			'route_to' => _request('route_to'),
			'route_cc' => _request('route_cc'),
			'route_cci' => _request('route_cci'),
			'route_confirm_id' => _request('route_confirm_id')
		);
		
		array_map('trim', $arParams);
		
		return serialize($arParams);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see forms/ICopixFormRoute#checkParams()
	 */
	public function checkParams() {
		$errors = array();
		$emailValidator = _validator ('email');
//TODO	
		if ($this->_params === false) {
			$errors[] = "Vous devez saisir en paramètre l'adresse d'envoi des mails. (Vous pouvez en saisir plusieures en les séparant par des ;)";
			return $errors;
		}
		
		foreach ($this->_params as $mail) {
			if ($emailValidator->check($mail) !== true) {
				$errors[] = "L'adresse suivante n'est pas valide : " . $mail;
			}
		}
		
		return (count($errors) ? $errors : true);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see forms/CopixAbstractFormRoute#_process()
	 */
	protected function _process($arData) {
		if (!isset($this->_params['route_to']) || empty($this->_params['route_to'])) {
			return;
		}
        $cc = isset($this->_params['route_cc']) ? $this->_params['route_cc'] : null;
        $cci = isset($this->_params['route_cci']) ? $this->_params['route_cci'] : null;
		$this->_sendMails($this->_params['route_to'], $cc, $cci, $arData);
	}
	
	/**
	 * Envoit du mail et de l'accusé
	 * @param $to
	 * @param $arData
	 * @return void
	 */
	protected function _sendMails($to, $cc, $cci, $arData){
		
		if (empty($cc)) {
			$cc = CopixConfig::get ('form|cc');
		}
		if (empty($cci)) {
			$cci = CopixConfig::get ('form|cci');
		}
		$strInfoEnvoi = '';
			
		//Gestion du destinataire forcé
		$destinataireForce = CopixConfig::get('form|destinataireForce');
		if (!empty($destinataireForce)) {
			$strInfoEnvoi = <<<STR_INFO_ENVOI
<pre>
	Infos d'envoi :
		To: $to
		Cc: $cc
		Cci: $cci
</pre>		
STR_INFO_ENVOI;
			$to = $destinataireForce;
		}
	
		$tpl = new CopixTpl();
		$tpl->assign('date', date (CopixI18N::getDateTimeFormat ()));
		$tpl->assign('formTitle', $this->_mailTitle);
		$tpl->assign('arData', $arData);
		$tpl->assign('strInfoEnvoi', $strInfoEnvoi);
		$tpl->assign('formContent', $tpl->fetch($this->_formContentTemplate));
		$message = utf8_decode($tpl->fetch($this->_mailTemplate));

		$monMail = new CopixHTMLEMail ($to, $cc, $cci, utf8_decode($this->_mailTitle) . ' (Formulaire CMS)', $message);
		$monMail->send ();
		
		if(!empty($this->_mailEnvoiConfirmation)) {
			//Envoi du mail de confirmation
			$accuse = utf8_decode($tpl->fetch ($this->_mailConfirmTemplate));
			$monMail = new CopixHTMLEMail ($this->_mailEnvoiConfirmation, null, null, 'Confirmation : ' . utf8_decode($this->_mailTitle), $accuse);
			$monMail->send ();
		}
	}
	
	
}