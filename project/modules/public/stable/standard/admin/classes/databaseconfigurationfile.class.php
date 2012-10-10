<?php
/**
 * @package standard
 * @subpackage admin 
 * n
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */


/**
 * Permet l'écriture de la configuration
 * @package standard
 * @subpackage admin 
 */
class DatabaseConfigurationFile {
	/**
	 * Ecriture du fichier de configuration
	 * @param	array	$pData	le tableau des connexions à créer	
	 * @param   string $pDefault variable du profile par défaut
	 * @return boolean	si le fichier à été crée convenablement
	 */
	public function write ($pData,$pDefault){
	    $generator = new CopixPHPGenerator ();
	    $pDefault = ($pDefault=='nodefault') ? null : (substr($pDefault,7));
	    $str = $generator->getPHPTags ($generator->getVariableDeclaration ('$_db_profiles', $pData)."\n\n".$generator->getVariableDeclaration ('$_db_default_profile', $pDefault ));
		if($toReturn = CopixFile::write ($this->getPath (), $str)) {
			CopixConfig::reload ();
		}
		return $toReturn;
	}
	
	/**
	 * Indique si le fichier de configuration est modifiable
	 * @return boolean
	 */
	public function isWritable (){
		if (! file_exists ($this->getPath ())){
			return CopixFile::write ($this->getPath (), '<?php $_db_profiles = array (); ?>');
		}
		return is_writable ($this->getPath ());
	}
	
	/**
	 * Indique le chemin du fichier de configuration pour les bases de données
	 * @return string
	 */
	public function getPath (){
		return COPIX_VAR_PATH.'config/db_profiles.conf.php';
	}
	
	/**
	 * Retourne les connections existantes dans le fichier de configuration
	 * @return array 
	 */
	public function getConnections (){
	    if (file_exists($this->getPath ())) {
		    require ($this->getPath ());
		    return isset ($_db_profiles) && is_array ($_db_profiles) ? $_db_profiles : array ();
	    } else {
	        return array();
	    }
	}
}
?>