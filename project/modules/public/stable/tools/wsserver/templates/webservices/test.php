<h2 class="first">Webservice</h2>
<table class="CopixVerticalTable">
	<tr>
		<th style="width: 90px">Webservice</th>
		<td style="width: 10px">
			<select name="webservice" id="webservice" onchange="onChangeWebservice ()">
				<option value="NOTHING">-----</option>
				<?php foreach ($ppo->webservices as $webservice) { ?>
					<option value="<?php echo _url ('wsserver|default|wsdl', array ('wsname'=> $webservice->name_wsservices)); ?>" <?php if ($webservice->id_wsservices == $ppo->webservice) { echo 'selected="selected"'; } ?>><?php echo $webservice->name_wsservices ?></option>
				<?php } ?>
			</select>
		</td>
		<td><input type="text" name="webservice_url" id="webservice_url" style="width: 98%" onchange="onChangeWebserviceUrl ()" /></td>
	</tr>
	<tr class="alternate">
		<th>Fonction</th>
		<td id="listFunctions" colspan="2"></td>
	</tr>
</table>

<h2>Paramètres</h2>
<div id="parameters"></div>

<br />
<center>
	<input type="button" value="Appeler avec SOAP" onclick="getResult (true)" />
	<input type="button" value="Appeler la méthode" onclick="getResult (false)" id="callMethod" />
</center>

<h2>Résultat</h2>
<div id="result"></div>

<script type="text/javascript">
function onChangeWebservice () {
	if ($ ('webservice').value != 'NOTHING') {
		$ ('webservice_url').value = $ ('webservice').value;
		onChangeWebserviceUrl ();
	} else {
		$ ('callMethod').style.display = 'none';
		$ ('webservice_url').value = '';
		$ ('listFunctions').innerHTML = '';
		$ ('parameters').innerHTML = '';
		$ ('result').innerHTML = '';
	}
};

function onChangeWebserviceUrl () {
	$ ('listFunctions').innerHTML = '';
	$ ('parameters').innerHTML = '';
	$ ('result').innerHTML = '';
	
	var inWebserviceList = false;
	var newUrl = $ ('webservice_url').value;
	for (boucle = 0; boucle < $ ('webservice').options.length; boucle++) {
		if ($ ('webservice').options[boucle].value == newUrl) {
			inWebserviceList = true;
		}
	}
	if (!inWebserviceList) {
		$ ('webservice').selectedIndex = 0;
	}
	$ ('callMethod').style.display = (inWebserviceList) ? '' : 'none';
	
	new Ajax ('<?php echo _url ('wsserver|admin|getFunctions'); ?>', {
		method: 'POST',
		data : 'webservice=' + $ ('webservice_url').value,
		onComplete: function (pResponse) {
			$ ('listFunctions').innerHTML = pResponse;
			if ($ ('function') != undefined) {
				onChangeFunction ();
			}
		}
	}).request ();
}

function onChangeFunction () {
	$ ('result').innerHTML = '';
	new Ajax ('<?php echo _url ('wsserver|admin|getParameters'); ?>', {
		method: 'POST',
		data : 'webservice=' + $ ('webservice_url').value + '&function=' + $ ('function').value,
		onComplete: function (pResponse) {
			$ ('parameters').innerHTML = pResponse;
		}
	}).request ();
}

function getResult (pWithSOAP) {
	$ ('result').innerHTML = '<img src="<?php echo _resource ('img/tools/load.gif') ?>" />';
	var queryString = 'function=' + $ ('function').value;
	if (pWithSOAP) {
		queryString = queryString + '&webservice=' + $ ('webservice_url').value;
	} else {
		queryString = queryString + '&soap=false&webservice=' + $ ('webservice').options[$ ('webservice').selectedIndex].getText ();
	}
	if ($ ('formParameters') != undefined) {
		queryString += '&' + $ ('formParameters').toQueryString ();
	}
	new Ajax ('<?php echo _url ('wsserver|admin|getResult'); ?>', {
		method: 'POST',
		data : queryString,
		onComplete: function (pResponse) {
			$ ('result').innerHTML = pResponse;
		}
	}).request ();
}
</script>

<?php CopixHTMLHeader::addJSDOMReadyCode ('onChangeWebservice ()') ?>