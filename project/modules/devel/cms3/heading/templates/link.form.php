<?php
CopixHTMLHeader::addCSSLink (_resource ('heading|css/headingadmin.css'));

_eTag ('mootools', array ('plugins' => 'resize'));
CopixHTMLHeader::addJSDOMReadyCode ("new Resizing('description_hei',{'min':50,'max':400});");
$imgSrc = _resource ('img/tools/help.png');
?>

<?php _eTag ('beginblock', array ('title' => 'Informations', 'isFirst' => true)) ?>
<form method="post" action="<?php echo _url ('adminlink|valid', array('editId' => $ppo->editId)); ?>" id="linkForm">
<input type="hidden" name="publish" id="publish" value="0" />
<input type="hidden" name="published_date" id="published_date" />
<input type="hidden" name="end_published_date" id="end_published_date" />
<table class="CopixVerticalTable">
	<tr>
		<th style="width: 120px"><label for="caption_hei">Nom</label></th>
		<th style="width: 20px"><?php _eTag('popupinformation', array('alt' => "Aide", 'img' => $imgSrc), "Le nom du lien sera utilisé lors des demandes de téléchargement."); ?></th>
		<td colspan="2"><?php _eTag ('inputtext', array ('name' => 'caption_hei', 'value' => $ppo->editedElement->caption_hei, 'style' => 'width: 99%')) ?></td>
	</tr>
	<tr class="alternate">
		<th class="last"><label for="description_hei">Description</label></th>
		<th class="last"></th>
		<td>
			<div id="linkDescription" style="display: <?php echo (CopixUserPreferences::get ('heading|showLinkDescription', true)) ? 'block' : 'none' ?>">
				<textarea class="cmsElementDescription" style="height: 50px" id="description_hei" name="description_hei"><?php echo $ppo->editedElement->description_hei; ?></textarea>
			</div>
		</td>
		<td style="width: 20px"><?php _eTag ('showdiv', array ('id' => 'linkDescription', 'userpreference' => 'heading|showLinkDescription', 'alternate' => '(Description cachée)')) ?></td>
	</tr>
</table>
<?php _eTag ('endblock') ?>

