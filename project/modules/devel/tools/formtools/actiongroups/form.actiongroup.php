<?php

_classInclude ('formtools|inputelementfactory');
_classInclude ('formtools|routingrule');

class ActionGroupForm extends CopixActionGroup {

	public function processDefault (){
		
		$form = _class ('formtools|formulaire');
		/**
		 * Création d'un élément inArray
		 */
		$elemInArray = inputElementFactory::get ('inarray', 'couleur', 'Couleur');
		$elemInArray->setValues (array ('rouge', 'vert'));
		$form->addInputElement ($elemInArray);
		
		// Création d'un élément texte
		$form->addInputElement (inputElementFactory::get ('text', 'nom', 'nom'));

		$route = _class ('formtools|route');
		$route->setUrlSuccess (_url ('formtools|default|showresult'));
		$route->setUrlFail (_url ('formtools|form|default', array ('error'=>true)));
		$route->setUrlForm (_url ('formtools|form|default'));
				
		$route->addRule (new RoutingRule ('mailto:bfavre@sqli.com', 'couleur', 'EQUALS', 'rouge'));
		$route->addRule (new RoutingRule ('mailto:brice.favre@gmail.com', 'couleur', 'EQUALS', 'vert'));
		$route->addDefaultRule (new RoutingRule ('mailto:neverhappens@toto.com'));
		
		$newRoute = new Route ($route->asXml ());
		//
		$ppo = _ppo ();
		$ppo->form = $form->showForm ($form->getSessionValues (), (bool) _request ('error'));

		CopixSession::set ('formulaire', $form, 'formtools');
		CopixSession::set ('route', $route, 'formtools');

		return _arPpo ($ppo, 'default.template.php');
	}
	
	public function processNewForm (){
		// Avant tout, il faut intégrer la classe inputelementfactory
		
		// Création du formulaire
		$form = _class ('formtools|formulaire');
		
		// Création des élements
		$elemNom = inputElementFactory::get ('text', 'nom', 'Nom');
		$elemPrenom = inputElementFactory::get ('text', 'prenom', 'Prénom');
		$elemPhone = inputElementFactory::get ('textformat', 'email', 'Adresse e-mail')->setFormat ('email');
		$elemEmail = inputElementFactory::get ('textformat', 'telephone', 'Téléphone')->setFormat ('phone');
		// Ajout des éléments
		$form->addInputElement ($elemNom);
		$form->addInputElement ($elemPrenom);
		$form->addInputElement ($elemPhone);
		$form->addInputElement ($elemEmail);

		// Création de la route avec mise en place des URL
		$route = _class ('formtools|route');
		$route->setUrlSuccess (_url ('formtools|default|showresult'));
		$route->setUrlFail (_url ('formtools|form|newform', array ('error'=>true)));
		$route->setUrlForm (_url ('formtools|form|newform'));
		
		// Ajout des règles
		$route->addDefaultRule (new RoutingRule ('db:form_result'));
		
		// Pour l'affichage, le formulaire se sauvegarde en session
		CopixSession::set ('formulaire', $form, 'formtools');
		CopixSession::set ('route', $route, 'formtools');
				
		$ppo = _ppo ();
		$ppo->form = $form->showForm ($form->getSessionValues (), (bool) _request ('error'));
		return _arPpo ($ppo, 'default.template.php');
	}
	
	public function processFormEssai (){
		$form = _class ('formtools|formulaire');
		
		$elemPhone = inputElementFactory::get ('textformat', 'email', 'Adresse e-mail')->setFormat ('email');
		$elemEmail = inputElementFactory::get ('textformat', 'telephone', 'Téléphone')->setFormat ('phone');
		$elemNom = inputElementFactory::get ('text', 'nom', 'Nom');
		// $elemRib = inputElementFactory::get ('rib', 'prelevement', 'Prélèvement');
		$form->addInputElement ($elemPhone);
		$form->addInputElement ($elemEmail);
		$form->addInputElement ($elemNom);
		// $form->addInputElement ($elemRib);
		
		$route = _class ('formtools|route');
		$route->setUrlSuccess (_url ('formtools|default|showresult'));
		$route->setUrlFail (_url ('#', array ('error'=>true)));
		$route->setUrlForm (_url ('#'));
		
		$route->addRule (new RoutingRule ('ALL', 'mailto:all@toto.com'));
		
		CopixSession::set ('formulaire', $form, 'formtools');
		CopixSession::set ('route', $route, 'formtools');
		
		$ppo = _ppo ();
		$values  = $form->getSessionValues ();
		$values['prelevement'] = _class ('genericzone|rib');
		$ppo->form = $form->showForm ($form->getSessionValues (), (bool) _request ('error'));
		
		return _arPpo ($ppo, 'default.template.php');
	}
	
	public function processFormEssai2 () {
		$ppo = new CopixPPO();
		
		$ppo->formobj = _form ('test');
		
		$fieldVarchar = _field ('varchar')->attach (_validator('email'));
		$fieldVarchar2 = _field ('varchar')->attach (_validator('email'));
		$fieldVarchar3 = _field ('varchar')->attach (_validator('email'));
		
		$ppo->formobj->attachField ('test1', $fieldVarchar, array ('edit'=>false));
		$ppo->formobj->attachField ('test2', $fieldVarchar2);
		$ppo->formobj->attachField ('test3', $fieldVarchar3);
		
		$ppo->form = $ppo->formobj->getRenderer ();
		
		return _arPpo($ppo, 'default.template.php');	
	}
}