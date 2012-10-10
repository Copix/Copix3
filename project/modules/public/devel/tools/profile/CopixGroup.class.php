<?php
/**
* @package		copix
* @subpackage	profile
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Représente un groupe d'utilisateurs
 * @package copix
 * @subpackage profile
 */
class CopixGroup {
    /**
    * Droits disponibles pour la personne
    * @var array of CopixCapabilities
    */
    private $_capabilities = false;

    /**
    * Liste des logins dans le groupe
    * @var array of users
    */
    private $_users = false;

    /**
    * Identifiant du groupe
    * @var int
    */
    var $id_cgrp = null;

    /**
    * Nom du groupe
    * @var string
    */
    var $name_cgrp = null;

    /**
    * Description du groupe
    * @var string
    */
    var $description_cgrp = null;

    /**
    * Si le groupe est publique (tout le monde appartient au groupe)
    * @var boolean
    */
    var $all_cgrp = false;

    /**
    * Si le groupe est "authentifié". (tous les utilisateurs 
    *  connus du site appartiennent au groupe)
    * @var boolean
    */
    var $known_cgrp = false;

    /**
    * Si c'est un groupe de super administration (tous les droits seront attribués au groupe)
    * @var boolean
    */
    var $isadmin_cgrp = false;

    /**
    * Constructeur.
    * @param int $id l'identifiant du groupe
    */
    public function __construct ($id){
        //Définition de l'identifiant du groupe
        $this->id_cgrp = $id;
        if ($id !== null){
            //Loads the group
            $daoGroup   = CopixDAOFactory::getInstanceOf ('copix:CopixGroup');
            $group      = $daoGroup->get ($id);
            if ($group === null){
            	throw new Exception ("Le groupe demandé n'existe pas");
            }

            $this->description_cgrp = $group->description_cgrp;
            $this->name_cgrp        = $group->name_cgrp;
            $this->all_cgrp         = $group->all_cgrp;
            $this->known_cgrp       = $group->known_cgrp;
            $this->isadmin_cgrp     = $group->isadmin_cgrp == '1' ? true : false;
            
            $this->_loadCapabilities ();
        }
    }
    
    /**
     * Chargement des capabilities si ce n'est pas déja fait. 
     */
    private function _loadCapabilities (){
        if ($this->_capabilities === false){
	        $this->_capabilities = array ();
	        //Loads the capabilities
	        $daoCap      = CopixDAOFactory::getInstanceOf ('copix:CopixGroupCapabilities');
	        $capabilities = $daoCap->findByGroup ($this->id_cgrp);
	        foreach ($capabilities as $capability){
	            $this->setCapability ($capability->name_ccpt,
	            $capability->name_ccpb,
	            $capability->value_cgcp);
	        }
        }
    }
    
    /**
     * Chargement des logins si nécessaire
     */
    private function _loadLogins (){
    	if ($this->_users === false){
    		$this->_users = array ();
	        $daoUserGroup = CopixDAOFactory::getInstanceOf ('copix:CopixUserGroup');
	        $logins = $daoUserGroup->findByGroup ($this->id_cgrp);
	        foreach ($logins as $login){
	            $this->addUsers ($login->login_cusr);
	        }   		
    	}
    }

    /**
    * Récupération de la liste des utilisateurs (tableau de login)
    * @return array of string
    */
    public function getUsers (){
    	$this->_loadLogins ();
        return $this->_users;
    }
    
    /**
    * Ajout d'utilisateurs au groupe
    * @param	mixed	$pUsers	Le ou les utilisateurs à ajouter au groupe (un tableau ou une chaine de caractère qui représente le login)
    */
    public function addUsers ($users){
    	$this->_loadLogins ();
        if (is_array ($users)){
            foreach ($users as $user){
                if (!in_array ($user, $this->_users)){
                    $this->_users[] = $user;
                }
            }
        }else{
            if (!in_array ($users, $this->_users)){
                $this->_users[] = $users;
            }
        }
    }

