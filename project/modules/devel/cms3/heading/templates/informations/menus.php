<?php echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => 'Menus', 'icon' => _resource ('heading|img/togglers/menu.png'))) ?>

<div class="element" rel="menu">
	<div class="elementContent">
		<a href="<?php echo _url('heading|menueditor|', array('public_id'=>$record->public_id_hei)); ?>">Modifier les menus de l'élément avec l'outil d'édition de menus.</a>
		<br /><br />
		<table class="CopixVerticalTable">
			<tr <?php _eTag ('trclass', array ('id' => 'mainMenus')) ?>>
				<th style="width: 80px" colspan="2">Visibilité</th>
				<td>
					<?php
					$visibility_caption = ($visibility) ? 'Visible' : 'Invisible';

					if ($uniqueElement) {
						if ($record->public_id_hei == 0) {
							$visibility_values = array (1 => 'Visible', 0 => 'Invisible');
						} else if ($visibility_inherited_from) {
							$visibility_values = array (1 => 'Visible', 0 => 'Invisible', 2 => $visibility_caption . ' (Hérité de ' . $visibility_inherited_from . ')');
						} else {
							$visibility_values = array (1 => 'Visible', 0 => 'Invisible', 2 => 'Hériter de la visibilité parent');
						}
					} else {
						if ($multiple_visibility == -1) {
							$visibility_values = array (-1 => '******', '1' => 'Visible', 0 => 'Invisible', 2 => 'Hériter de la visibilité parent');
						} else {
							$visibility_values = array (1 => 'Visible', 0 => 'Invisible', 2 => $visibility_caption . '(Hérité de ' . $visibility_inherited_from . ')');
						}
					}
					_eTag ('select', array ('name' => 'show_in_menu_hei', 'emptyShow' => false, 'values' => $visibility_values, 'selected' => $record->show_in_menu_hei));
					?>
				</td>
			</tr>
			<tr <?php _eTag ('trclass', array ('id' => 'mainMenus')) ?>>
				<th colspan="2">Classes</th>
				<td>
					<?php
					_eTag ('inputtext', array (
						'id' => 'class' . $record->id_helt,
						'name' => 'class_name_hei',
						'class' => 'longInputMenu',
						'value' => $record->menu_html_class_name_hei
					))
					?>
				</td>
			</tr>
			<tr <?php _eTag ('trclass', array ('id' => 'mainMenus')) ?>>
				<th class="last">Modules</th>
				<th style="width: 20px" class="last"><?php _eTag ('popupinformation', array ('img' => _resource ('img/tools/help.png')), 'Modules pour lesquels seront affichés les menus, sépares par ; .'); ?></th>
				<td>
					<?php _eTag ('inputtext', array ('id' => 'class' . $record->id_helt, 'name' => 'modules_hem', 'class' => 'longInputMenu', 'value' => $modules_hem)) ?>
				</td>
			</tr>
		</table>
		
		<?php foreach ($listInformationsMenus as $menu) { 
			$type = $menu['name'];?>
			<br />
			<div class="titleHeadingMenu">
				<table style="width: 100%">
					<tr>
						<td><?php echo $menu['caption'] ?></td>
						<td style="text-align: right">
							<select name="menu_select[<?php echo $type; ?>]" id="check_<?php echo $type; ?>" typeMenu="<?php echo $type; ?>" class="checkMenu">
								<option <?php if (!isset ($liste_menus[$type])) { ?>selected="selected"<?php } ?> value="2">Hérité de <em><?php echo $inherited_menu[$type]->caption_hei ?></em></option>
								<option <?php if (isset ($liste_menus[$type]) && $liste_menus[$type]->is_empty_hem == 0) { ?>selected="selected"<?php } ?> value="1">Définir le menu</option>
								<option <?php if (isset ($liste_menus[$type]) && $liste_menus[$type]->is_empty_hem == 1) { ?>selected="selected"<?php } ?> value="0">Pas de menu</option>
							</select>
						</td>
					</tr>
				</table>
			</div>
			<div id="divSlide<?php echo $type ?>">
				<?php
				$selectedIndex = (isset ($liste_menus[$type])) ? $liste_menus[$type]->public_id_hem : null;
				$js = <<<JS
$ ('menu_public_id_hem[$type]').addEvent ('change', function () {
	$ ('level_$type').disabled = $ ('menu_public_id_hem[$type]').get ('type_hei') != 'heading';
	$ ('depth_$type').disabled = $ ('menu_public_id_hem[$type]').get ('type_hei') != 'heading';
	$ ('template_$type').disabled = $ ('menu_public_id_hem[$type]').get ('type_hei') != 'heading';
	$ ('class_$type').disabled = $ ('menu_public_id_hem[$type]').get ('type_hei') != 'heading';
});
JS;
				?>
				<table class="CopixVerticalTable">
					<tr <?php _eTag ('trclass', array ('id' => 'menus')) ?>>
						<th style="width: 100px">Elément de base</th>
						<td colspan="3">
							<?php
							_eTag ('copixzone', array ('process' => 'heading|headingelement/headingelementchooser',
								'linkOnHeading' => true,
								'id' => 'zoneElementChooser' . $type . '_' . $uniqId,
								'zoneParams_id' => 'menu_public_id_hem[' . $type . ']',
								'selectedIndex' => $selectedIndex,
								'inputElement' => 'menu_public_id_hem[' . $type . ']',
								'identifiantFormulaire' => $type . '_' . $uniqId,
								'onComplete' => $js
							));
							?>
						</td>
					</tr>
					<tr <?php _eTag ('trclass', array ('id' => 'menus')) ?>>
						<th>Niveau</th>
						<td>
							<?php
							_eTag ('inputText', array (
								'class' => 'text inputMenu',
								'style' => 'width: 20px',
								'name' => 'menu_level[' . $type . ']',
								'id' => 'level_' . $type,
								'value' => (isset ($liste_menus[$type]) && $liste_menus[$type]->level_hem) ? $liste_menus[$type]->level_hem : 0,
								'disabled' => (isset ($liste_menus[$type]) && $liste_menus[$type]->portlet_hem) ? 'disabled' : null
							))
							?>
						</td>
						<th style="width: 100px">Profondeur</th>
						<td>
							<?php
							_eTag ('inputText', array (
								'class' => 'text inputMenu',
								'style' => 'width: 20px',
								'name' => 'menu_depth[' . $type . ']',
								'id' => 'depth_' . $type,
								'value' => (isset ($liste_menus[$type]) && $liste_menus[$type]->depth_hem) ? $liste_menus[$type]->depth_hem : 1,
								'disabled' => (isset ($liste_menus[$type]) && $liste_menus[$type]->portlet_hem) ? 'disabled' : null
							))
							?>
						</td>
					</tr>
					<tr <?php _eTag ('trclass', array ('id' => 'menus')) ?>>
						<th>Template</th>
						<td colspan="3">
							<?php
							_eTag ('inputText', array (
								'class' => 'text longInputMenu',
								'name' => 'template[' . $type . ']',
								'id' => 'template_' . $type,
								'value' => (isset ($liste_menus[$type])) ? $liste_menus[$type]->template_hem : $menu['template']
							))
							?>
						</td>
					</tr>
					<tr <?php _eTag ('trclass', array ('id' => 'menus')) ?>>
						<th class="last">Classes</th>
						<td colspan="3">
							<?php
							_eTag ('inputText', array (
								'class' => 'text longInputMenu',
								'name' => 'class[' . $type . ']',
								'id' => 'class_' . $type,
								'value' => (isset ($liste_menus[$type])) ? $liste_menus[$type]->class_hem : null
							))
							?>
						</td>
					</tr>
				</table>
			</div>
		<?php } ?>
	</div>
</div>

<?php
$js = <<<JS
$$ ('.checkMenu').each (function (el) {
	var slide = new Fx.Slide ('divSlide' + el.get ('typeMenu'), {
		onComplete : function (elem) {
			refreshMenus ();
			if (elem.getParent ().getStyle ('height') != "0px") {
				elem.getParent ().setStyle ('height', '');
			}
		}
	});

	if (el.value != 1) {
		slide.hide ();
	}

	el.addEvent ('change', function () {
		if (!el.checked) {
			$ ('zoneElementChooser' + el.get ('typeMenu') + '_$uniqId').setStyle ('display', '');
			$ ('zoneElementChooser' + el.get ('typeMenu') + '_$uniqId').fireEvent ('display');
		}
		if (el.value == 1 || (el.value != 1 && slide.open)) {
			slide.toggle();
		}
	});
	if (el.value == 1 || (el.value != 1 && slide.open)) {
		$ ('zoneElementChooser' + el.get ('typeMenu') + '_$uniqId').fireEvent ('display');
	}
});
JS;
CopixHTMLHeader::addJSDOMReadyCode ($js);