<?php echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => 'Flux RSS', 'icon' => _resource ('cms_rss|img/icon_rss.png'))) ?>

<div class="element">
	<div class="elementContent">
		<input type="checkbox" value="1" name="herite_rss" id="herite_rss" <?php if (empty ($arElementFlux)) { ?>checked="checked"<?php } ?> />
		<label for="herite_rss">Hériter du parent</label>
		<br />
		<label for="input_rss">Liste des flux publiés à afficher dans la barre de navigation</label>
		<br />
		<?php _eTag ('multipleselect', array ('name' => 'rss' . $uniqId, 'values' => $arFlux, 'selected' => $arElementFlux, 'objectMap' => 'id_rss;caption_hei')) ?>
	</div>
</div>

<?php
$js = <<<JS
$ ('input_rss$uniqId').addEvent ('change', function () {
	$ ('herite_rss').checked = $ ('input_rss$uniqId').value == '';
});
$ ('herite_rss').addEvent ('click', function () {
	$ ('input_rss$uniqId').value = '';
	$ ('hidden_rss$uniqId').empty ();
});
JS;
CopixHTMLHeader::addJSDOMReadyCode ($js);