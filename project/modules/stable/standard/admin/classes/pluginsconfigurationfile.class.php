<?php
/**
 * @package standard
 * @subpackage admin 
 * 
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */


/**
 * Permet l'écriture de la configuration des plugins enregistrés
 * @package standard
 * @subpackage admin 
 * 
 */
class PluginsConfigurationFile {
	/**
	 * Ecriture du fichier de configuration
	 * @param	array	$pData	le tableau des plugins a enregistrer	
	 * @return boolean	si le fichier à été crée convenablement
	 */
	public function write ($pPluginsList){
	    $generator = new CopixPHPGenerator ();
	    $str = $generator->getPHPTags ($generator->getVariableDeclaration ('$_plugins', $pPluginsList));
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
			return CopixFile::write ($this->getPath (), '<?php $_plugins = array (); ?>');
		}
		return is_writable ($this->getPath ());
	}

	/**
	 * Indique le chemin du fichier de configuration pour les logs
	 * @return string
	 */
	public function getPath (){
		return COPIX_VAR_PATH.'config/plugins.conf.php';
	}
}