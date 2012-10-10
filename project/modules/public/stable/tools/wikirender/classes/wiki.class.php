<?php
/**
* @package		tools 
 * @subpackage	wikirender
 * @author	Patrice Ferlet
 * @copyright 2001-2006 CopixTeam
 * @link      http://copix.org
 * @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		tools 
 * @subpackage	wikirender
 */
class Wiki{
	private $toc = "";
	
	private $footnotes = array();
	
	private $anchor_separator = "-";

	public function render($content,$canwritespecialtags=true,$tags=array()){
		$content = preg_replace('/<del>(.*?)<\/del>/', "_strike_\\1_end_strike_", $content);
		$content = preg_replace('/<code(.*?)>/', "\n".'[code\\1]', $content);
		$content = preg_replace('/<\/code>/', '[/code]'."\n\n", $content);
		$content = preg_replace('/<math(.*?)>/', "\n".'[math\\1]', $content);
		$content = preg_replace('/<\/math>/', '[/math]'."\n\n", $content);
		$content = preg_replace('/<graphviz(.*?)>/', "\n".'[graphviz\\1]', $content);
		$content = preg_replace('/<\/graphviz>/', '[/graphviz]'."\n\n", $content);
		$content = preg_replace('/<html(.*?)>/', "\n".'[html\\1]', $content);
		$content = preg_replace('/<\/html>/', '[/html]'."\n\n", $content);
		$elements = $this->_prepareElements ($content);

		$content = "";
		foreach ($elements as $elem) {
			if (in_array ($elem->type,$tags) && !$canwritespecialtags){
				$line = htmlentities ($elem->data);
			}else{
				$line = $elem->data;
				if ($elem->type == "paragraph" && strlen (trim($elem->data)) > 0) {
					//specials
					$line = preg_replace ('/&/', '&amp;', $line);
					$this->cleanCode ($line, (isset ($page) && isset ($page->title_wiki)) ? $page->title_wiki : "Image");
					$line = stripslashes ($line);
					$line = "\n<p>" . str_replace ('\\'."\n","<br />",$line) . "\n</p>\n";
				}elseif ($elem->type == "table"){
					$this->_transformTable ($line);
				}elseif ($elem->type == "lists"){
					$line = $this->_treatLists ($elem);
				}elseif ($elem->type == "code"){
					$line = $this->_treatCode ($elem);
				}elseif ($elem->type == "math"){
					$line = $this->_treatMath ($elem);
				}elseif ($elem->type == "graphviz"){
					$line = $this->_treatGraphviz ($elem);
				}elseif ($elem->type == "html"){
					$line = $this->_treatHtml ($elem);
				}elseif ($elem->type == "language"){
					$line="";
				}elseif ($elem->type == "frompage"){
					$line="";
				}elseif ($elem->type == "cols"){
					$line = "";
					$js  = '<script type="text/javascript" />';
					$js .= "\n".$this->_addCols($elem);
					$js .= "\n".'</script>';
				}
			}
			$content .= $line;
		}

		//for code:
		$content = str_replace ("_ocrochet_", "[", $content);
		$content = str_replace ("_ccrochet_", "]", $content);

		//create the Table of contents

		$toc = "<div id=\"wiki_toc\"><strong>Index</strong><br />".$this->toc."</div>";
		$content = str_replace ("[toc]",$toc,$content);
		
		//footer
		$footer = "";
		if(count($this->footnotes)){
			$footer="<div id=\"wiki_footnotes\"><a name=\"wiki_footnotes\"></a><h2>Footnotes</h2>\n<ul>";
			$i=1;
			foreach($this->footnotes as $footnote){
				$footer.="<li>$i - ".$footnote."</li>";
				$i++;
			}
			$footer.="</ul>\n</div>";
		}


		if (!isset ($js)){
			$js="";
		}
		return $js."\n".$content.$footer;

	}
	public function cleanCode(&$content,$title=""){
		$content = preg_replace('/<</', '&laquo;', $content);
		$content = preg_replace('/>>/', '&raquo;', $content);
		$content = preg_replace('/\(c\)/', '&copy;', $content);
		$content = preg_replace('/\(r\)/', '&reg;', $content);
		$content = preg_replace('/¿/', '&iquest;', $content);

		//money
		$content = preg_replace('/€/', '&euro;', $content);
		$content = preg_replace('/¥/', '&yen;', $content);
		$content = preg_replace('/¢/', '&cent;', $content);
		$content = preg_replace('/£/', '&pound;', $content);

		//arrows
		$content = preg_replace('/-->/', '&rarr;', $content);
		$content = preg_replace('/<--/', '&larr;', $content);
		$content = preg_replace('/<->/', '&harr;', $content);
		$content = preg_replace('/==>/', '&rArr;', $content);
		$content = preg_replace('/<==/', '&lArr;', $content);
		$content = preg_replace('/<=>/', '&hArr;', $content);

		//math
		//$content = preg_replace('/`(.*?)`/', '&\\1;', $content);
		$content = preg_replace('/=</', '&le;', $content);
		$content = preg_replace('/>=/', '&ge;', $content);
		$content = preg_replace('/!=/', '&ne;', $content);
		$content = preg_replace('/~=/', '&cong;', $content);

		//remove tags
		$content = str_replace('<', '_lower_', $content);
		$content = str_replace('>', '_greater_', $content);

		//now replace _lower_ and _greater_ codes...
		$content = str_replace('_lower_', '&lt;', $content);
		$content = str_replace('_greater_', '&gt;', $content);

		//recreate some tags
		$content = str_replace('_end_strike_', "</del>", $content);
		$content = str_replace('_strike_', "<del>", $content);

		//rule
		$content = preg_replace('/\n*\-{4}\n*/', "<hr />", $content);

		//code
		$content = preg_replace('/\'\'(.*?)\'\'/', "<code>\\1</code>", $content);

		//links
		$links = array ();
		preg_match_all('/\[\[(.*?)\]\]/', $content, $links);
		$foundlinks = $links[1];
		$i = 0;
		foreach ($foundlinks as $foundlink) {
			//seek if we have | to set caption
			$elem = explode("|", $foundlink);
			$link = $elem[0];
			$caption = (isset ($elem[1])) ? $elem[1] : $elem[0];
			$style = "wiki_exists";

			//anchors
			$anchor = explode("#", $link);
			$link = strlen($anchor[0]) ? $anchor[0] : $title;
			if (isset ($anchor[1])) {
				$anchor = $anchor[1];
			} else
			$anchor = "";
			//is it an external link ?
			if (!preg_match('/http:/', $link)) {
				//no, so i think this is a wiki link
				//page exists ?
				$parts = explode('/',$link);
				$heading='';
				$lang='';
				if(count($parts)==3){
					$heading=$parts[0];
					$link=$parts[1];
				}elseif(count($parts)==2){
					$heading=$parts[0];
					$link=$parts[1];
				}
				
				$dao = _ioDao ('wikipages');
				$res = $dao->findBy(_daoSp()->startGroup()->
									addCondition('title_wiki',"=",CopixUrl::escapeSpecialChars ($link))->
									addCondition('heading_wiki','=',$heading)->
									endGroup()								
				);
				if (!$res) {
					$style = "wiki_no_exists";
				} else {
					$anchor = preg_replace("/[^a-zA-Z0-9]/", $this->anchor_separator, $anchor);
				}
				$link = _url ('wiki||show', array (
				'title' => $link,
				'heading' => $heading
				));
			}

			$link .= (isset ($anchor) && strlen($anchor)) ? "#" . $anchor : "";

			//now, replace link
			$link = "<a href=\"" . $link . "\" title=\"" . $caption . "\" class=\"$style\">$caption</a>";
			//link has "//" but this is used for italic... so we have to
			//change it for now... see end function to restore links
			$link = str_replace("//", "_double_slashes_", $link);

			$content = str_replace($links[0][$i], $link, $content);
			$i++;
		}
		//images
		$images = array ();
		preg_match_all('/\{\{(.*?)\}\}/', $content, $images);
		$foundimages = $images[1];
		$i = 0;
		foreach ($foundimages as $foundimg) {

			$elem = explode(":",$foundimg);
			if($elem[0]=="file"){
				//case of file
				$class="wiki_dl_file";
				$image="<a href=\""._url ('wiki|file|getfile', array (
				'title' => $elem[1]
				))."\" title=\"".$elem[1]."\" class=\"$class\">".$elem[1]."</a>";
			}
			else{
				$elem = explode("|", $foundimg);
				$foundimg = $elem[0];
				$width = (isset ($elem[1])) ? $elem[1] : "";
				$align = (isset ($elem[2])) ? $elem[2] : "";
				$disp = "";

				//is it an external link ?
				if (preg_match('/http:/', $foundimg)) {
					$alt = explode("/", $foundimg);
					$alt = $alt[count($alt) - 1];
					$alt = explode('.', $alt);
					$alt = $alt[0];
					if ($width) {
						//$disp = '<a href="' . $foundimg . '" target="_blank" title="' . _i18n ("wiki|wiki.show.fullsize") . '">(-)</a>';
						$icon = "<img src=\""._resource ('img/tools/loupe.png')."\" alt=\"download\"/>";
						$disp = '<a href="' . $foundimg . '" target="_blank" title="' . _i18n ("wiki|wiki.show.fullsize") . '">'.$icon.'</a>';
						$disp = "<span style=\"height:15px;margin-left: -15px;\">$disp</span>";

					}
				}
				//if(!preg_match('/http:/',$foundimg)){
				else {
					//no, so i think this is a wiki link
					$alt = $foundimg;
					$foundimg = _url ('wiki|file|getFile', array (
					'title' => $alt,
					'size' => $width
					));
					$_foundimg= _url ('wiki|file|getFile', array (
					'title' => $alt
					));
					//$_foundimg="javascript:WikiSeeImage('$_foundimg','$alt')";
					$_foundimg='<a href="'.$_foundimg.'" rel="lightbox['.$title.']" title="'.$alt.'">';
					if ($width) {
						$icon = "<img src=\""._resource ("img/tools/loupe.png")."\" alt=\"download\" style=\"z-index: 99\" />";
						$disp = $_foundimg.$icon."</a>";
						$disp = "<span style=\"height:20px;margin-left: -15px;\">$disp</span>";
					}
					if(function_exists("gd_info")){
						$width=""; //because gd resized image
					}
				}
				if (strlen($align)>0)
				$align = ' align="' . $align . '"';
				if (strlen($width)>0)
				$width = ' width="' . $width . '"';
				$image="";
				$image="<span style=\"display: inline\">";
				$image .= '<img name="wiki_image" src="' . $foundimg . '"' . $width . $align . ' alt="' . $alt . '" />';
				$image.=$disp."</span>";
			}
			$content = str_replace($images[0][$i], $image, $content);
			$i++;
		}

		//footnotes
		preg_match_all('/\(\((.*?)\)\)/', $content, $fn);
		$fns = $fn[1];
		$i=count($this->footnotes);
		$j=0;
		foreach ($fns as $footnote) {
			$this->footnotes[]=$footnote;
			$content = str_replace($fn[0][$j], "<a href=\"#wiki_footnotes\" title=\"$footnote\"><sup>".($i+1)."</sup></a>", $content);
			$i++;$j++;
		}

		//other
		$content = preg_replace('/\*{2}(.*?)\*{2}/', "<strong>\\1</strong>", $content);
		$content = str_replace("http://", "_URI_STRING_", $content);
		$content = preg_replace('/\/{2}(.*?)\/{2}/', "<em>\\1</em>", $content);
		$content = str_replace("_URI_STRING_", "http://", $content);
		$content = preg_replace('/_{2}(.*?)_{2}/', "<u>\\1</u>", $content);

		//sup
		$content = preg_replace('/(.+?)\^\((.+?)\)/', '\\1<sup>\\2</sup>', $content);
		$content = preg_replace('/sqrt\((.+?)\)/', ' &radic;<span style="text-decoration: overline">\\1</span>', $content);

		//last modification:
		//$content = preg_replace("/(\s)+/", "\\1", $content);
		$content = str_replace("<br>", "<br />", $content);
		$content = preg_replace('/<h(\d)><br \/>/', "<h\\1>", $content);
		$content = str_replace("_double_slashes_", "//", $content);
	}



