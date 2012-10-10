<?php
/**
* @package	cms
* @subpackage survey
* @author	Bertrand Yan
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('survey|surveyoption');
/**
* @package	cms
* @subpackage survey
* ActionGroupSurvey
*/
class ActionGroupSurvey extends CopixActionGroup {
   /**
   * add a vote to a survey.
   * save to datebase and set cookie and session flag to true.
   */
   function doVote (){
      $dao  = & CopixDAOFactory::getInstanceOf ('Survey');
      if (!$toEdit = $dao->get ($this->vars['id'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('survey.unable.get'),
            'back'=>CopixUrl::get ('||')));
      }
      $toEdit->option_svy = unserialize($toEdit->option_svy);
      $toEdit->option_svy[$this->vars[$toEdit->id_svy]]->addResponse ();
      $toEdit->response_svy++;
      $toEdit->option_svy = serialize($toEdit->option_svy);
      $dao->update ($toEdit);
      $this->_setSesssionCookie ($toEdit->id_svy);
      $this->_setCookie ($toEdit->id_svy);
      return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
   }

   /**
   * sets flag survey in session.
   * @param int id survey identifier

   */
   function _setSesssionCookie ($id){
      $_SESSION['MODULE_SURVEY_VOTED_'.$id] = true;
   }

   /**
   * sets flag survey in cookie
   * @param int id survey identifier.

   */
   function _setCookie ($id) {
      //expire in 4 years
      setcookie('MODULE_SURVEY_VOTED_'.$id, true, time() + 60*60*24*365*4);
   }
}
?>