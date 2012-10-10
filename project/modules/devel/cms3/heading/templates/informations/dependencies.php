<?php echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => 'Utilisations', 'icon' => _resource ('heading|img/togglers/used.png'))) ?>

<div class="element">
	<div class="elementContent" id="usedContent" style="text-align: center">
		<?php
		_eTag ('button', array ('id' => 'usedSearch', 'type' => 'button', 'img' => 'heading|img/togglers/used.png', 'caption' => 'Rechercher les utilisations'));
		$url = _url ('heading|ajax|getDependencies');
		$imgSrc = _resource ('img/tools/load.gif');
		$js = <<<JS
$ ('usedSearch').addEvent ('click', function () {
	$ ('usedContent').set ('html', '<img src="$imgSrc" alt="Chargement en cours ..." title="Chargement en cours ..." /><br />Chargement en cours ...');
	new Request.HTML ({
		url: '$url',
		evalScripts: true,
		update: $ ('usedContent')
	}).get ({public_id: $record->public_id_hei});
});
JS;
		CopixHTMLHeader::addJSDOMReadyCode ($js);
		?>
	</div>
</div>