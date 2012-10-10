{literal}
<script type="text/javascript">
//<![CDATA[

function addHeader(n){
    var h="";
	if(n==1) h="======";
	if(n==2) h="=====";
	if(n==3) h="====";
	if(n==4) h="===";
	if(n==5) h="==";
	
	var editor = document.getElementById('wiki_area_content');
	fontStyle(h+" "," "+h,"Header"+n);
}

var onPreview = false;
function sendForPreview(){
	url = "{/literal}{copixurl dest="wiki|admin|preview" title_wiki=$page->title_wiki}{literal}"
	if(!onPreview){
		var aj = new Ajax(url,{
			data : {'content': $('wiki_area_content').value},
			method: "post",
			update: 'wiki_preview'
		}).request();
		//seulement dans le cas d'un resizer
		if($('wiki_area_content_resizer'))
			$('wiki_area_content_resizer').setStyle('display','none');
		$('wiki_area_content').setStyle('display','none');
		$('wiki_preview').setStyle('display' , 'block');
	}else{
		//seulement dans le cas d'un resizer
		if($('wiki_area_content_resizer'))
			$('wiki_area_content_resizer').setStyle('display','block');
		$('wiki_area_content').setStyle('display','block');
		$('wiki_preview').setStyle('display' , 'none');
		$('wiki_preview').set('html', '');
	}
	onPreview = ! onPreview;	
}


/**
 * Some browser detection
 */
var clientPC  = navigator.userAgent.toLowerCase(); // Get client info
var is_gecko  = ((clientPC.indexOf('gecko')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('khtml') == -1) && (clientPC.indexOf('netscape/7.0')==-1));
var is_safari = ((clientPC.indexOf('AppleWebKit')!=-1) && (clientPC.indexOf('spoofer')==-1));
var is_khtml  = (navigator.vendor == 'KDE' || ( document.childNodes && !document.all && !navigator.taintEnabled ));
if (clientPC.indexOf('opera')!=-1) {
    var is_opera = true;
    var is_opera_preseven = (window.opera && !document.childNodes);
    var is_opera_seven = (window.opera && document.childNodes);
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
  var txtarea = $('wiki_area_content');
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
    if(theSelection.charAt(theSelection.length - 1) == " "){// exclude ending space char, if any
      theSelection = theSelection.substring(0, theSelection.length - 1);
      r = document.selection.createRange();
      r.text = tagOpen + theSelection + tagClose + " ";
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
    if(myText.charAt(myText.length - 1) == " "){ // exclude ending space char, if any
      subst = tagOpen + myText.substring(0, (myText.length - 1)) + tagClose + " ";
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
    var re1=new RegExp("\\$1","g");
    var re2=new RegExp("\\$2","g");
    copy_alertText=copy_alertText.replace(re1,sampleText);
    copy_alertText=copy_alertText.replace(re2,tagOpen+sampleText+tagClose);
    var text;
    if (sampleText) {
      text=prompt(copy_alertText);
    } else {
      text="";
    }
    if(!text) { text=sampleText;}
    text=tagOpen+text+tagClose;
    //append to the end
    txtarea.value += "\n"+text;

    // in Safari this causes scrolling
    if(!is_safari) {
      txtarea.focus();
    }

  }
  // reposition cursor if possible
  if (txtarea.createTextRange) txtarea.caretPos = document.selection.createRange().duplicate();
}
//]]>
</script>
{/literal}