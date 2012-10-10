<?php
/**
 * @package standard
 * @subpackage admin 
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Actions de configuration pour la base de données.
 * @package standard
 * @subpackage admin 
 */
class ActionGroupDatabase extends CopixActionGroup {
	/**
	 * Vérifie que l'on est bien administrateur
	 *
	 * @param string $actionName Nom de l'action.
	 */
	public function beforeAction ($actionName){
		switch (strtolower ($actionName)) {
			case 'done': case 'checkinstallframework':
				return;
			
			default:
				CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');				
		}
	}
	
	/**
	 * Action par défaut
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault (){
	    return $this->processConfigurationForm ();
	}

	/**
	 * Validation du formulaire de configuration des connections
	 *
	 * @return CopixActionReturn
	 */
	public function processValidForm (){
		$arConnections = array ();
		
        $arKeyConnections = array_keys($this->_getConnections());
        $arKeyConnections[] = "";
        
        foreach ($arKeyConnections as $key => $i) {
            try {
                
        	    CopixRequest::assert ('driver'.$i, 'connectionString'.$i, 'user'.$i);
        	    
        	    $connectionName = str_replace(' ', '_', CopixRequest::getAlphaNum('connectionName'.$i));
        	    if ($connectionName == "") {
        	        $connectionName = $key;
        	    }
				if (in_array (_request ('driver'.$i), CopixDB::getAvailableDrivers ())){
                    $connectionString = _request ('connectionString'.$i);
                    if (_request ('driver'.$i) == 'pdo_mysql' || _request ('driver'.$i) == 'mysql'){
                       if (strpos ($connectionString, 'dbname=') !== 0){
                          $connectionString = 'dbname='.$connectionString;
                       }
                    }
					$arConnections[$connectionName] = array (
					'driver'=>_request ('driver'.$i),
					'connectionString'=>$connectionString,
					'user'=>_request ('user'.$i),
					'password'=>_request ('password'.$i),
					'extra'=>array (),
					'default'=>(_request ('defaultRadio') == 'default'.$i));
				}
			} catch (Exception $e){
			    // var_dump($e);
				break;
			}
        }
		
		CopixSession::set ('admin|database|configure', $arConnections);
		$default_db =  (CopixRequest::get('defaultRadio')=='nodefault') ? 'nodefault' : substr(CopixRequest::get('defaultRadio'),7);
		CopixSession::set ('admin|database|default', $default_db);

		$result = $this->_testConnections ();
		if ((CopixRequest::get ('btn') == _i18n ('install.database.save')) && ($result && count (CopixSession::get('admin|database|configure')) >= 1)){
		    if (_ioClass ('DatabaseConfigurationFile')->write (CopixSession::get('admin|database|configure'), CopixRequest::get('defaultRadio'))){
		        CopixSession::set ('admin|database|configure',null);
		        CopixSession::set ('admin|database|default', null);
		        if (CopixRequest::get('defaultRadio')=='nodefault') {
			        _class('admin|admintemp')->clearTemp();
     		        return _arRedirect (_url ('admin||'));
     		    } else {	
     		        return _arRedirect ($this->_checkInstallFramework($default_db));
     		    }
		    }
		}
		
		if (count (CopixSession::get('admin|database|configure'))==1) {
			return _arRedirect (_url ('admin|database|ConfigurationForm', array('valid'=>($result && count (CopixSession::get('admin|database|configure')) >= 1), 'forcedefault'=>true)));
		}else{

			return _arRedirect (_url ('admin|database|ConfigurationForm',array('valid'=>($result && count (CopixSession::get('admin|database|configure')) >= 1))));
		}
	}

