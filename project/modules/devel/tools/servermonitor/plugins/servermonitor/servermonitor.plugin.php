<?php
class PluginServerMonitor extends CopixPlugin {
	/**
	 * Le timer utilisé pour le calcul des temps
	 * @var int
	 */
	private $_timer;

	/**
	 * Constructeur
	 */
	function __construct ($config){
		parent::__construct ($config);
		$this->_timer = new CopixTimer ();
		$this->_timer->start ();
	}
	
	
	/**
	 * On Stocke dans la base l'ensemble des paramètres envoyés à l'url pour la loguer 
	 */
	public function beforeSessionStart (){
		_classInclude ('servermonitor|servermonitor');
		$this->_request = new ServerMonitorRequest ();
		_ioClass ('servermonitor|servermonitor')->saveRequest ($this->_request);
	}
	
	/**
	 * Enter description here...
	 *
	 */
	public function beforeProcess (){
		$this->_request->retrieveSession ();
		_ioClass ('servermonitor|servermonitor')->saveRequest ($this->_request);
	}

	/**
	 * @param string  $pContent le contenu à afficher
	 */
	function afterDisplay (){
		$this->_request->setElapsedTime ($this->_timer->stop ());
		_ioClass ('servermonitor|servermonitor')->closeRequest ($this->_request);
	}
}
?>