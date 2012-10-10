<?php
/**
* @package	cms
* @subpackage pictures
* @author	Bertrand Yan
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package	cms
* @subpackage pictures
* Implémentation des actions standards sur la Phototèque
*/
/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('pictures|PicturesSearchParams');
/**
* @package	cms
* @subpackage pictures
* ActionGroupBrowser
*/

class ActionGroupBrowser extends CopixActionGroup {

   /**
   * Affichage de la fen^etre de sélection de la phototèque.
   */
   function getBrowser () {
      $tpl = & new CopixTpl ();
      $tpl->assign ('TITLE_BAR', CopixI18N::get ('browser.title'));

      //verifie si la variable de session existe sinon initialise les paramètres
      $params  = $this->_getSessionSearchParams ();
      $params->cols = isset($params->cols) ? $params->cols : CopixConfig::get('pictures|nbCols');
      $params->rows = isset($params->rows) ? $params->rows : CopixConfig::get('pictures|nbRows');
      
      $tpl->assign ('MAIN', CopixZone::process ('PicturesBrowser', array ('searchParams'=>$params, 
            'maxX'=>CopixConfig::get ('pictures|maxX'), 'maxY'=>CopixConfig::get ('pictures|maxY'), 
            'popup'=>CopixRequest::get ('popup', '', true), 'select'=>CopixRequest::get('select'), 
            'id_head'=>CopixRequest::get ('id_head', '', true), 'back'=>CopixRequest::get('back'))));

      if (CopixRequest::get ('popup', false, true)) {
         return new CopixActionReturn (CopixactionReturn::DISPLAY_IN, $tpl, '|blank.tpl');
      }else{
         return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
      }
   }

   /**
   * méthode gérant les paramètres de recherche
   */
   function doValidSearchParams () {
      //recuperation des variables dans la session
      $params        = $this->_getSessionSearchParams ();

      //Mise à jour des paramètres de recherche reçus
      $arMaj = array ('category', 'theme', 'keyWord', 'format', 'maxWeight',
                      'maxWidth', 'maxHeight', 'cols', 'rows', 'searchByTheme');
      foreach ($arMaj as $majVar){
         if (isset ($this->vars[$majVar])){
            $params->$majVar = $this->vars[$majVar];
         }
      }

      $params->category = isset ($this->vars['category']) ? $this->vars['category'] : array ();
      $params->theme    = isset ($this->vars['theme'])    ? $this->vars['theme']    : array ();

      //inscription en session des paramètres
      $this->_setSessionSearchParams ($params);

      //redirectioon vers le get browser
      return CopixActionGroup::process ('pictures|browser::getBrowser',
                     array ('popup'=>$this->vars['popup'], 'select'=>CopixRequest::get ('select'), 
                     'back'=>CopixRequest::get ('back'), 'id_head'=>$this->vars['id_head']));
   }

   /**

   */
   function _getSessionSearchParams (){
      return isset($_SESSION['MODULE_PICTURES_SEARCHPARAMS']) ? unserialize($_SESSION['MODULE_PICTURES_SEARCHPARAMS']) : new PicturesSearchParams(array(),array(),null,'all',null,null,null,CopixConfig::get ('pictures|nbCols'),CopixConfig::get ('pictures|nbRows'),false);
   }

   /**

   */
   function _setSessionSearchParams ($params){
      $_SESSION['MODULE_PICTURES_SEARCHPARAMS'] = serialize ($params);
   }

   /**

   */
   function _getSessionSelect (){
      return isset ($_SESSION['MODULE_PICTURES_SELECT']) ? unserialize ($_SESSION['MODULE_PICTURES_SELECT']) : null;
   }
}
?>
