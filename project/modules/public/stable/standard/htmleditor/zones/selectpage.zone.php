<?php
/**
* @package		standard 
 * @subpackage	htmleditor
* @author		Bertrand Yan
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Permet de sélectionner une page du CMS
* @package		standard 
 * @subpackage	htmleditor
*/
class ZoneSelectPage extends CopixZone {
    function _createContent (&$toReturn) {
      //Création du sous template.
      $tpl = new CopixTpl ();

      CopixClassesFactory::fileInclude ('cms|ServicesCMSPage');

      CopixContext::push('cms');
      $sHeadings = CopixClassesFactory::getInstanceOf ('copixheadings|CopixHeadingsServices');
      $headings  = $sHeadings->getTree();

      $cmsPages  = new ServicesCMSPage ();
      $pages     = $cmsPages->getList ();
      if (isset ($this->_params['onlyLastVersion']) && $this->_params['onlyLastVersion'] == 1){
         $pages = $this->_filterLastVersion ($pages);
      }
      CopixContext::pop();
      //pagination
      foreach ($pages as $page){
          $arPages[$page->id_head][] = $page;
      }

      $tpl->assign ('arPublished', $arPages);
      $tpl->assign ('arHeadings', $headings);
      
      $tpl->assign ('select'     , $this->getParam ('select'));
      $tpl->assign ('back'       , $this->getParam ('back'));
      $tpl->assign ('popup'      , $this->getParam ('popup'));
      $tpl->assign ('height'     , Copixconfig::get('htmleditor|height'));
      $tpl->assign ('width'      , Copixconfig::get('htmleditor|width'));
      //$tpl->assign ('editorType' , CopixConfig::get('htmleditor|type'));
      $tpl->assign ('editorName' , $this->getParam ('editorName'));
      $toReturn = $tpl->fetch ('page.select.ptpl');
      return true;
	}

   /**
   * Filtrage sur les derniers version seulement.
   */
   function _filterLastVersion ($arPublished){
      $toReturn = array ();
      foreach ($arPublished as $key=>$page){
         if (!isset ($toReturn[$page->id_cmsp])){
            //C'est la première version trouvée, on la met.
            $toReturn[$page->id_cmsp] = $page;
         }else if ($toReturn[$page->id_cmsp]->version_cmsp < $page->version_cmsp){
            //C'est une version plus récente, on remplace.
            $toReturn[$page->id_cmsp] = $page;
         }
      }
      return $toReturn;
	}
}
?>