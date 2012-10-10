<?php _eTag ('beginblock', array ('title' => 'Selection du menu', 'isFirst' => true)); ?>
<?php
if (empty($ppo->arListeMenus)){
	echo "Il n'y a pas de menus pour le thème ".$ppo->theme->getName();
} else {
	echo "Il y a ".count($ppo->arListeMenus)." menu".(count($ppo->arListeMenus) > 1 ? 's' : '')." pour le thème ".$ppo->theme->getName() ." et pour l'élément <b>".$ppo->element->caption_hei."</b>.";
	?>
	<table class="CopixVerticalTable">
		<tr>
	    	<th width="30%">Menu à éditer</th>
	    	<td>
	    		<select name="menu" id="menu">
	    			<option value="0">---Selectionnez un menu---</option>
	    		<?php 
	    		foreach ($ppo->arListeMenus as $menu){
	    			echo "<option ".(count($ppo->arListeMenus) == 1 ? "selected='selected'" : '')." value='".$menu['name']."'>".$menu['caption']."</option>";
	    		}
	    		?>
	    		</select>
	    	</td>
	  	</tr>
	</table>
	<?php 
	CopixHTMLHeader::addJSDOMReadyCode("
	$('menu').addEvent('change', function(){
		new Request.HTML({
			url : '"._url('heading|menueditor|getmenuinformations')."',
			update : 'menuedition',
			evalScripts : true
		}).post({'public_id_hei':'".$ppo->element->public_id_hei."', 'type_hem':$('menu').value});
	});
	");
	if(count($ppo->arListeMenus) == 1){
		CopixHTMLHeader::addJSDOMReadyCode("$('menu').fireEvent('change');");
	}
} ?>
<?php _eTag ('endblock'); ?>