	/**
	 * Affichage du formulaire de configuration des connections
	 *
	 * @return CopixActionReturn
	 */
	public function processConfigurationForm (){

		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('install.database.configure');
	
		$ppo->connections = $this->_getConnections ();
		
		$currentDefault = CopixConfig::instance ()->copixdb_getDefaultProfileName();
        
	    if (CopixSession::get('admin|database|default') !== null) {
        	$currentDefault = CopixSession::get('admin|database|default');
        }
        
        _log($currentDefault, "debug", CopixLog::INFORMATION);
        
	   	if (CopixRequest::get ('forcedefault') != null) {
			//$ppo->connections[0]['default']=true;
			list ($currentDefault) = array_keys($ppo->connections);
			$ppo->connections[$currentDefault]['default']=true;
   		}
   		
		// Tout les profils de connexion existant
		// Mise en commentaire car non utilisé.
        // $allConnectionsName = CopixConfig::instance()->copixdb_getProfiles();

		//Les connexions en dur
        $ppo->nodefault = ($currentDefault == 'nodefault');
        
		$ppo->drivers = CopixDB::getAvailableDrivers ();
		$ppo->configurationFileIsWritable = _ioClass ('DatabaseConfigurationFile')->isWritable ();
		$ppo->configurationFilePath = _ioClass ('DatabaseConfigurationFile')->getPath ();
		
		//Tips
		$checker = _class ('InstallChecker');
		
		$ppo->tips = array ();
        $ppo->importantTips = array ();
		
		if (($ppo->pdomysqlEnabled = $checker->typeDbInstalled ('pdo_mysql'))) {
		    $ppo->tips[]=_i18n('database.tips.pdomysql');
		} else {
		    $ppo->importantTips[]=_i18n('database.importanttips.pdomysql');
		}
		
		if (($ppo->pdosqliteEnabled = $checker->typeDbInstalled ('pdo_sqlite'))) {
		    $ppo->tips[]=_i18n('database.tips.pdosqlite');
		}
		
		if (($ppo->pdopgsqlEnabled = $checker->typeDbInstalled ('pdo_pgsql'))) {
		    $ppo->tips[]=_i18n('database.tips.pdopgsql');
		}
		
		$ppo->valid = false;
		if (CopixRequest::get ('valid')) {
			$ppo->valid = true;
    			
		}
		
		return _arPPO ($ppo, 'configuration.form.php');
	}
	
	/**
	 * Récupération des connexions
	 *
	 * @return array liste des connections
	 */
	private function _getConnections (){
		if ((CopixSession::get ('admin|database|configure')) === null){
			CopixSession::set ('admin|database|configure', _ioClass ('DatabaseConfigurationFile')->getConnections ());
		}
		return CopixSession::get ('admin|database|configure');
	}
	
	/**
	 * Marque les tests de connection pour les connections configurées en session
	 *
	 * @return boolean Vrai si les connections sont toutes accessibles
	 */
	private function _testConnections (){
		$toReturn = true;
		$arConnections = $this->_getConnections();
		foreach ($arConnections as $position=>$connection){
			try {
				$profile = new CopixDBProfile ('test_'.$position,
					$connection['driver'].':'.$connection['connectionString'],
					$connection['user'],
					$connection['password'],
					$connection['extra']
					);

					if (($result = CopixDB::testConnection ($profile)) !== true){
						$toReturn = false;
					}
					$arConnections[$position]['available'] = ($result === true);
					$arConnections[$position]['errorNotAvailable'] = ($result === true ? '' : $result);
			}catch (CopixDBException $e){
			    $toReturn = false;
			    $arConnections[$position]['available'] = false;
			    $arConnections[$position]['errorNotAvailable'] = $e->getMessage ();
			}
		}
		// var_dump($arConnections);
		CopixSession::set ('admin|database|configure', $arConnections);
		return $toReturn;
	}
	
	/**
	 * Confirmation de l'installation et affichage des infos login / mot de passe
	 *
	 * @return CopixActionReturn 
	 */
	public function processDone (){
		_currentUser ()->logout ();
		if (($loginInformations = CopixSession::get ('admin|database|loginInformations')) !== null){
			//CopixSession::set ('admin|database|loginInformations', null);
			$ppo = new CopixPpo ();
			$ppo->TITLE_PAGE = _i18n ("install.result.installok");
			$ppo->loginInformations = $loginInformations;
			return _arPpo ($ppo, 'install.done.tpl');
		}
		return _arRedirect (_url ('||'));
	} 

	/**
	 * Vérifie si le framework est installé sur la base par défaut.
	 *
	 * @return 	CopixActionReturn Redirige l'utilisateur vers l'administration si le framework est installé, vers l'installation sinon
	 */
	public function processCheckInstallFramework () {
        return _arRedirect ($this->_checkInstallFramework ());
    }
    
    /**
     * Vérifie si le framework est installé sur la connection passée en paramètre 
     *
     * @param 	string $pConnection
     * @return 	string url à utiliser
     */
    private function _checkInstallFramework ($pConnection = null) {
    	$ct     = CopixDb::getConnection($pConnection);
	    $tables = $ct->getTableList();
        $adminTemp = _class ('admin|admintemp');
	    if (!in_array ('copixmodule', $tables) || !in_array ('copixconfig', $tables) || !in_array ('copixlog', $tables)) {
	        $adminTemp->clearTemp();
	        return _url ('admin|install|installFramework'); 
        }
        $adminTemp->clearTemp();
        return _url ('admin||');
    }
}
?>