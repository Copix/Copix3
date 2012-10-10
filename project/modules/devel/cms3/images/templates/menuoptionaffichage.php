<?php 
	_eTag('mootools', array('plugins'=>'copixformobserver'));
	$tabs = array(
		$identifiantFormulaire.'optionsgeneralesimage'=>"Options générales",
		$identifiantFormulaire.'optionsminiatureimage'=>"Miniature",
		$identifiantFormulaire.'optionsapparenceimage'=>"Apparence"
	);
	//$tabs = array("Options générales", "Miniature", "Apparence");
	_eTag ('tabgroup', array ('tabs' => $tabs, 'default' => $identifiantFormulaire.'optionsgeneralesimage'));
?>
<form id="formOptionImage<?php echo $identifiantFormulaire; ?>">
<input type="hidden" name="editId" value="<?php echo $editId; ?>">
<input type="hidden" name="portletId" value="<?php echo $portletId; ?>">
<div id="<?php echo $identifiantFormulaire; ?>optionsgeneralesimage">
	<table class="CopixVerticalTable">
		<tr>
			<th><label for='title_image_<?php echo $identifiantFormulaire; ?>'>Titre (title)</label></th>
			<td>
				<select name="title_image">
					<option <?php echo $options->getParam ('title_image') == 'none' ? "selected='selected'" : ''; ?> value="none">--Aucun--</option>
					<option <?php echo $options->getParam ('title_image') == 'caption' ? "selected='selected'" : ''; ?> value="caption">Nom de l'image</option>
					<option <?php echo $options->getParam ('title_image') == 'description' ? "selected='selected'" : ''; ?> value="description">Description</option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for='alt_image_<?php echo $identifiantFormulaire; ?>'>Texte alternatif</label></th>
			<td>
				<select name="alt_image">
					<option <?php echo $options->getParam ('alt_image') == 'none' ? "selected='selected'" : ''; ?> value="none">--Aucun--</option>
					<option <?php echo $options->getParam ('alt_image') == 'caption' ? "selected='selected'" : ''; ?> value="caption">Nom de l'image</option>
					<option <?php echo $options->getParam ('alt_image') == 'description' ? "selected='selected'" : ''; ?> value="description">Description</option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for='legend_image_<?php echo $identifiantFormulaire; ?>'>Légende</label></th>
			<td>
				<input type="text" name="legend_image" id="legend_image_<?php echo $identifiantFormulaire; ?>" value="<?php echo $options->getParam ('legend_image'); ?>"/>
			</td>
		</tr>
		<tr>
			<th class="last">Lien : </th>
			<td><?php echo CopixZone::process ('heading|headingelement/headingelementchooser', array(
				'identifiantFormulaire'=>'link'.$identifiantFormulaire, 
				'selectedIndex'=>$options->getParam ('link'), 
				'id'=>'link'.$identifiantFormulaire, 
				'inputElement'=>'link')); ?></td>
		</tr>
	</table>
</div>
            
