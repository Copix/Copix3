<?php
/**
* @package		cms
* @subpackage	copixheadings
* @author		Croes Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Services for the headings in copix
* @package cms
* @subpackage copixheadings
*/
class CopixHeadingsServices {
    /**
    * the headings tree
    * _treeHeading[id] = the element
    */
    var $_treeHeadings = false;

    /**
    * hash table for a quick father finding
    */
    var $_hashFather = false;

    /**
    * hash table for a quick child finding.
    * will store the actual childs (not only Ids) int his tab.
    * we will get something like: $this->_hashChild[idFather] = array ('idChild'=>$child, 'idChild2'=>$child2, ...);
    */
    var $_hashChild = false;

    /**
    * get the path for a given child.
    *  The returned array WILL contain the child itself
    *
    * @param $idChild the child we wants to know the ascendant
    * @return array (0=>root, 1=>firstChild, 2=>secondChild, ..., X=>$idChild)
    */
    function getPath ($idChild) {
        $this->_loadHashFather ();

        //we first put the child in the array
        $toReturn   = array ();
        $idChild = $idChild == null ? 'ROOT_NODE' : $idChild;
        if (isset ($this->_treeHeadings[$idChild])){
            $toReturn[] = $this->_treeHeadings[$idChild];
        }

        while ($this->_hashFather[$idChild] !== null){
            //assign in the tab the father of the current element,
            //while switching to the next element.
            $toReturn[] = $this->_treeHeadings[$idChild = $this->_hashFather[$idChild]];
        }

        return array_reverse ($toReturn);
    }
    
    /**
    * Indique le nom de domaine associé à la rubrique donnée.
    * @param int $pIdHead l'identifiant de la rubrique
    * @return string le domaine ou null si aucun
    */
    function getDomainFor ($pIdHead){
    	static $domain = array ();
    	if ($pIdHead === null){
    		$pIdHead = 'ROOT_NODE';
    	}
    	if (isset ($domain[$pIdHead])){
    		return $domain[$pIdHead];
    	}
    	//on récupère le chemin et on va s'arrêter au premier qui dispose d'une url attachée
    	$arPath = array_reverse ($this->getPath ($pIdHead));
    	foreach ($arPath as $headingItem){
    		if ($headingItem->url_head != null){
                return $domain[$pIdHead] = $headingItem->url_head;
    		}
    	}
		return $domain[$pIdHead] = (($rootDomain = CopixConfig::get ('copixheadings|rootHeadingDomain')) != '' ? 
		                             $rootDomain : 
		                             null);
    }
    
    /**
    * Récupération de la liste des domaines possibles
    */
    function getDomainList (){
    	static $result = false;
    	if ($result === false){
         $result = array ();
    		$dao = CopixDAOFactory::getInstanceOf ('copixheadings|copixheadings');
    		foreach ($dao->getDomainList () as $domainName){
    			if (strlen ($domainName)){
    				$result[] = $domainName;
    			}
    		}
          $current = CopixUrl::getRequestedBasePath ().CopixUrl::getRequestedScriptName();
    	    if (!in_array ($current, $result)){
    	    	$result[] = $current;
    	    }

    	}
    	return $result;
    }

    /**
    * get the list of heading
    *
    * @param $from the node we wants the tree to begin with.
    */
    function getFullList ($from = null) {
        return $from == null ? $this->_hashChild['ROOT_NODE'] : $this->_hashChild[$from];
    }

    /**
    * Gets the ordered list
    */
    function getFlatOrderedList ($from = null) {
        $element = $this->getTree ($from);

        $toReturn[] = $element;
        foreach ($element->childs as $idChild=>$child) {
            $this->_appendChildsInArray ($child, $toReturn);
        }

        return $toReturn;
    }

