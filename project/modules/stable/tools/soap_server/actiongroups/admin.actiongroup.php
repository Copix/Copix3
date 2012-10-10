<?php
/**
 * @package		tools
 * @subpackage	soap_server
 * @author		Favre Brice
 * @copyright	2001-2008 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Opérations d'administration sur les soap_server
 * @package soap_server
 */
class ActionGroupAdmin extends CopixActionGroup {
	/**
	 * Vérifie que l'on est bien administrateur
	 */
	public function beforeAction ($pActionName){
		CopixPage::get ()->setIsAdmin (true);
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Affiche la liste des classes exportables
	 *
	 * @return CopixActionReturn
	 */
	public function processManageWebservices () {
		_notify ('breadcrumb', array (
			'path' => array ('#' => _i18n ('soap_server.title.manageWebServices'))
		));

		$ppo = _ppo (array ('TITLE_PAGE'=>_i18n ('soap_server.title.manageWebServices')));
		$ppo->arModules = _class ('soapserver_utils')->findAvailableClasses ();
		return _arPpo ($ppo, 'classes.list.tpl');
	}

	/**
	 * Affiche la liste des web services exportés
	 *
	 * @return CopixActionReturn
	 */
	public function processListWebservices () {
		$ppo = _ppo (array ('TITLE_PAGE'=>_i18n ('soap_server.title.listWebServices')));
		$ppo->arWebservices = _ioDAO ('webservices')->findAll ();
		return _arPPO ($ppo, 'webservices.list.php');
	}

	/**
	 * Supprime un webservice
	 *
	 * @return CopixActionReturn
	 */
	public function processDeleteWsService () {
		CopixRequest::assert ('id_wsservice');
		$id_wsservice = _request ('id_wsservice');
		$wsservice    = _ioDao ('webservices')->get ($id_wsservice);

		// si on n'a pas encore confirmé
		if (_request ('confirm') === null) {
			return CopixActionGroup::process (
			'generictools|Messages::getConfirm',
			array (
			'message' => sprintf ('Etes vous sûr de vouloir supprimer le webservice "%s" ?', $wsservice->name_webservices),
			'confirm' =>_url ('admin|deleteWsService', array ('id_wsservice' => $id_wsservice, 'confirm' => 1)),
			'cancel' => _url ('admin|listWebServices')
			)
			);
			 
			// si on a confirmé la suppression	
		} else {
			_ioDao ('webservices')->delete ($id_wsservice);
			return _arRedirect (_url ('admin|listWebServices'));
		}
	}

	/**
	 * Permet d'exporter les classes des modukes
	 *
	 * @return CopixActionReturn
	 */
	public function processExportClass () {
		//Paramètres obligatoires
		CopixRequest::assert ('class');

		//Fil d'ariane
		_notify ('breadcrumb', array (
			'path' => array ('admin|manageWebServices' => _i18n ('soap_server.title.manageWebServices'),
			                 '#'=>_i18n ('soap_server.title.configureWebService'))
		));
		
		//Vérifications d'usages sur la validité de l'identifiant
		if (count ($class = explode ('|', _request ('class'))) !== 2){
			throw new CopixException ('Mauvais identifiant de classe');
		}

		//Préparation des données
		$ppo = _ppo (array ('TITLE_PAGE'=>_i18n ('soap_server.title.manageWebServices')));
		$ppo->module = $class[0];
		$ppo->class = $class[1];
		$ppo->arErrors = array ();

		// Erreur "service existant" passée en paramètre
		if (_request ('error') !== null) {
			$ppo->arErrors[] = _i18n ('soap_server.error.' . _request ('error'));
		}

		//On regarde s'il existe au moins une classe dans le fichier choisi
		if (count ($ppo->arClass = _class ('soapserver_utils')->findClassesIn (_request ('class'))) == 0) {
			throw new CopixException ('Pas de classe à exporter');
		}

		return _arPPO ($ppo, 'webservices.add.php');
	}

	/**
	 * Fonction de confirmation de l'exportation de la classe
	 *
	 * @return CopixActionReturn
	 */
	public function processDoExport (){
		CopixRequest::assert ('class');

		// Nom de service vide
		if ($pServiceName = trim (_request ('name')) == '') {
			return _arRedirect (_url ('admin|ExportClass', array ('error' => 'serviceEmpty', 'class' => _request ('class'))));
		}
		 
		// verification si on n'a pas déja un service de ce nom
		if (_ioDao ('webservices')->countBy (
		   _daoSP ()->addCondition ('name_webservices', '=', $pServiceName)
		) > 0) {
			return _arRedirect (_url ('admin|ExportClass', array ('error' => 'serviceExists', 'module' => $pModuleName, 'class' => $pClassFileName)));
		}
		
		if (count ($class = explode ('|', _request ('class'))) !== 3){
			throw new CopixException ('Mauvais identifiant de classe');			
		}
		
		//Insertion du nouveau webservice dans la base
		$record = _record ('webservices');
		$record->name_webservices = _request ('name');
		$record->file_webservices = $class[0].'|'.$class[1];
		$record->class_webservices = $class[2];
		_dao ('webservices')->insert ($record);
		
		return _arRedirect (_url ('admin|confirmExport', array ('name'=>_request ('name'))));
	}
	
	/**
	 * Confirmation de l'export du service web
	 *
	 * @return unknown
	 */
	public function processConfirmExport (){
		CopixRequest::assert ('name');
		
		//Affichage du résultat
		$ppo = _ppo (array ('TITLE_PAGE'=>_i18n ('soap_server.confirm')));
		$ppo->url = _url ('soap_server||', array ('name'=>_request ('name'))); 
		$ppo->url_wsdl = _url ('soap_server|default|wsdl', array ('name'=>_request ('name'))); 
		$ppo->url_wsdl_1_1 = _url ('soap_server|default|wsdl', array ('name'=>_request ('name'), 'version'=>'1.1')); 

		return _arPpo ($ppo, 'confirm_export.php');
	}

	/**
	 * Test un webservice
	 *
	 * @return CopixActionReturn
	 */
	public function processTest () {
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('soap_server.test.title');
		$ppo->webservices = _ioDAO ('webservices')->findAll ();
		$ppo->webservice = _request ('webservice');
		return _arPPO ($ppo, 'webservices/test.php');
	}

	/**
	 * Retourne les fonctions du webservice demandé, dans un select HTML
	 *
	 * @return CopixActionReturn
	 */
	public function processGetFunctions () {
		//_dump (CopixRequest::asArray ());
		//exit ();
		$soap = @new SoapClient (_request ('webservice'));
		$functions = array ();
		foreach ($soap->__getFunctions () as $function) {
			preg_match ('%^(.*?)\s(.*?)\((.*?)\)$%', $function, $matches);
			list (, $functionType, $functionName, $strParams) = $matches;
			$functions[$functionName] = $functionName;
		}
		ksort ($functions);

		$ppo = new CopixPPO ();
		$ppo->MAIN = _tag ('select', array ('values' => $functions, 'name' => 'function', 'emptyShow' => false, 'extra' => 'onchange="onChangeFunction ()"'));
		return _arDirectPPO ($ppo, 'generictools|blank.tpl');
	}

	/**
	 * Retourne le formulaire pour les paramètres d'un webservice
	 *
	 * @return CopixActionReturn
	 */
	public function processGetParameters () {
		$soap = new SoapClient (_request ('webservice'));
		$ppo = new CopixPPO ();
		$ppo->parameters = array ();
		foreach ($soap->__getFunctions () as $function) {
			preg_match ('%^(.*?)\s(.*?)\((.*?)\)$%', $function, $matches);
			list (, $functionType, $functionName, $strParams) = $matches;
			if ($functionName == _request ('function')) {
				foreach (explode (',', $strParams) as $param) {
					preg_match ('%^\s?(.*?)\s(.*?)$%', $param, $matches);
					if (count ($matches) >= 2) {
						list (, $functionParamType, $functionParamName) = $matches;
					} else {
						continue;
					}
					$ppo->parameters[substr ($functionParamName, 1)] = $functionParamType;
				}
			}
		}
		return _arDirectPPO ($ppo, 'webservices/parameters.php');
	}

	/**
	 * Affiche le résultat de l'appel au webservice
	 *
	 * @return CopixActionReturn
	 */
	public function processGetResult () {
		$ppo = new CopixPPO ();
		$parameters = _request ('parameters');
		$function = _request ('function');
		$webservice = _request ('webservice');

		// appel avec SOAP
		if (_request ('soap', 'true') == 'true') {
			$timer = new CopixTimer ();
			$timer->start ();
			$client = new SoapClient ($webservice);
			$ppo->result = call_user_func_array (array ($client, _request ('function')), _request ('parameters'));
			$ppo->time = $timer->getInter ();

		// appel direct de la méthode
		} else {
			$results = _dao ('webservices')->findBy (_daoSP ()->addCondition ('name_webservices', '=', $webservice));
			if (count ($results) != 1) {
				throw new CopixException ('Aucun webservice trouvé pour "' . $webservice . '".');
			}
			$record = $results->offsetGet (0);
			$className = $record->class_webservices;
			$object = new $className ();
			$timer = new CopixTimer ();
			$timer->start ();
			$ppo->result = $object->$function ($parameters[0]);
			$ppo->time = $timer->getInter ();
		}

		return _arDirectPPO ($ppo, 'soap_server|webservices/result.php');
	}
}