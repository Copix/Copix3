<?php
/**
 * @package		wsserver
 * @author		Favre Brice
 * @copyright	2001-2007 CopixTeam
 * @link		http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.htmlGNU Leser General Public Licence, see LICENCE file
 */
 

/**
 * Opérations d'administration sur les wsserver
 * @package wsserver
 */
class ActionGroupAdmin extends CopixActionGroup {
    
	/**
	 * Vérifie que l'on est bien administrateur
	 */
	public function beforeAction (){
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
		
		return _arPPO ($ppo, 'wsservices.list.php');
		
	}
	
	/**
	 * Supprime un WebService
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
			
			return _arRedirect (_url ('wsserver||', array ('wsname'=>$pServiceName)));
			
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

			return _arPPO ($ppo, 'wsservices.add.php');
		}
	}
}
?>