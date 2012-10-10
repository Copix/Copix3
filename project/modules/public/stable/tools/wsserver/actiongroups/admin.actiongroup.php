<?php
/**
 * @package		tools
 * @subpackage	wsserver
 * @author		Favre Brice
 * @copyright	2001-2008 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */
 

/**
 * Opérations d'administration sur les wsserver
 * @package wsserver
 */
class ActionGroupAdmin extends CopixActionGroup {

	/**
	 * Vérifie que l'on est bien administrateur
	 */
	public function beforeAction ($pActionName){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}
	
	/**
	 * Affiche la liste des classes exportables
	 *
     * @return CopixActionReturn
	 */
	public function processManageWebservices () {
		$tpl = new CopixTpl ();

		$tpl->assign ('TITLE_PAGE', _i18n ('wsserver.title.manageWebServices'));
		$tpl->assignZone ('MAIN', 'wsserver|customizedinstall');

		return _arDisplay ($tpl);
	}

	/**
	 * Affiche la liste des web services exportés
	 * 
	 * @return CopixActionReturn
	 */
	public function processListWebservices () {
		$arWebservices = _ioDAO ('wsservices')->findAll ();
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('wsserver.title.manageWebServices');
		$ppo->arWebservices = $arWebservices;
		return _arPPO ($ppo, 'webservices/list.php');
	}
	
	/**
	 * Supprime un webservice
	 *
	 * @return CopixActionReturn
	 */
	public function processDeleteWsService () {
		CopixRequest::assert ('id_wsservice');
		$id_wsservice = _request ('id_wsservice');
		$wsservice = _ioDao ('wsservices')->get ($id_wsservice);
		
		// si on n'a pas encore confirmé
		if (_request ('confirm') === null) {
			return CopixActionGroup::process (
				'generictools|Messages::getConfirm',
   				array (
   					'message' => sprintf ('Etes vous sûr de vouloir supprimer le webservice "%s" ?', $wsservice->name_wsservices),
   					'confirm' =>_url ('admin|deleteWsService', array ('id_wsservice' => $id_wsservice, 'confirm' => 1)),
   					'cancel' => _url ('admin|listWebServices')
   				)
   			);
			
		// si on a confirmé la suppression			
		} else {			
			_ioDao ('wsservices')->delete ($id_wsservice);		
			return _arRedirect (_url ('admin|listWebServices'));
		}
	}
	
	/**
	 * Permet d'exporter les classes des modukes
	 *
	 * @return CopixActionReturn
	 */
	public function processExportClass () {
		$pModuleName = CopixRequest::get ('moduleName');
		$pClassFileName = CopixRequest::get ('classFileName');
		
		// si on a confirmé l'ajout
		if (CopixRequest::get('confirm')) {
			$pServiceName = trim (CopixRequest::get ('serviceName'));
			$pClassName = CopixRequest::get ('className');
			$pClassFileName = _request ('classFileName');
			$pModuleName = _request ('moduleName');
			
			// nom de service vide
			if ($pServiceName == '') {
				return _arRedirect (_url ('admin|ExportClass', array ('error' => 'serviceEmpty', 'moduleName' => $pModuleName, 'classFileName' => $pClassFileName)));
			}
			
			// verification si on n'a pas déja un service de ce nom
			$wsservices = _ioDao ('wsservices')->findBy (
				_daoSP ()->addCondition ('name_wsservices', '=', $pServiceName)
			);
			if (count ($wsservices) > 0) {
				return _arRedirect (_url ('admin|ExportClass', array ('error' => 'serviceExists', 'moduleName' => $pModuleName, 'classFileName' => $pClassFileName)));
			}
			
			CopixDB::getConnection ()->doQuery ("insert into wsservices (name_wsservices, module_wsservices, file_wsservices, class_wsservices) values ('".$pServiceName."','".$pModuleName."','".$pClassFileName."', '".$pClassName."')");
			$res = 'Url du Webservice  : <a href="'._url ('wsserver||', array ('wsname'=>$pServiceName)).'">'. _url ('wsserver||', array ('wsname'=>$pServiceName)).'</a><br/>'."\n";
			$res .= 'Url du fichier wsdl : <a href="'._url ('wsserver|default|wsdl', array ('wsname'=>$pServiceName)).'">'. _url ('wsserver|default|wsdl', array ('wsname'=>$pServiceName)).'</a><br/>'."\n";
			$res .= '<br />';
			$res .= '<input type="button" value="' . _i18n ('wsserver.back') . '" onclick="javascript: document.location=\'' . _url ('admin|manageWebServices') . '\';" />';
			
			$tpl = new CopixTpl ();
			$tpl->assign('MAIN',$res);
			return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
			
		// si on doit afficher le formulaire d'ajout
		} else {
			$ppo = new CopixPPO ();
			$ppo->TITLE_PAGE = _i18n ('wsserver.title.manageWebServices');
			$ppo->classFileName = $pClassFileName;
			
			$ppo->arErrors = array ();
			// erreur "service existant" passée en paramètre
			if (_request ('error') !== null) {
				$ppo->arErrors[] = _i18n ('wsserver.error.' . _request ('error'));
			}
			
			$ppo->ModuleName = $pModuleName;

			$arBefore = get_declared_classes ();
			include (CopixModule::getPath ($pModuleName) . COPIX_CLASSES_DIR . $pClassFileName );
			$arAfter = get_declared_classes ();
			$arClass = array_diff ($arAfter, $arBefore);
			sort($arClass);
			if (count($arClass) == 0) {
				throw new Exception ('Pas de classe à exporter');
			}
			$ppo->arClass = $arClass; 

			return _arPPO ($ppo, 'webservices/add.php');
		}
	}

	/**
	 * Test un webservice
	 *
	 * @return CopixActionReturn
	 */
	public function processTest () {
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('wsserver.test.title');
		$ppo->webservices = _ioDAO ('wsservices')->findAll ();
		$ppo->webservice = _request ('webservice');
		return _arPPO ($ppo, 'webservices/test.php');
	}

	/**
	 * Retourne les fonctions du webservice demandé, dans un select HTML
	 *
	 * @return CopixActionReturn
	 */
	public function processGetFunctions () {
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
			$results = _dao ('wsservices')->findBy (_daoSP ()->addCondition ('name_wsservices', '=', $webservice));
			if (count ($results) != 1) {
				throw new CopixException ('Aucun webservice trouvé pour "' . $webservice . '".');
			}
			$record = $results->offsetGet (0);
			$className = $record->class_wsservices;
			$object = new $className ();
			$timer = new CopixTimer ();
			$timer->start ();
			$ppo->result = call_user_func_array (array ($object, $function), $parameters);
			$ppo->time = $timer->getInter ();
		}

		return _arDirectPPO ($ppo, 'wsserver|webservices/result.php');
	}
}