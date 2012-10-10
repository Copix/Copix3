<?php
/**
 * @package tools
 * @subpackage servermonitor
 * @author Croës Gérald
 * @license GNU LGPL
 * @copyright Copix Team
 */

/**
 *  
 */
class ServerMonitorRequest {
	private $_recordId = null;
	
	/**
	 *
	 */
	public function __construct (){
		$this->_datetime = date ('YmdHis');
		$this->_url = CopixUrl::getCurrentUrl ();
		$this->_request = CopixRequest::asArray ();

		$this->_module = CopixRequest::get ('module', 'default');
		$this->_group = CopixRequest::get ('group', 'default');
		$this->_action = CopixRequest::get ('action', 'default');

		$this->_env = $_ENV;
		$this->_serv = $_SERVER;
		$this->_files = $_FILES;
		$this->_cookie = $_COOKIE;
	}
	
	public function getModule (){
		return $this->_module;
	}

	public function getAction (){
		return $this->_action;
	}
	
	public function getGroup (){
		return $this->_group;
	}
	
	/**
	 * Récupération d'un élément depuis la requête stockée
	 *
	 * @param string	$pElement	l'index de l'élément que l'on souhaite récupérer
	 * @return mixed
	 */
	public function getRequestElement ($pElement){
		if (isset ($this->_request[$pElement])){
			return $this->_request[$pElement]; 
		}
		return null;
	}
	
	/**
	 * Date et heure de la requête
	 * @return string
	 */
	public function getDateTime (){
		return $this->_datetime;
	}
	
	/**
	 * Récupère dans les informations à marker la session
	 */
	public function retrieveSession (){
		$this->_session = $_SESSION;
	}

	/**
	 * 
	 *
	 * @param unknown_type $pId
	 */
	public function setRecordId ($pId){
		$this->_recordId = $pId;
	}

	/**
	 * 
	 *
	 * @return unknown
	 */
	public function getRecordId (){
		return $this->_recordId;
	}
	
	/**
	 * Url demandée
	 *
	 * @return string
	 */
	public function getUrl (){
		return $this->_url;
	}
	
	/**
	 * Définition du temps d'exécution d'une requête
	 * 
	 * @param unknown_type $pTime
	 */
	public function setElapsedTime ($pTime){
		$this->_elapsedTime = $pTime; 
	}
	
	public function getElapsedTime (){
		return $this->_elapsedTime;
	}
}

/**
 * 
 */
class ServerMonitor {
	/**
	 * 
	 *
	 * @param ServerMonitorRequest $pRequest
	 */
	public function saveRequest (ServerMonitorRequest $pRequest){
		$record = _record ('servermonitorrequest');
		$record->url_smr = $pRequest->getUrl ();
		$record->data_smr = var_export ($pRequest, true);
		$record->datetime_smr = $pRequest->getDateTime ();
		$record->duration_smr = 0;
		$record->closed_smr = 0;
		$record->module_smr = $pRequest->getModule ();
		$record->group_smr = $pRequest->getGroup ();
		$record->action_smr = $pRequest->getAction ();

		if (($record->id_smr = $pRequest->getRecordId ()) === null){
			_ioDao ('servermonitorrequest')->insert ($record);
			$pRequest->setRecordId ($record->id_smr);
		}else{
			_ioDao ('servermonitorrequest')->update ($record);
		}
	}

	/**
	 *
	 *
	 * @param ServerMonitorRequest $pRequest
	 */
	public function closeRequest (ServerMonitorRequest $pRequest){
		$record = _ioDao ('servermonitorrequest')->get ($pRequest->getRecordId ());
		$record->closed_smr = 1;
		$record->duration_smr = $pRequest->getElapsedTime ()*1000;
		_ioDao ('servermonitorrequest')->update ($record);
		
		//On consolide les résultats tous les 1000 accès
		if (($pRequest->getRecordId () % 1000) === 1){

		}
	}
}
?>