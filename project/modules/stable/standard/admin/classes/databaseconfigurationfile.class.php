<?php
/**
 * @package		standard
 * @subpackage	adminn
 * @author		Gérald Croës, Steevan BARBOYON
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Permet l'écriture de la configuration des bases de données
 * 
 * @package		standard
 * @subpackage	admin 
 */
class DatabaseConfigurationFile {
	/**
	 * Ecriture du fichier de configuration
	 * 
	 * @param array$ pData Tableau des connexions à créer	
	 * @param string $pDefault Variable du profile par défaut
	 * @return boolean Indique si le fichier à été crée convenablement
	 */
	public function write ($pData, $pDefault) {
	    $generator = new CopixPHPGenerator ();
	    $pDefault = ($pDefault=='nodefault') ? null : (substr ($pDefault, 7));
	    $str = $generator->getPHPTags (
	    	$generator->getVariableDeclaration ('$_db_profiles', $pData) . "\n\n" .
	    	$generator->getVariableDeclaration ('$_db_default_profile', $pDefault)
	    );
		if ($toReturn = CopixFile::write ($this->getPath (), $str)) {
			CopixConfig::reload ();
		}
		return $toReturn;
	}
	
	/**
	 * Indique si le fichier de configuration est modifiable
	 * 
	 * @return boolean
	 */
	public function isWritable () {
		if (!file_exists ($this->getPath ())) {
			return CopixFile::write ($this->getPath (), '<?php $_db_profiles = array (); ?>');
		}
		return is_writable ($this->getPath ());
	}
	
	/**
	 * NE PLUS UTILISER ! Retourne le chemin du fichier de configuration pour les bases de données
	 * 
	 * @deprecated Utiliser CopixConfig::copixdb_getConfigFilePath () à la place
	 * @return string
	 */
	public function getPath () {
		return CopixConfig::instance ()->copixdb_getConfigFilePath ();
	}
	
	/**
	 * Retourne les connections existantes dans le fichier de configuration
	 * @return array 
	 */
	public function getConnections () {
	    if (file_exists ($this->getPath ())) {
			require ($this->getPath ());
		    if (isset ($_db_profiles)) {
			    foreach ($_db_profiles as $profil => &$infos) {
					$arExtras = explode (';', $infos['connectionString']);
					$infos['dbname'] = null;
					$infos['host'] = 'localhost';
					foreach ($arExtras as $extra) {
						list ($key, $value) = explode ('=', $extra);
						$infos[$key] = $value;
					}
			    }
			    ksort ($_db_profiles);
				return $_db_profiles;
			} else {
				return array ();
			}
	    } else {
	        return array();
	    }
	}
	
	/**
	 * Retourne le nom du profil par défaut
	 *
	 * @return string
	 */
	public function getDefaultProfile () {
		if (file_exists ($this->getPath ())) {
			require ($this->getPath ());
		    return $_db_default_profile;
		} else {
			return null;
		}
	}
}