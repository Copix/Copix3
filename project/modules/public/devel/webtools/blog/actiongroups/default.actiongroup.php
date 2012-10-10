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
 * 
 */
class ActionGroupDefault extends CopixActionGroup{

	public $canwrite = false;
	public $TITLE_PAGE="";
	public $TITLE_BAR="";

	public function beforeAction (){
		CopixHtmlHeader::addCSSLink (_resource ('styles/wiki.css.php'));
		CopixHtmlHeader::addCSSLink (_resource ('styles/blog.css.php'));
		$this->canwrite = _ioClass('blog|blogauth')->canWrite();
	}

	public function afterAction ($action,$return){
		if ($return->code == CopixActionReturn::PPO) {
			$tpl = new CopixTpl();
			$tpl->assign('ppo',$return->data);

			$ppo = new CopixPPO ();
			$ppo->TITLE_PAGE= $this->TITLE_PAGE;
			//$ppo->TITLE_BAR= $this->TITLE_BAR;
			$ppo->main_content = $tpl->fetch($return->more);
			$ppo->panel = _ioClass('blog|blogservices')->getPanel();
			return _arPPO($ppo, 'blog.tpl');
		}

	}

	public function processDefault (){
		$ppo = new CopixPPO();
		$page = _request('page',1)-1;
		$day = _request('day',date('d'));
		$month = _request('month',date('m'));
		$year = _request('year',date('Y'));
		$tag = _request('tag');
		$heading = _request('heading',false);

		$ppo->tag = $tag;
		$ppo->heading = $heading;

		$this->TITLE_BAR = "blog";
		$this->TITLE_PAGE = "blog";


		$date="";
		if((int)$month<10){
			$month="0".(int)$month;
		}
		if((int)$day<10){
			$day="0".(int)$day;
		}
		 
		if(_request('year')){
			$date .= $year;
			$ppo->year = $year;
		}
		if(_request('month')){
			$date .= $month;
			$ppo->month = $month;
		}
		if(_request('day')){
			$date .= $day;
			$ppo->day = $day;
		}

		//date works only if given in url
		$daoSp = _daoSp()->addCondition('date_blog','like',$date.'%');

		if($heading){
			$daoSp->addCondition('heading_blog','=',$heading);
		}

		if($tag){
			$daoSp->addCondition('tags_blog','like',"%$tag%");
		}

		$startAt = abs(Copixconfig::get('blog|perpage')*$page);
		$offset= Copixconfig::get('blog|perpage');
		$ppo->tickets = _ioDao("blog_ticket")->findBy($daoSp
		->setLimit($startAt,$offset)
		->orderBy(array('date_blog','DESC'))
		);
		_ioClass('blog|blogservices')->cutDates($ppo->tickets);
		$ppo->canwrite = $this->canwrite;

		//seek next page
		$startAt = abs(Copixconfig::get('blog|perpage')*($page+1));
		$next = _ioDao("blog_ticket")->findBy($daoSp
		->setLimit($startAt,$offset)
		->orderBy(array('date_blog','DESC'))
		);

		//seek prev page
		$prev=array();
		if($page>0){
			$startAt = abs(Copixconfig::get('blog|perpage')*($page-1));
			$prev = _ioDao("blog_ticket")->findBy($daoSp
			->setLimit($startAt,$offset)
			->orderBy(array('date_blog','DESC'))
			);

		}
		$ppo->havenext = (count($next)) ? true : false;
		$ppo->haveprev = (count($prev)) ? true : false;

		//page numbers
		$count = _ioDao("blog_ticket")->countBy($daoSp);
		$nbpage = round(($count/$offset)+0.4); //arrondi supérieur

		$ppo->pagenum = $page+1;
		$ppo->count = $nbpage;

		_ioClass('blog|blogservices')->processTickets($ppo->tickets);
			
		return _arPPO($ppo,"index.tpl");
	}

	public function processShowTicket (){
		CopixRequest::assert('year','month','day','title');
		$date = _request('year')._request('month')._request('day');
		$title = _request('title');
		$ppo = new CopixPPO();
		$ppo->ticket = _ioDao("blog_ticket")->findBy(_daoSp()
		->addCondition('date_blog','like',$date."%")
		->addcondition('title_blog','=',$title)
		->setCount (1)
		);
		$ppo->ticket=$ppo->ticket[0];
		$this->TITLE_BAR = $ppo->ticket->title_blog;
		$this->TITLE_PAGE = $this->TITLE_BAR;
		$ppo->canwrite = $this->canwrite;
		$ppo->url=_url ('showticket', array ('title'=>$ppo->ticket->title_blog,
							'heading'=>$ppo->ticket->heading_blog,
							'day'=>date('d'),
							'month'=>date('m'),
							'year'=>date('Y')
							)
						);
						
		_ioClass('blog|blogservices')->processTickets(array($ppo->ticket));
		if(_request('notify')==1){
			$textonly = $ppo->ticket->content_blog;
			$textonly = trim(strip_tags($textonly));
			$textonly = substr($textonly,0,252).'...';
			_notify ('Visited', array (
				'id'=>$ticket->heading_blog."/".$ppo->ticket->title_blog,
				'kind'=>'blog',
				'keywords'=>$ppo->ticket->tags_blog,
				'title'=>$ppo->ticket->title_blog,
				'summary'=>$textonly,
				'content'=>$ppo->ticket->content_blog,
				'url'=>$ppo->url
				)
			);
			return _arNone();
		}
		return _arPPO($ppo,"showticket.tpl");
	}
}

?>