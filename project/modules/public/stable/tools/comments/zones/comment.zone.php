<?php
/**
* @package		tools 
 * @subpackage	comments
 * @author	 	Fersing Estelle
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Zone qui affiche un commentaire
* @package		tools 
 * @subpackage	comments
 */
class ZoneComment extends CopixZone {
    function _createContent (&$toReturn){
    	CopixHtmlHeader::addCSSLink (_resource ('styles/comments.css'));

		//Si pas d'éléments d'identifiant donné, alors on utilise l'ensemble des paramètres de la requête
    	if (($id = $this->getParam ('id')) == "") {
			$id = array_keys (CopixRequest::asArray ());
		}

		if(($mode = $this->getParam ('mode')) == "" || $mode == "request") {
			if (($mode = _request ('comments')) == "") {
				$mode = "summary";
			}
		}
		
    	if (($newUrl = $this->getParam ('moreUrl')) == "") {
			$newUrl = _url ('#', array ('comments'=>'list'));
    	}
    	
		$tpl = new CopixTpl (); 
		$tpl->assign ('mode', $mode);
		$tpl->assign ('newUrl', $newUrl);

		// On teste si nous sommes dans l'actions de prévisualisation
        if (_request('preview') !== null) {
            $tpl->assign ('preview', 1);
            $tpl->assign ('previewDate', date('YmdHis'));
    	}   
    	
   	    if (CopixAuth::getCurrentUser ()->testCredential ('basic:admin')){
    	    $tpl->assign ('isAdmin', 1);
   	    }else{
    	    $tpl->assign ('isAdmin', 0);
   	    }
    	
		$idComment = _ioClass ('commentsservices')->getId ($id);
		
    	// On vérifie si les commentaires sont ouvert
		$tpl->assign('locked', _dao('commentslocked')->countBy(_daoSp ()->addCondition('locked_page_comment', '=', $idComment)));
		
		if ($informations = _ioClass ('commentsservices')->getEnabled ($idComment)){
			$tpl->assign ('newComment', $informations['object']);
			if (_request ('errors') !== null){
				$tpl->assign ('errors', _ioDAO ('comments')->check ($informations['object']));
			}
		}
		_ioClass ('commentsservices')->addEnabled (array ('fromPage'=>_url('#'), 
														'writeCredential'=>$this->getParam ('credentialWrite'), 
														'id'=>$idComment));
		$tpl->assign ('idComment', $idComment);

		switch ($mode) {
			case "list":
				if($this->getParam ('credentialRead') != "") {
					CopixAuth::getCurrentUser ()->assertCredential ($this->getParam ('credentialRead'));
				}
				$tpl->assign('arrComments', _dao ('comments')->findBy ( _daoSp ()->addCondition('page_comment', '=', $idComment)));
				break;
			case "summary":
				$tpl->assign('nbComments', _dao ('comments')->countBy ( _daoSp ()->addCondition('page_comment', '=', $idComment)));
				break;
		}
		
		// Mise en place du captcha si besoin : 
		if (CopixConfig::get('comments|captcha') != 0) {
		    $arrCaptchaMax = _ioDao('commentscaptcha')->findBy(_daoSp()->orderBy (array('captcha_id','DESC'))->setLimit(0,1));
			$arrCaptchaMin = _ioDao('commentscaptcha')->findBy(_daoSp()->orderBy ('captcha_id')->setLimit(0,1));
			$captcha= false;
			while(!$captcha || is_null($captcha)){
				srand();
				$rand = rand($arrCaptchaMin[0]->captcha_id,$arrCaptchaMax[0]->captcha_id);
				$captcha = _ioDao('commentscaptcha')->get($rand);
			}
			$tpl->assign('captcha',$captcha);
		}
    	$toReturn = $tpl->fetch ('zone.comment.tpl');
    	//_log ('URL: '._url('#'));
    	//_log ('ID: ' . $idComment);
        return true;
    }
}
?>