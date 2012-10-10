<?php
CopixHTMLHeader::addCSSLink (_resource ('heading|css/headingadmin.css'));

_eTag('mootools', array('plugins'=>"resize"));

CopixHTMLHeader::addJSDOMReadyCode("
new Resizing('description_hei',{'min':100,'max':400});
");

_eTag ('error', array ('message' => $ppo->errors));

$imgSrc = _resource ("img/tools/help.png");

if (!isset ($ppo->editedElement->public_id_hei)) {
	?>
	Vous utilisez actuellement la méthode de mise en ligne "classique", qui ne permet l'envoi que d'un seul média à la fois.
	Essayez plutôt <a href="<?php echo _url ('medias|admin|edit', array ('editId' => _request ('editId'))); ?>">l'outil Flash</a>.
	<?php
	//echo CopixZone::process ('uploader|ChooseFlashForm', array ('url' => _url ('medias|admin|edit', array ('editId' => _request ('editId')))));
}

$imgSrc = _resource ('img/tools/help.png');

_eTag ('beginblock', array ('title' => 'Informations', 'isFirst' => true));
?>
<form action="<?php echo CopixUrl::get ("admin|valid", array("editId" => $ppo->editId)); ?>" enctype="multipart/form-data" method="POST" id="formMedia">
<input type="hidden" name="publish" id="publish" value="0" />
<input type="hidden" name="published_date" id="published_date" />
<input type="hidden" name="end_published_date" id="end_published_date" />
<table class="CopixVerticalTable">
	<tr>
		<th>Nom du media</th>
		<th class="help"><?php _eTag('popupinformation', array('width' => '300', 'alt' => "help", 'img' => $imgSrc), "Le nom du média sera utilisé lors des demandes de téléchargement."); ?></th>
		<td colspan="2"><?php _eTag ('inputtext', array ('name' => 'caption_hei', 'value' => $ppo->editedElement->caption_hei, 'style' => 'width: 99%')) ?></td>
	</tr>
	<tr>
		<th>Description</th>
		<th>
			<?php
			_eTag ('popupinformation', array('width' => '300', 'alt' => "help", 'img' => $imgSrc), "Cette description sera utilisée par les moteurs de recherche lors du référencement, ainsi que par le
	       	moteur de recherche interne de CopixCMS, lorsqu'il présentera les résultats de sa recherche.");
			?>
		</th>
		<td><?php _eTag ('textarea', array ('name' => 'description_hei', 'value' => $ppo->editedElement->description_hei, 'class' => 'cmsElementDescription')) ?></td>
        <td></td>
	</tr>
	<tr>
		<th colspan="2">Fichier</th>
		<td>	
			<input style="border:1px solid #4B4D46;" id="file_media" type="file" name="file_media" />
		</td>
        <td>
			<?php if ($ppo->editedElement->media_type){ ?>
			<img id="imageViewer"  src="<?php echo _resource ('medias|img/' . $ppo->editedElement->media_type . '.png'); ?>" title="<?php echo $ppo->editedElement->caption_hei; ?>" />
			<?php echo $ppo->editedElement->media_name;
			}?>
		</td>
	</tr>
    <?php if ($ppo->type == 'flash') { ?>
		<tr>
			 <th class="last">Image de substitution</th>
			 <th class="last"><?php _eTag('popupinformation', array('width' => '300', 'alt' => "help", 'img' => $imgSrc), "Cette image est affichée à la place du Flash lorsqu'un utilisateur n'a pas le player Flash installé sur sa machine."); ?></th>
			<td>
				<input style="border:1px solid #4B4D46;" id="image_media" type="file" name="image_media" />
			</td>
			<td>
				<?php
					if($ppo->editedElement->image_media) {
				?>
				<img style="max-width:64px;" alt="Image de substitution" src="<?php echo _url('medias|mediafront|getimage', array('id_media' => $ppo->editedElement->id_media, 'image_media' => $ppo->editedElement->image_media));?>" title="<?php echo $ppo->editedElement->caption_hei; ?>" />
				<?php echo $ppo->editedElement->image_media; ?>
				<?php
				}
				?>
			</td>
		 </tr>
    <?php } ?>
</table>	
</form>

<?php _eTag ('endblock') ?>
<?php echo CopixZone::process ('heading|headingelement/HeadingElementButtons', array ('showBack'=>!$ppo->popup, 'form' => 'formMedia', 'actions' => array ('savedraft', 'savepublish', 'saveplanned'), 'element'=>$ppo->editedElement)) ?>