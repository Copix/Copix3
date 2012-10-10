<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Patrice Ferlet (metal3d@copix.org)
 * @copyright	2006-2007 CopixTeam
 * @link		http://copix.org
 * @license 	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de dialogue via SSH
 * 
 * @package copix
 * @subpackage utils
 */
class CopixSSH {
	private $host= "";
	private $port= "";
	private $password= "";
	private $login= "";
	private $conn= false;
	private $sftp= false;
	private $stream= false;
	private $key= "";
	private $connected= false;
	private $shell_opened = false;
    private $already_sent = false;
    
	//-- constructor and destructors
	/**
	 * Constructor
	 */
	public function __construct() {
		if (!function_exists("ssh2_connect")) {
			throw new Exception("Error, you have to install ssh2 extension on your PHP server.");
		}
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		if ($this->stream) {
			$this->closeStream();
		}
	}

	//----------- Public methods -------------
	//----------- Setters -------------  
	/**
	 * Set Login and Password for authication by login
	 * @param string login
	 * @param string password
	 */
	public function setAuth($login, $password) {
		$this->login= $login;
		$this->password= ($password) ? $password : "";
	}

	/**
	 * Set ssh server host and port
	 * @param string hostname or ip
	 * @param string port (default 22)
	 */
	public function setServer($host, $port=22) {
		$this->host= $host;
		$this->port= $port;
	}

	//----------- Connexions -------------
	/**
	 * Try connexion with public key
	 * @param string user
	 * @param string keyname (default ssh-rsa)
	 * @param string passphrase (default null)
	 * @param string host (default false)
	 * @param string port (default false)
	 * @return bool connection ok
	 */
	public function connectWithKeys($username, $key= 'ssh-rsa', $passphrase= null, $host= false, $port= false) {
		$this->login= $username;
		$this->key= $key;
		$pubkey= str_replace('-', '_', $this->key) . ".pub";
		$privkey= str_replace('-', '_', $this->key);
		if ($host) {
			$this->setServer($host, $port);
		}

		try {
			$this->connect(true);
		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . "\n");
			return false;
		}

		//open connection
		if (@ ssh2_auth_pubkey_file($this->conn, $username, '/home/' .
			$username . '/.ssh/' . $pubkey, '/home/' .
			$username . '/.ssh/' . $privkey, $passphrase)) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Try to connect with login and password
	 * Parameters are optional if you used setServer and/or setAuth
	 * @param string login
	 * @param string password
	 * @param string host
	 * @param string port
	 */
	public function connectWithLogin($login= false, $password= false, $host= false, $port= false) {
		if ($login) {
			$this->setAuth($login, $password);
		}
		if ($host) {
			$this->setServer($host, $port);
		}

		try {
			$this->connect(true);
		} catch (Exception $e) {
			die("Exception: " . $e->getMessage() . "\n");
			return false;
		}

		if (!@ ssh2_auth_password($this->conn, $this->login, $this->password)) {
			throw new Exception("Error while authentication");
			return false;
		}

		if (!$this->conn) {
			throw new Exception("Error : no connection");

			return false;
		}
		return true;
	}

	//----------- Streams -------------
	/**
	 * Open remote file with sftp
	 * @param string remote file with path
	 * @param char mode (default "r")
	 * @return bool open ok
	 */
	public function sftpOpen($file, $mode= "r") {		
		$this->shell_opened=false;
		
		$this->commonTests();
		if ($this->stream) {
			$this->closeStream();
		}

		$this->sftp= @ ssh2_sftp($this->conn);
		$stream= @ fopen("ssh2.sftp://" . $this->sftp . $file, $mode);
		if (!$stream) {
			throw new Exception("not sftp file opened");
		}
		$this->stream= $stream;
		return true;
	}

	/**
	 * Excute single command on remote server
	 * @param string command
	 * @return bool command sended
	 */
	public function exec($command) {
		$this->commonTests();
		if ($this->stream) {
			fclose($this->stream);
		}
		$this->stream= @ ssh2_exec($this->conn, $command);
		sleep(1);
		if (!$this->stream) {
			throw new Exception("SSH execution may have troubles");
		}
		return true;
	}

