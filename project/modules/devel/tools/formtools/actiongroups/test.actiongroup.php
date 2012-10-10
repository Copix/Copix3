<?php

class ActionGroupTest extends CopixActionGroup {

	/**
	 * Initialisation des formulaires et route
	 *
	 */
	function processInit (){
		_classInclude ('formtools|inputelementfactory');
		$form = _class ('formtools|formulaire', array (_url ('formtools||validform'), 'post'));
		$form->addInputElement (inputElementFactory::get ('text', 'nom', 'Nom'));
		$form->addInputElement (inputElementFactory::get ('text', 'prenom', 'Prénom'));
		$form->addInputElement (inputElementFactory::get ('text', 'code_postal', 'code postal'));
		$form->addInputElement (inputElementFactory::get ('telephone', 'telephone', 'Téléphone'));
		
		// Création des valeurs par défaut
		_classinclude ('genericzone|rib');
		$pDefaultRibValues = array ();
		$pDefaultRibValues['RIB'] = new RIB ();
		
		$form->addInputElement (inputElementFactory::get ('rib', 'prelevement', 'Rib Payeur', $pDefaultRibValues));

		$route = _ioClass ('formtools|route');
		_classInclude ('formtools|routingrule');
		
		$route->addRule (new RoutingRule ('BEGINWITH', 'mailto:bfavre@sqli.com', 'code_postal', array ('01','69','42','07','26','73','74')));
		$route->addRule (new RoutingRule ('DEFAULT', 'mailto:brice.favre@gmail.com'));
		
		$route_record = _record ('routeobject');
		$route_record->route = serialize (new CopixSerializableObject ($route));
		_ioDao ('routeobject')->insert ($route_record);
		
		$form_record = _record ('formobject');
		$form_record->form = serialize (new CopixSerializableObject ($form));
		$form_record->route_id = $route_record->id;
		_dump ($form_record);
		$res = _ioDao ('formobject')->insert ($form_record);
		_dump ($res);
		
		$ppo = _ppo ();
		$ppo->form_id = $form_record->id;
		$ppo->route_id = $route_record->id;
		
		return _arPPO ($ppo, 'init.test.template.php');
		//CopixSession::set ('formulaire', $this->_form, 'formtools');
	}
	
}