<?php
/**
 * Thread manager wich can spool thread
 * @package tools
 * @subpackage thread
 * @author Patrice Ferlet <metal3d@copix.org>
 *
 */
class ThreadManager {

	/**
	 * Spooler containing thread declarations
	 *
	 * @var array
	 */
	private $_spool = array ();

	/**
	 * Servers we can call to use
	 *
	 * @var array
	 */
	private $_servers = array ();

	/**
	 * Responses from thread
	 *
	 * @var array
	 */
	public  $responses = array ();

	/**
	 * Local counter
	 *
	 * @var int
	 */
	private $_pointer = 0;


	/**
	 * Constructor for manager
	 *
	 */
	function __construct (){
		$this->initServerList();
		$this->_host = null;
		$this->_port = 80;
	}
	
	
	/**
	 * Append server url to the server list
	 *
	 * @param string $url
	 */
	public function addServer($url){
		$this->_servers [] = $url;
	} 
	
	
	/**
	 * Empty server list
	 */
	public function emptyServersList(){
		$this->_servers = array();
	}
	
	public function initServerList(){
		$this->_servers = (array)explode (',',CopixConfig::get ('thread|servers'));		
	}
	

	/**
	 * Append a thread class
	 *
	 * @param string $threadname as form 'module|classname'
	 * @param CopixPPO $params object
	 */
	function add ($pThreadname,$pParams=null){
		$this->_servers[$this->_pointer] = trim ($this->_servers[$this->_pointer]);

		if ($this->_servers[$this->_pointer] == "local"){
			$scanned = $this->_scan_url (_url ('thread||'));			
		}else{
			$scanned = $this->_scan_url ($this->_servers[$this->_pointer]);
		}
		//for next loop
		if (isset ($this->_servers[$this->_pointer+1])){
			$this->_pointer++;
		}else{
			$this->_pointer=0;
		}

		$this->_spool[]=_ppo (array (
			'host' => $scanned->host,
			'uri' => $scanned->uri,
			'threadname' => $pThreadname,
			'params' => $pParams
		));
	}

	/**
	 * Execute threads and wait for the end of every process lauchned
	 *
	 * @return array $responses from process () methods of threads
	 */
	function execute (){
		//foreach thread in this manager, call the server to execute
		$i = 0;
		$timeout = 15;
		$sockets = array ();
		$status = array ();
		$map = array ();
		$errors = 0;
		
		foreach ($this->_spool as $thread) {
			$host = $thread->host;
			try{
				$s = stream_socket_client ($host.":80", $errno, $errstr, $timeout, STREAM_CLIENT_ASYNC_CONNECT|STREAM_CLIENT_CONNECT);
				$map[] = $thread;
				$sockets[] = $s;			
			}
			catch(Exception $e)	{
				$status["error".$errors] = serialize(_ppo (array ('error'=>"Unable to connect to ".$host)));
				$errors++;
			}
			
		}
		//foreach existing sockets, try to read then delete
		while (count ($sockets)) {
			$read = $write = $sockets;
			$n = stream_select ($read, $write, $e = null, $timeout);
			if ($n > 0) {
				//get datas from readable sockets
				foreach ($read as $r) {
					$id = array_search ($r, $sockets);
					$data = fread ($r, 4096);
					if (strlen ($data) == 0) {
						//no data, error or end of socket life
						fclose ($r);
						if (!isset ($status[$id])){
							$status[$id] = serialize(_ppo (array ('error'=>"Unable to get data from thread")));
						}
						unset ($sockets[$id]);
					}
					else {
						//skip reponse header:
						$lines = explode ("\n",$data);
						$c = 0;
						$flag = false;
						$status[$id]="";
						while ($c<count ($lines) && $line=$lines[$c]){
							if ($flag) $status[$id] .= $line;
							if (trim ($line)=='') $flag=true;
							$c++;
						}
					}

				}
				//Send header to the writables sockets
				foreach ($write as $w) {
					$id = array_search ($w, $sockets);
					if(is_resource($w)){
						//keep params
						$p = array ();
						$p['params'] = serialize ($map[$id]->params);
						$p['thread'] = $map[$id]->threadname;
						$header = $this->_createPostRequest ($p,$map[$id]->host,$map[$id]->uri);			
						stream_socket_sendto($w,$header, STREAM_OOB);
					}
				}
			} else {
				foreach ($sockets as $id => $s) {
					$status[$id] = "timed out " . $status[$id];
				}
				break;
			}
				
		}

		//send results
		foreach ($status as $response) {
			$this->responses[]=unserialize ($response);
		}
		return $this->responses;
	}



	/**
	 * Get correct URI and Hostname for POST connection
	 *
	 * @param string $basepath
	 * @return ppo information
	 */
	private function _scan_url ($pReq) {
		$pos = strpos ($pReq, '://');
		$this->_protocol = strtolower (substr ($pReq, 0, $pos));
		$pReq = substr ($pReq, $pos+3);
		$pos = strpos ($pReq, '/');
		if ($pos === false)
		$pos = strlen ($pReq);
		$host = substr ($pReq, 0, $pos);
		$_uri = substr ($pReq, $pos);
		if ($_uri == '')
		$_uri = '/';
		return _ppo (array (
			'uri' =>$_uri,
			'host'=>$host
		));
	}



	/**
	 * Create a POST request to be sended to host
	 *
	 * @param array $datas
	 * @param string $target_url
	 * @return string $content_header
	 */
	private function _createPostRequest ($pDatas,$pHost,$pUri) {
		$body = "";
		foreach ($pDatas as $key=>$val) {
			if (!empty ($body)) $body.= "&";
			$body.= $key."=".urlencode ($val);
		}

		$contentlength = strlen ($body);

		$header = "POST $pUri HTTP/1.1\r\n";
		$header .= "Host: $pHost\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: $contentlength";
		$header .= "\r\nConnection: close";
		$header .= "\r\n\r\n";
		$header .= "$body\r\n";
		return $header;
	}


}

/**
 * Thread base class used to implement threads
 * @abstract
 * @package tools
 * @subpackage thread
 * @author Patrice Ferlet <metal3d@copix.org>
 */

abstract class Thread {
	/**
	 * Process method which will be called while thread process
	 *
	 * @param CopixPPO $pParams object of arguments to give to the thread
	 */
	public abstract function process ($pParams=null);
}


?>