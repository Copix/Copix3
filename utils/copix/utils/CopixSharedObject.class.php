<?php
/**
 * @author Patrice Ferlet <metal3d@copix.org>
 * @package copix
 * @subpackage utils
 * @copyright  2001-2008 CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */


/**
 * Persistance class (singleton, get it by CopixPersistance::instance()
 * 
 * CopixSharedObject write a persistant object available for every instances
 *
 * Each time the persistance object is called, used, set, deleted...  the server object is get to merge
 * content. This reduce concurrence troubles wich can appear.
 *
 * To get more efficient system for persistance, you could mount tmpfs fileystem on you Linux/unix server.
 * To do this, simply execute this lines on Bash:
 * $ su -
 * $ cd /path/to/copix/temp
 * $ rm -rf persistance/* && mount -t tmpfs tmpfs /path/to/copix/persistance
 *
 * now type 'mount' to check your mounted filesystem
 * $ mount | grep tmpfs
 *
 * You should have a tmpfs mounted on you directory. This increase access process for persistance file
 * because it write on memory instead of hard drive.
 * 
 * @package copix
 * @subpackage utils  
 */
class CopixSharedObject {
	/**
	 * instance property to get singleton
	 *
	 * @var static instance
	 */
	static $instances;

	/**
	 * namespace used by instance
	 *
	 * @var string name
	 */
	private $_namespace;

	/**
	 * Persistance file
	 * @var string persistance path and filename
	 */
	private $_persistancefile;

	/**
	 * Persistant Object really used and writtent on server
	 *
	 * @var CopixPPO
	 */
	private $_persist;

	/**
	 * Return the singleton
	 *
	 * @return self
	 */
	public static function instance ($name=null){

		if ( is_null($name) || empty($name) ){
			$name = "default";
		}

		if ( is_null(self::$instances )) {
			self::$instances = array();
		}

		if (! array_key_exists($name,self::$instances) ){
			self::$instances[$name] = new self($name);
		}
		return self::$instances[$name];
	}

	/**
	 * Constructor: Create persistant object, do not use directly, use instance() instead
	 *
	 */
	public function __construct ($namespace){
		$this->_namespace = $namespace;
		$this->_persistancefile = COPIX_TEMP_PATH."/persistance/copix_persistance.".$this->_namespace.".php";
		$this->_persist = _ppo();
		$this->_init();
	}

	/**
	 * Destructor: Write persistants datas on server while PHP script is ended
	 *
	 * @return true
	 */
	public function __destruct (){
		$this->_write();
		return true;
	}

	/**
	 * Initialize persistant object from server
	 *
	 */
	private function _init (){
		if($persistance = CopixFile::read($this->_persistancefile)){
			$this->_persist->merge(unserialize($persistance));
		}
	}


	/**
	 * Save persistant object on server
	 *
	 */
	private function _write (){
		if($persistance = CopixFile::read($this->_persistancefile)){
			$persistance = unserialize($persistance);
			$this->_persist->merge($persistance);
			$data = serialize($this->_persist);
			CopixFile::write($this->_persistancefile,$data);
		}else{
			CopixFile::write($this->_persistancefile,serialize($this->_persist));
		}

	}


	/**
	 * Returns property after re-read of persistance
	 *
	 * @param string $propertyName Nom de la propriété à récupérer
	 */
	public function &__get ($pPropertyName) {
		$this->_init();
		return $this->_persist->$pPropertyName;
	}

	/**
	 * Set propoerties and write persistance
	 * @param string $pPropertyName property name
	 * @param mixed $pValue value to assign
	 *
	 */
	public function __set($pPropertyName,$pValue){
		$this->_init();
		$this->_persist->$pPropertyName = $pValue;
		$this->_write();
		return true;
	}

	/**
	 * Destroy the persistance file
	 *
	 */
	public function destroy(){
		unlink($this->_persistancefile);
	}
}