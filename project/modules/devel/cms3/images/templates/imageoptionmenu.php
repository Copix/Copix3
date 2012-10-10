<a href="#" id="edition_image_<?php echo $identifiantFormulaire; ?>">
	<img src="<?php echo _resource ('img/tools/update.png'); ?>" alt="Editer" />
</a>
<a href="#" id="options_affichage_<?php echo $identifiantFormulaire; ?>">
	<img src="<?php echo _resource ('img/tools/config.png'); ?>" alt="Options" />
</a>
<?php
echo '<a href="'._url ("portal|admin|moveDownElement", array ('position' => $position, 'editId'=>_request('editId'), 'portal_id'=>$portlet_id)).'">'._tag('copixicon', array('type' => 'movedown')).'</a>';
echo '<a href="'._url ("portal|admin|moveUpElement", array ('position' => $position, 'editId'=>_request('editId'), 'portal_id'=>$portlet_id)).'">'._tag('copixicon', array('type' => 'moveup')).'</a>';

$tpl = new CopixTpl();
$tpl->assign('identifiantFormulaire', $identifiantFormulaire);
$tpl->assign('options', $options);
$tpl->assign('portletId', $portlet_id);
$tpl->assign('editId', _request('editId'));
$optionsContent = $tpl->fetch("menuoptionaffichage.php");
_etag ('copixwindow', array ('id'=>'copixWindowImageOptionMenu'.$identifiantFormulaire, 'clicker'=>'options_affichage_'.$identifiantFormulaire, 'title'=>"options d'affichage"), $optionsContent);

if ($image){
	$optionsContent = "<iframe id='editionimageframe".$identifiantFormulaire."' style='border:none;' width='850px' height='600px' src='"._url("heading|element|prepareedit", array('type'=>'image', 'id'=>$image->id_helt, 'heading'=>$image->parent_heading_public_id_hei, 'then'=>_url('images|default|confirmImageEdition', array('identifiantFormulaire'=>$identifiantFormulaire, 'heading'=>$image->parent_heading_public_id_hei))))."'></iframe>";
	_etag ('copixwindow', array ('modal'=>true, 'id'=>'copixWindowImageEdition'.$identifiantFormulaire, 'fixed' => 1, 'clicker'=>'edition_image_'.$identifiantFormulaire, 'title'=>"Edition de l'image"), $optionsContent);
	CopixHTMLHeader::addJSCode("
		function refreshEditionImage".$identifiantFormulaire."(){
			$('copixWindowImageEdition".$identifiantFormulaire."').fireEvent('close');
			$('editionimageframe".$identifiantFormulaire."').set('src', '"._url("heading|element|prepareedit", array('type'=>'image', 'id'=>$image->id_helt, 'heading'=>$image->parent_heading_public_id_hei, 'then'=>_url('images|default|confirmImageEdition', array('identifiantFormulaire'=>$identifiantFormulaire, 'heading'=>$image->parent_heading_public_id_hei))))."');
			$('image".$identifiantFormulaire.$image->id_helt."').set('src', '"._url('images|imagefront|GetImage', array('id_image'=>$image->id_helt))."&'+(Math.random()*1000).round());
		}
	");
}
?>