<div id="<?php echo $identifiantFormulaire; ?>optionsminiatureimage">
	<table class="CopixVerticalTable">
		<tr>
			<th><label>Afficher une miniature</label></th>
			<td>
				<input type="radio" name="thumb_enabled" value="1" id="thumb_enabled_yes_<?php echo $identifiantFormulaire; ?>" <?php echo (($options->getParam ('thumb_enabled', false)) ? "checked='checked'" : ""); ?>/><label for="thumb_enabled_yes">Oui</label>
				<input type="radio" name="thumb_enabled" value="0" id="thumb_enabled_no_<?php echo $identifiantFormulaire; ?>" <?php echo (($options->getParam ('thumb_enabled', false) == false) ? "checked='checked'" : ""); ?> /><label for="thumb_enabled_no">Non</label>
			</td>
		</tr>
		<tr>
			<th><label>Dimensions</label></th>
			<td>
				<input type="text" name="thumb_width" id="thumb_width_<?php echo $identifiantFormulaire; ?>" style="width: 30px" value="<?php echo $options->getParam ('thumb_width'); ?>" />
				x <input type="text" name="thumb_height" id="thumb_height_<?php echo $identifiantFormulaire; ?>" style="width: 30px" value="<?php echo $options->getParam ('thumb_height'); ?>" /> px
			</td>
		</tr>
		<tr>
			<th><label>Conserver les proportions</label></th>
			<td>
				<input type="radio" name="thumb_keep_proportions" value="1" id="thumb_keep_proportions_yes<?php echo $identifiantFormulaire; ?>" <?php echo (($options->getParam ('thumb_keep_proportions', true)) ? "checked='checked'" : ""); ?> /><label for="thumb_keep_proportions_yes">Oui</label>
				<input type="radio" name="thumb_keep_proportions" value="0" id="thumb_keep_proportions_no<?php echo $identifiantFormulaire; ?>"  <?php echo (($options->getParam ('thumb_keep_proportions', true) == false) ? "checked='checked'" : ""); ?> /><label for="thumb_keep_proportions_no">Non</label>
			</td>
		</tr>
		<tr>
			<th><label for='thumb_show_image'>Affichage taille réelle</label></th>
			<td>
				<select name="thumb_show_image" id="thumb_show_image<?php echo $identifiantFormulaire; ?>" onchange="javascript: showGalery ('<?php echo $identifiantFormulaire; ?>');">
					<option <?php echo $options->getParam ('thumb_show_image') == 'none' ? "selected='selected'" : ''; ?> value="none">-- Aucun --</option>
					<option <?php echo $options->getParam ('thumb_show_image') == 'smoothbox' ? "selected='selected'" : ''; ?> value="smoothbox">Galerie d'images (SmoothBox)</option>
					<option <?php echo $options->getParam ('thumb_show_image') == '_blank' ? "selected='selected'" : ''; ?> value="_blank">Nouvelle fenêtre</option>			
				</select>
			</td>
		</tr>
		<tr id="trgalery<?php echo $identifiantFormulaire; ?>" <?php echo $options->getParam ('thumb_show_image') == 'smoothbox' ? '' : 'style="display:none;"'; ?>>
			<th class="last"><label for='thumb_show_image'>Galerie d'images</label></th>
			<td>
				<input type="text" style="width: 99%;" name="thumb_galery_id" value="<?php echo $options->getParam ('thumb_galery_id'); ?>">
			</td>
		</tr>
	</table>
</div>

<div id="<?php echo $identifiantFormulaire; ?>optionsapparenceimage">
	<table class="CopixVerticalTable">
		<tr>
			<th><label for='espace_vertical_<?php echo $identifiantFormulaire; ?>'>Espacement vertical</label></th>
			<td><input id='espace_vertical_<?php echo $identifiantFormulaire; ?>' type="text" name="vspace" style="width: 30px" /></td>
		</tr>

		<tr>
			<th><label for='espace_horizontal_<?php echo $identifiantFormulaire; ?>'>Espacement horizontal</label></th>
			<td><input id='espace_horizontal_<?php echo $identifiantFormulaire; ?>' type="text" name="hspace" style="width: 30px" /></td>
		</tr>
		<tr>
			<th><label for='image_align".$identifiantFormulaire."'>Alignement : </label></th>
			<td><?php echo _tag("select", array('name'=>'align_image', 'id'=>"align_image_$identifiantFormulaire", 'values'=>array('left'=>'gauche', 'center'=>'centre', 'right'=>'droite'), 'selected'=>$options->getParam ('align_image', 'center'), 'emptyShow'=>false)); ?></td>
		</tr>
		<tr>
			<th><label for='style_image_<?php echo $identifiantFormulaire; ?>'>Style : </label></th>
			<td>
				<input type='text' value='<?php echo $options->getParam ('style_image', ''); ?>' name='style_image' id='style_image_<?php echo $identifiantFormulaire; ?>' />
			</td>
		</tr>
		<tr>
			<th class="last"><label for='classe_image_<?php echo $identifiantFormulaire; ?>'>Classe : </label></th>
			<td>
				<input type='text' value='<?php echo $options->getParam ('classe_image', ''); ?>' name='classe_image' id='classe_image_<?php echo $identifiantFormulaire; ?>' />
			</td>
		</tr>
	</table>
</div>
<div style="clear: both;text-align: right;">
	<input id="formOptionImageSubmit<?php echo $identifiantFormulaire; ?>" type="submit" value="Enregistrer" /> 
</div>
</form>
<?php 
CopixHTMLHeader::addJSDOMReadyCode("
	$('formOptionImageSubmit$identifiantFormulaire').addEvent('click', function(){
		if ($('legend_image_$identifiantFormulaire').value){
			$('legendImage$identifiantFormulaire').innerHTML = $('legend_image_$identifiantFormulaire').value;
		}
		updateImage('$identifiantFormulaire', '$portletId', '$editId');
		$('copixWindowImageOptionMenu$identifiantFormulaire').fireEvent('close');
		return false;
	});
");
?>