<?php _eTag ('beginblock', array ('title' => 'Lien')) ?>
<table class="CopixVerticalTable">
	<tr>
		<th style="width: 120px">Type</th>
		<th style="width: 20px"></th>
		<td>
			<?php
			$selected = 0; 
			if (!is_null ($ppo->editedElement->href_link)) {
				$selected = 0;
			} else if (!is_null ($ppo->editedElement->linked_public_id_hei) || $ppo->editedElement->id_hei == null) {
				$selected = 1;
			} else if (!is_null ($ppo->editedElement->module_link)) {
				$selected = 2;
			}
			_eTag('radiobutton', array('name'=>'type_link', 'values'=>array(0=>'Adresse extérieure', 1=>'Element du CMS', 2=>'Module COPIX'), 'selected'=>$selected, 'separator' => '&nbsp;&nbsp;')); ?>
		</td>
	</tr>

	<tr class="alternate typelink type_0" style="display:<?php echo (!is_null ($ppo->editedElement->href_link)) ? '' : 'none'; ?>">
		<th><label for="url">URL</label></th>
		<th><?php _eTag ('popupinformation', array ('img' => $imgSrc, 'width' => 400), 'Vous pouvez indiquer une adresse relative à index.php, par exemple /admin/logs/default, ou une adresse complète.<br />{$copixurl:domain} sera remplacé par le domaine courant.'); ?></th>
		<td colspan="2"><input type="text" style="width: 99%" id="url" class="inputText" name="href_link" value="<?php echo ($ppo->editedElement->href_link != "") ? $ppo->editedElement->href_link : 'http://' ;?>" /></td>
	</tr>

	<tr class="alternate typelink type_1" style="display:<?php echo !is_null ($ppo->editedElement->linked_public_id_hei) || $ppo->editedElement->id_hei == null ? '' : 'none'; ?>">
		<th>Elément du CMS</th>
		<th></th>
		<td><?php echo CopixZone::process ('heading|headingelement/headingelementchooser', array('selectedIndex'=>(!is_null($ppo->editedElement->linked_public_id_hei)) ? $ppo->editedElement->linked_public_id_hei : '', 'inputElement'=>'link', 'linkOnHeading'=>true, 'showAnchor'=>true)); ?></td>
	</tr>
	<tr class="typelink type_1" style="display:<?php echo !is_null ($ppo->editedElement->linked_public_id_hei) || $ppo->editedElement->id_hei == null ? '' : 'none'; ?>">
		<th>Libellé à afficher</th>
		<th></th>
		<td>
			<?php
			$values = array (LinkServices::CAPTION_LINK => 'Nom du lien', LinkServices::CAPTION_ELEMENT => 'Nom de l\'élément pointé');
			_eTag ('radiobutton', array ('name' => 'caption_link', 'values' => $values, 'selected' => $ppo->editedElement->caption_link, 'separator' => '&nbsp;&nbsp;'))
			?>
		</td>
	</tr>
	<tr class="alternate typelink type_1" style="display:<?php echo !is_null ($ppo->editedElement->linked_public_id_hei) || $ppo->editedElement->id_hei == null ? '' : 'none'; ?>">
		<th class="last">Adresse</th>
		<th class="last"></th>
		<td>
			<?php
			$values = array (LinkServices::URL_LINK => 'Adresse du lien', LinkServices::URL_ELEMENT => 'Adresse de l\'élément pointé');
			_eTag ('radiobutton', array ('name' => 'url_link', 'values' => $values, 'selected' => $ppo->editedElement->url_link, 'separator' => '&nbsp;&nbsp;'))
			?>
		</td>
	</tr>

	<tr class="alternate typelink type_2" style="display:<?php echo !is_null ($ppo->editedElement->module_link) ? '' : 'none'; ?>">
		<th><label for="module_link">Trigramme Copix</label></th>
		<th><?php _eTag ('popupinformation', array ('img' => $imgSrc, 'width' => 400), 'Vous pouvez indiquer l\'adresse d\'un module COPIX : module|group|action?param1=1&param2=2.'); ?></th>
		<td><?php _eTag ('inputtext', array ('name' => 'module_link', 'value' => $ppo->editedElement->module_link, 'style' => 'width: 99%')) ?></td>
	</tr>
	<tr id="rewrite" style="display:<?php echo $ppo->editedElement->linked_public_id_hei == null && $ppo->editedElement->id_hei != null ? '' : 'none'; ?>">
		<th class="last">Réécriture d'URL</th>
		<th class="last"><?php _eTag ('popupinformation', array ('img' => $imgSrc), 'Si vous activez la réécriture d\'URL, elle sera réécrite dans cet ordre :<br /><ul><li>Nom donné dans la partie URL de l\'admin du lien</li><li>Adresse générique : cms/TITRE/ID</li></ul>Sinon, l\'adresse pointée sera exactement celle indiquée.'); ?></th>
		<td>
			<input type="radio" name="not_rewritten_link" value="no" id="not_rewritten_link_no" <?php echo (!$ppo->editedElement->not_rewritten_link) ? 'checked="checked"' : '' ?> /><label for="not_rewritten_link_no">Oui</label>
			<input type="radio" name="not_rewritten_link" value="yes" id="not_rewritten_link_yes" <?php echo ($ppo->editedElement->not_rewritten_link) ? 'checked="checked"' : '' ?> /><label for="not_rewritten_link_yes">Non</label>
		</td>
	</tr>
</table>
<?php _eTag ('endblock') ?>
<?php echo CopixZone::process ('heading|headingelement/HeadingElementButtons', array ('form' => 'linkForm', 'actions' => array ('savedraft', 'savepublish', 'saveplanned'), 'element'=>$ppo->editedElement)) ?>
</form>

<?php
_eTag ('formfocus', array ('id' => 'caption_hei'));

$js = '';
for ($i = 0 ; $i < 3 ; $i++){
	$js .= "
	$ ('type_link_$i').addEvent ('change', function () {
		if ($ ('type_link_$i').checked) {
			$$ ('.typelink').each (function (el) {
				el.setStyle ('display', 'none');
			});
			$$ ('.type_$i').each (function (pElement) { pElement.setStyle ('display', ''); });
			$ ('rewrite').setStyle ('display', ($i == 1) ? 'none' : '');
		}
	})";
}
	
CopixHTMLHeader::addJSDOMReadyCode($js);
?>