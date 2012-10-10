<div class="HeadingElementChooserDetail">
	<table width="100%" cellspacing="0">
		<tr>
			<th></th>
			<th>Visu.</th>
			<th>Nom de l'image</th>
			<th>Taille</th>
			<th>Modifié</th>
		</tr>
	<?php
		foreach ($ppo->children as $children){
			$element = _ioClass('document|documentservices')->getByPublicId($children->public_id_hei);
			echo "<tr>";
			echo "<td><input type='checkbox' ";
			if (sizeof($ppo->children) == 1){
				echo "checked='checked' class='elementchooserfileselectedstate' ";
			} else {
				echo "class='elementchooserfilenoselectedstate' ";
			}
			echo " name='' libelle='".$element->caption_hei."' pih='".$element->public_id_hei."' /></td>";
			echo "<td>";
			echo "<a href='"._url('document|documentfront|ShowDocumentFile', array('public_id'=>$element->public_id_hei))."' title=".$element->caption_hei." >";
			$extension = pathinfo($element->file_document, PATHINFO_EXTENSION);
			echo '<img width="18px" class="docelementchooserfile" src="'._resource('heading|'.(array_key_exists($extension, $ppo->arDocIcons) ? $ppo->arDocIcons[$extension] : 'img/docicons/unknow.png')).'" />';
			echo "</a>";
			echo "<td>".$element->caption_hei."</td>";
			echo "<td>".($element->size_document ? _filter ('bytesToText')->get ($element->size_document) : '-')."</td>";
			echo "<td>".CopixDateTime::yyyymmddhhiissToFormat($element->date_update_hei, 'Y-m-d')."</td>";
			echo "</tr>";
		}
	?>
	</table>
</div>