    /**
    * Suppression d'utilisateurs
    * @param string $pUserName le nom de l'utilisateur à enlever
    */
    public function removeUser ($pUserName){
    	$this->_loadLogins ();
        if (in_array ($pUserName, $this->_users)){
            unset ($this->_users[array_search ($pUserName, $this->_users)]);
        }
    }

    /**
    * Indique la valeur du droit $cap dans le chemin $path
    * @param string $path le chemin dans lequel on veut tester le droit 
    * @param string $cap l'élément sur lequel on veut tester le droit  
    * @return Valeur du droit
    */
    public function valueOf ($cap, $path = null) {
        if ($this->isadmin_cgrp) {
            return PROFILE_CCV_ADMIN;
        }

        $this->_loadCapabilities ();
        $currentValue = PROFILE_CCV_NONE;//starts with NONE
        $lastValue    = null;
        $testString   = '';
        $values       = explode ('|', $path);
        $first = true;

        //test all given elements.
        //eg for site|module|something|other
        //   testing site,
        //           site|module,
        //           site|module|something,
        //           site|module|something|other
        foreach ($values as $element){
            if (!$first) {
                $testString .= '|';
            }
            $first = false;
            $testString .= $element;//the test string.

            //If the value is known, and if the value is below (to remeber the maximum value)
            if (isset ($this->_capabilities[$testString][$cap])){
                //Best value has changed.
                if (($this->_capabilities[$testString][$cap] > $currentValue)){
                    $currentValue = $this->_capabilities[$testString][$cap];
                }
                //last defined value
                $lastValue = $this->_capabilities[$testString][$cap];

            }
        }
        return (($lastValue === null) ? $currentValue : $lastValue);
    }

    /**
    * Indique la valeur maximale du droit sur $cap dans le chemin $basePath et ses sous chemin
    * @param string $basepath le chemin de base à partir duquel on va chercher la valeur du droit
    * @param string $cap le droit que l'on souhaite tester 
    */
    public function valueOfIn ($cap, $basePath){
        if ($this->isadmin_cgrp) {
            return PROFILE_CCV_ADMIN;
        }
        $this->_loadCapabilities ();        
        $currentValue = $this->valueOf ($cap, $basePath);
        foreach ($this->_capabilities as $path=>$infos){
            if (CopixProfileTools::checkBelongsTo ($basePath, $path)){
                $value = $this->valueOf ($cap, $path);//gets the value of cap in path
                if ($value > $currentValue){
                    $currentValue = $value;
                }
            }
        }
        return $currentValue;
    }

    /**
    * Définition du droit par simple remplacement
    * @param string $path le chemin dans lequel se situe le droit
    * @param string $cap le droit sur lequel s'applique la valeur
    * @param int $value la valeur du droit
    */
    public function setCapability ($path, $cap, $value){
        $this->_loadCapabilities ();
        $this->_capabilities[$path][$cap] = $value;
    }

    /**
    * Retire un droit du groupe
    * @param string $path le chemin du droit à supprimer
    * @param string $cap l'identifiant du droit à supprimer
    */
    public function removeCapability ($path, $cap){
        $this->_loadCapabilities ();
        if (isset ($this->_capabilities[$path][$cap])){
            unset ($this->_capabilities[$path][$cap]);
        }
    }

    /**
    * Ajout de capacités au groupe
    * @param array $paths tableau de chemins / capacité
    * @param int $value la valeur du droit que l'on veut appliquer sur les chemins/éléments donnés.
    */
    public function addCapabilities ($paths, $value = PROFILE_CCV_READ){
        $this->_loadCapabilities ();
        foreach ($paths as $path=>$capabilities) {
            foreach ($capabilities as $name=>$noGivenValue){
                $this->setCapability ($path, $name, $value);
            }
        }
    }

    /**
    * Retourne la liste des capacités du groupe
    * @return array
    */
    public function getCapabilities () {
        $this->_loadCapabilities ();
        return $this->_capabilities;
    }
}
?>