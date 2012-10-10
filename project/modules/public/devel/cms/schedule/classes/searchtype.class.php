<?php
/**
* @package	cms
* @subpackage schedule
* @version	$Id: searchtype.class.php,v 1.1 2007/04/08 18:08:14 gcroes Exp $
* @author	Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam

* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage schedule
* SearchType
*/
class searchType{
    var $dateto_evnt=null;
    var $datefrom_evnt=null;
    //var $title_evnt="";
    var $id_evtc=null;

    function searchType($df,$dt,$evtc){
        $this->dateto_evnt=preg_replace(':(\d{2})/(\d{2})/(\d{4}):','\\3\\2\\1',$dt);
        $this->datefrom_evnt=preg_replace(':(\d{2})/(\d{2})/(\d{4}):','\\3\\2\\1',$df);
        //$this->title_evnt=$t;
        $this->id_evtc=$evtc;
    }

    function isEmpty(){
        $vars=get_object_vars($this);
        foreach($vars as $var){
            if($var>0 and $var!="" and $var!=null){
                return false;
            }
        }
        return true;
    }

}
?>