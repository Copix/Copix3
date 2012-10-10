<?php
/**
 * @package		tools 
 * @subpackage	wsserver
 * @author		Favre Brice
 * @copyright	2001-2008 CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion des webservices
 * @package		tools 
 * @subpackage	wsserver
 */
class ActionGroupDefault extends CopixActionGroup {
	
	/**
	 * module exporté
	 *
	 * @var string
	 */
	private $_exportModule;

	/**
	 * Chemin du module exporté
	 *
	 * @var  string
	 */
	private $_path;
	
	/**
	 * Classe exportée
	 *
	 * @var  string
	 */
	private $_exportClass;

	/**
	 * Nom du fichier contenant la classe exporté
	 * 
	 * @var  string
	 */
	private $_exportClassFilename;
	
	/**
	 * Nom du webservices
	 * 
	 * @var string
	 */
	private $_wsname = null;
	
	/**
	 * Traitements avant l'action
	 *
	 */
	public function beforeAction ($pActionName){
		$pServiceName = CopixRequest::get('wsname');

		$this->_path = CopixModule::getPath ('wsserver');

		if (isset ($pServiceName) ) {
			$this ->_wsname = $pServiceName;
			$arRes = _ioDAO ('wsservices')->findBy (_daoSP ()->addCondition ('name_wsservices', '=', $pServiceName));
			if (count ($arRes) == 0) {
				if (CopixRequest::get('xml') != null){
					throw new CopixException ('Service introuvable '.htmlentities ($pServiceName)."<br/>".htmlentities (CopixRequest::get('xml') ));
				}else{
					throw new CopixException ('Service introuvable '.htmlentities ($pServiceName));
				}
			}
			$wsServiceInfo = $arRes[0];
			$this ->_exportModule = $wsServiceInfo->module_wsservices;							
			$this ->_exportClass = $wsServiceInfo->class_wsservices;			
			$this ->_exportClassFilename = CopixModule::getPath ( $this->_exportModule ) . COPIX_CLASSES_DIR . strtolower ( $wsServiceInfo->file_wsservices ) ;
			
		} else {
			$this ->_exportModule = CopixConfig::get('wsserver|exportedModule');		
			$this ->_exportClass = CopixConfig::get('wsserver|exportedClass');			
			$this ->_exportClassFilename = CopixModule::getPath ( $this->_exportModule ) . COPIX_CLASSES_DIR . strtolower ( CopixConfig::get('wsserver|exportedClassFile') ) ;			
		}
		 
	}
	
	/**
	 * Traitement par défaut
	 */
	function processDefault () {
		// On charge la classe exportée  
		Copix::RequireOnce ($this->_exportClassFilename);
		
		$arParams = array ();
		$arParamSoap = array ();
		$version = _request ('version', null);
		// Récupération de la version
		if ($version != null && $version == '1.2'){
			$arParamSoap['soap_version'] = SOAP_1_2; 
		}
		// Définition du serveur Soap
		if (isset ($this->_wsname)) {
			$arParams ['wsname'] = $this->_wsname;
		} 
		
		$server = new SoapServer (_url('wsserver|default|wsdl', $arParams), $arParamSoap);
		// Assignation de la classe exportée au serveur
		$server->setclass( $this->_exportClass );
		
		// Traitement des appels 
		if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
			$server->handle();
			return _arNone();
		} else {
			$res = '<strong>' . _i18n('wsserver.handle.title').'</strong>';    
    		$res .= '<ul>';
    		foreach ($server -> getFunctions() as $func) {        
        		$res .=  '<li>' . $func . '</li>';
    		}
    		$res .= '</ul>';
    		$res;
		}
			
		$tpl = new CopixTpl ();
		$tpl->assign('MAIN',$res);
		return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
		
	}
	
	/**
	 * Fonction permettant de générer le fichier WSDL
	 *
	 * @return CopixActionReturn
	 */
	function processWsdl () {
	   $version = _request ('version', '1');
	   if ($version == 1){
	      return $this->_processWsdl1_0 (); 
	   }
	   return $this->_processWsdl1_1 ();
	}
	
	protected function _processWsdl1_0 (){
		$ppo = new CopixPPO ();
		require_once ($this ->_exportClassFilename);
	    require_once ($this->_path . COPIX_CLASSES_DIR . "WSDL_Gen.php");

	    if (isset ($this->_wsname)) {
			$wsdl = new WSDL_Gen( $this->_exportClass , _url('wsserver||',array('wsname'=>$this->_wsname)),_url('wsserver|default|wsdl',array('wsname'=>$this->_wsname)));
		} else {
			$wsdl = new WSDL_Gen( $this->_exportClass , _url('wsserver||'),_url('wsserver|default|wsdl'));	
		}
	    
	    $res = $wsdl->toXML();        	    
		$tpl = new CopixTpl ();
		$tpl->assign('MAIN',$res);

		return _arContent ($res,  array ('content-type'=>'text/xml'));
	}
	
	protected function _processWsdl1_1 (){
		require_once ($this->_path . COPIX_CLASSES_DIR . "WsdlDefinition.php");
		require_once ($this->_path . COPIX_CLASSES_DIR . "WsdlWriter.php");
		require_once ($this ->_exportClassFilename);
		
		$def = new WsdlDefinition();
		$def->setDefinitionName ($this->_wsname);
		$def->setClassFileName ($this ->_exportClass);
		$def->setWsdlFileName ($this->_wsname.".wsdl");
		$def->setNameSpace (_url('wsserver|default|wsdl',array('wsname'=>$this->_wsname)));
		$def->setEndPoint (_url('wsserver||',array('wsname'=>$this->_wsname)));

		$wsdl = new WsdlWriter ($def);
		return _arContent ($wsdl->classToWsdl(),  array ('content-type'=>'text/xml'));
	}
}