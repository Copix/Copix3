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
* @package	cms
* @subpackage survey
* Survey Option
*/
class SurveyOption {
   /**
   * Titre de la réponse
   */
   var $title;

   /**
   * Nombre de réponse sur le sujet ?
   */
   var $response = 0;

   /**
   * Constructeur
   * @param $title - le titre de la réponse.
   */
   function SurveyOption ($title) {
      $this->title     = $title;
   }

   /**
   * Ajoute une réponse sur le sondage en question
   */
   function addResponse () {
      $this->response++;
   }

}
?>