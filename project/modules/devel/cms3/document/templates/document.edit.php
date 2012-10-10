<?php
CopixHTMLHeader::addCSSLink (_resource ('heading|css/headingadmin.css'));

_eTag ('error', array ('message' => $ppo->errors));

if (!isset ($ppo->editedElement->public_id_hei)) {
	?>
	Vous utilisez actuellement la méthode de mise en ligne "classique", qui ne permet l'envoi que d'un seul document à la fois.
	Essayez plutôt <a href="<?php echo _url ('document|admin|edit', array ('editId' => _request ('editId'))); ?>">l'outil Flash</a>.
	<?php
	//echo CopixZone::process ('uploader|ChooseFlashForm', array ('url' => _url ('document|admin|edit', array ('editId' => _request ('editId')))));
}
_eTag ('mootools', array ('plugins' => 'resize'));

CopixHTMLHeader::addJSDOMReadyCode("
new Resizing('description_hei',{'min':100,'max':400, 'userpreference': 'document|descriptionHeight'});
");

$imgSrc = _resource ('img/tools/help.png');

_eTag ('beginblock', array ('title' => 'Informations', 'isFirst' => true));
?>
<form action="<?php echo _url ('admin|valid', array ('editId' => $ppo->editId)); ?>" enctype="multipart/form-data" method="POST" id="formDocument">
	<input type="hidden" name="publish" id="publish" value="0" />
	<input type="hidden" name="published_date" id="published_date" />
	<input type="hidden" name="end_published_date" id="end_published_date" />
	<table class="CopixVerticalTable">
		<tr>
			<th style="width: 90px">Nom</th>
			<th class="help"><?php _eTag ('popupinformation', array('alt' => "help", 'img' => $imgSrc), "Le nom du document sera utilisé lors des demandes de téléchargement."); ?></th>
			<td colspan="2"><input type="text" name="caption_hei" value="<?php echo htmlentities($ppo->editedElement->caption_hei, ENT_COMPAT, 'UTF-8'); ?>" class="inputText" maxlength="255" style="width: 99%" /></td>
		</tr>
		<tr class="alternate">
			<th>Description</th>
			<th class="help">
				<?php _eTag ('popupinformation', array ('width' => '450', 'alt' => "help", 'img' => $imgSrc), "Cette description sera utilisée par les moteurs de recherche lors du référencement, ainsi que par le moteur de recherche interne de CopixCMS, lorsqu'il présentera les résultats de sa recherche."); ?>
			</th>
			<td>
				<div id="documentDescription" style="display: <?php echo (CopixUserPreferences::get ('document|showDescription', true)) ? 'block' : 'none' ?>">
					<textarea class="cmsElementDescription" id="description_hei" name="description_hei" class="formTextarea"><?php echo $ppo->editedElement->description_hei; ?></textarea>
				</div>
			</td>
			<td style="width: 20px"><?php _eTag ('showdiv', array ('id' => 'documentDescription', 'userpreference' => 'document|showDescription', 'alternate' => '(Description cachée)')) ?></td>
		</tr>
		<tr>
			<th class="last">Fichier</th>
			<th class="help last"></th>
			<td colspan="2">
				<?php
				if ($ppo->editedElement->file_document) {
					_eTag ('button', array ('img' => 'img/tools/show.png', 'caption' => 'Voir le document', 'url' => _url ('admin|download', array ('editId' => $ppo -> editId))));
					_eTag ('button', array ('img' => 'img/tools/update.png', 'caption' => 'Modifier', 'id' => 'documentEdit', 'type' => 'button'));
					CopixHTMLHeader::addJSDOMReadyCode ("$ ('documentEdit').addEvent ('click', function () { $ ('file_document').setStyle ('display', 'block'); });");
				}
				?>
				<input id="file_document" <?php echo ($ppo->editedElement->file_document)?'style="display:none;"':''; ?> type="file" name="file_document" />
			</td>
		</tr>
	</table>
</form>

<?php _eTag ('endblock') ?>
<br />
<?php echo CopixZone::process ('heading|headingelement/HeadingElementButtons', array ('showBack'=>!$ppo->chooseHeading, 'form' => 'formDocument', 'actions' => array ('savedraft', 'savepublish', 'saveplanned'), 'element'=>$ppo->editedElement)); ?>