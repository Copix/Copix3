<?php echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => 'Cible', 'icon' => _resource ('heading|img/togglers/target.png'))) ?>

<div class="element">
	<div class="elementContent">
		<table class="CopixVerticalTable">
			<tr>
				<th style="width: 80px">Ouverture</th>
				<td colspan="3">
					<?php
					_eTag ('select', array (
						'emptyShow' => false,
						'name' => 'target_hei',
						'values' => array ('Page courante', 'Nouvelle page', 'Popup', 'SmoothBox'),
						'selected' => $record->target_hei
					))
					?>
				</td>
			</tr>
			<tr class="alternate">
				<th>Taille</th>
				<td>
					<?php
					_eTag ('inputtext', array (
						'size' => 3,
						'name' => 'target_width',
						'disabled' => ($record->target_hei == 0 || $record->target_hei == 1),
						'id' => 'targetWidth',
						'value' => $target_width
					))
					?>
					x
					<?php
					_eTag ('inputtext', array (
						'size' => 3,
						'name' => 'target_height',
						'disabled' => ($record->target_hei == 0 || $record->target_hei == 1),
						'id' => 'targetHeight',
						'value' => $target_height
					))
					?>
					px
				</td>
			</tr>
		</table>
	</div>
</div>

<?php
$js = <<<JS
$$ ('select[name=target_hei]').each (function (el) {
	el.addEvent ('change', function () {
		$ ('targetWidth').disabled = ($ ('target_hei').value == 0 || $ ('target_hei').value == 1);
		$ ('targetHeight').disabled = ($ ('target_hei').value == 0 || $ ('target_hei').value == 1);
	});
});
JS;
CopixHTMLHeader::addJSDOMReadyCode ($js);