<?php
/**
 * @package standard
 * @subpackage admin 
 * 
 * @author		Landry Benguigui
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Permet l'écriture de la configuration des profils de log
 * @package standard
 * @subpackage admin 
 */
class cacheConfigurationFile {
	/**
	 * Ecriture du fichier de configuration
	 * @param	array	$pData	le tableau des profils de log à créer	
	 * @param   string $pDefault variable du profile par défaut
	 * @return boolean	si le fichier à été crée convenablement
	 */
	public function write ($pData){
	    $generator = new CopixPHPGenerator ();
	    $str = $generator->getPHPTags ($generator->getVariableDeclaration ('$_cache_types', $pData));
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
			return CopixFile::write ($this->getPath (), '<?php $_log_profiles = array (); ?>');
		}
		return is_writable ($this->getPath ());
	}
	
	/**
	 * Indique le chemin du fichier de configuration pour les logs
	 * @return string
	 */
	public function getPath (){
		return COPIX_VAR_PATH.'config/cache_profiles.conf.php';
	}	
}
?>