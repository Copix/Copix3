<?php
/**
 * @package standard
 * @subpackage admin 
* 
* @author   Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Objet de distribution du framework
*
* Cherche les fichiers install.typedb.sql
* Crée le fichier de conf XML de CopixDB
* Execute les scripts dans la base courante
* @package standard
* @subpackage admin 
*/
class InstallService {
	/**
	 * Modules par défaut à installer avec Copix
	 *
	 * @var array
	 */
	private $_defaultModules = array ('generictools', 'auth', 'default', 'admin');
	
	/**
	 * Retourne les modules par défaut à installer avec Copix
	 *
	 * @return array
	 */
	public function getDefaultModules () {
		return $this->_defaultModules;
	}
	
    /**
    * Install
    *
    * Install the database, execute all the module SQL script
    */
    function installAll () {
        $arTemp = $this->getModules ();
        //build an array
        $arModules = array ();
        foreach ($arTemp as $module){
            CopixModule::install($module->name);
        }
        
//        return $arError;
    }

    /**
    *  get all installable modules and their status (install or not), and depedency
    *  @return array of object
    *  @access private
    */
    function getModules ($pGroupId = null) {
        $toReturn = array ('installed' => array (), 'availables' => array ());
        $arInstalledModule = CopixModule::getList (true);

        //Liste des modules installables
        foreach (CopixModule::getList (false, $pGroupId) as $name){
            if (($infos = CopixModule::getInformations ($name)) !== null) {
				$infos->haveConfig = (count (CopixConfig::getParams ($name)) > 0);
                //check if they are installed or not
                if (in_array ($infos->name, $arInstalledModule)) {
                    $key = &$toReturn['installed'];
                } else {
                     $key = &$toReturn['availables'];
                }
				if (!array_key_exists ($infos->getGroup ()->getId (), $key)) {
					$key[$infos->getGroup ()->getId ()] = array ();
				}
                $key[$infos->getGroup ()->getId ()][] = $infos;
            }
        }
        
		foreach ($toReturn as &$status) {
			foreach ($status as &$modules) {
				usort ($modules, array ($this, 'sortModuleName'));
			}
		}
        return $toReturn;
    }
    
    /**
     * Fonction pour trier les modules par ordre alpha de leur description
     *
     * @param ModuleDescription $pModuleDescription1
     * @param ModuleDescription $pModuleDescription2
     * @return int
     */
    public function sortModuleName ($pModuleDescription1, $pModuleDescription2){
    	return strcasecmp ($pModuleDescription1->getName (), $pModuleDescription2->getName ());
    }

    /**
     * Prepare installation, launch sql script needed during installation
     */
    function installFramework () {
        // find the current connection type (defined in /plugins/copixDB/profils.definition.xml)
    	$config = CopixConfig::instance ();
    	$driver = $config->copixdb_getProfile ();
    	$typeDB = $driver->getDriverName ();

        // Search each module install file
        $scriptName = 'prepareinstall.'.$typeDB.'.sql';
        $file = CopixModule::getPath ('admin') . COPIX_INSTALL_DIR . 'scripts/' . $scriptName;
        CopixDB::getConnection ()->doSQLScript ($file);
        //make sure that copixmodule is reset
        CopixModule::reset();
        foreach (array('admin','default','auth', 'generictools') as $module) {
            if (($message = CopixModule::installModule($module)) !== true) {
                throw new Exception ($message);
            }
        }
        return $this->_generatePassword ();
    }
    
    function afterInstall (){
        // find the current connection type (defined in /plugins/copixDB/profils.definition.xml)
    	$config = CopixConfig::instance ();
    	$driver = $config->copixdb_getProfile ();
    	$typeDB = $driver->getDriverName ();

        // Search each module install file
        $scriptName = 'afterinstall.'.$typeDB.'.sql';
        $file = CopixModule::getPath ('admin') . COPIX_INSTALL_DIR . 'scripts/' . $scriptName;
        CopixDB::getConnection ()->doSQLScript ($file);
    }

    /**
    * Paramètres de la base de données
    */
    function getCurrentParameters (){
    	return CopixConfig::instance ()->copixdb_getProfile ();
    }
}