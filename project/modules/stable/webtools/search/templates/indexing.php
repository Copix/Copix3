<?php 
_tag ('mootools', array ('plugin'=>'progressbar'));
?>

<div id="statusProgressBar" style="width: 300px;text-align: center">&nbsp;</div> 
<div id="progressBar" style="border: 1px solid #000; width: 300px;"></div>
<h2>Erreurs</h2>
<ol id="errors"></ol>
<h2>Warnings</h2>
<ol id="warnings"></ol>
<br />
<a href="<?php echo _url ('admin||'); ?>"><input type="button" value="<?php echo _i18n ('copix:common.buttons.back'); ?>" /></a>


<?php
$url = _url ('indexing|prepareIndexAll');
$js = <<<EOF
var linkList, total, position, progressBar;

function doIndex (XMLList){
	position = 0;
	linkList = XMLList.getElementsByTagName ('link');
	total = linkList.length;
	progressBar = new ProgressBar ('progressBar', {steps: total, length: 300, statusBar: 'statusProgressBar'});
	makeCall ();
}

function makeCall (){
        if (this && this.response && this.response.text && this.response.text != null) {
            warnAndContinue (this.response.html);
            return;
        }
	if (position < total){
		progressBar.step ();
		//new Ajax (linkList[position].getAttribute ('url'), {method: 'get', onComplete: makeCall, onFailure:logAndContinue}).request ();
                new Request.HTML ({url:linkList[position].getAttribute ('url'), method: 'get', onComplete: makeCall, onFailure:logAndContinue}).send ();

	}
	position++;
}

function logAndContinue (){
	$('errors').adopt( new Element('li', {'text': 'Erreur ' + linkList[position-1].getAttribute ('url')}) );
	makeCall ();
}

function warnAndContinue (text){
        $('warnings').adopt( new Element('li', {'text': linkList[position-1].getAttribute ('url')+" : "+text}) );
        makeCall ();
}

/*
new Ajax('$url', {
	'onComplete' : function () {
		if (! this.response['xml']){
			//gestion de l'erreur
			$('errors').adopt( new Element('li', {'text': 'Erreur de récupération de la liste des éléments à indexer'}) );
		}else{
			//ok, bien eu une réponse correcte
			doIndex (this.response['xml']);
		}
	}
}).request ();
*/

new Request.HTML({'url':'$url',
        'onComplete' : function () {
                if (! this.response['xml']){
                   //gestion de l'erreur
	           $('errors').adopt( new Element('li', {'text': 'Erreur de récupération de la liste des éléments à indexer'}) );
                }else{
                   //ok, bien eu une réponse correcte
                   doIndex (this.response['xml']);
                }
        }
}).send ();

EOF;
CopixHTMLHeader::addJSDOMReadyCode( $js );
?>
