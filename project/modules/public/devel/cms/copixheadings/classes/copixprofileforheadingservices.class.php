<?php
/**
* @package	 cms
* @subpackage	copixheadings
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Services de droits sur les éléments de la rubrique
 * @package cms
 * @subpackage copixheadings
 */
class CopixProfileForHeadingServices {
    /**
    * Gets the CapabilityPath for the given Heading level
    * @param int level the id of the heading we wants to know the path of
    * @return string the path of the capability
    */
    function getPath ($level){
        $hs = CopixClassesFactory::getInstanceOf ('copixheadings|CopixHeadingsServices');

        $path = 'modules|copixheadings';//initial path
        foreach ((array)$hs->getPath ($level) as $element) {
            $path .= '|'.$element->id_head;
        }
        return $path;
    }

    /**
    * removes element that do not match given filterLevel. The remaing
    *   elements gets a new property named profileInformation.
    * @param array by adress $toFilter the array of Headings we wants to filter
    * @param int PROFILE_CCV_VALUE the level we wants to match to keep informations
    * @return void
    */
    function filter (& $toFilter, $filterLevel = null){
        CopixProfileForHeadingServices::appendProfileInformation($toFilter);
        foreach ((array)$toFilter as $key => $element){
            if ($element->profileInformation < $filterLevel){
                unset ($toFilter[$key]);
            }
        }
    }

    /**
    * append the user's profile value on each element
    * @param array by reference $toAppend the headings we wants to know the
    *   user's permission on
    * @return void

    */
    function appendProfileInformation (& $toAppend){
        foreach ((array)$toAppend as $key=>$element){
            $toAppend[$key]->profileInformation = CopixUserProfile::valueOf (CopixProfileForHeadingServices::getPath ($element->id_head), 'copixheadings');
        }
    }

    /**
    * Removes tree node that do not match given filterLevel. The remaining 
    * elements gets au new property named profileInformation
    * APPLY to a tree formated like copixheadingsservices::getTree
    * @param tree by adress $tree the tree of headings we wants to filter
    * @param int PROFILE_CCV_VALUE the level we wants to match to keep informations
    * @return void

    */
    function filterTree ($tree, $filterLevel = null){
        $this->filter($tree->childs,$filterLevel);

        if (is_array($tree->childs) && count($tree->childs)>0) {
            foreach ($tree->childs as $key=>$elem) {
                $tree->childs[$key] = $this->filterTree($elem,$filterLevel);
            }
        }
        return $tree;
    }
}
?>