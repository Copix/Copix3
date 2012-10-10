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

	public function beforeAction ($pAction){
		_ioClass('blog|blogauth')->assertWrite ();
		CopixHtmlHeader::addCSSLink (_resource ('wiki|styles/wiki.css'));
		CopixHtmlHeader::addCSSLink (_resource ('|styles/blog.css'));
	}

	public function afterAction ($action,$return){
		if ($return->code == CopixActionReturn::PPO) {
			$tpl = new CopixTpl();
			$tpl->assign('ppo',$return->data);

			$ppo = new CopixPPO ();
			$ppo->TITLE_PAGE= $return->data->TITLE_PAGE;
			//$ppo->TITLE_BAR= $this->TITLE_BAR;
			$ppo->main_content = $tpl->fetch($return->more);
			$ppo->panel = _ioClass('blog|blogservices')->getPanel();
			return _arPPO($ppo, 'blog.tpl');
		}
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
		
		//$ticket->date_blog = date("YmdHis");
		$ticket->tags_blog = _request("tags_blog");
		$ticket->typesource_blog = _request("typesource_blog","wiki");
		$method="insert";
        //remove tags and tickets caches
        CopixCache::clear('tags','blogtags');
        CopixCache::clear(_request('id'),'blogtickets');
		if(_request('id') || _request('blog_ticket_id')){
			$ticket = _ioDao('blog_ticket')->get( _request('id'));
			$ticket->heading_blog = _request("heading_blog");
                	$ticket->title_blog = _request('title_blog');
        	        $ticket->content_blog = _request('content_blog');
	                $ticket->author_blog = $user = CopixAuth::getCurrentUser()->getLogin();
			$ticket->tags_blog = _request("tags_blog");
			$ticket->typesource_blog = _request("typesource_blog","wiki");
			
			
			$ticket->id_blog = _request('id');
			$method='update';
		}else{
			$ticket->date_blog = date("YmdHis");
		}
		_ioDao("blog_ticket")->$method($ticket);


		$html = $ticket->content_blog;
		if($ticket->typesource_blog =="wiki")
			$html = _ioClass('wikirender|wiki')->render($ticket->content_blog);

		$textonly = _ioClass('blog|blogservices')->html_substr($html, 252, '...');
		/*$textonly = trim(strip_tags($html));
		$textonly = substr($textonly,0,252).'...';*/
		_log($textonly,"debug");
		
		
		if($method!="update"){
			_notify ('Content', array (
				'id'=>$ticket->heading_blog."/".$ticket->title_blog,
				'kind'=>'blog',
				'keywords'=>$ticket->tags_blog,
				'title'=>$ticket->title_blog,
				'summary'=>$textonly,
				'content'=>$html,
				'author'=>$ticket->author_blog,
				'url'=>_url ('showticket', array ('title'=>$ticket->title_blog,
							'heading'=>$ticket->heading_blog,
							'day'=>date('d'),
							'month'=>date('m'),
							'year'=>date('Y')
							)
						)
				)
			);
		}

		return _arRedirect(_url("blog||"));
	}


    /**
    * A simple rediretion to go to parameters module with "blog" as choice
    */
    public function processGotoParameters(){
        return _arRedirect(_url('admin|parameters|', array('choiceModule'=>'blog')));
    }
	
}
