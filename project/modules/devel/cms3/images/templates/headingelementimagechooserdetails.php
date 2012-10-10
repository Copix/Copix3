<?php echo _tag('mootools', array ('plugins'=>"smoothbox")); ?>
<?php CopixHTMLHeader::addJSDOMReadyCode("TB_init();"); ?>
<div class="HeadingElementChooserDetail">
	<table width="100%" cellspacing="0">
		<tr>
			<th></th>
			<th>Visu.</th>
			<th>Nom de l'image</th>
			<th>Dimensions</th>
			<th>Taille</th>
			<th>Modifié</th>
		</tr>
	<?php
		foreach ($ppo->children as $children){
			$element = _ioClass('images|imageservices')->getByPublicId($children->public_id_hei);
			echo "<tr>";
			echo "<td><input type='checkbox' ";
			if (sizeof($ppo->children) == 1){
				echo "checked='checked' class='elementchooserfileselectedstate' ";
			} else {
				echo "class='elementchooserfilenoselectedstate' ";
			}		
			echo " name='' libelle='".$element->caption_hei."' pih='".$element->public_id_hei."' /></td>";
			echo "<td>";
			$extension = pathinfo($element->file_image, PATHINFO_EXTENSION);
			echo "<a href='"._url('images|imagefront|GetImage', array('id_image'=>$element->id_helt, 'extension'=>'.'.$extension))."' title=".$element->caption_hei." class='smoothbox' ";
			if (sizeof($ppo->children) > 1){
				echo "rel='gallery-imagechooser'";
			}
			echo "><img  title='".$element->caption_hei."' src='"._url('images|imagefront|GetImage', array('id_image'=>$element->id_helt, 'width'=>18, 'height'=>18, 'keepProportions'=>true, 'resizeIfNecessary'=>true))."' alt='".$element->caption_hei."' /></td>";
			echo "</a>";
			echo "<td>".$element->caption_hei."</td>";
			
			$imagesize = getimagesize(COPIX_VAR_PATH.ImageServices::IMAGE_PATH.$element->file_image);
			echo "<td>".$imagesize[0]." x ".$imagesize[1]."</td>";
			echo "<td>".($element->size_image ? _filter ('bytesToText')->get ($element->size_image) : '-')."</td>";
			echo "<td>".CopixDateTime::yyyymmddhhiissToFormat($element->date_update_hei, 'Y-m-d')."</td>";
			echo "</tr>";
		}
	?>
	</table>
</div>