    /**
    * appends the given child and its childs to the given array
    * @param object toAppend the child to append with its childs
    * @param array appendTo the array to append the childs to
    */
    function _appendChildsInArray ($toAppend, & $appendTo){
        $appendTo[] = $toAppend;
        foreach ($toAppend->childs as $idChild=>$child){
            $this->_appendChildsInArray ($child, $appendTo);
        }
    }

    /**
    * Appends the childs to a given element.
    */
    function _appendChilds (& $element){
        $element->childs = isset ($this->_hashChild[$element->id_head]) ? $this->_hashChild[$element->id_head] : array ();
        foreach ($element->childs as $key=>$child){
            $this->_appendChilds ($element->childs[$key]);
        }
    }

    /**
    * get the list of immediat childs of the given headings
    *  We DO NOT apply the Rights System
    *
    * @param $from the node we wants the child of
    */
    function getLevel ($from = null) {
        $this->_loadHashChild ();
        $from = $from == null ? 'ROOT_NODE' : $from;
        if (!isset ($this->_hashChild [$from])){
            $this->_hashChild [$from] = array ();
        }
        return $this->_hashChild [$from];
    }

    /**
    * gets the fathers list from a node.
    */
    function getFathers ($from) {
        $this->_loadHashFather();
        $toReturn = array ();
        while (isset ($this->_hashFather[$from])){
            $toReturn[] = $this->_hashFather[$from];
            $from = $this->_hashFather[$from];
        }
        return $toReturn;
    }

    /**
    * gets the heading as a tree.
    */
    function getTree ($from = null) {
        $this->_loadTree ();
        $this->_loadHashFather();
        $this->_loadHashChild();

        if ($from == null){
        	$element = new StdClass ();
            $element->id_head = null;
            $element->caption_head = CopixI18N::get ('copixheadings|headings.message.root');
        }else{
            $element = $this->_treeHeadings[$from];
        }

        $element->childs = isset ($this->_hashChild[$from == null ? 'ROOT_NODE' : $from]) ? $this->_hashChild[$from == null ? 'ROOT_NODE' : $from] : array ();
        foreach ($element->childs as $childId=>$child) {
            $this->_appendChilds($element->childs[$childId]);
        }
        return $element;
    }

    /**
    * Loads the headings tree in the class itself
    */
    function _loadTree () {
    	try {
    	    if ($this->_treeHeadings === false){
	            $this->_treeHeadings = array ();
	            $dao = CopixDAOFactory::getInstanceOf ('copixheadings|CopixHeadings');
	            foreach ($dao->findAll () as $heading){
	                $this->_treeHeadings[$heading->id_head] = $heading;
	            }
	        }
    	} catch(Exception $e) {
    		echo $e->getMessage();
    	}
    }

    /**
    * builds the hash table for the father finding.
    */
    function _loadHashFather (){
        if ($this->_hashFather === false){
            //load the tree.... if needed.
            $this->_loadTree ();

            foreach ($this->_treeHeadings as $heading) {
                $this->_hashFather[$heading->id_head] = $heading->father_head;
            }
            $this->_hashFather['ROOT_NODE'] = null;
        }
    }

    /**
    * builds the hash table for the child finding.
    */
    function _loadHashChild (){
        if ($this->_hashChild === false) {
            //load the tree.... if needed.
            $this->_loadTree ();
            foreach ($this->_treeHeadings as $heading) {
                if ($heading->father_head == null){
                    //Will create a virtual key for the root node.
                    //We assume we will never get a father_head == ROOT_NODE as for now, father_head is an integer
                    $this->_hashChild['ROOT_NODE'][$heading->id_head] = $this->_treeHeadings[$heading->id_head];
                } else {
                    //adding childs to the hash table
                    $this->_hashChild[$heading->father_head][$heading->id_head] = $this->_treeHeadings[$heading->id_head];
                }
            }
        }
    }

    /**
    * initialization of the hash tables.
    */
    function _initHash () {
        $this->_hashFather   = false;
        $this->_hashChild    = false;
        $this->_treeHeadings = false;
    }
}
?>
