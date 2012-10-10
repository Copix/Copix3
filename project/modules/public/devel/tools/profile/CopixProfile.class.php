<?php
/**
* @package		copix
* @subpackage	profile
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://www.copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Représentation d'un profil utilisateur
 * 
 * @package		copix
 * @subpackage	profile
 */
class CopixProfile {
   /**
   * Les groupes auquel l'utilisateur appartient
   * @var array of CopixGroup
   */
   var $_groups = array ();

   /**
   * Lecture d'un profil utilisateur à partir de son login
   * 
   * @param string $login le login de l'utilisateur auquel sont attachés les groupes
   */
   function __construct ($login) {
      $daoGroup = CopixDAOFactory::getInstanceOf ('copix:CopixUserGroup');

      //Groupes "spécifiques" de l'utilisateur
      $groups = $daoGroup->findByLogin ($login);
      foreach ($groups as $group) {
         $this->_groups[$group->id_cgrp] = new CopixGroup ($group->id_cgrp);
      }

      //Groupes publiques ou "authentifiés"
      $daoGroup = CopixDAOFactory::getInstanceOf ('copix:CopixGroup');
      if ($login !== null) {
         $groups = $daoGroup->findCommonOrKnownGroup();
      }else{
         $groups = $daoGroup->findCommonGroup();
      }

      //Chargement des groupes de l'utilisateur
      foreach ($groups as $group) {
         $this->_groups[$group->id_cgrp] = new CopixGroup ($group->id_cgrp);
      }
   }

   /**
   * Indique si l'utilisateur appartient au groupe ou non
   * 
   * @param string $groupName le groupe que l'on veut tester
   * @return boolean
   */
   public function belongsTo ($groupName){
      return isset ($this->_groups[$groupName]);
   }

   /**
   * Gets the max value of the capability for the given path. We'll test every
   *   group of the profile.
   * 
   * @param $path the path we want to test
   * @param the capability we want to test in the given path
   * @return int the best Capability value founded
   */
   public function valueOf ($cap, $path = null) {
      $currentValue = PROFILE_CCV_NONE;
      foreach ($this->_groups as $group) {
         $groupValue = $group->valueOf ($cap, $path);

         if ($currentValue < $groupValue) {
            $currentValue = $groupValue;
         }
      }
      return $currentValue;
   }

   /**
   * Gets the max value in any of the subcapabilities
   * 
   * @param string $basePath the path we wants to know cap is in or not
   * @param string $cap the assumed child path
   * @return boolean
   */
   public function valueOfIn ($cap, $basePath){
      $currentValue = PROFILE_CCV_NONE;

      foreach ($this->_groups as $group) {
         $groupValue = $group->valueOfIn ($cap, $basePath);
         if ($currentValue < $groupValue) {
            $currentValue = $groupValue;
         }
      }
      return $currentValue;
   }

   /**
   * Gets the groups the profile belongs to.
   * 
   * @return array of CopixGroup
   */
   public function getGroups () {
      return $this->_groups;
   }
}
?>