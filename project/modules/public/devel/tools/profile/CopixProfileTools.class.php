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
* Serveral services for the CopixProfile API
* 
* @package		copix
* @subpackage	profile
*/
class CopixProfileTools {
   /**
   * Creates a capability path.
   *   we'll first check if the capability exist or not
   * @param string $path the capability path we wants to create
   * @param string description the description of the capability
   * @return boolean success or failure.
   * @access public
   */
   public function createCapabilityPath ($path, $description, $ct = null) {
      $dao    = CopixDAOFactory::getInstanceOf ('copix:CopixCapabilityPath');
      $record = CopixDAOFactory::createRecord ('copix:CopixCapabilityPath');

      //check if the capability already exists.
      //If so, we won't create it.
      if ($dao->get ($path) !== false){
         return false;
      }

      $record->name_ccpt = $path;
      $record->description_ccpt = $description;

      return $dao->insert ($record, $ct);
   }

   /**
   * Moves all the path that are linked to the given path
   */
   public function moveCapabilityPath ($path, $newPath) {
      $dao         = CopixDAOFactory::getInstanceOf ('copix:CopixCapabilityPath');
      $daoGroupCap = CopixDAOFactory::getInstanceOf ('copix:CopixGroupCapabilities');

      //gets the moved list.
      $listToMove = CopixProfileTools::getList ($path);
      $listToMove[] = $path;

      //moves the elements.
      foreach ((array) $listToMove as $pathToReplace){
         $pathToCreate = str_replace ($path, $newPath, $pathToReplace);

         //creates the dest cap.
         $oldCap = $dao->get ($pathToReplace);
         $newCap = CopixDAOFactory::createRecord ('copix:CopixCapability');

         $newCap->name_ccpt        = str_replace ($path, $newPath, $pathToReplace);
         $newCap->description_ccpt = $oldCap->description_ccpt;

         $dao->insert ($newCap);

         //moves associations.
         $daoGroupCap->movePath ($pathToReplace, $pathToCreate);
         $dao->delete ($pathToReplace);
      }
   }

   /**
   * deletes all the related path.
   */
   public function deleteCapabilityPath ($path) {
      $dao  = CopixDAOFactory::getInstanceOf ('copix:CopixCapabilityPath');
      $daoGroupCapabilities = CopixDAOFactory::getInstanceOf ('copix:CopixGroupCapabilities');

      //gets the moved list.
      $listToDelete = CopixProfileTools::getList ($path);

      //moves the element childs.
      foreach ((array) $listToDelete as $pathToDelete){
         $daoGroupCapabilities->removePath ($pathToDelete);
         $dao->delete ($pathToDelete);
      }

      //moves the element itself
      $daoGroupCapabilities->removePath ($path);
      $dao->delete ($path);
   }

   /**
   * gets the list of capablities from a base path.
   */
   public function getList ($fromPath = null){
      $sp = CopixDAOFactory::createSearchParams ();
      //if given a path.
      if ($fromPath !== null){
         $sp->addCondition ('name_ccpt', 'like', $fromPath.'|%');
      }

      //search
      $dao     = CopixDAOFactory::getInstanceOf ('copix:CopixCapabilityPath');
      $results = $dao->findBy ($sp);

      //we only wants names
      $toReturn = array ();
      foreach ($results as $cap) {
         $toReturn [] = $cap->name_ccpt;
      }

      //we're gonna put the list in the correct order now
      return $toReturn;
   }

   /**
   * updates the capability path description
   */
   public function updateCapabilityPathDescription ($name, $description) {
      $dao    = CopixDAOFactory::getInstanceOf ('copix:CopixCapabilityPath');

      //Check if the given capability path exists or not.
      if (($record = $dao->get ($name)) === false){
         //does not exist, cannot update
         return null;
      }

      //updating
      $record->description_ccpt = $description;
      return $dao->update ($record);
   }
   
   /**
   * On vérifie que le chemin $motherPath comprend bien $childPath
   * eg : On ne souhaites pas que site|1 soit considéré comme étant la mère de 
   * site|1234.
   */
   public static function checkBelongsTo ($motherPath, $childPath) {
      $motherPath = '/^'.preg_replace ( '/([^\w\s\d])/', '\\\\\\1',$motherPath).'(\||$)/';
      return (preg_match ($motherPath,$childPath) > 0);
   }
}
?>