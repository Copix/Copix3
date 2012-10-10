<?php
$selectedTypes= explode (',', $ppo->editedElement->element_types_rss);
CopixHTMLHeader::addJSLink(_resource('portal|js/tools.js'));
CopixHTMLHeader::addCSSLink(_resource('portal|styles/style.css'));
_eTag('mootools', array('plugins'=>"resize"));

CopixHTMLHeader::addJSDOMReadyCode("
new Resizing('description_hei',{'min':25,'max':400, 'userpreference': 'rss|descriptionHeight'});
");

$imgSrc = _resource('img/tools/help.png');

echo _tag('error', array('message'=>$ppo->errors));
?>
<?php _eTag ('beginblock', array ('title' => 'Informations', 'isFirst' => true)) ?>
<form id="rssForm" action="<?php echo CopixUrl::get ("admin|valid", array("editId" => $ppo->editId)); ?>" enctype="multipart/form-data" method="POST">
<input type="hidden" name="publish" id="publish" value="0" />
<input type="hidden" name="published_date" id="published_date" />
<input type="hidden" name="end_published_date" id="end_published_date" />
	<table class="CopixVerticalTable">
		 <tr <?php _eTag ('trclass') ?>>
			<th colspan="2">Nom</th>
			<td colspan="2"><input type="text" name="caption_hei" class="inputText" value="<?php echo htmlentities($ppo->editedElement->caption_hei, ENT_COMPAT, 'UTF-8'); ?>" style="width: 99%" /></td>
		 </tr>
		 <tr <?php _eTag ('trclass') ?>>
			<th style="width: 160px">Description</th>
			<th class="help">
				<?php
				_eTag ('popupinformation', array('width' => '450', 'alt' => "Aide", 'img' => $imgSrc),
				"Cette description sera affichée directement dans le flux.");
				?>
			</th>
			<td>
				<textarea class="cmsElementDescription" id="description_hei" name="description_hei"
				style="height: <?php echo CopixUserPreferences::get ('rss|descriptionHeight') ?>px"><?php echo $ppo->editedElement->description_hei; ?></textarea>
			</td>
		 </tr>
		<tr <?php _eTag ('trclass') ?>>
			<th colspan="2" style="width: 145px">Rubrique des articles</th>
			<td><?php echo CopixZone::process ('heading|headingelement/headingelementchooser', array('selectedIndex'=>(!is_null($ppo->editedElement->heading_public_id_rss)) ? $ppo->editedElement->heading_public_id_rss : '', 'inputElement'=>'heading_public_id_rss', 'linkOnHeading'=>true)); ?></td>
		</tr>
		<tr <?php _eTag ('trclass') ?>>
			<th colspan="2">Tri</th>
			<td><?php echo _tag('select', array('name'=>'order_rss', 'values'=>array(RSSServices::ORDER_HEI_ORDER=>'Ordre de l\'administration', RSSServices::DATE_CREATE_ORDER=>'Date de création', RSSServices::DATE_UPDATE_ORDER=>'Date de modification'), 'emptyShow'=>false, 'selected'=>isset($ppo->editedElement->order_rss) ? $ppo->editedElement->order_rss : RSSServices::ORDER_HEI_ORDER)); ?></td>
		</tr>
		<tr <?php _eTag ('trclass') ?>>
			<th colspan="2">Type d'élément</th>
			<td><?php echo _tag('checkbox', array('name'=>'element_types', 'values'=>$ppo->availableTypeRss, 'selected' => $selectedTypes )); ?></td>
		</tr>
		<tr <?php _eTag ('trclass') ?>>
			<th colspan="2" class="last">Rechercher de façon récursive</th>
			<td><input type="checkbox" name="recursive_flag" value="1" <?php echo ($ppo->editedElement->recursive_flag == 1) ? 'checked="checked"' : '';?> /></td>
		</tr>
	</table>
</form>
<?php _eTag ('endblock') ?>
<?php echo CopixZone::process ('heading|headingelement/HeadingElementButtons', array ('form' => 'rssForm', 'actions' => array ('savedraft', 'savepublish', 'saveplanned'), 'element'=>$ppo->editedElement)) ?>