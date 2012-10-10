<?php
/**
* @package	webtools
* @subpackage	blog
* @author	Patrice Ferlet
* @copyright CopixTeam
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	webtools
* @subpackage	blog 
 */
class ActionGroupAdmin extends CopixActionGroup{

	public function beforeAction (){
		_ioClass('blog|blogauth')->assertWrite ();
		CopixHtmlHeader::addCSSLink (_resource ('styles/wiki.css.php'));
		CopixHtmlHeader::addCSSLink (_resource ('styles/blog.css.php'));
	}

	public function processDefault (){
		$ppo = new CopixPPO();
		$ppo->TITLE_BAR = "blog|";
		$ppo->TITLE_PAGE = $ppo->TITLE_BAR;
		$ppo->headings = _ioDao('blog_heading')->findAll();
		return _arPPO ($ppo,'admin.tpl');
	}

	public function processAddHeading (){
		CopixRequest::assert('heading_blog','description_blog');

		$head = _record('blog_heading');
		$head->heading_blog = _request('heading_blog');
		$head->description_blog = _request('description_blog');
		_ioDao('blog_heading')->insert($head);
		return _arRedirect(_url("blog|admin|"));
	}

	public function processEditTicket(){
		return self::processNewTicket();
	}

	public function processNewTicket (){
		$ppo = new CopixPPO();
		if(_request('id')){
			$ppo->ticket = _ioDao('blog_ticket')->get(_request('id'));
		}
		$ppo->TITLE_BAR = "blog|";
		$ppo->TITLE_PAGE = $ppo->TITLE_BAR;
		$ppo->headings = _ioDao('blog_heading')->findAll();
		$user = CopixAuth::getCurrentUser()->getLogin();
		if(!is_null ($user) && strlen ($user)>0){
			$ppo->author= $user;
		}else{
			$ppo->author = false;
		}
		return _arPPO($ppo,"edit.ticket.tpl");
	}

	public function processSaveTicket (){
		$ticket = _record("blog_ticket");
		$ticket->heading_blog = _request("heading_blog");
		$ticket->title_blog = _request('title_blog');
		$ticket->content_blog = _request('content_blog');
		$ticket->author_blog = $user = CopixAuth::getCurrentUser()->getLogin();
		$ticket->date_blog = date("YmdHis");
		$ticket->tags_blog = _request("tags_blog");
		$method="insert";
		if(_request('id')){
			$ticket->id_blog = _request('id');
			$method='update';
		}else{
			$ticket->date_blog = date("YmdHis");
		}
		_ioDao("blog_ticket")->$method($ticket);


		$textonly = _ioClass('wikirender|wiki')->render($ticket->content_blog);
		$textonly = trim(strip_tags($textonly));
		$textonly = substr($textonly,0,252).'...';
		_log($textonly,"debug");
		_notify ('Content', array (
			'id'=>$ticket->heading_blog."/".$ticket->title_blog,
			'kind'=>'blog',
			'keywords'=>$ticket->tags_blog,
			'title'=>$ticket->title_blog,
			'summary'=>$textonly,
			'content'=>$ticket->content_blog,
			'url'=>_url ('showticket', array ('title'=>$ticket->title_blog,
						'heading'=>$ticket->heading_blog,
						'day'=>date('d'),
						'month'=>date('m'),
						'year'=>date('Y')
						)
					)
			)
		);

		return _arRedirect(_url("blog||"));
	}
}
?>