	/**
	 * Modify content with list items translated in html tag
	 * @param string &content
	 */
	private function _treatLists($element) {
		//echo "corrige ".$element->data;
		$lines = array ();
		$lines = explode("\n", $element->data);
		$level = 0;
		$lastlevel = 0;
		$items = array();
		$arLevel=array();
		foreach ($lines as $line) {
			$matches = array ();
			if (preg_match('/(\s+)(\*|-)(.*?)$/', $line, $matches)) {
				$listitem = new _wiki_listelem();
				if ($matches[2] == "*"){
					$listitem->type = "ul";
				} else {
					$listitem->type = "ol";
				}
				$c = strlen($matches[1]);
				if ($lastlevel<$c){
					if(!isset($arLevel[$c])){
						$arLevel[$c]=++$level;
					}
						
				} elseif ($lastlevel>$c){
					if(!isset($arLevel[$c])){
						$arLevel[$c]=--$level;
					}
				}
				$listitem->level =$arLevel[$c];
				$lastlevel=$arLevel[$c];
				$listitem->data = $matches[3];
				$items[]=$listitem;
			} //-end list line
		}	//-end foreach line
		//now we can work:
		$level =0;
		$block = "";
		$desc=0;
		$started= false;
		$closedLevel=999999;
		//

		for ($i=0;$i<count($items);$i++){
			$item = &$items[$i];
			if($started && $level == $item->level){
				$block.="</li>\n";
			}
			if($level < $item->level){
				$block.="<".$item->type.">\n";
			}elseif($level > $item->level){
				$j=$i;
				while($items[$j-1]->level!=$item->level){
					$j--;
					if($items[$j]->level<$closedLevel){
						$block.="</li></".$items[$j]->type.">\n";
						$closedLevel=$items[$j]->level;
					}
				}
				$closedLevel=999999;
			}
			$this->cleanCode($item->data);
			$block.="<li>".$item->data;
			$started= true;
			$level=$item->level;
		}

		//close tags
		if($level > 1){
			$j=$i-1; // $i > count($items) cause $i++ on last loop
			while($items[$j-1]->level>=1){
				$j--;
				if($items[$j]->level<$closedLevel){
					$block.="</li></".$items[$j]->type.">\n";
					$closedLevel=$items[$j]->level;
				}
			}
		}

		$block.="</li>\n</".$items[$i-1]->type.">\n\n";

		return $block;
	}

