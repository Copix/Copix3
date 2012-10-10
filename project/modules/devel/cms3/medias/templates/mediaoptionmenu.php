<div class="portletOptions">
	<table width="100%">
		<tr>
			<td>
				<?php if($mediaType != 'audio') {?>
				<a href="#" id="options_affichage_<?php echo $identifiantFormulaire; ?>">
					<img src="<?php echo _resource ('img/tools/config.png'); ?>" alt="" />Options d'affichage
				</a>
				<?php
					$tpl = new CopixTpl();
					$tpl->assign('identifiantFormulaire', $identifiantFormulaire);
					$tpl->assign('options', $options);
					$tpl->assign('mediaType', $mediaType);
					$tpl->assign('portletId', $portlet_id);
					$tpl->assign('editId', _request('editId'));
					$optionsContent = $tpl->fetch("menuoptionaffichage.php");
					_etag ('copixwindow', array ('id'=>'copixWindowMediaOptionMenu'.$identifiantFormulaire, 'clicker'=>'options_affichage_'.$identifiantFormulaire, 'title'=>"options d'affichage"), $optionsContent);
				}
					echo  "</td><td style='text-align:center'>";					
				    echo '<a href="'._url ("portal|admin|moveDownElement", array ('position' => $position, 'editId'=>_request('editId'), 'portal_id'=>$portlet_id)).'">'._tag('copixicon', array('type' => 'movedown')).'</a>';
				   	echo '<a href="'._url ("portal|admin|moveUpElement", array ('position' => $position, 'editId'=>_request('editId'), 'portal_id'=>$portlet_id)).'">'._tag('copixicon', array('type' => 'moveup')).'</a>';			   
					?>
		   	</td>
	   	</tr>
	</table>
</div>