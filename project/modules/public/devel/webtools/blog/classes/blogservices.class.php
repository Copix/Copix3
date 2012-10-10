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
class BlogServices {
	public function getPanel(){
		$panel = new stdClass();
		$panel->calendar = $this->getCalendar();
		$panel->tags =$this->getTags();
		$panel->headings = $this->getHeadings();
		return $panel;
	}

	public function getCalendar(){
		$y = _request("year",date('Y'));
		$m = _request("month",date('m'));
		$d = _request("day",date('d'));
		$date = $y.$m.$d;
		if(CopixCache::exists($date,"blog_calendar")){
			$calendar = CopixCache::read($date,"blog_calendar");
		}
		else{
			$calendar = _ioClass('blog|calendar')->getMonthView($m,$y);
			CopixCache::write($date,$calendar,"blog_calendar");
		}
		return $calendar;
	}

	public function getTags(){
		$tickets = _ioDao("blog_ticket")->findAll();
		$tags = array();
		$checked=array();
		$rankmax=0;
		foreach($tickets as $ticket){
			$_tags=preg_split("/,|;/",$ticket->tags_blog);
			foreach ($_tags as $tag){
				if(!in_array($tag,$checked)){
					$tags[$tag] = new stdClass();
					$tags[$tag]->tagname=$tag;
					$tags[$tag]->rank=1;
					$checked[]=$tag;
				}
				else{
					$tags[$tag]->rank++ ;
				}
				if($tags[$tag]->rank>$rankmax){
					$rankmax=$tags[$tag]->rank;
				}
			}
		}

		$fontmax = CopixConfig::get('blog|maxfontsize');
		$fontmin = CopixConfig::get('blog|minfontsize');
		$fontmax = $fontmax-$fontmin; //we will add fontmin size after to not have 0

		$copy=array();
		foreach($tags as $key=>$tag){
			$percent = $tag->rank * 100 / $rankmax;
			$tag->size =(int)round(($percent * $fontmax / 100)+$fontmin);
			$copy[$key] = $tag;
		}

		return $copy;
	}

	public function getHeadings(){
		return  _ioDao('blog_heading')->findBy(_daoSp()->orderBy('heading_blog'));
	}
	
	public function cutDates($tickets){
		foreach($tickets as $ticket){
			$ticket->year = preg_replace('/^(\d\d\d\d)(\d\d)(\d\d).*/','\\1',$ticket->date_blog);
			$ticket->month = preg_replace('/^(\d\d\d\d)(\d\d)(\d\d).*/','\\2',$ticket->date_blog);
			$ticket->day = preg_replace('/^(\d\d\d\d)(\d\d)(\d\d).*/','\\3',$ticket->date_blog);

			$ticket->human_date = CopixDateTime::yyyymmddhhiissToText($ticket->date_blog);
		}
	}
	
	
	public function processTickets($tickets){
		foreach ($tickets as $ticket){
			$ticket->content_blog = _ioClass("wikirender|wiki")->render($ticket->content_blog);
		}
	}
}
?>