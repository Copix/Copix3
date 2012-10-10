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
        $toReturn    = array ();
        $arInstalledModule = CopixModule::getList (true);

        //Liste des modules installables
        foreach (CopixModule::getList (false, $pGroupId) as $name){
            if (($temp = CopixModule::getInformations ($name)) !== null) {
                //check if they are installed or not
                if (in_array ($temp->name, $arInstalledModule)) {
                    $temp->isInstalled = true;
                }else{
                    $temp->isInstalled = false;
                }
                $toReturn[] = $temp;
            }
        }
        
        // tri des modules par ordre alphabétique sur la description
        $tri = true;
        $nbrModule = count ($toReturn);
        while ($tri) {
        	$tri = false;
        	for ($boucle = 1; $boucle < $nbrModule; $boucle++) {
        		$moduleDesc1 = ($toReturn[$boucle - 1]->description <> '') ? $toReturn[$boucle - 1]->description : $toReturn[$boucle - 1]->name;
        		$moduleDesc2 = ($toReturn[$boucle]->description <> '') ? $toReturn[$boucle]->description : $toReturn[$boucle]->name;
        		
        		//echo '[<b>' . $moduleDesc1 . '</b>] [<font color="red">' . $moduleDesc2 . '</font>]<br />';
        		
        		$comp = strcasecmp ($moduleDesc1, $moduleDesc2);
        		if ($comp > 0) {
        			$temp = $toReturn[$boucle - 1];
        			$toReturn[$boucle - 1] = $toReturn[$boucle];
        			$toReturn[$boucle] = $temp;
        			$tri = true;
        		}
        	}
        }
        
        return $toReturn;
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
?>