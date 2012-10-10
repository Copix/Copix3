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

	public function beforeAction ($pAction){
		$this->canwrite = _ioClass('blog|blogauth')->canWrite();
		CopixHtmlHeader::addCSSLink (_resource ('wiki|styles/wiki.css'));
		CopixHtmlHeader::addCSSLink (_resource ('|styles/blog.css'));
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
		$day =  sprintf("%02d",_request('day',date('d')));
		$month = sprintf("%02d",_request('month',date('m')));
		$year = _request('year',date('Y'));
		$tag = _request('tag');
		$heading = _request('heading',false);

		$ppo->tag = $tag;
		$ppo->heading = $heading;

		$this->TITLE_BAR = CopixConfig::get('blog|titlebar');
		$this->TITLE_PAGE = CopixConfig::get('blog|titlebar');
		if($heading){
			$this->TITLE_BAR .=" :: ".$heading;
			$this->TITLE_PAGE .=" :: ".$heading;
		}

		if($tag){
			$this->TITLE_BAR .=" :: tag :: ".$tag;
			$this->TITLE_PAGE .=" :: tag :: ".$tag;
		}

		$date="";
		
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

		if(_request('debug')){
			echo $date;
		}

		//date works only if given in url
		if($date){
			$daoSp = _daoSp()->addCondition('date_blog','like',$date.'%');
		}else{
			$daoSp = _daoSp()->addCondition('date_blog','<=',date('YmdHis'));
		}
		
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
		$keywords = "";
		foreach ($ppo->tickets as $tick) {
                     $keywords .= $tick->tags_blog.',';
                     $tick->_arTags = array();
                     $tags = preg_split('/,|;/',$tick->tags_blog);
                     foreach($tags as $tag){
                     	$tick->_arTags[] = trim(strtolower($tag));
                     }
		}
		$kw = explode (',',$keywords);
		$keywords = array();
		foreach($kw as $key){
			if (strlen(trim($key))>0) $keywords[] = $key;
		}
		$keywords = implode (',',array_unique($keywords));
                CopixHTMLHeader::addOthers('<meta name="keywords" content="'.$keywords.'" />');
		return _arPPO($ppo,"index.tpl");
	}

	public function processShowTicket (){
		CopixRequest::assert('year','month','day','title');
		$date = _request('year')._request('month')._request('day');
		$title = _request('title');
		$ppo = new CopixPPO();
		
		$day = sprintf("%02d",_request('day',date('d')));
		$month = sprintf("%02d",_request('month',date('m')));
		$year = sprintf("%02d",_request('year',date('Y')));
		$date="";
		 
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
		
		$ppo->ticket = _ioDao("blog_ticket")->findBy(_daoSp()
			->addCondition('date_blog','like',$date."%")
			->addcondition('title_blog','=',$title)
			->setCount (1)
		);
		
		$ppo->ticket=$ppo->ticket[0];

		//get tags
		$tags = preg_split('/,|;/',$ppo->ticket->tags_blog);
      foreach($tags as $tag){
      	$ppo->ticket->_arTags[] = trim(strtolower($tag));
      }
		
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
		
		CopixHTMLHeader::addOthers("<meta name=\"keywords\" content=\"".$ppo->ticket->tags_blog."\" />");
		_ioClass('blog|blogservices')->processTickets(array($ppo->ticket));
		if(_request('notify')==1){
			$html = $ticket->content_blog;
			if($ticket->typesource_blog =="wiki")
				$html = _ioClass('wikirender|wiki')->render($ticket->content_blog);					
			$textonly = _ioClass('blog|blogservices')->html_substr($html, 252, '...');
			_notify ('Visited', array (
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
			return _arNone();
		}
		return _arPPO($ppo,"showticket.tpl");
	}
}
