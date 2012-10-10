<?php 
	_eTag('mootools', array('plugins'=>'copixformobserver'));
	$tabs = array(
		$identifiantFormulaire.'optionsgeneralesmedia'=>"Options générales",
		$identifiantFormulaire.'contenualternatifmedia'=>"Contenu alternatif"
	);
	
	_eTag ('tabgroup', array ('tabs' => $tabs, 'default' => $identifiantFormulaire.'optionsgeneralesmedia'));
	echo '<br />';

?>
<form id="formOptionMedia<?php echo $identifiantFormulaire; ?>">
	<input type="hidden" name="editId" value="<?php echo $editId; ?>">
	<input type="hidden" name="portletId" value="<?php echo $portletId; ?>">
	<input type="hidden" name="mediaType" value="<?php echo $mediaType; ?>">
	<div id="<?php echo $identifiantFormulaire; ?>optionsgeneralesmedia">
		<table class="CopixVerticalTable">
			<tr>
				<th><label for="taille_media">Taille du m&eacute;dia : </label></th>
				<td>
					<label>x </label>
					<?php _etag ('inputtext', array ('name'=>'x', 'value'=>$options->getParam ('x', '300'), 'size'=>2)); ?>
					<label>y </label>
					<?php _etag ('inputtext', array ('name'=>'y', 'value'=>$options->getParam ('y', '200'), 'size'=>2)); ?>
					<label>&nbsp;px</label>
				</td>
			</tr>
			<?php 
			if($mediaType == 'flash') {
				echo "<tr>";
		        echo '<th>Variables (flashvar) : </th>';
		        echo "<td>" . _tag ('inputtext', array ('name'=>'variable', 'value' => $options->getParam ('variable'), 'size' => 11)) . "</td></tr>";
		        echo "<tr><th>Version du player : </th>";
		        echo "<td>" . _tag ('inputtext', array ('name'=>'version', 'value' => $options->getParam ('version'), 'size' => 2)) . "</td></tr>";
		        echo "<tr><th>Type d'affichage : </th>";
		        echo "<td>" . _tag ('select', array ('name'=>'typeAffichage', 'values' => array('opaque' => 'opaque', 'transparent' => 'transparent', 'window' => 'window'), 'selected' => $options->getParam ('typeAffichage', 'window'), 'emptyValues' => 'window')) . "</td></tr>";
		    } else {
		    	if($mediaType != 'flash') {
		    		echo "<tr>";
			        echo '<th>Image presentation : </th>';
			        echo "<td>" . CopixZone::process ('heading|headingelement/headingelementchooser', array(
			        'arTypes'=>array('image'), 'mode'=>ZoneHeadingElementChooser::IMAGE_CHOOSER_MOD,
					'identifiantFormulaire'=>'imagepresentation'.$identifiantFormulaire, 
					'selectedIndex'=>$options->getParam ('imagePresentation'), 
					'inputElement'=>'imagePresentation')) . "</td></tr>";
		    	}
		    }
		    ?>
		</table>
	</div>
	<div id="<?php echo $identifiantFormulaire; ?>contenualternatifmedia">
	<table class="CopixVerticalTable">
		<tr>
			<th><label>Texte alternatif</label></th>
		</tr>
		<tr>
			<td>
				<?php echo CopixZone::process('cms_editor|cmswikieditor', array('name' => 'contenuAlternatif', 'height' => '150', 'value' => $options->getParam('contenuAlternatif'), 'preview' => false)); ?>
			</td>
		</tr>
	</table>
</div>
<div style="clear: both;text-align: right;">
	<input type="submit" onclick="updateMedia('<?php echo $identifiantFormulaire; ?>', '<?php echo $portletId; ?>', '<?php echo $editId; ?>');$('copixWindowMediaOptionMenu<?php echo $identifiantFormulaire; ?>').fireEvent('close');return false;" value="Enregistrer" /> 
</div>
</form>