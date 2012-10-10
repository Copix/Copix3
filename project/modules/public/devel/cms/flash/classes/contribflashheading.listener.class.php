<?php
/**
* @package		cms
* @subpackage	flash
* @author		Croës Gérald, Salleyron Julien
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package		cms
* @subpackage	flash
* Listener pour les événements relatifs à la gestion de contenu (ici module copixheadings)
*/
class ListenerContribFlashHeading extends CopixListener {
   /**
   * Indique que l'on peut administrer les documents flash
   * @param	CopixEvent			$pEvent			l'événement
   * @param CopixEventResponse	$pEventResponse	La réponse à donner si l'on peut ou non administrer l'élément
   */
   function performHeadingAdminBrowsing ($pEvent, $pEventResponse){
   	  CopixClassesFactory::fileInclude ('flash|AuthFlash');
   	  if (AuthFlash::canWrite ($pEvent->getParam ('id'))){
         $pEventResponse->add (array ("module"=>"flash", 
                                     "icon"=>CopixUrl::getResource("img/modules/flash/flash.png"), 
                                     'shortDescription'=>CopixI18N::get ('flash|flash.shortDescription'), 
                                     'longDescription'=>CopixI18N::get ('flash|flash.moduleDescription')));   	  	
   	  }
   }

   /**
   * Indique s'il existe du contenu dans la rubrique donnée
   * @param	CopixEvent			$pEvent			l'événement (avec un paramètre id pour l'identifiant de la rubrique)
   * @param CopixEventResponse	$pEventResponse	La réponse à donner si l'on peut ou non administrer l'élément (ou il faut renseigner hasContent)
   */
   function performHasContentRequest ($pEvent, $pEventResponse){
       $dao  = CopixDAOFactory::getInstanceOf ('flash|flash');
       $sp   = CopixDAOFactory::createSearchParams ();
       $sp->addCondition ('id_head', '=', $pEvent->getParam ('id'));
       $arFlashDocuments = $dao->findBy ($sp);

       if (count ($arFlashDocuments)){
           $pEventResponse->add (array ('hasContent'=>true));
           return;
       }

       //no documents
       $pEventResponse->add (array ('hasContent'=>false));
   }
}
?>