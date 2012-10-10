<?php 
if($ppo->type_hem){
_eTag ('beginblock', array ('title' => 'Edition du menu')); ?>
		<table class="CopixVerticalTable">
			<tr>
				<th style="width: 30%">Définition du menu</th>
				<td>
					<select name="menu_select" id="menu_select" class="checkMenu">
						<option <?php if (!isset ($ppo->menu_informations)) { ?>selected="selected"<?php } ?> value="2">Hérite le menu "<?php echo $ppo->menu_caption?>" de <em><?php echo $ppo->inherited_menu->caption_hei ?></em></option>
						<option <?php if (isset ($ppo->menu_informations) && $ppo->menu_informations->is_empty_hem == 0) { ?>selected="selected"<?php } ?> value="1">Définir le menu "<?php echo $ppo->menu_caption?>" pour cet élément</option>
						<option <?php if (isset ($ppo->menu_informations) && $ppo->menu_informations->is_empty_hem == 1) { ?>selected="selected"<?php } ?> value="0">Pas de menu pour cet élément</option>
					</select>
				</td>
			</tr>
		</table>
		<div id="divSlide">
				<?php
				$selectedIndex = (isset ($ppo->menu_informations)) ? $ppo->menu_informations->public_id_hem : null;
				$js = <<<JS
$ ('menu_public_id_hem').addEvent ('change', function () {
	$ ('level').disabled = $ ('menu_public_id_hem').get ('type_hei') != 'heading';
	$ ('depth').disabled = $ ('menu_public_id_hem').get ('type_hei') != 'heading';
	$ ('template').disabled = $ ('menu_public_id_hem').get ('type_hei') != 'heading';
	$ ('class').disabled = $ ('menu_public_id_hem').get ('type_hei') != 'heading';
});
JS;
				?>
				<table class="CopixVerticalTable">
					<tr <?php _eTag ('trclass', array ('id' => 'menus')) ?>>
						<th style="width: 30%">Racine du menu</th>
						<td colspan="7">
							<?php
							_eTag ('copixzone', array ('process' => 'heading|headingelement/headingelementchooser',
								'linkOnHeading' => true,
								'id' => 'zoneElementChooser',
								'selectedIndex' => $selectedIndex,
								'arTypes'=>array('heading', 'portlet'),
								'inputElement' => 'menu_public_id_hem',
								'identifiantFormulaire' => $ppo->type_hem,
								'onComplete' => $js
							));
							?>
						</td>
					</tr>
				</table>
				<br />
				<fieldset class="invisible">
					<legend id="optionsLegend">
						<img src="<?php echo _resource ('heading|img/browser_folded.png') ?>" alt="" />
						Options avancées
					</legend>
					<div id="optionsContent" class="cmsHide">
						<table class="CopixVerticalTable">
							<tr <?php _eTag ('trclass', array ('id' => 'menus')) ?>>
								<th>Niveau</th>
								<td>
									<?php
									_eTag ('inputText', array (
										'class' => 'text inputMenu',
										'style' => 'width: 20px',
										'name' => 'menu_level',
										'id' => 'level',
										'value' => (isset ($ppo->menu_informations) && $ppo->menu_informations->level_hem) ? $ppo->menu_informations->level_hem : 0,
										'disabled' => (isset ($ppo->menu_informations) && $ppo->menu_informations->portlet_hem) ? 'disabled' : null
									))
									?>
								</td>
								<th>Profondeur</th>
								<td>
									<?php
									_eTag ('inputText', array (
										'class' => 'text inputMenu',
										'style' => 'width: 20px',
										'name' => 'menu_depth',
										'id' => 'depth',
										'value' => (isset ($ppo->menu_informations) && $ppo->menu_informations->depth_hem) ? $ppo->menu_informations->depth_hem : 2,
										'disabled' => (isset ($ppo->menu_informations) && $ppo->menu_informations->portlet_hem) ? 'disabled' : null
									))
									?>
								</td>
								<th>Template</th>
								<td>
									<?php
									_eTag ('inputText', array (
										'class' => 'text longInputMenu',
										'name' => 'template',
										'id' => 'template',
										'value' => (isset ($ppo->menu_informations)) ? $ppo->menu_informations->template_hem : ''
									))
									?>
								</td>
								<th class="last">Classes</th>
								<td>
									<?php
									_eTag ('inputText', array (
										'class' => 'text longInputMenu',
										'name' => 'class',
										'id' => 'class',
										'value' => (isset ($ppo->menu_informations)) ? $ppo->menu_informations->class_hem : null
									))
									?>
								</td>
								<th class="last">Modules</th>
								<th style="width: 20px" class="last"><?php _eTag ('popupinformation', array ('img' => _resource ('img/tools/help.png')), 'Modules pour lesquels seront affiché le menu, séparés par ; .'); ?></th>
								<td>
									<?php _eTag ('inputtext', array ('id' => 'class' . $ppo->element->id_helt, 'name' => 'modules_hem', 'class' => 'longInputMenu', 'value' => $ppo->menu_informations->modules_hem)) ?>
								</td>
							</tr>
						</table>
					</div>
				</fieldset>
			</div>
	<?php _eTag ('endblock'); 
	_eTag ('beginblock', array ('title' => 'Aperçu du menu', 'id'=>'blocapercu')); ?>
	<div id="zoneapercu"></div>
	<?php _eTag ('endblock'); ?>
	<input type="submit" value="Enregistrer le menu" name="editmenusubmit" />
<?php

$imgFolded = _resource ('heading|img/browser_folded.png');
$imgUnfolded = _resource ('heading|img/browser_unfolded.png');

CopixHTMLHeader::addJSDOMReadyCode ("
// Masquer / afficher les options de recherche
$('optionsLegend').setStyle('cursor', 'pointer');
$('optionsLegend').addEvent('click', function(){
	var img = $('optionsLegend').getElements('img')[0];
	$('optionsContent').toggleClass('cmsHide');
	img.src = ( $('optionsContent').hasClass('cmsHide') )? '$imgFolded' : '$imgUnfolded'; 
});

$('menu_select').addEvent ('change', function () {
	$ ('divSlide').setStyle ('display', $('menu_select').value == 1 ? '' : 'none');
	$('blocapercu').setStyle('display', $('menu_select').value != 0 ? '' : 'none');
});
$ ('divSlide').setStyle ('display', $('menu_select').value == 1 ? '' : 'none');
$('blocapercu').setStyle('display', $('menu_select').value == 1 ? '' : 'none');

$('menu_public_id_hem').addEvent('change', function(){
	new Request.HTML({
		url : '"._url("heading|menueditor|getapercumenu")."',
		update : $('zoneapercu'),
		evalScripts : true
	}).post({'public_id_hei':$('menu_public_id_hem').value});
});");

if($selectedIndex != null){
	CopixHTMLHeader::addJSDOMReadyCode("
	$('menu_public_id_hem').fireEvent('change');
	");
}
}