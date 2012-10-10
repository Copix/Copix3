<h3>Options de l'élément dans les menus</h3>
<table class="CopixVerticalTable">
	<tr <?php _eTag ('trclass', array ('id' => 'mainMenus')) ?>>
		<th style="width: 30%" colspan="2">Visibilité</th>
		<td>
			<?php
			$visibility_caption = ($ppo->visibility) ? 'Visible' : 'Invisible';
			if ($ppo->element->public_id_hei == 0) {
				$ppo->visibility_values = array (1 => 'Visible', 0 => 'Invisible');
			} else if ($ppo->visibility_inherited_from) {
				$ppo->visibility_values = array (1 => 'Visible', 0 => 'Invisible', 2 => $visibility_caption . ' (Hérité de ' . $ppo->visibility_inherited_from . ')');
			} else {
				$ppo->visibility_values = array (1 => 'Visible', 0 => 'Invisible', 2 => 'Hériter de la visibilité parent');
			}

			_eTag ('select', array ('name' => 'show_in_menu_hei', 'emptyShow' => false, 'values' => $ppo->visibility_values, 'selected' => $ppo->element->show_in_menu_hei));
			?>
		</td>
		<th colspan="2">Classes</th>
		<td>
			<?php
			_eTag ('inputtext', array (
				'id' => 'class' . $ppo->element->id_helt,
				'name' => 'class_name_hei',
				'class' => 'longInputMenu',
				'value' => $ppo->element->menu_html_class_name_hei
			))
			?>
		</td>
	</tr>
</table>