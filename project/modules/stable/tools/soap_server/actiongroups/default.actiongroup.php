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
 * Gestion des webservices
 * @package		tools
 * @subpackage	soap_server
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Classe exportée
	 *
	 * @var  string
	 */
	private $_class;

	/**
	 * Nom du webservices
	 *
	 * @var  string
	 */
	private $_name;
	
	/**
	 * On vérifie systématiquement que le nom du WS est passé et qu'il existe 
	 */
	public function beforeAction ($pActionName){
		//Paramèter nom du WS obligatoire
		CopixRequest::assert ('name'); 
		
		//On vérifie que le WS est connu
		if (count ($arRes = _ioDAO ('webservices')->findBy (_daoSP ()->addCondition ('name_webservices', '=', $this->_name = _request ('name')))) == 0) {
			throw new CopixException ('Service introuvable '.htmlentities ($this->_name));
		}
		$wsServiceInfo = $arRes[0];		

		//Inclusion du fichier de la classe exportée & on se souvient de la classe voulue 
		_classInclude ($wsServiceInfo->file_webservices);
		$this ->_class  = $wsServiceInfo->class_webservices;
	}

	/**
	 * Processus par défaut : Serveur SOAP
	 *
	 * @return CopixActionReturn
	 */
	function processDefault () {
		// Paramétrage de l'objet SOAP
		$server = new SoapServer (_url('soap_server|default|wsdl', array ('name'=>$this->_name)));
		$server->setclass ($this->_class );

		// Si c'est un appel SOAP, prise en charge
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$server->handle();
			return _arNone();
		} 
		
		//C'est une interrogation classique, on affiche la liste des méthodes supportées par le serveur SOAP
		$ppo = _ppo (array ('TITLE_PAGE'=>'SOAP'));
		$ppo->arFunctions = $server -> getFunctions ();  
		return _arPpo ($ppo, 'function.list.tpl');
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
	
	/**
	 * Prise en charge de la génération des WSDL en version 1.0
	 */
	protected function _processWsdl1_0 (){
		//Inclusion du générateur de WSDL
	    require_once (CopixModule::getPath ('soap_server') . COPIX_CLASSES_DIR . "WSDL_Gen.php");
	    //Génération du WSDL et retour direct du contenu 
	    $wsdl = new WSDL_Gen( $this->_class , _url('soap_server||',array('name'=>$this->_name)),_url('soap_server|default|wsdl',array('name'=>$this->_name)));
		return _arContent ($wsdl->toXML (),  array ('content-type'=>'text/xml'));
	}
	
	/**
	 * Prise en charge de la génération des WSDL en version 1.0
	 */
	protected function _processWsdl1_1 (){
		//Inclusion du générateur de WSDL 1.1
		require_once (CopixModule::getPath ('soap_server') . COPIX_CLASSES_DIR . "WsdlDefinition.php");
		require_once (CopixModule::getPath ('soap_server') . COPIX_CLASSES_DIR . "WsdlWriter.php");

		//Paramétrage de la définition
		$def = new WsdlDefinition();
		$def->setDefinitionName ($this->_name);
		$def->setClassFileName ($this ->_class);
		$def->setWsdlFileName ($this->_name.".wsdl");
		$def->setNameSpace (_url('soap_server|default|wsdl',array('name'=>$this->_name)));
		$def->setEndPoint (_url('soap_server||',array('name'=>$this->_name)));

		//Génération du WSDL et retour direct du contenu
		$wsdl = new WsdlWriter ($def);
		return _arContent ($wsdl->classToWsdl(),  array ('content-type'=>'text/xml'));
	}
}