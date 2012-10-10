 <?php echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => 'Développeurs', 'icon' => _resource ('heading|img/togglers/developper.png'))) ?>

<div class="element">
	<div class="elementContent">
		<?php
		$isFirst = true;
		foreach ($sections as $name => $values) {
			if (!$isFirst) {
				echo '<br />';
			}
			$isFirst = false;
			?>
			<div class="titleHeadingMenu"><?php echo $name ?></div>
			<table class="CopixVerticalTable">
			<?php foreach ($values as $value) { ?>
				<tr <?php _eTag ('trclass', array ('id' => 'developper')) ?>>
					<th style="width: 120px"><?php echo $value ?></th>
					<td><?php echo $record->$value ?></td>
				</tr>
			<?php } ?>
			</table>
		<?php } ?>
	</div>
</div>