<?php
/**
* @package		standard 
 * @subpackage	htmleditor
* @author		Audrey Vassal, Julien Lechevanton
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Actions pour les barres d'outils
* @package		standard 
 * @subpackage	htmleditor
 */
class ActionGroupCryptMail extends CopixActionGroup {
	/**
	* Retourne la page qui pemet de saisir le mail à encrypter
	*/
	function processGetCryptMail() {
	  	$tpl          = new CopixTpl ();
		$tplInterieur = new CopixTpl ();

        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('htmleditor.titlePage.mailcrypt'));
        $tpl->assign ('MAIN', $tplInterieur->fetch ('mailto.tpl'));
				
        return new CopixActionReturn (CopixActionReturn::DISPLAY_IN, $tpl, '|blank.tpl');        
	}
	
	/**
	* Retourne la page "autofermée" qui va rajouter le mail crypté demandé dans l'éditeur HTML
	*/
	function processdoCryptMailto () {
	  	$tpl          = new CopixTpl ();
		$tplInterieur = new CopixTpl ();

		$address = CopixRequest::get ('mailAdress');
	    $encodedAddress = '';
        for ($i=0; $i < strlen ($address); $i++){
            if (preg_match('!\w!', $address[$i])){
                $encodedAddress .= '%'.bin2hex ($address[$i]);
            }else{
                $encodedAddress .= $address[$i];
            }
        }

		$tplInterieur->assign ('mailto', $encodedAddress);
        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('htmleditor.titlePage.mailcrypt'));
        $tpl->assign ('MAIN', $tplInterieur->fetch ('cryptmailto.tpl'));
        return new CopixActionReturn (CopixActionReturn::DISPLAY_IN, $tpl, '|blank.tpl');
	}
}
?>