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
		$panel = _ppo(array(
			'tags' => $this->getTags(),
			'headings' => $this->getHeadings(),
			'calendar' => $this->getCalendar(_request('year',date('Y')),_request('month',date('m')),_request('day',date('d')))
		));
		return $panel;
	}

	public function getCalendar($y,$m,$d){
		/* Testing Shared Object instead of cache, it's a try */
		//this method return really quickly the calendar if it's been created
		//less than one hour ago	
		$p = CopixSharedObject::instance('blog.calendar');
		if(isset($p->calendar[$y.$m]) && $p->calendar[$y.$m]->generated < date('YmdH')){
			return $p->calendar[$y.$m]->calendar;
		}else{
			if(isset($p->calendar[$y.$m])){
				unset ($p->calendar[$y.$m]);
			}
			$p->calendar[$y.$m] = _ppo(array(
				'generated' => date('YmdH'),
				'calendar' => _ioClass('blog|calendar')->getMonthView($m,$y)
			));
		}
		return $p->calendar[$y.$m]->calendar;		
	}

	public function getTags(){
		if(!CopixCache::exists('tags','blogtags')){
			$tickets = _ioDao("blog_ticket")->findAll();
			$tags = array();
			$checked=array();
			$rankmax=0;
			foreach($tickets as $ticket){
				$_tags=preg_split("/,|;/",$ticket->tags_blog);
				foreach ($_tags as $tag){
					$tag = trim($tag);
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
			$tpl = new CopixTpl();
			$ppo = _ppo(array('tags'=>$copy));
			$tpl->assign('ppo',$ppo);
			CopixCache::write('tags', $tpl->fetch('blog|tags.tpl'),'blogtags');
		}
		return CopixCache::read('tags','blogtags');
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


	/**
	 * Generate tickets content and caches
	 *
	 * @param unknown_type $tickets
	 */
	public function processTickets($tickets){
		foreach ($tickets as $ticket){
			if(!CopixCache::exists($ticket->id_blog,"blogtickets")){
				if($ticket->typesource_blog=="wiki"){
					CopixCache::write($ticket->id_blog,_ioClass("wikirender|wiki")->render($ticket->content_blog),'blogtickets');
				}else{
					CopixCache::write($ticket->id_blog,$ticket->content_blog,'blogtickets');
				}
			}
			$ticket->content_blog = CopixCache::read($ticket->id_blog,'blogtickets');
		}
	}


	public function html_substr($string, $length, $addstring=""){
		$addstring = " " . $addstring;

		if (strlen($string) > $length) {
			if( !empty( $string ) && $length>0 ) {
				$isText = true;
				$ret = "";
				$i = 0;

				$currentChar = "";
				$lastSpacePosition = -1;
				$lastChar = "";

				$tagsArray = array();
				$currentTag = "";
				$tagLevel = 0;

				$noTagLength = strlen( strip_tags( $string ) );

				// Parser loop
				for( $j=0; $j<strlen( $string ); $j++ ) {

					$currentChar = substr( $string, $j, 1 );
					$ret .= $currentChar;

					// Lesser than event
					if( $currentChar == "<") $isText = false;

					// Character handler
					if( $isText ) {

						// Memorize last space position
						if( $currentChar == " " ) { $lastSpacePosition = $j; }
						else { $lastChar = $currentChar; }

						$i++;
					} else {
						$currentTag .= $currentChar;
					}

					// Greater than event
					if( $currentChar == ">" ) {
						$isText = true;

						// Opening tag handler
						if( ( strpos( $currentTag, "<" ) !== FALSE ) &&
						( strpos( $currentTag, "/>" ) === FALSE ) &&
						( strpos( $currentTag, "</") === FALSE ) ) {

							// Tag has attribute(s)
							if( strpos( $currentTag, " " ) !== FALSE ) {
								$currentTag = substr( $currentTag, 1, strpos( $currentTag, " " ) - 1 );
							} else {
								// Tag doesn't have attribute(s)
								$currentTag = substr( $currentTag, 1, -1 );
							}

							array_push( $tagsArray, $currentTag );

						} else if( strpos( $currentTag, "</" ) !== FALSE ) {
							array_pop( $tagsArray );
						}

						$currentTag = "";
					}

					if( $i >= $length) {
						break;
					}
				}

				// Cut HTML string at last space position
				if( $length < $noTagLength ) {
					if( $lastSpacePosition != -1 ) {
						$ret = substr( $string, 0, $lastSpacePosition );
					} else {
						$ret = substr( $string, $j );
					}
				}

				// Close broken XHTML elements
				while( sizeof( $tagsArray ) != 0 ) {
					$aTag = array_pop( $tagsArray );
					// only add string if text was cut
					if ( strlen($string) > $length ) {
						$ret.=$addstring;
					}
					$ret .= "</" . $aTag . ">\n";
				}

			} else {
				$ret = "";
			}
			return ( $ret );
		}
		else {
			return ( $string );
		}
	}


}
?>