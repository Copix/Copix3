<?php
/**
* @package	cms
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|CMSParseContext');
CopixClassesFactory::fileInclude ('cms|PortletFactory');
CopixClassesFactory::fileInclude ('cms|CMSPortletTools');

/**
 * @package cms
* Une page du CMS
*/
class CMSPage {
    /**
    * Identifiant interne et unique de la page
    * @var int
    */
    var $id_cmsp = null;

    /**
    * Identifiant publique de la page. Les versions d'une page se partagent cet identifiant
    * @var int
    */
    var $publicid_cmsp = null;

    /**
    * La version de la page. Les brouillons disposent d'un numéro de version égal à zéro
    * @var int
    */
    var $version_cmsp = 0;

    /**
    * La rubrique à laquelle la page appartient
    * @var int
    */
    var $id_head = null;

    /**
    * Le titre de la page
    * @var string
    */
    var $title_cmsp = null;

    /**
    * Le titre du navigateur pour la page
    * @var string
    */
    var $titlebar_cmsp = null;

    /**
    * L'auteur de la page (login)
    * @var string
    */
    var $author_cmsp = null;

    /**
    * Le résumé de la page
    * @var string
    */
    var $summary_cmsp = null;

    /**
    * Le statut de la page
    * @see CopixWorkflow
    * @var int
    */
    var $status_cmsp = null;

    /**
    * Les mots clefs associés à la page.
    * @var string
    */
    var $keywords_cmsp = null;

    /**
    * Date de péremption de la page
    * @var string
    */
    var $datemax_cmsp = null;

    /**
    * Date de mise en ligne de la page
    * @var string
    */
    var $datemin_cmsp = null;

    /**
    * Date de passage au statut courant
    * @var string
    */
    var $statusdate_cmsp = null;

    /**
    * Qui a fait passer la page à son statut actuel ?
    * @var string
    */
    var $statusauthor_cmsp = null;

    /**
    * Commentaire associé au dernier changemet de statut
    * @var string
    */
    var $statuscomment_cmsp = null;
    
    /**
    * L'objet tel qu'il est stocké en base
    * @var string
    */
    var $content_cmsp = null;

    /**
    * Le template associé à la page
    * @var mixed
    */
    var $templateId = null;

    /**
    * template vars list.
    * only a single array with $templateVars = array ('name', 'name2', 'name3')
    */
    var $templateVars = array ('Zone1', 'Zone2');

    /**
    * portlets
    * will be stored as $portlets[varName] = array (position => $portlet)
    */
    var $portlets = array ();

    /**
    * Portlets messages (very specific)
    * @var array
    */
    var $_portletMessages = array ();

    /**
    * @param $initFrom CMSPageDAORecord the object we'll init the CMSPage with
    */
    function CMSPage ($initFrom = null){
    	if ($initFrom !== null){
    		$this->initFrom ($initFrom);
    	}
    }

    /**
    * Initialise la page depuis un enregistrement
    * @param object $pCms l'objet provenant d'un fetch représentant l'enregistrement page
    */
    function initFrom (& $pCms){
        $this->id_cmsp       = $pCms->id_cmsp;
        $this->version_cmsp  = $pCms->version_cmsp;
        $this->publicid_cmsp = $pCms->publicid_cmsp;

        $this->titlebar_cmsp   = $pCms->titlebar_cmsp;
        $this->title_cmsp   = $pCms->title_cmsp;
        $this->summary_cmsp = $pCms->summary_cmsp;
        $this->author_cmsp  = $pCms->author_cmsp;
        $this->id_head      = $pCms->id_head;
        $this->keywords_cmsp    = $pCms->keywords_cmsp;
        $this->status_cmsp     = $pCms->status_cmsp;
        $this->datemax_cmsp    = $pCms->datemax_cmsp;
        $this->datemin_cmsp    = $pCms->datemin_cmsp;        
        $this->statusdate_cmsp = $pCms->statusdate_cmsp;
        $this->statusauthor_cmsp = $pCms->statusauthor_cmsp;
        $this->statuscomment_cmsp = $pCms->statuscomment_cmsp;

        //unserialization
        $serializedInformations = unserialize ($pCms->content_cmsp);
        $this->templateId = $serializedInformations['templateId'];
        $this->templateVars = $serializedInformations['templateVars'];
        $this->portlets     = $serializedInformations['portlets'];
    }

