<?php 
_tag ('mootools', array ('plugin'=>'progressbar'));
?>

<div id="progressDiv">
 <div id="statusProgressBar" style="width: 300px;text-align: center">&nbsp;</div> 
 <div id="progressBar" style="border: 1px solid #000; width: 300px;"></div>
 <div id="CopixAjaxResults"></div>
</div>

<?php
$jsCode = "var baseCallUrl = '"._url ()."'
";
$jsCode .= <<<EOF
var linkList;
var position = 0;
var progressBar1;

function doIndex (List){
   var i = 0;
   linkList = List;
   progressBar1 = new ProgressBar ('progressBar', {steps: linkList.length, length: 300, statusBar: 'statusProgressBar'});
   makeCall ();
}

function sleep(millis) {
    var notifier = new EventNotifier();
    setTimeout (notifier, millis);
    notifier.wait();
}

function makeCall (){
   if (position < linkList.length){
			$('CopixAjaxResults').set ('html', linkList[position]);
   	  new Ajax (baseCallUrl + 'test.php?xml=1&tests[]='+linkList[position], {method: 'get', onComplete: function (e){
			if (! evalResults (this.response['xml'])){
			   markUnknownResponse (linkList[position], this.response['text']);
			}
			progressBar1.step ();
			makeCall ();
      }}).request ();
   }else{
		$('progressDiv').set ('html', '<p>Tests terminés</p>');
   }
   position = position+1;
}

function markUnknownResponse (tested, text){
	addTextLineToResults (tested, '', text, 'ffff00');
}

function getFirstTagContent (list, tagName){
   var element = list.getElementsByTagName (tagName);
   if (element[0]){
      return element[0].textContent;
   }
   return null;
}

function addTextLineToResults (name, text1, text2, color){
	var tableTestResults = document.getElementById ("TableTestsTextResults");
	$('TableTestsTextResults').setStyle ('visibility', 'visible');

   	newRow = tableTestResults.insertRow (-1);
   	newCell = newRow.insertCell (0);
   	newCell.innerHTML = name + '-' + text1;

	styleRow = new Fx.Style(newRow, 'background-color', {duration: 1000});
	styleRow.start ('ffffff', color);

	newRow = tableTestResults.insertRow (-1);
    newCell = newRow.insertCell (0);
   	newCell.innerHTML = text2;
}


function evalResults (XMLResponse){
	if (XMLResponse){
		var color = '00ff00';
		var name = getFirstTagContent (XMLResponse, 'name');
		var error = getFirstTagContent (XMLResponse, 'error');
		var failure = getFirstTagContent (XMLResponse, 'failure');
		var incomplete = getFirstTagContent (XMLResponse, 'incomplete');
		var success = getFirstTagContent (XMLResponse, 'success');
		
       	if (error != '0' || failure != '0'){
       	    color = 'ff0000';
       	}else if (incomplete != '0'){
       		color = 'ffff00';
       	}

       	addErrors (XMLResponse.getElementsByTagName ('errors'), 'Errors', 'ff0000');
       	addErrors (XMLResponse.getElementsByTagName ('failures'), 'Failures', 'ff0000');
       	addErrors (XMLResponse.getElementsByTagName ('incompletes'), 'Incompletes', 'ffff00');
       	addErrors (XMLResponse.getElementsByTagName ('skipped'), 'Skipped', 'ffff00');       	
		return true;
	}
	return false;
}

function addErrors (ErrorList, ErrorType, color){
   var i = 0;
   for (i=0; i<ErrorList.length; i++){
      addTextLineToResults (ErrorType, getFirstTagContent (ErrorList[i], 'name'), getFirstTagContent (ErrorList[i], 'description'), color);
   }
}
EOF;
$array = array ();
foreach ($ppo->arTests as $moduleName=>$modulesTest){
	if (count ($modulesTest)){
		foreach ($modulesTest as $test){
			$array[] = '"'.$test.'"';
		}
	}
}
$jsCode .= 'doIndex (new Array ('.implode (",", $array).'));';
CopixHTMLHeader::addJSDOMReadyCode ($jsCode);
?>

<table class="CopixTable" id="TableTestsTextResults" style="visibility:hidden">
  <thead>
   <tr>
    <th>Problèmes détectés</th>
   </tr>
  </thead>
  <tbody>
   <tr>
   </tr>
  </tbody>
</table>

<br />