	/**
	 * To render pur HTML content
	 * @return string html code
	 */
	private function _treatHtml($code){
		$code->data = preg_replace('/\[html(.*?)\]/', "",$code->data);
		$code->data = preg_replace('/\[\/html\]/', "",$code->data);
		return $code->data;
	}


	/**
	 * Create TOC and return  headers in html
	 *
	 * @param string $line
	 * @return string header html with named anchor
	 */

	private function _renderHeader($line) {
		//($line);
		preg_match('/(={2,6}) (.*?) (={2,6})/', $line, $matches);
		$name = preg_replace("/[^a-zA-Z0-9]/", $this->anchor_separator, $matches[2]);

		$sp = str_repeat("&nbsp;",(2*(7 - strlen($matches[1]))));
		$this->toc.=$sp."- <a href=\"#$name\">".$matches[2]."</a><br />";

		return "\n<a name=\"" . $name . "\"></a>" .
		"<h" . (7 - strlen($matches[1])) . ">" .
		$matches[2] .
		"</h" . (7 - strlen($matches[3])) . ">\n";

	}

	/**
	 * prepare Elements to be rendered
	 *
	 * @param string $content
	 * @return array elements
	 */
	public function _prepareElements($content) {
		$content = str_replace("\r\n", "\n", $content);
		$content = str_replace("\n\r", "\n", $content);
		$content = str_replace("[newcol]", "\n[newcol]\n", $content);
		//$lines = explode("\n", $content);
		////print_r("preg\n");
		$lines = preg_split("/\n/", $content,-1,PREG_SPLIT_DELIM_CAPTURE);
		////print_r($lines_);
		//($lines);
		$final = "";
		_classInclude('wiki|WikiElement');
		$elements = array ();
		$i = 0;
		$first = true;
		for ($j = 0; $j < count($lines); $j++) {
			$line = $lines[$j];
			if (preg_match('/^={2,} (.*?) ={2,}$/', trim($line))) {
				$elements[$i] = new WikiElement("header", $this->_renderHeader($line));
				$first = true;
			}elseif (preg_match('/\[newcol\]/', trim($line))) {
				//nouvelle colonne
				$elements[$i] = new WikiElement("colon", $line);
				$first = true;
			}elseif (preg_match('/\[frompage:(.*?)\]/', trim($line),$matches)) {
				//i18n
				$elements[$i] = new WikiElement("frompage", trim($matches[1])); //translate from page
				$first = true;
			}elseif (preg_match('/\[lang:(.*?)\]/', trim($line),$matches)) {
				$elements[$i] = new WikiElement("language", trim($matches[1])); //page lang
				$first = true;
			}elseif (preg_match('/\[cols:(\d*?)\]/', trim($line),$matches)) {
				//gestion de colonnes
				$elements[$i] = new WikiElement("cols", trim($matches[1])); //page
				$first = true;
			}elseif (preg_match('/^\[code(.*?)\]/', trim($line),$matches)) {
				//bloc de code
				$first = true;
				$elements[$i] = new WikiElement("code", $line);
				if(count($matches)) $elements[$i]->other=$matches[1];
				do {
					$j++;
					$line = $lines[$j];
					$elements[$i]->data .= "\n" . $line;
				} while (!preg_match('/\[\/code\]/', $line));
			}elseif (preg_match('/^\[math(.*?)\]/', trim($line))) {
				//Math va utiliser Tex
				$first = true;
				$elements[$i] = new WikiElement("math", $line);
				do {
					$j++;
					$line = $lines[$j];
					$elements[$i]->data .= "\n" . $line;
				} while (!preg_match('/\[\/math\]/', $line));
			}elseif (preg_match('/^\[graphviz(.*?)\]/', trim($line),$matches)) {
				//Mode graph génération par graphviz (neato et dot)
				$first = true;
				$elements[$i] = new WikiElement("graphviz", $line);
				if(count($matches)) $elements[$i]->other=$matches[1];

				do {
					$j++;
					$line = $lines[$j];
					$elements[$i]->data .= "\n" . $line;
				} while (!preg_match('/\[\/graphviz\]/', $line));
			}elseif (preg_match('/^(\||\^).*(\||\^)$/',trim($line))){
				//génération de tableau
				$first=true;
				$elements[$i] = new WikiElement("table", "");
				while (preg_match('/^(\||\^).*(\||\^)$/', $line)){
					$elements[$i]->data .= "\n" . $line;
					$j++;
					isset($lines[$j]) ? $line = $lines[$j] : $line = "\n";
				}
				$j--;
			}elseif (preg_match('/^(\s+)(\*|-)(.*?)$/', $line)) {
				//listes à puce ou numéraire		
				$first = true;
				$elements[$i] = new WikiElement("lists", "");
				while (preg_match('/^(\s*)(\*|-)(.*?)$/', $line)){
					$elements[$i]->data .= "\n" . $line;
					$j++;
					isset($lines[$j]) ? $line = $lines[$j] : $line = "\n";
				}
				$j--;
			}elseif (preg_match('/^\[html(.*?)\]/', trim($line),$matches)) {
				//Mode graph génération par graphviz (neato et dot)		
				$first = true;
				$elements[$i] = new WikiElement("html", $line);
				if(count($matches)) $elements[$i]->other=$matches[1];

				do {
					$j++;
					$line = $lines[$j];
					$elements[$i]->data .= "\n" . $line;
				} while (!preg_match('/\[\/html\]/', $line));
			}else {
				//assume it's a simple line
				if(strlen(trim($line))<1){
					$elements[$i] = new WikiElement("paragraph", "");
				}
				elseif(strlen(trim($line))>0 && isset($elements[$i-1]) && $elements[$i-1]->type=="paragraph"){
					$i--;
					$elements[$i]->data .= "\n" . $line;
				}
				else {
					$elements[$i] = new WikiElement("paragraph", "");
					$elements[$i]->data .= "\n" . $line;
				}
			}
			$i++;
		}
		//print_r($elements);
		return $elements;
	}

