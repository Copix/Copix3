<h2>Elément<?php echo count ($ppo->toPublish) == 1 ? '' : 's';?> à publier</h2>
<?php echo count ($ppo->toPublish) == 1 ? "Cet élément est appelé dans la ".$ppo->currentElement->type_hei." mais n'est pas publié. Voulez-vous le publier ?" : "Ces éléments sont appelés dans la ".$ppo->currentElement->type_hei." mais ne sont pas publiés. Voulez-vous les publier ?"; ?>
<br />
<br />
<form id="formPublish" action="<?php echo _url('heading|element|publish') ?>">
<table class="CopixTable">
	<tr>
		<th><a onclick="checkUncheck()" title="Tout sélectionner / tout déselectionner" style="cursor: pointer"><?php echo _etag('copixicon', array ('type' => 'select', 'alt'=>"Tout sélectionner / tout déselectionner", 'title' => 'Tout sélectionner / tout déselectionner')) ?></a></th>
		<th>Libellé</th>
		<th>Type</th>
		<th>Rubrique</th>
	</tr>
<?php 
foreach ($ppo->toPublish as $element){	?>
	<tr>
		<td><input class="elementCheckbox" type="checkbox" name="elements[]" value="<?php echo $element->id_helt.'|'.$element->type_hei.'|'.$element->public_id_hei; ?>" /></td>
		<td><a href="<?php echo _url('heading|element|prepareedit', array('type'=>$element->type_hei, 'id'=>$element->id_helt, 'heading'=>$element->parent_heading_public_id_hei)); ?>"><?php echo $element->caption_hei; ?></a></td>
		<td><img width="32px" height="32px" src="<?php echo _resource ($ppo->arHeadingElementTypes[$element->type_hei]['image']); ?>" /></td>
		<?php 
			$path = explode('-', $element->hierarchy_hei);
			$breadcrumb = "Racine du site";
			foreach ($path as $id => $value) {
				$elementPath = _ioClass('heading|headingelementinformationservices')->get ($value);
				$breadcrumb .= " / " . $elementPath->caption_hei;
			}
		?>
		<td><?php echo $breadcrumb; ?></td>
	</tr>
<?php } ?>
	<tr>
		<td colspan="4">
			<!-- Actions sur les éléments séléctionnés -->
			<img src="<?php echo _resource ('heading|img/with_selection.png'); ?>" alt="Avec la sélection : " />
			<input id="action_publish" type="image" title="Publier la sélection" alt="Publier" name="fonction" src="<?php echo _resource('heading|img/actions/publish.png') ?>" value="publish" />
		</td>
	</tr>
</table>
</form>
<input type="button" value="Tout publier" onclick="javascript:publishAll()" />
<input type="button" value="Retour à l'administration" onclick="document.location.href='<?php echo _url('heading|element|default', array('heading'=>$ppo->currentElement->parent_heading_public_id_hei, 'selected[0]'=>$ppo->currentElement->id_helt.'|'.$ppo->currentElement->type_hei)); ?>'" />

<?php
CopixHTMLHeader::addJSCode("
var check = true;
function checkUncheck(){
	$$('.elementCheckbox').each(function (el){
		el.checked = check;
	});
	check = !check;
}

function publishAll (){
	$$('.elementCheckbox').each(function (el){
		el.checked = true;
	});
	$('formPublish').submit();
}
");
