<?php
/**
 * @package standard
 * @subpackage admin 
 * 
 * @author		Julien Salleyron
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */


/**
 * Permet la lecture/ecriture des fichiers de configuration des handler
 * @package standard
 * @subpackage auth 
 * 
 */
class useConfigurationFile {
	
	/**
	 * Le type de handler que l'objet va gérer
	 *
	 * @var string le type d'handler
	 */
	private $_handlerType = null;
	
	/**
	 * Constructeur qui permet de préciser les types de handler que l'objet va gérer
	 *
	 * @param string $pType le type d'handler
	 */
	public function __construct ($pType) {
		$this->_handlerType = $pType;
	}
	
	/**
	 * Ecriture du fichier de configuration
	 * @param	array	$pData	le tableau des handlers à créer	
	 * @return boolean	si le fichier à été crée convenablement
	 */
	public function write ($pData){
	    $generator = new CopixPHPGenerator ();
	    $str = $generator->getPHPTags ($generator->getVariableDeclaration ('$_'.$this->_handlerType.'_handlers', $pData));
		$file = new CopixFile ();
		if (($oldContent = $file->read ($this->getPath ())) !== false){
			$file->write ($this->getPath (true), $oldContent);		
		}
		return $file->write ($this->getPath (), $str);
	}
	
	/**
	 * Indique si le fichier de configuration est modifiable
	 * @return boolean
	 */
	public function isWritable (){
		if (! file_exists ($this->getPath ())){
			return CopixFile::write ($this->getPath (), '<?php $_'.$this->_handlerType.'_handlers = array (); ?>');
		}
		return is_writable ($this->getPath ());
	}
	
	/**
	 * Indique le chemin du fichier de configuration
	 * @return string
	 */
	public function getPath ($pSaveFile = false){
		return ($pSaveFile ? COPIX_TEMP_PATH : COPIX_VAR_PATH).'config/'.$this->_handlerType.'_handlers.conf.'.($pSaveFile ? date ('YmdHis').'.' : '').'php';
	}
	
	/**
	 * Retourne les handlers existants dans le fichier de configuration
	 * @return array 
	 */
	public function get (){
	    if (file_exists ($this->getPath ())) {
		    require ($this->getPath ());
		    $configVar = '_'.$this->_handlerType.'_handlers';
		    return isset ($$configVar) && is_array ($$configVar) ? $$configVar : array ();
	    } else {
	        return array();
	    }
	}
}
?>