	/**
	 * Create table in html format and replace it in content
	 *
	 * @param string $code
	 */

	private function _transformTable(&$code){
		$lines = explode("\n",$code);
		//
		$code = '<br /><table class="wiki_table" cellspacing="0" cellpadding="0">'."\n";
		foreach($lines as $line){
			if(strlen(trim($line))<1) continue;
				
			//remove first and last pipe
			/*
			$line = preg_replace('/^\|/',"",$line);
			$line = preg_replace('/\|$/',"",$line);
			*/
			$cells = preg_split('/(\||\^)/s',$line,-1,PREG_SPLIT_DELIM_CAPTURE);
			$code.="\n<tr>\n\t";
			$colspan=1;
			$tomerge=false;
			$cellnum = 0;
				

			$counter=0;
			$_cells=array();
			foreach($cells as $cell){
				//the first and last elems is always empty
				if($counter==0 || $counter==count($cells)-1){
					$counter++;
				}
				else{
					if($cell=="^" || $cell=="|"){
						//type detected
						$_cell = new _wiki_table_cell();
						$_cell->type= ($cell=="|") ? "td" : "th";
					}
					else{
						$_cell->data = $cell;
						$_cells[]=$_cell;
					}
					$counter++;
				}
			}
				
			$cells = $_cells;
				
			for ($i=0;$i<count($cells);$i++){
				//foreach($cells as $cell){
				$cell = $cells[$i];
				while(isset($cells[$i+1]) && strlen($cells[$i+1]->data)==0) {
					$colspan++;
					$tomerge=true;
					$i++;
				}

				//check alignement
				$alignement = "";
				if(preg_match('/^(\s{2,})/',$cell->data)){
					$alignement="right";
				}
				if(preg_match('/(\s{2,})$/',$cell->data)){
					$alignement="left";
				}

				if(preg_match('/^(\s{2,})/',$cell->data) && $alignement=="left"){
					$alignement="center";
				}

				if(strlen($alignement)){
					$alignement=' style="text-align: '.$alignement.'" ';
				}

				if($tomerge){
					$code.='<'.$cell->type.' colspan="'.$colspan.'"'.$alignement.'>';
					$colspan=1;
					$tomerge=false;
				}
				else{
					$code.='<'.$cell->type.$alignement.'>';
				}
				if(strlen($cell->data)>0) {
					if($cell->data==" ")
					$code.='&nbsp;</'.$cell->type.'>';
					else
					$code.=$cell->data.'</'.$cell->type.'>';
				}
				$cellnum++;
			}
			$code.="</tr>\n";
		}
		$code .= "</table>\n";
	}


