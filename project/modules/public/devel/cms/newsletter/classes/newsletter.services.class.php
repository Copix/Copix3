<?php
/**
* @package	cms
* @subpackage newsletter
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
require_once (COPIX_UTILS_PATH.'CopixEMailer.class.php');
CopixClassesFactory::fileInclude ('cms|ServicesCMSPage');

/**
 * @package cms
 * @subpackage newsletter
 * ServicesNewsLetter
 */
class ServicesNewsletter {
    function _getParsedPage ($page){
    	$errors = array ();
        $main  = ServicesCMSPage::getPageContent($page, $errors, false);
        /* Ajout PGU 06/01/2006 suite à demande Stex pour ne pas faire apparaître le lien de désinscription
        $main .= '<p>';
        $main .= CopixI18N::get ('newsletter.mail.unsubscribe');
        $main .= '&nbsp;<a href="'.CopixUrl::get('newsletter|default|unsubscribe', array('mail'=>'[--COPIX-NEWSLETTER-MAILADRESS-SPECIAL-TO-REPLACE-STRING--]'));
        $main .= '">'.CopixI18N::get ('newsletter.mail.subject.here').'</a></p>';
        */

        $tpl = & new CopixTpl ();
        $tpl->assign ('TITLE_PAGE', $page->title_cmsp);
        $tpl->assign ('MAIN'      , $main);
        
        $config = CopixConfig::instance ();
        $content = $tpl->fetch ($config->mainTemplate);

        $content = str_replace ('href="index.php','href="'.CopixUrl::get().'index.php',$content);
        $content = str_replace ('src="index.php','src="'.CopixUrl::get().'index.php',$content);
        $content = str_replace ('src="./', 'src="'.CopixUrl::get().'./',$content);
        $content = str_replace ('src="/', 'src="http://'.$_SERVER['HTTP_HOST'].'/',$content);
        $content = str_replace ('href="/', 'href="http://'.$_SERVER['HTTP_HOST'].'/',$content);
        return $content;
    }

    function sendTest ($page, $to) {
        $subject = $page->title_cmsp;
        $message = $this->_getParsedPage ($page);

        $messageTextAlternatif = $this->_getTextAlternatif ($page, 'TEST');
        $monMailHTML = & new CopixHTMLEMail ($to, null, null, $subject, $message, $messageTextAlternatif);
        $monMailHTML->send ();
    }
    
    function _getTextAlternatif ($page, $date) {
        $toReturn  = CopixI18N::get ('newsletter.mail.alternativeLink').' ';
        $toReturn .= CopixUrl::get ('newsletter|default|get', array('id'=>$page->id_cmsp, 'date'=>$date));
        return $toReturn;
    }
    
    /**
    * Make and return all mail from given groups
    */
    function _getArMailFromGroups ($groupId, $copixGroupId) {
        $daoMail  = & CopixDAOFactory::getInstanceOf ('NewsletterMail');
        $daoLink  = & CopixDAOFactory::getInstanceOf ('NewsletterMailLinkGroups');
        $sp       = CopixDAOFactory::createSearchParams ();
        
        $sp->addCondition ('id_nlg', '=', $groupId);
        $mailFromNewsletterGroup = $daoLink->findBy($sp);
        $toReturn = array ();
        foreach ((array)$mailFromNewsletterGroup as $mail){
            $toReturn[] = $mail->mail_nlm;
        }

        foreach ((array)$copixGroupId as $id){
            $copixGroup = & new CopixGroup($id);
            foreach ((array)$copixGroup->getUsers() as $login){
         	   $auth       = & CopixController::instance ()->getPlugin ('auth|auth');
         	   $userObj    = $auth->getUser ();
               $user       = $userObj->get($login);
               $toReturn[] = $user->email;
            }
        }
        return $toReturn;
    }

    function sendGroup ($page, $groupId, $copixGroupId, $first, $date) {
//        if (!($arMail = $this->_getSessionArMail())) {
           $arMail = $this->_getArMailFromGroups ($groupId, $copixGroupId);
//           $this->_setSessionArMail($arMail);
//        }
        //Nombre de mail à envoyer par passe
        $nbToSend = 10;

        if (count($arMail)) {
         /*Sylvain*/
         if ($first){
            $_SESSION['NEWSLETTER_COUNTER'] = 0;//aucun envois encore effectué.
            $toAdd = 0;
         }else{
            $toAdd = $_SESSION['NEWSLETTER_COUNTER'];
         }
         //echo $_SESSION['NEWSLETTER_COUNTER'].'<hr/>';
         /* /Sylvain*/
         $toSend = array ();
         $i = 0;
         
         $daoMail  = & CopixDAOFactory::getInstanceOf ('NewsletterMail');
         foreach ($arMail as $idMail){
         
            // on ajoute que les éléments de la fenetre actuelle
            if ($i >= $toAdd && $i < $toAdd + $nbToSend) {
               $toSend[] = $idMail;
               //echo $idMail->id_nlm.'<br/>';
               $lastSent = $i+1; // permet de savoir quel est le dernier élément envoyé. à la fin du script on test s'il est égale au dernier élément de la liste des mails dans ce cas on retourne false pour terminer
            }
            $i++;
         }

        /* $sp = CopixDAOFactory::createSearchParams ();
         $sp->addCondition ('id_nlm','=',$tab);
         $toSend = $daoMail->findBy($sp);*/

         $subject       = $page->title_cmsp;
         $debutMessageAlternatif  = CopixI18N::get ('newsletter.messages.mail');
         $debutMessageAlternatif .= ' '.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
         $debutMessageAlternatif .= '?module=cms&id='.$page->id_cmsp;

//         $debutMessage  = '<center>'.$debutMessageAlternatif.'</center><br />'.$this->_getParsedPage ($page);
         $debutMessage  = $this->_getParsedPage ($page);
         $counterSend = 0;
         foreach ($toSend as $currentMail) {
            $message = $debutMessage;
            $messageTextAlternatif = $debutMessageAlternatif;

            //$to = $toSend[$i+$toAdd]->mail_nlm;
            $to = $currentMail;
            $cc = '';
            $cci = '';

	         $message = str_replace ('[--COPIX-NEWSLETTER-MAILADRESS-SPECIAL-TO-REPLACE-STRING--]', $to, $message);

	         $messageTextAlternatif .= "\n\r".CopixI18N::get ('newsletter.mail.unsubscribe');
            //$messageTextAlternatif .= '&nbsp;'.CopixI18N::get ('newsletter.messages.url');
            $messageTextAlternatif .= '&nbsp;http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
            $messageTextAlternatif .= '?module=newsletter&action=unsubscribe&mail=';
            $messageTextAlternatif .= $to;
            $monMailHTML = new CopixHTMLEMail ($to, $cc, $cci, $subject, $message, $messageTextAlternatif);
            $monMailHTML->send ();
            $counterSend++;
         }
         $_SESSION['NEWSLETTER_COUNTER'] += $nbToSend;
         if ($i == $lastSent) { // il ne reste plus de mails à envoyer
            return false;
         }else{
            return true;
         }

        }
    }
    
    /**
    * gets the current array of mail.

    */
    function _getSessionArMail () {
        return isset ($_SESSION['MODULE_NEWSLETTER_ARRAY_MAIL']) ? unserialize ($_SESSION['MODULE_NEWSLETTER_ARRAY_MAIL']) : null;
    }

    /**
    * sets the current array of mail.

    */
    function _setSessionArMail ($toSet){
        $_SESSION['MODULE_NEWSLETTER_ARRAY_MAIL'] = ($toSet !== null ? serialize($toSet) : null);
    }
}
?>
