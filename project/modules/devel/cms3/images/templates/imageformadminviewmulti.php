<?php
foreach ($listeImage as $image){
$imageSrcLite = _url('images|imagefront|GetImage', array('id_image'=>$image->id_helt, 'width'=>100, 'height'=>100, 'keepProportions'=>0, 'crop'=>1));
?>
<a href="javascript:void(0);" id="imgClicker<?php echo $identifiantFormulaire;?>" onClick="$('clicker<?php echo $identifiantFormulaire; ?>').fireEvent('click');">
	<img alt="<?php echo $image->caption_hei; ?>" src="<?php echo $imageSrcLite; ?>" title="<?php echo $image->caption_hei; ?>" />
</a>
<?php } ?>