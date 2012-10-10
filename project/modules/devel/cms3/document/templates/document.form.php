Vous utilisez actuellement le formulaire "Flash", qui permet d'envoyer plusieurs documents à la fois. 
Si vous rencontrez des problèmes, essayez <a href="<?php echo _url ('document|admin|edit', array ('classic' => true, 'editId' => _request ('editId'))); ?>"> la méthode "classique"</a>.
<br />
<br />
<?php
_eTag ('beginblock', array('title'=>"Envoi de documents"));
//echo CopixZone::process ('uploader|ChooseClassicForm', array ('url' => _url ('document|admin|edit', array ('classic' => true, 'editId' => _request ('editId')))));

echo CopixZone::process('uploader|uploader', array (
	'zone'=>'document|documentuploader',
	'action'=>_url('document|upload|saveFiles', array("editId" => _request('editId'))), 
	/*'cancel'=>_url("document|admin|cancel", array("editId" => _request('editId'))),*/
	'id_session'=>_request ('editId'),
	'parent_heading_public_id_hei'=>($ppo->chooseHeading ? $ppo->editedElement->parent_heading_public_id_hei : false)
));

_eTag ('endblock');
if (!$ppo->chooseHeading){
	_eTag ('back', array ('url' => _url ('document|admin|cancel', array ('editId' => _request ('editId')))));
}
?>