<?php
/**
 * @package		tools 
 * @subpackage	wsserver
 * @author		Favre Brice
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @licence		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion des webservices
 * @package		tools 
 * @subpackage	wsserver
 */
class ActionGroupDefault extends CopixActionGroup {
	
	private $_exportModule;
	private $_exportActiongroup;
	private $_path;
	private $_exportClassFilename;
	private $_wsname = null;
	/**
	 * Vérifie que l'on est bien authentifié (A voir).
	 */
	public function beforeAction (){
		$pServiceName = CopixRequest::get('wsname');

		$this->_path = CopixModule::getPath ('wsserver');

		if (isset ($pServiceName) ) {
			$this ->_wsname = $pServiceName;
			$arRes = _ioDAO ('wsservices')->findBy (_daoSP ()->addCondition ('name_wsservices', '=', $pServiceName));
			if (count ($arRes) == 0) {
				throw new CopixException ('Service introuvable '.html_entities ($pServiceName));
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

		// Définition du serveur Soap
		if (isset ($this->_wsname)) {
			$server = new SoapServer (_url('wsserver|default|wsdl', array ('wsname'=>$this->_wsname)));
		} else {
			$server = new SoapServer (_url('wsserver|default|wsdl'));
		}

		// Assignation de la classe exportée au serveur
		$server->setclass( $this->_exportClass );
		
		// Traitement des appels 
		if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
			$server->handle();
			$tpl = new CopixTpl();
			$tpl->assign('MAIN','');
			return new CopixActionReturn (CopixActionReturn::DISPLAY_IN, $tpl, "wsserver|blank.tpl");			exit();
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
	 */
	function processWsdl () {
		$ppo = new CopixPPO ();
		require_once ($this ->_exportClassFilename);
		
	    require_once ($this->_path . COPIX_CLASSES_DIR . "WSDL_Gen.php");
	    // Generation du WSDL
		// @ todo : improve with significant URL

		if (isset ($this->_wsname)) {
			$wsdl = new WSDL_Gen( $this->_exportClass , _url('wsserver||',array('wsname'=>$this->_wsname)),_url('wsserver|default|wsdl',array('wsname'=>$this->_wsname)));
		} else {
			$wsdl = new WSDL_Gen( $this->_exportClass , _url('wsserver||'),_url('wsserver|default|wsdl'));	
		}
	    
	    header('Content-Type: application/xml; charset=UTF-8');
	    
	    $res = $wsdl->toXML();        	    
		$tpl = new CopixTpl ();
		$tpl->assign('MAIN',$res);
		
		return new CopixActionReturn (CopixActionReturn::DISPLAY_IN, $tpl, "wsserver|blank.tpl");
	}

	/**
	 * Récupération des exceptions SOAP 
	 */
	function catchActionExceptions ($e) {
    	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        	return new SoapFault ('Serveur', $e->getMessage ());
		} else {
        	parent::catchActionExceptions($e);
        }
	}
	
}
?>