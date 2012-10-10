<?php
CopixHTMLHeader::addCSSLink (_resource ('heading|css/headingadmin.css'));
_eTag('mootools', array('plugins'=>array("resize", "smoothbox")));
CopixHTMLHeader::addJSDOMReadyCode("
new Resizing('description_hei',{'min':100,'max':400, 'userpreference': 'images|descriptionHeight'});
$('file_image').addEvent('change', function(){
	$('imageloading').setStyle('display', '');
	$('preview').value = 1;
	$('formImage').submit();
});
");
$imgSrc = _resource ("img/tools/help.png");

if (!$ppo->editedElement->public_id_hei) {
	?>
	Vous utilisez actuellement la méthode de mise en ligne "classique", qui ne permet l'envoi que d'une seule image à la fois.
	Essayez plutôt <a href="<?php echo _url ('images|admin|edit', array ('editId' => _request ('editId'))); ?>">l'outil Flash</a>.
	<br />
	<br />
	<?php 
	//echo CopixZone::process ('uploader|ChooseFlashForm', array ('url' => _url ('images|admin|edit', array ('editId' => _request ('editId')))));
}

echo _tag ('error', array ('message' => $ppo->errors));

_eTag ('beginblock', array ('title' => 'Informations', 'isFirst' => true));
?>

<form action="<?php echo CopixUrl::get ("admin|valid", array("editId" => $ppo->editId)); ?>" enctype="multipart/form-data" method="POST" id="formImage">
<input type="hidden" name="publish" id="publish" value="0" />
<input type="hidden" name="preview" id="preview" value="0" />
<input type="hidden" name="classic" id="classic" value="1" />
<input type="hidden" name="published_date" id="published_date" />
<input type="hidden" name="end_published_date" id="end_published_date" />
<table class="CopixVerticalTable">
	<tr <?php _eTag ('trclass') ?>>
		<th style="width: 90px">Nom</th>
		<th style="width: 1px"><?php _eTag ('popupinformation', array ('width' => '300', 'img' => $imgSrc), 'Le nom de l\'image sera utilisée lors des demandes de téléchargement.'); ?></th>
		<td colspan="2"><input type="text" name="caption_hei" value="<?php echo htmlentities($ppo->editedElement->caption_hei, ENT_COMPAT, 'UTF-8'); ?>" class="inputText" maxlength="255" style="width: 99%" /></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th>Description</th>
		<th></th>
		<td>
			<div id="imageDescription" style="display: <?php echo (CopixUserPreferences::get ('images|showDescription', true)) ? 'block' : 'none' ?>">
				<textarea class="cmsElementDescription" id="description_hei" name="description_hei" style="width: 99%" style="height: <?php echo CopixUserPreferences::get ('images|descriptionHeight') ?>px"><?php echo $ppo->editedElement->description_hei; ?></textarea>
			</div>
		</td>
		<td style="width: 20px"><?php _eTag ('showdiv', array ('id' => 'imageDescription', 'userpreference' => 'images|showDescription', 'alternate' => '(Description cachée)')) ?></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th>Fichier</th>
		<th></th>
		<td colspan="2"><input style="border:1px solid #4B4D46;" id="file_image" type="file" name="file_image" /><img style="display: none;" id="imageloading" src="<?php echo _resource("img/tools/load.gif"); ?>" /></td>
	</tr>
<?php if ($ppo->editedElement->file_image){ ?>
	<tr <?php _eTag ('trclass') ?>>
		<th class="last">Image</th>
		<th class="last"></th>
		<td colspan="2">		
			<script>
			function resizeFrameHeight(height){
				$('frameImageEditor').setStyle('height', height); 
			}
			</script>
			<iframe id="frameImageEditor" style="border: none;" height="" width="100%" src="<?php echo _url("images|admin|edittempimage", array('editId'=>_request('editId')));?>"></iframe>
		</td>
	</tr>
	<?php
}
if ($ppo->chooseHeading){ ?>
	<tr <?php _eTag ('trclass') ?>>
		<th>Dossier d'enregistrement</th>
		<th></th>
		<td colspan="2">
			<?php echo CopixZone::process ('heading|headingelement/headingelementchooser', array('inputElement'=>'parent_heading_public_id_hei', 'linkOnHeading'=>true, 'arTypes'=>array('heading'), 'selectedIndex'=>$ppo->editedElement->parent_heading_public_id_hei));?>
		</td>
	</tr>
<?php }?>
</table>
</form>

<?php _eTag ('endblock') ?>
<?php echo CopixZone::process ('heading|headingelement/HeadingElementButtons', array ('showBack'=>!$ppo->popup, 'form' => 'formImage', 'actions' => array ('savedraft', 'savepublish', 'saveplanned'), 'element'=>$ppo->editedElement)) ?>