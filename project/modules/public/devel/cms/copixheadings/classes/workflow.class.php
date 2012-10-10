<?php
/**
* @package	 cms
* @subpackage copixheadings
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Représente les étapes de workflow
 * @package cms
 * @subpackage copixheadings
 */
class Workflow {
    /**
    * Draft code
    */
    function getDraft (){
        return 0;
    }

    /**
    * Proposed code
    */
    function getPropose (){
        return 1;
    }

    /**
    * Valid code
    */
    function getValid (){
        return 2;
    }

    /**
    * Published code
    */
    function getPublish (){
        return 3;
    }

    /**
    * Trash code
    */
    function getTrash (){
        return 4;
    }

    /**
    * Refused code
    */
    function getRefuse (){
        return 9;
    }
    
    /**
    * getBest
    * @param id_head
    * @param capability example 'cms' 'document'
    * @return 0 1 2 3
    */
    function getBest ($id_head, $capability){
        $servicesHeading = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $valueHeadingPath = $servicesHeading->getPath ($id_head);

        $value = CopixUserProfile::valueOf ($valueHeadingPath , $capability);

        switch ($value){
            case PROFILE_CCV_NONE:
            case PROFILE_CCV_SHOW:
            case PROFILE_CCV_READ:
            return 0;
            break;

            case PROFILE_CCV_WRITE:
            return 1;
            break;

            case PROFILE_CCV_VALID:
            return 2;
            break;

            case PROFILE_CCV_PUBLISH:
            case PROFILE_CCV_MODERATE:
            case PROFILE_CCV_ADMIN:
            return 3;
            break;
        }
    }

    /**
    * getNext
    * get next autorized status level
    * @param id_head
    * @param capability example 'cms' 'document'
    * @param currentStatus
    * @return 0 1 2 3
    */
    function getNext ($id_head, $capability, $currentStatus){
        $servicesHeading = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $valueHeadingPath = $servicesHeading->getPath ($id_head);

        $value = CopixUserProfile::valueOf ($valueHeadingPath , $capability);
        switch($currentStatus){
            case $this->getTrash () :
            case $this->getRefuse () :
            case $this->getDraft () :
            if ($this->getBest ($id_head, $capability) > $this->getDraft ()){ // On a le droit de publier
            return $this->getPropose();
            } else { // On a pas le droit de publier
            return $this->getDraft ();
            }
            break;
            
            case $this->getPropose () :
            if ($this->getBest ($id_head, $capability) > $this->getPropose()){ // On a le droit de valider
            return $this->getValid();
            }else{
                return $this->getPropose();
            }
            break;
            
            case $this->getValid () :
            if ($this->getBest ($id_head, $capability) > $this->getValid ()){ // On a le droit de publier
            return $this->getPublish ();
            }else{
                return $this->getValid ();
            }
            break;

            case $this->getPublish () :
            return $this->getPublish (); // publish is the higher WFL status
            break;
        }
    }

    /**
    * getCaption
    * @param idWFL
    * @return I18NCaptionValue
    */
    function getCaption ($WFLValue) {
        switch ($WFLValue){
            case 0:
            return CopixI18N::get ('copix:common.buttons.save');
            break;

            case 1:
            return CopixI18N::get ('copix:common.buttons.propose');
            break;

            case 2:
            return CopixI18N::get ('copix:common.buttons.valid');
            break;

            case 3:
            return CopixI18N::get ('copix:common.buttons.publish');
            break;
        }
    }
}
?>