	/**
	 * Open a shell stream
	 * Every parameters are optionals
	 * @param string type (see in /etc/termcap default is vt102)
	 * @param array environnement variables to set (null)
	 * @param int height for console (80)
	 * @param int width for console (24)
	 * @param const how to read width and height (SSH2_TERM_UNIT_CHARS)
	 * @return string shell prompt
	 */
	public function openShell($type="vt102", $env= null, $width= 80, $height= 24, $width_height_type= SSH2_TERM_UNIT_CHARS) {
		$this->commonTests();
		if ($this->stream) {
			fclose($this->stream);
		}

		$this->stream= @ ssh2_shell($this->conn, $type, $env, $width, $height, $width_height_type);

		if (!$this->stream) {
			throw new Exception("SSH Shell is not opened");
		}
		//now wait for prompt
		while (!$res = fread($this->stream,512)){
			sleep(0.1);	
		}
		$this->shell_opened=true;
		return $res;
		
	}
	
	/**
	 * Close shell prompt
	 */
	public function closeShell(){
		$this->closeStream();
	}
	
	/**
	 * Close previously opened stream on sftp
	 */
	public function closeStream() {
		fclose($this->stream);
		$this->shell_opened=false;
	}

	/**
	 * Read stream (shell, sftpfile or exec return)
	 * @return string output
	 */
	public function read() {
		$res = $this->_read();
		return (strlen($res)>0) ? $res : false ;
	}
	
	
	/**
	 * Write to opened file with sftp or commands to shell
	 * @param string content 
	 * @return bool write ok
	 */
	public function write($string) {
		if($this->shell_opened){
			//add \n
			$string.="\n";
			//add echo "stop" > /dev/null to stop read
			$string.='echo "stop" > /dev/null'."\n";
		}
		$this->commonTests();
		if ($this->stream) {
			$s = fwrite($this->stream, $string, strlen($string));
			return $s;
		}
		return false;
	}

	//----------- Private methods -------------	
	/**
	 * Some common tests for exceptions on no connexion
	 */
	private function commonTests() {
		if (!$this->connected) {
			throw new Exception("Not connected to SSH");
		}
	}

	/**
	 * Try real connexion
	 */
	private function connect($hostkey= false) {
		
		$this->shell_opened=false;
		if ($hostkey) {
			$_t= @ ssh2_connect($this->host, $this->port, array (
				'hostkey' => $this->key
			));
			if (!$_t) {
				$this->connected= false;
				throw new Exception("SSH2 connection error with hostkey" . $this->key);
			}
			$this->conn= $_t;
			$this->connected= true;
			return true;
		} else {
			$_t= @ ssh2_connect($this->host, $this->port);
			if (!$_t) {
				$this->connected= false;
				throw new Exception("SSH2 connection error with:" . $this->host . " " . $this->port);
			}
			$this->conn= $_t;
			$this->connected= true;
			return true;
		}
	}
	
	/**
	 * Try to read file if sftp connexion, shell output is shell opened 
	 * or the exec output...
	 */
	private function _read(){
		$this->commonTests();
		$res = "";
		$stop=false;
		
		if($this->shell_opened){	
			//look for "echo stop" sended by write command
			//to stop reading empty strings...
			//TODO: echo returns 0... we can't use $? to verify the last
			//      exit status on shell	
			while(!preg_match("/echo \"stop\" > \/dev\/null/",$res)){
				$data = fread($this->stream, 512);
				$res.=$data;
				sleep(1);
			}	
			//then remove the echo command... not asked by yourself...
			$res = preg_replace("/.* echo \"stop\" > \/dev\/null/","",$res);
		}
		else{
			while($data = fread($this->stream, 512)){			
				$res.=$data;			
			}
		}
		return $res;
	}
}
?>