    /**
    * get the parsed page.
    * @param CopixCMSParseContext $context the context of the requested parsing.
    *   eg we wants the parsed page for a search engine, for a private access, for ..., ...
    */
    function getParsed ($context){
        $tpl = new CopixTpl ();
        //parcour des variables du template.
        CopixHTMLHeader::addJSCode ("
               function toggleDisplayAddPortletLink(id) {
                  if (document.getElementById(id).style.visibility=='hidden') {
                     document.getElementById(id).style.visibility='visible';
                  }else{
                     document.getElementById(id).style.visibility='hidden';
                  }
                  if (document.getElementById(id).style.display=='none') {
                     document.getElementById(id).style.display='';
                  }else{
                     document.getElementById(id).style.display='none';
                  }
                  return false;
               }
            ");

        foreach ((array)$this->templateVars as $nameVar){
            $contentVar = '';
            if (isset ($this->portlets[$nameVar])){
                foreach ($this->portlets[$nameVar] as $position=>$portlet){
                    //sets the page AND the params
                    $portlet->setParams (CopixController::instance ()->vars);
                    $portlet->setPage ($this);

                    //Now parsing.
                    if ($context === CMSParseContext::edit) {
                        $textBar  = '<div class="bar">';

                        //GOT FROM ZONE
                        //Pour déterminer les action possibles, on calcule position et nombre
                        //d'éléments dans la page
                        $id_element                     = $portlet->id;
                        $position_of_element            = $position;
                        $number_of_element_in_same_zone = count ($this->portlets[$nameVar]);

                        //indique au template les actions possibles.
                        $up_enabled   = ($position_of_element > 0) ? true : false;
                        $down_enabled = (($position_of_element < ($number_of_element_in_same_zone - 1)) ? true : false);
                        //END GOT FROM ZONE

                        $textBar .= '<a href="'.CopixUrl::get ('cms|admin|preparePortletEdit', array ('id'=>$portlet->id)).'" ><img src="'.CopixUrl::getResource('img/tools/update.png').'" alt="'.CopixI18N::get ('copix:common.buttons.update').'" title="'.CopixI18N::get ('copix:common.buttons.update').'"/></a>&nbsp;';
                        //now we asks for the modules to know if we can delete portlet
                        $response = CopixEventNotifier::notify(new CopixEvent ('DeletePortlet', array ('id_cmsp'=>$this->id_cmsp,'portlet'=>$portlet)));
                        $who = array ();
                        if (!$response->inResponse ('canDelete', false, $who)){
                            $textBar .= '<a href="'.CopixUrl::get ('cms|admin|deletePortlet', array ('id'=>$portlet->id)).'"><img src="'.CopixUrl::getResource('img/tools/delete.png').'"  alt="'.CopixI18N::get ('copix:common.buttons.delete').'" title="'.CopixI18N::get ('copix:common.buttons.delete').'" /></a>&nbsp;';
                        }
                        $textBar .= '<a href="'.CopixUrl::get ('cms|admin|copyPortlet', array ('id'=>$portlet->id)).'"><img src="'.CopixUrl::getResource ('img/tools/copy.png').'"  alt="'.CopixI18N::get ('copix:common.buttons.copy').'"  title="'.CopixI18N::get ('copix:common.buttons.copy').'" /></a>&nbsp;';
                        $textBar .= '<a href="'.CopixUrl::get ('cms|admin|cutPortlet', array ('id'=>$portlet->id)).'"><img src="'.CopixUrl::getResource ('img/tools/cut.png').'"  alt="'.CopixI18N::get ('copix:common.buttons.cut').'"  title="'.CopixI18N::get ('copix:common.buttons.cut').'" /></a>&nbsp;';
                        if ($down_enabled) {
                            $textBar .= '<a href="'.CopixUrl::get ('cms|admin|movePortletDown', array ('id'=>$portlet->id)).'"><img src="'.CopixUrl::getResource ('img/tools/down.png').'" alt="'.CopixI18N::get ('copix:common.buttons.movedown').'"  title="'.CopixI18N::get ('copix:common.buttons.movedown').'" /></a>';
                        }
                        if ($up_enabled) {
                            $textBar .= '<a href="'.CopixUrl::get ('cms|admin|movePortletUp', array ('id'=>$portlet->id)).'"><img src="'.CopixUrl::getResource ('img/tools/up.png').'" alt="'.CopixI18N::get ('copix:common.buttons.moveup').'"  title="'.CopixI18N::get ('copix:common.buttons.moveup').'" /></a>';
                        }
                        $textBar .= '</div>';
                        $contentVar .= $textBar;

                        $contentVar .= '<div id="a_'.$portlet->id.'" class="portlet">';
                        $contentVar .= $portlet->getParsed ($context);
                        $contentVar .= '</div>';
                    }else{
                        $contentVar .= '<div class="portlet">'.$portlet->getParsed ($context).'</div>';
                    }
                }
            }
            if ($context === CMSParseContext::edit) {
                $contentVar .= '<a id="a_'.$nameVar.'" href="'.CopixUrl::get ('cms|admin|portletChoice', array ('templateVar'=>$nameVar)).'" onclick="return toggleDisplayAddPortletLink(\''.$nameVar.'\')">';
                $contentVar .= '<img src="'.CopixUrl::getResource ('img/tools/add.png').'" alt="'.CopixI18N::get ('copix:common.buttons.add').'" />'.CopixI18N::get ('copix:common.buttons.add').'</a>';
                $contentVar .= '<div class="popupInformation" id="'.$nameVar.'" style="visibility:hidden;display:none" >';
                $contentVar .= '<ul>';

                //sorting portlets by name
                $sort = array ();
                foreach ((array)PortletFactory::getList () as $elem){
                	PortletFactory::includePortlet ($elem);
                    $sort[CopixI18N::get(eval ('return Portlet'.$elem.'::getI18NKey ();'))] = $elem;
                }
                ksort ($sort);

                //get all installed portlet
                foreach ($sort as $caption=>$elem){
                  $contentVar .= '<li><a href="'.CopixUrl::get('cms|admin|newPortlet', array('portlet'=>$elem, 'templateVar'=>$nameVar)).'">'.$caption.'</a></li>';
                }
                $contentVar .= '<ul></div>';
                if (CMSPortletTools::getClipboardPortlet()) {
                   $contentVar .= ' <a href="'.CopixUrl::get ('cms|admin|pastePortlet', array ('templateVar'=>$nameVar)).'"><img src="'.CopixUrl::getResource ('img/tools/paste.png').'"  alt="'.CopixI18N::get ('copix:common.buttons.paste').'"  title="'.CopixI18N::get ('copix:common.buttons.paste').'" /></a>';
                }
            }
            $tpl->assign ($nameVar, $contentVar);
        }
        $tpl->assign ('TITLE_PAGE', $this->title_cmsp);
        if ($context === CMSParseContext::newsletter){
            $tplMain = new CopixTpl ();
            $tplMain->assign ('MAIN', $tpl->fetch ($this->templateId ? $this->templateId : 'cms|colonnes_1.layout.tpl'));
            return $tplMain->fetch ('|blank.ptpl');
        }else{
         	return $tpl->fetch ($this->templateId ? $this->templateId : 'cms|colonnes_1.layout.tpl');
        }
    }

    /**
    * Gets the CMSPage Record
    */
    function getRecord () {
        $record = CopixDAOFactory::createRecord ('CMSPage');

        $record->id_cmsp      = $this->id_cmsp;
        $record->version_cmsp = $this->version_cmsp;
        $record->publicid_cmsp = $this->publicid_cmsp;

        $record->titlebar_cmsp   = $this->titlebar_cmsp;
        $record->title_cmsp   = $this->title_cmsp;
        $record->summary_cmsp = $this->summary_cmsp;
        $record->author_cmsp  = $this->author_cmsp;
        $record->id_head      = $this->id_head;
        $record->status_cmsp  = $this->status_cmsp;
        $record->keywords_cmsp   = $this->keywords_cmsp;
        $record->datemax_cmsp    = $this->datemax_cmsp;
        $record->datemin_cmsp    = $this->datemin_cmsp;
        $record->statusdate_cmsp = $this->statusdate_cmsp;
        $record->statusauthor_cmsp = $this->statusauthor_cmsp;
        $record->statuscomment_cmsp = $this->statuscomment_cmsp;

        $serializedInformations['templateId'] = $this->templateId;
        $serializedInformations['templateVars'] = $this->templateVars;
        $serializedInformations['portlets'] = $this->portlets;
        $record->content_cmsp = serialize ($serializedInformations);

        return $record;
    }
    
    /**
     * Fonction qui vérifie si la page peut être sauvegardée en base
     * c'est globalement un alias à la méthode check du record associé.
     * @return true si ok, tableau d'erreurs sinon.
     */
    function check (){
       $dao = CopixDAOFactory::create ('CMSPage');    	
       $record = $this->getRecord ();
       return $dao->check ($record);    	
    }

    /**
    * updates the given portlet in the page
    * @param object the portlet to update
    * @return boolean if the portlet was updated
    */
    function updatePortlet ($portlet){
        foreach ($this->portlets as $varName=>$tab){
            foreach ($tab as $position => $port){
                if ($portlet->id == $port->id){
                    $this->portlets[$varName][$position] = $portlet;
                    return true;
                }
            }
        }
        return false;
    }

    /**
    * adds a portlet
    * @param object $portlet the portlet we wants to add
    * @param int position the portlet position (variable where we wants to add the portlet)
    * @return boolean if the portlet was added
    */
    function addPortlet ($portlet, $position){
        if ($this->findPortletById ($portlet->id) !== null){
            return false;
        }

        if (! in_array ($position, $this->templateVars)){
            return false;
        }

        $this->portlets[$position][] = $portlet;
        return true;
    }

    /**
    * Search for a portlet from its id.
    * @param string $id the id of the portlet
    * @return object the portlet
    */
    function findPortletById ($id){
        foreach ($this->portlets as $varName=>$tab){
            foreach ($tab as $position => $portlet){
                if ($portlet->id == $id){
                    return $this->portlets[$varName][$position];
                }
            }
        }
        return null;
    }

    /**
    * Deletes a portlet of the given id
    * @param string $id the portlet id
    * @return boolean if the portlet was deleted
    */
    function deletePortlet ($id){
        foreach ($this->portlets as $varName=>$tab){
            $moved = false;
            foreach ($tab as $position => $portlet){
                if ($portlet->id == $id){
                    array_splice ($this->portlets[$varName], $position, 1);
                    return true;
                }
            }
        }
        return false;
    }

    /**
    * gets the position of a portlet.
    * @param string $id the portlet id
    * @returns obj->position, obj->varName
    */
    function getPortletPosition ($id){
        foreach ($this->portlets as $varName=>$tab){
            foreach ($tab as $position => $portlet){
                if ($portlet->id == $id){
                	$toReturn = new StdClass ();
                    $toReturn->position = $position;
                    $toReturn->varName  = $varName;
                    return $toReturn;
                }
            }
        }
        return null;
    }

    /**
    * Moves a portlet up
    */
    function movePortletUp ($id){
        $position = $this->getPortletPosition ($id);
        if ($position === null){
            return false;
        }
        $portlet = $this->portlets[$position->varName][$position->position-1];
        $this->portlets[$position->varName][$position->position-1] = $this->portlets[$position->varName][$position->position];
        $this->portlets[$position->varName][$position->position]   = $portlet;
        return true;
    }

    /**
    * Moves a portlet down
    */
    function movePortletDown ($id){
        $position = $this->getPortletPosition ($id);
        if ($position === null){
            return false;
        }
        $portlet = $this->portlets[$position->varName][$position->position+1];
        $this->portlets[$position->varName][$position->position+1] = $this->portlets[$position->varName][$position->position];
        $this->portlets[$position->varName][$position->position]   = $portlet;
        return true;
    }

    /**
    * Says if the page has portlets of a given kind
    * @param string kind the portlet kind id we're looking for
    * @return boolean
    */
    function hasPortletOfKind ($kind){
        foreach ((array)$this->portlets as $varName=>$portlets){
            foreach ($portlets as $portlet){
                if ($portlet->getAddOnName () == $kind) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
    * adds a message, who's purpose is to be passed to a portlet
    */
    function addPortletMessage ($messageName, $messageValue){
        $this->_portletMessages[$messageName] = $messageValue;
    }

    /**
    * gets a given message.
    */
    function gePortletMessage ($messageName) {
        return isset ($this->_portletMessages[$messageName]) ? $this->_portletMessages[$messageName] : null;
    }

    /**
    * sets the template for the portlet
    */
    function setTemplate ($templateName){
    		$this->templateId = $templateName;
    }

	/**
	* Extract the vars of a template.
	*/
	function _extractTemplateVars ($templateContent){
		$out = array ();
		preg_match_all('{\$(\w+)}', $templateContent, $out);
		if (count ($out)){
		   return $out[1];
		}else{
			return array ();
		}
	}
}
?>
