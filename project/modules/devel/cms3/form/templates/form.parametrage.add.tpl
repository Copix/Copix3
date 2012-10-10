<tr id="form_tr_{$ppo->cfc_id_element}" class="sortable">
	<td>{$ppo->cfe_type_label}</td>
	<td>{$ppo->cfe_label}</td>
	<td style="text-align:center;">
		<input id="cb_form_content_{$ppo->cfc_id_element}" type='checkbox' onclick="addRemoveRequiredContent({$ppo->cfc_id_element});" />
	</td>
	<td>
		{assign var='id_element' value=$ppo->cfc_id_element}
		{assign var='selected' value=$ppo->cfc_orientation}
		{radiobutton name="cfc_orientation_$id_element" values="0=>horizontal;1=>vertical"|toarray selected=$selected}
	</td>
	<td align="center">
		<a href="{copixurl dest="adminajax|moveDownElement"}" onclick="moveDownElement({$ppo->cfc_id_element});return false;" />
			<img class="image_button" src="{copixresource path='img/tools/movedown.png'}"/>
		</a>
	</td>
	<td align="center">
		<a href="{copixurl dest="adminajax|moveUpElement"}" onclick="moveUpElement({$ppo->cfc_id_element});return false;" />
			<img class="image_button" src="{copixresource path='img/tools/moveup.png'}"/>
		</a>
	</td>
	<td align="center" class="draggable">
		<script>
			/* Drap / Drop du contenu */
			initDragContent ("{copixurl dest='adminajax|updateContentOrder'}");
		</script>
		<img src="{copixresource path='heading|img/actions/move_up_down.png'}"/>
	</td>
</tr>