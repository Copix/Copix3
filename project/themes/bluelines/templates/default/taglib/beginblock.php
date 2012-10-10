<?php
$idElement = uniqid ('beginblock');
_eTag ('showdiv', array ('id' => $idElement, 'clicker' => 'td_' . $idElement, 'alternateelement' => 'alternate' . $idElement));
$showed = ($id != null) ? CopixUserPreferences::get ('default|beginblock_' . $id . '_showed', 'block') : 'block';
$js = <<<JS
$ ('td_$idElement').addEvent ('click', function (pEl) {
	var style = $ ('$idElement').getStyle ('display');
JS;
if ($id != null) {
	$js .= 'Copix.savePreference (\'default|beginblock_' . $id . '_showed\', style);';
}
$js .= <<<JS
	if (style == 'none') {
		$ ('table_$idElement').addClass ('TagBlockTitleHidden');
	} else {
		$ ('table_$idElement').removeClass ('TagBlockTitleHidden');
	}
});
JS;
CopixHTMLHeader::addJSDOMReadyCode ($js);

?>
<table class="TagBlockTitle <?php if ($showed == 'none') echo 'TagBlockTitleHidden' ?> <?php if ($isFirst) echo 'TagBlockTitleFirst' ?>" id="table_<?php echo $idElement ?>">
	<tr>
		<td class="Left"></td>
		<td class="Center" id="td_<?php echo $idElement ?>">
			<?php if ($icon != null) { ?>
				<img src="<?php echo $icon ?>" alt="<?php echo str_replace ('"', "''", $title) ?>" title="<?php echo str_replace ('"', "''", $title) ?>" style="vertical-align: middle" />
			<?php } ?>
			<?php echo $title ?>
		</td>
		<td class="Right"></td>
	</tr>
</table>
<div id="alternate<?php echo $idElement ?>" <?php if ($showed != 'none') { ?>style="display: none"<?php } ?> class="TagBlockAlternateElement">(Cliquez sur le titre pour afficher le contenu)</div>
<div id="<?php echo $idElement ?>" <?php if ($showed == 'none') { ?>style="display: none"<?php } ?>>