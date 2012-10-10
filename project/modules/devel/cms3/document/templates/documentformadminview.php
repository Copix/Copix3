<?php
foreach ($listeDocs as $document){	
	$extension = pathinfo($document->file_document, PATHINFO_EXTENSION);
	$url = _resource('heading|'.(array_key_exists($extension, $arDocIcons) ? $arDocIcons[$extension] : 'img/docicons/unknow.png'));
?>
<a href="javascript:void(0);" id="docClicker<?php echo $identifiantFormulaire;?>" onClick="$('clicker<?php echo $identifiantFormulaire; ?>').fireEvent('click');">
	<img alt="<?php echo $document->caption_hei; ?>" src="<?php echo $url; ?>" title="<?php echo $document->caption_hei; ?>" />
</a>
<?php } ?>