	/**
	 * Create code highlights
	 * @param _wikielement codeelement
	 */

	private function _treatCode($code){
		//syntaxe highlight
		$code->data = preg_replace('/\[code(.*?)\]/', "",$code->data);
		$code->data = preg_replace('/\[\/code(.*?)\]/', "",$code->data);

		require_once (CopixModule::getPath ('geshi').'lib/geshi/geshi.php');

		$lang = $code->other;
		$code = $code->data;
		$geshi = new GeSHi($code, $lang);
		$geshi->set_header_type (GESHI_HEADER_DIV);
		$code = $geshi->parse_code ();

		$code = "\n" . '<div class="wiki_code">' . $code . '</div>' . "\n";
		return $code;
	}

	/**
	 * Create math render with TeX
	 * @param _wikielement mathelement
	 */
	private function _treatMath($code){
		$code->data = preg_replace('/\[math(.*?)\]/', "",$code->data);
		$code->data = preg_replace('/\[\/math(.*?)\]/', "",$code->data);
		_classInclude('wikirender|MathRender');
		$code = $code->data;
		$converter = new MathRender($code);
		$img = $converter->render();
		$code = "\n" . '<img src="'._url ("wiki|file|getMathImage",array("math"=>$img))."\" />\n";
		return $code;
	}


