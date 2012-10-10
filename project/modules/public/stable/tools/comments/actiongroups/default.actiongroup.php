<?php
/**
* @package		tools 
 * @subpackage	comments
 * @author		Estelle Fersing
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Actions d'ajout de commentaires
* @package		tools 
 * @subpackage	comments
 */
class ActionGroupDefault extends CopixActionGroup {
   /**
   * Ajout d'un commentaire dans la base après vérification des droits d'écriture
   * et que l'on a bien la bonne session
   */
	public function processAddComment (){
		//Vérifie que l'on a bien un paramètre id
		CopixRequest::assert ('id');
		if (($informations = _ioClass ('commentsservices')->getEnabled (_request ('id'))) === false){
			throw new Exception (_i18n('comments.error.badidsession'));
		}
		
		//_log ('FROMPAGE: '.$informations['fromPage']);
		//_log ('POUR ID: '._request ('id'));

		//vérifie les droits d'écriture
		if ($informations['writeCredential'] != "") {
			CopixAuth::getCurrentUser ()->assertCredential ($informations['writeCredential']);
		}
				
		//Ajout du commentaire
		$objComment = _record ('comments');
		$objComment->content_comment = _request ('content');
		$objComment->format_comment = 'TEXT';//aujourd'hui on ne supporte que le format text pour les commentaires
		$objComment->authorlogin_comment = _request ('author');
		$objComment->authoremail_comment = _request ('mail');
		$objComment->authorsite_comment = _request ('site');
		$objComment->page_comment = $informations['id'];
		$objComment->date_comment = date ('YmdHis');
		if (CopixConfig::get('comments|captcha') != 0) {
		    $objComment->captcha_id = _request('captcha_id');
		    $objComment->captcha_answer = _request('captcha_answer');
		}

		try {
		    if (_request('preview') === null) {
			    _ioDAO ('comments')->insert ($objComment);
			    _ioClass ('commentsservices')->removeEnabled ($informations['id']);
				_notify ('Content', array (
						'id'=>$informations['id'],
						'kind'=>'comment',
						'keywords'=>null,
						'title'=>$informations['id'],
						'summary'=>null,
						'content'=>$objComment->content_comment,
						'url'=>$informations['fromPage']
						)
				);				
			    return _arRedirect ($informations['fromPage']);
		    } else {
		        _ioClass ('commentsservices')->updateEnabled ($objComment);
		        return _arRedirect (_url ($informations['fromPage'], array ('preview'=>1, 'comments'=>'list')));    
		    }
		}catch (CopixDAOCheckException $e){
			_ioClass ('commentsservices')->updateEnabled ($objComment);
			return _arRedirect (_url ($informations['fromPage'], array ('errors'=>1, 'comments'=>'list')));    
		}
	}
}
?>