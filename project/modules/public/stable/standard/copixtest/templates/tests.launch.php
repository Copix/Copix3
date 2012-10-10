<?php 
_tag ('mootools', array ('plugin'=>'progressbar'));
?>

<div id="progessDiv">
 <div id="statusProgressBar" style="width: 300px;text-align: center">&nbsp;</div> 
 <div id="progressBar" style="border: 1px solid #000; width: 300px;"></div>
 <div id="CopixAjaxResults"></div>
</div>

<script defer language="Javascript">
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
      new Ajax ('<?php echo _url (); ?>test.php?xml=1&tests[]='+linkList[position], {onComplete: function (e){
			$('CopixAjaxResults').setHTML (linkList[position]);
			if (! evalResults (this.response['xml'])){
			   markUnknownResponse (linkList[position], this.response['text']);
			}
			progressBar1.step ();
			makeCall ();
      }}).request ();
   }else{
		$('progessDiv').setHTML ('');
   }
   position = position+1;
}

function markUnknownResponse (tested, text){
	addLineToResults (tested, 0, 0, 100, 0, new Array (), 'ffff00');
}

function getFirstTagContent (list, tagName){
   var element = list.getElementsByTagName (tagName);
   if (element[0]){
      return element[0].textContent;
   }
   return null;
}

function addTextLineToResults (name, text1, text2){
	var tableTestResults = document.getElementById ("TableTestsTextResults");

   	newRow = tableTestResults.insertRow (-1);
   	newCell = newRow.insertCell (0);
   	newCell.innerHTML = name;
	
	newRow = tableTestResults.insertRow (-1);
    newCell = newRow.insertCell (0);
   	newCell.innerHTML = text1;
   	
	newRow = tableTestResults.insertRow (-1);
    newCell = newRow.insertCell (0);
   	newCell.innerHTML = text2;
}

function addLineToResults (name, error, failure, incomplete, success, color){
	var tableTestResults = document.getElementById ("TableTestsResults");

   	newRow = tableTestResults.insertRow (-1);
   	newCell = newRow.insertCell (0);
   	newCell.innerHTML = name;

    newCell = newRow.insertCell (1);
   	newCell.innerHTML = error;

   	newCell = newRow.insertCell (2);
   	newCell.innerHTML = incomplete;        	

    newCell = newRow.insertCell (3);
	newCell.innerHTML = failure;

    newCell = newRow.insertCell (4);
    newCell.innerHTML = success;
       	
	styleRow = new Fx.Style(newRow, 'background-color', {duration: 1000});
	styleRow.start ('ffffff', color);
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
       	
       	addLineToResults (name, error, failure, incomplete, success, color);
       	addErrors (XMLResponse.getElementsByTagName ('errors'), 'Errors');
       	addErrors (XMLResponse.getElementsByTagName ('failures'), 'Failures');
       	addErrors (XMLResponse.getElementsByTagName ('incompletes'), 'Incompletes');
       	addErrors (XMLResponse.getElementsByTagName ('skipped'), 'Skipped');       	

		return true;
	}
	return false;
}

function addErrors (ErrorList, ErrorType){
   var i = 0;
   for (i=0; i<ErrorList.length; i++){
      addTextLineToResults (ErrorType, getFirstTagContent (ErrorList[i], 'name'), getFirstTagContent (ErrorList[i], 'description'));
   }
}
<?php
$array = array ();
foreach ($ppo->arTests as $moduleName=>$modulesTest){
	if (count ($modulesTest)){
		foreach ($modulesTest as $test){
			$array[] = '"'.$test.'"';
		}
	}
}
echo 'doIndex (new Array ('.implode (",", $array).'));';
?>
</script>

<table class="CopixTable" id="TableTestsTextResults">
  <thead>
   <tr>
    <th>Problèmes</th>
   </tr>
  </thead>
  <tbody>
   <tr>
   </tr>
  </tbody>
</table>

<br />

<table class="CopixTable" id="TableTestsResults">
  <thead>
   <tr>
    <th>Nom</th>
    <th width="50">Erreur</th>
    <th width="50">Incomplet</th>
    <th width="50">Echec</th>
    <th width="50">Succès</th>
   </tr>
  </thead>
  <tbody>
   <tr>
   </tr>
  </tbody>
</table>