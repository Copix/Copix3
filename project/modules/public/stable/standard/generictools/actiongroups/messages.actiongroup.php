<?php
/**
* @package		standard 
 * @subpackage	generictools
* @author	Croes Gérald
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Prise en charges d'écrans de dialogues communs
* @package		standard 
 * @subpackage	generictools
 */
class ActionGroupMessages extends CopixActionGroup {
	/**
    * Affichage d'un message d'erreur
    */
	public function processGetError () {
		$tpl = new CopixTpl ();
		$back = _request ('back', 'javascript: history.back ();');
		$tpl->assign ('TITLE_PAGE', _request ('TITLE_PAGE', _i18n ('messages.titlePage.error')));
		$tpl->assignZone ('MAIN', 'PassThrough', array    ('message'=>_request ('message'),
		'back'=>$back,
		'template'=>'messages.error.tpl')
		);

		return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
	}
	
	/**
    * Affichage d'un message de confirmation
    */
	public function processGetConfirm () {
		$tpl = new CopixTpl ();

		$tpl->assign ('TITLE_PAGE', _request ('TITLE_PAGE', _i18n ('messages.titlePage.confirm')));
		$tpl->assignZone ('MAIN', 'PassThrough', array    ('title'=>_request ('title'),
		'message'=>_request ('message'),
		'confirm'=>_request ('confirm'),
		'cancel'=>_request ('cancel'),
		'template'=>'messages.confirm.tpl')
		);
		return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
	}
	
	/**
	 * Affichage d'un message d'information
	 */
	public function processGetInformation (){
		$tpl = new CopixTpl ();

		$tpl->assign ('TITLE_PAGE', _request ('TITLE_PAGE', _i18n ('messages.titlePage.information')));
		$tpl->assignZone ('MAIN', 'PassThrough', array    ('message'=>_request ('message'),
		'back'=>_request ('continue'),
		'template'=>'messages.information.tpl')
		);

		return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
	}
	
	/**
	 * Fonction permettant d'afficher une exception à l'écran.
	 * Principalement destinée à l'utilisation interne, pas nécessairement à l'utilisation depuis l'url
	 *
	 */
	public function processException (){
		CopixRequest::assert ('exception');
		$e = _request ('exception');
		
		//Absolument impossible dans le contexte normal d'utilisation, $e est nécessairement une exception 
		//catchée par le processus.
		if (! ($e instanceof Exception)){
			return _arRedirect ();
		}
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = get_class ($e);
		switch (get_class ($e)) {
		    case 'CopixDAOCheckException':
        		$ppo->message = sprintf ('Une erreur de validation est survenue avec le message [%s]', implode (', ', $e->getErrors ()));
        		break;
		    default:
        		$ppo->message = $e->getMessage ();
		}
		$ppo->type = get_class ($e);
		$ppo->file = $e->getFile ();
		$ppo->line = $e->getLine ();
		$ppo->trace = $e->getTrace ();
		$ppo->id = uniqid();
		switch (CopixConfig::instance ()->getMode ()) {
			case CopixConfig::DEVEL : $ppo->mode = 'DEVEL'; break;
			case CopixConfig::PRODUCTION : $ppo->mode = 'FORCE_INITIALISATION'; break;
			case CopixConfig::FORCE_INITIALISATION : $ppo->mode = 'FORCE_INITIALISATION'; break;
			default : $ppo->mode = 'UNKNOW'; break;
		}

		return _arPpo ($ppo, 'default|exception.tpl');
	}
}
?>