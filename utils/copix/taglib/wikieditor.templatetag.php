<?php

class TemplateTagWikiEditor extends CopixTemplateTag {
    public function process($pParams) {
        
        extract($pParams);
        if (!isset($name)) {
            throw new CopixTemplateTagException("Manque nom");
        }
        
        $toReturn  = "<div id=\"wiki_toolbar\" style=\"clear:both\">\n";
		$toReturn .= "<a class=\"wiki_toolbar\" onclick=\"javascript:fontStyle('**','**','"._i18n("wiki|wiki.bold")."');\" title=\""._i18n("wiki|wiki.bold")."\"><img src=\"".CopixUrl::getResource('/img/modules/wiki/bold.png')."\" /></a>\n";
        $toReturn .= "<a class=\"wiki_toolbar\" onclick=\"javascript:fontStyle('//','//','"._i18n("wiki|wiki.italic")."');\" title=\""._i18n("wiki|wiki.italic")."\"><img src=\"".CopixUrl::getResource('/img/modules/wiki/italic.png')."\" /></a>\n";
        $toReturn .= "<a class=\"wiki_toolbar\" onclick=\"javascript:fontStyle('__','__','"._i18n("wiki|wiki.underline")."');\" title=\""._i18n("wiki|wiki.underline")."\"><img src=\"".CopixUrl::getResource('/img/modules/wiki/underline.png')."\" /></a>\n";		
        $toReturn .= "<a class=\"wiki_toolbar\" onclick=\"javascript:fontStyle('   *','','"._i18n("wiki|wiki.listitem")."');\" title=\""._i18n("wiki|wiki.listitem")."\"><img src=\"".CopixUrl::getResource('/img/modules/wiki/list.png')."\" /></a>\n";		        
        $toReturn .= "<a class=\"wiki_toolbar\" onclick=\"javascript:fontStyle('<del>','</del>','"._i18n("wiki|wiki.strike")."');\" title=\""._i18n("wiki|wiki.strike")."\"><img src=\"".CopixUrl::getResource('/img/modules/wiki/strike.png')."\" /></a>\n";		                
        $toReturn .= "<a class=\"wiki_toolbar\" onclick=\"javascript:fontStyle('\n----\n','','');\" title=\""._i18n("wiki|wiki.hr")."\"><img src=\"".CopixUrl::getResource('/img/modules/wiki/hr.png')."\" /></a>\n";		                        
        $toReturn .= "<a class=\"wiki_toolbar\" onclick=\"javascript:fontStyle('\'\'','\'\'','"._i18n("wiki|wiki.code")."');\" title=\""._i18n("wiki|wiki.code")."\"><img src=\"".CopixUrl::getResource('/img/modules/wiki/code.png')."\" /></a>\n";		                
        $toReturn .= "<a class=\"wiki_toolbar\" onclick=\"javascript:addHeader(1);\" title=\""._i18n("wiki|wiki.header",1)."\"><img src=\"".CopixUrl::getResource('/img/modules/wiki/h1.png')."\" /></a>\n";
        $toReturn .= "<a class=\"wiki_toolbar\" onclick=\"javascript:addHeader(2);\" title=\""._i18n("wiki|wiki.header",2)."\"><img src=\"".CopixUrl::getResource('/img/modules/wiki/h2.png')."\" /></a>\n";
        $toReturn .= "<a class=\"wiki_toolbar\" onclick=\"javascript:addHeader(3);\" title=\""._i18n("wiki|wiki.header",3)."\"><img src=\"".CopixUrl::getResource('/img/modules/wiki/h3.png')."\" /></a>\n";
        $toReturn .= "<a class=\"wiki_toolbar\" onclick=\"javascript:addHeader(4);\" title=\""._i18n("wiki|wiki.header",4)."\"><img src=\"".CopixUrl::getResource('/img/modules/wiki/h4.png')."\" /></a>\n";
        $toReturn .= "<a class=\"wiki_toolbar\" onclick=\"javascript:addHeader(5);\" title=\""._i18n("wiki|wiki.header",5)."\"><img src=\"".CopixUrl::getResource('/img/modules/wiki/h5.png')."\" /></a>\n";
        $toReturn .= "<a class=\"wiki_toolbar\" onclick=\"javascript:sendForPreview();\" title=\""._i18n("wiki|wiki.show.preview")."\"><img src=\"".CopixUrl::getResource('/img/modules/wiki/preview.png')."\" /></a>\n";                                
		$toReturn .= "</div>";
		
		$toReturn .= "
<textarea class=\"noresizable\" id=\"wiki_area_content\" name=\"$name\"
	cols=\"100\" rows=\"30\">$content
</textarea>
<div id=\"aj_wiki_prev\" style=\"display: none\">
</div>

		";
		$urlofrenderer = _url('generictools|ajax|getwikipreview');
        CopixHTMLHeader::addJsCode("
var onPreviewMode = false;
function sendForPreview(){
	if(!onPreviewMode){
		var borders=$('wiki_area_content').getStyle('border');
		var width=$('wiki_area_content').getStyle('width');
		$('aj_wiki_prev').setStyles({
			'border': borders,
			'width' : width 
		});
		var aj = new Ajax('".$urlofrenderer."',{
			method : 'post',
			update :'aj_wiki_prev',
			data : 'torender='+$('wiki_area_content').value
		}).request();
		onPreviewMode = true;
		$('wiki_area_content').setStyle('display','none')
		$('aj_wiki_prev').setStyle('display','block')
	}else{
		$('wiki_area_content').setStyle('display','block');
		$('aj_wiki_prev').setStyle('display','none');
		onPreviewMode = false;
	}
}

function addHeader(n){
    var h=\"\";
	if(n==1) h=\"======\";
	if(n==2) h=\"=====\";
	if(n==3) h=\"====\";
	if(n==4) h=\"===\";
	if(n==5) h=\"==\";
	
	var editor = document.getElementById('wiki_area_content');
	fontStyle(h+\" \",\" \"+h,\"Header\"+n);
}

/**
 * apply tagOpen/tagClose to selection in textarea, use sampleText instead
 * of selection if there is none copied and adapted from phpBB
 *
 * @author phpBB development team
 * @author MediaWiki development team
 * @author Andreas Gohr <andi@splitbrain.org>
 * @author Jim Raynor <jim_raynor@web.de>
 */
function fontStyle(tagOpen, tagClose, sampleText) {
  var txtarea = document.getElementById('wiki_area_content');
  // IE
  if(document.selection  && !is_gecko) {
    var theSelection = document.selection.createRange().text;
    var replaced = true;
    if(!theSelection){
      replaced = false;
      theSelection=sampleText;
    }
    txtarea.focus();
 
    // This has change
    text = theSelection;
    if(theSelection.charAt(theSelection.length - 1) == \" \"){// exclude ending space char, if any
      theSelection = theSelection.substring(0, theSelection.length - 1);
      r = document.selection.createRange();
      r.text = tagOpen + theSelection + tagClose + \" \";
    } else {
      r = document.selection.createRange();
      r.text = tagOpen + theSelection + tagClose;
    }
    if(!replaced){
      r.moveStart('character',-text.length-tagClose.length);
      r.moveEnd('character',-tagClose.length);
    }
    r.select();
  // Mozilla
  } else if(txtarea.selectionStart || txtarea.selectionStart == '0') {
    var replaced = false;
    var startPos = txtarea.selectionStart;
    var endPos   = txtarea.selectionEnd;
    if(endPos - startPos) replaced = true;
    var scrollTop=txtarea.scrollTop;
    var myText = (txtarea.value).substring(startPos, endPos);
    if(!myText) { myText=sampleText;}
    if(myText.charAt(myText.length - 1) == \" \"){ // exclude ending space char, if any
      subst = tagOpen + myText.substring(0, (myText.length - 1)) + tagClose + \" \";
    } else {
      subst = tagOpen + myText + tagClose;
    }
    txtarea.value = txtarea.value.substring(0, startPos) + subst +
                    txtarea.value.substring(endPos, txtarea.value.length);
    txtarea.focus();
 
    //set new selection
    //modified by Patrice Ferlet
    // - selection wasn't good for selected text replaced
    txtarea.selectionStart=startPos+tagOpen.length;   
    txtarea.selectionEnd=startPos+tagOpen.length+myText.length;

    txtarea.scrollTop=scrollTop;
  // All others
  } else {
    var copy_alertText=alertText;
    var re1=new RegExp(\"\\$1\",\"g\");
    var re2=new RegExp(\"\\$2\",\"g\");
    copy_alertText=copy_alertText.replace(re1,sampleText);
    copy_alertText=copy_alertText.replace(re2,tagOpen+sampleText+tagClose);
    var text;
    if (sampleText) {
      text=prompt(copy_alertText);
    } else {
      text=\"\";
    }
    if(!text) { text=sampleText;}
    text=tagOpen+text+tagClose;
    //append to the end
    txtarea.value += text;

    // in Safari this causes scrolling
    if(!is_safari) {
      txtarea.focus();
    }

  }
  // reposition cursor if possible
  if (txtarea.createTextRange) txtarea.caretPos = document.selection.createRange().duplicate();
}
		");
        
        
        return $toReturn;
        
    }
}
?>