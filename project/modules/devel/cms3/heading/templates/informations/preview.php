<?php
if (is_array ($record)) {
	$titles = array ();
	foreach ($record as $element) {
		$titles[] = $element->caption_hei;
	}
	$title = implode (' - ', $titles);
} else {
	$title = $record->caption_hei;
}
echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => $title, 'icon' => _resource ('img/tools/information.png')));
?>

<div class="element">
	<div class="elementContent">
		<?php
		if (count ($preview) == 1) {
			echo $preview[0];
		} else {
			$isFirst = true;
			foreach ($preview as $index => $html) {
				if (!$isFirst) {
					echo '<br />';
				}
				$isFirst = false;
				?>
				<div class="titleHeadingMenu"><?php echo $record[$index]->caption_hei ?></div>
				<?php
				echo $html;
			}
		}
		?>
	</div>
</div>