	/**
	 * Create a graph done by graphviz
	 * @param _wikielement graph
	 */

	private function _treatGraphviz($code){
		$code->data = preg_replace('/\[graphviz(.*?)\]/', "",$code->data);
		$code->data = preg_replace('/\[\/graphviz(.*?)\]/', "",$code->data);
		//graphviz
		_classInclude('wikirender|GraphViz');
		$type = $code->other;
		$code = $code->data;
		$converter = new GraphViz($code,$type);
		$img = $converter->render();
		$map = $converter->getMap();
		$mapname = $converter->getMapName();
		//var_dump($converter);
		$code = $map;
		$code .= "\n" . '<img usemap="#'.$mapname.'" src="'._url ("wiki|file|getGraphViz",array("graph"=>$img))."\" />\n";
		return $code;
	}

	/**
	 * Create translations flags array
	 * @param _wikielement type "lang"
	 */
	private function _addCols($elem){

		return 'window.addEvent("domready",function(){
	$("wiki_content").divide({
		"cols" : '.$elem->data.',
		"firstclass": "moocolumn",
		"nextclass": "moocolumn2"
	});
	//move toc
	var cols = $$(".moocolumn2");
	var col = cols[cols.length-1];
	if($("wiki_toc")){
		$("wiki_toc").injectBefore(col.getChildren()[0]);
		$("wiki_content").divide({
			"cols" : '.$elem->data.'
		},true);
	}
});';
	}
}

// fly object
class _wiki_listelem{
	public $level;
	public $type;
	public $data;
	public $other;
}

class _wiki_table_cell{
	public $type;
	public $data;
}

?>