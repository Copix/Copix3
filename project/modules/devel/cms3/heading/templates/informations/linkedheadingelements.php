<?php echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => 'Eléments liés', 'icon' => _resource ('heading|img/togglers/linkedelements.png'))) ?>

<div class="element">
	<div class="elementContent">
		<table class="CopixTable" id="tableElements">
			<tr>
				<th style="width: 16px"></th>
				<th>Titre</th>
				<th></th>
			</tr>
		</table>

		<br />
		<?php
		echo CopixZone::process ('heading|headingelement/headingelementchooser', array (
			'inputElement' => 'addLinkedElement',
			'linkOnHeading' => false,
			'showAnchor' => false,
			'identifiantFormulaire' => 'linkedHeadingElements',
			'clickerCaption' => 'Ajouter un lien vers un élément du CMS',
			'linkOnHeading' => true,
			'img' => _resource ('img/tools/add.png')
		));
		?>

		<input type="hidden" id="linkedElements" name="linkedElements" value="" />
	</div>
</div>

<script type="text/javascript">
function addLinkedElement (pId, pCaption, pUrl, pIcon) {
	var addedElements = $ ('linkedElements').value.split (',');
	for (var i = 0; i < addedElements.length; i++) {
		if (addedElements[i] == pId) {
			alert ('L\'élément sélectionné est déja lié.');
			return false;
		}
	}

	var tableElements = $ ('tableElements');
	var line = document.createElement ('tr');

	// icone
	var cell1 = document.createElement ('td');
	if (pIcon != undefined) {
		cell1.innerHTML = '<img src="' + pIcon + '" />';
	} else {
		cell1.innerHTML = '&nbsp;';
	}

	// caption et lien
	var cell2 = document.createElement ('td');
	if (pUrl != undefined) {
		cell2.innerHTML = '<a href="' + pUrl + '" target="_blank">' + pCaption + '</a>';
	} else {
		cell2.innerHTML = pCaption;
	}

	// supprimer
	var html = '<img src="<?php echo _resource ('img/tools/delete.png') ?>" onclick="deleteLinkedElement (' + pId + ', this.parentNode.parentNode)" style="cursor:pointer" alt="Supprimer le lien" title="Supprimer le lien" />';
	var cell3 = document.createElement ('td');
	cell3.className = 'action';
	cell3.innerHTML = html;

	line.appendChild (cell1);
	line.appendChild (cell2);
	line.appendChild (cell3);
	tableElements.appendChild (line);

	$ ('linkedElements').value = $ ('linkedElements').value + pId + ',';
}

function deleteLinkedElement (pId, pLine) {
	$ ('tableElements').deleteRow (pLine.rowIndex);
	var addedElements = $ ('linkedElements').value.split (',');
	var newAddedElements = new Array ();
	for (var i = 0; i < addedElements.length; i++) {
		if (addedElements[i] != pId) {
			newAddedElements[newAddedElements.length] = addedElements[i];
		}
	}
	$ ('linkedElements').value = newAddedElements.join (',');
}

<?php foreach ($linkedHeadingElements as $element) { ?>
	addLinkedElement (
		<?php echo $element->public_id_hei ?>,
		'<?php echo str_replace ("'", "\'", $element->caption_hei) ?>',
		'<?php echo _url ('heading|element|', array ('heading' => $element->parent_heading_public_id_hei, 'selected' => array ($element->id_helt . '|' . $element->type_hei))) ?>',
		'<?php echo _resource ($elementTypes[$element->type_hei]['icon']) ?>'
	);
<?php } ?>
</script>

<?php
$JS = <<<JS
	$ ('addLinkedElement').addEvent ('change', function (pEvent) {
		if ($ ('addLinkedElement').value != '') {
			var addValue = $ ('addLinkedElement').value;
			var addCaption = $ ('libelleElementlinkedHeadingElements').innerHTML;
			$ ('deleteElementlinkedHeadingElements').fireEvent ('click');
			addLinkedElement (addValue, addCaption);
		}
	});
JS;
CopixHTMLHeader::addJSDOMReadyCode ($JS);
?>