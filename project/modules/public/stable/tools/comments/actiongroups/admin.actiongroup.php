<?php
/**
* @package		tools 
 * @subpackage	comments
 * @author		bricef
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
* @package		tools 
 * @subpackage	comments 
 */
class ActionGroupAdmin extends CopixActionGroup {

    /**
	 * Vérifie que l'on est bien administrateur
	 */
	public function beforeAction (){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}
    
    /**
     * Fonction par défaut, affichage des commentaires 
     */
    public function processDefault() {
        // Récuperation du numéro de page sur lequel nous sommes
        $numPage = _request('numpage',1);
        $nbComments = _ioDAO ('comments')->nbComments();
        $nbItems = CopixConfig::get('comments|adminitemsperpage');
              
        $ppo = new CopixPPO ();
        $ppo->TITLE_PAGE = _i18n ('comments.admin.list');        
        $ppo->arrComments = _ioDAO ('comments')->findBy(_daoSp()
        ->orderby( array('date_comment', 'DESC'))
        ->setLimit(($numPage-1)*$nbItems, $nbItems));
                
        $ppo->pagerUrl = _url('comments|admin|', array('numpage'=>''));
        $ppo->baseUrl = CopixUrl::getRequestedBaseUrl().'index.php';
        $ppo->pageNum = $numPage;
        $ppo->nbPage = ceil($nbComments / $nbItems);
                
        return _arPPO ($ppo, 'comments.admin.tpl');        
    }
    
    /**
     * Effacer un commentaire
     */
    public function processDeleteComment() {
        CopixRequest::assert ('id');
        if (CopixRequest::getInt ('confirm') == 1) {
            _ioDAO ('comments')->deleteBy (_daoSP()->addCondition("comment_id","=",_request('id')));
            return _arRedirect (_request ('url_return', _url('comments|admin|')));    
        } else {            
            return CopixActionGroup::process ('generictools|Messages::getConfirm', 
			array ('message'=>_i18n ('comments.admin.confirmdelete'),
						'confirm'=>_url ('comments|admin|deletecomment', array ('id'=>_request('id'), 'confirm'=>1, 'url_return'=>_request ('url_return', null))),
						'cancel'=>_url ('comments|admin|')));            
        }
    }
    
    /**
     * Editer un commentaire
     */
    public function processEditComment() {
        CopixRequest::assert ('id');
        if (CopixRequest::getInt ('confirm') == 1) {
            $arrComments = _ioDAO ('comments')->findBy (_daoSP()->addCondition("comment_id","=",_request('id')));
            $objComment = $arrComments[0];
            $objComment->comment_id = _request ('id');
		    $objComment->content_comment = _request ('content');
		    $objComment->format_comment = 'TEXT';//aujourd'hui on ne supporte que le format text pour les commentaires
		    $objComment->authorlogin_comment = _request ('author');
		    $objComment->authoremail_comment = _request ('mail');
		    $objComment->authorsite_comment = _request ('site');
		    $objComment->date_comment = date ('YmdHis');
		    $objComment->noCaptcha = 1;
		    _ioDAO ('comments')->update ($objComment);
            return _arRedirect (CopixRequest::get ('url_return', _url('comments|admin|')));    
        } else {
            $ppo = new CopixPPO ();
            $ppo->TITLE_PAGE = _i18n ('comments.admin.editcomment');
            $ppo->arrComments = _ioDAO ('comments')->findBy (_daoSP()->addCondition("comment_id","=",_request('id')));
            $ppo->url_return = _request('url_return', null);
            return _arPPO ($ppo, 'editcomment.admin.tpl');
        }
    }
    
    /**
     * Lister les questions réponses utilisé pour les captcha
     */
    public function processListCaptcha() {
        $ppo = new CopixPPO ();
        if (_request('status') !== null) {
            CopixConfig::set('comments|captcha', _request('status'));
        }
        $ppo->TITLE_PAGE = _i18n ('comments.admin.captchalist');        
        $ppo->arrCaptcha = _ioDAO ('commentscaptcha')->findall();
        $ppo->boolCaptcha = CopixConfig::get('comments|captcha');
       
        return _arPPO ($ppo, 'captcha.list.tpl');
        
    }
    
    /**
     * Effacer un captcha
     * @todo Ajout d'une bvoite de dialogue confirmation phpmyadmin-like
     */
    public function processDeleteCaptcha() {
        CopixRequest::assert ('captchaid');
        if (CopixRequest::getInt ('confirm') == 1) {
            _ioDAO ('commentscaptcha')->deleteBy (_daoSP()->addCondition("captcha_id","=",_request('captchaid')));
            return _arRedirect(_url('comments|admin|listcaptcha'));
        } else {
            return CopixActionGroup::process ('generictools|Messages::getConfirm', 
			array ('message'=>_i18n ('comments.admin.captcha.confirmdelete'),
						'confirm'=>_url ('comments|admin|deletecaptcha', array ('captchaid'=>_request('captchaid'), 'confirm'=>1)),
						'cancel'=>_url ('comments|admin|listcaptcha')));
        }
    }
    
    /**
     * Editer un captcha
     */
    public function processEditCaptcha() {
        CopixRequest::assert ('captchaid');
        if (CopixRequest::getInt ('confirm') == 1) {
            $arrCaptcha = _ioDAO ('commentscaptcha')->findBy (_daoSP()->addCondition("captcha_id","=",_request('captchaid')));
            $objCaptcha = $arrCaptcha[0];
            $objCaptcha->captcha_question = _request('captcha_question');
            $objCaptcha->captcha_answer = _request('captcha_answer');
            _ioDAO ('commentscaptcha')->update ($objCaptcha);
            return _arRedirect(_url('comments|admin|listcaptcha'));
        } else {
            $ppo->TITLE_PAGE = _i18n ('comments.admin.captchalist');        
            $ppo->arrCaptcha = _ioDAO ('commentscaptcha')->findall();
            $ppo->editedCaptcha = _request('captchaid');
            return _arPPO ($ppo, 'captcha.list.tpl');
        }
        
    }
    
    /**
     * Ajouter un captcha
     */
    public function processAddCaptcha() {       
        $objCaptcha = _record('commentscaptcha');
        $objCaptcha->captcha_question = _request('captcha_question');
        $objCaptcha->captcha_answer = _request('captcha_answer');
        _ioDAO('commentscaptcha')->insert($objCaptcha);
        return _arRedirect(_url('comments|admin|listcaptcha'));
    }
    
    /**
     * Verouille les commentaires sur un élément
     */
    public function processLock() {
        // On teste si on peut rediriger le lien
        CopixRequest::assert ('url_return');
        // On teste si on a un id
        if (_request('id') !== null) {
            $lock_status = CopixRequest::getInt ('lock_status');
            if ($lock_status === 0) {
                if (_ioDAO('commentslocked')->countBy(_daoSp ()->addCondition('locked_page_comment', '=', _request('id'))) !=0 ) {
                    _ioDAO('commentslocked')->deleteBy(_daoSp ()->addCondition('locked_page_comment', '=', _request('id')));
                }
            
            } else {
                $objLocked = _record('commentslocked');
                $objLocked->locked_page_comment = _request('id');
                     
                _ioDAO ('commentslocked')->insert($objLocked);
            }
        }
        return _arRedirect(_request('url_return'));
    }
}
?>