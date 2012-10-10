Vous utilisez actuellement le formulaire "Flash", qui permet d'envoyer plusieurs images à la fois. 
Si vous rencontrez des problèmes, essayez <a href="<?php echo _url ('images|admin|edit', array ('classic' => true, 'editId' => _request ('editId'))); ?>"> la méthode "classique"</a>.
<br />
<br />
<?php
_eTag ('beginblock', array('title'=>"Envoi d'images"));
//echo CopixZone::process ('uploader|ChooseClassicForm', array ('url' => _url ('images|admin|edit', array ('classic' => true, 'editId' => _request ('editId')))));



echo CopixZone::process('uploader|uploader', array(
	'zone'=>'images|imageuploader',
	'action'=>_url('images|upload|saveFiles', array("editId" => _request('editId'))),
	//'cancel'=>$ppo->chooseHeading ? null : _url("images|admin|cancel", array("editId" => _request('editId'))),
	'extensions'=>'*.jpg; *.jpeg; *.gif; *.png',
	'extensionsDescription'=>'Images (*.jpg, *.jpeg, *.gif, *.png)',
	'id_session'=>_request ('editId'),
	'parent_heading_public_id_hei'=>($ppo->chooseHeading ? $ppo->editedElement->parent_heading_public_id_hei : false))
);

_eTag ('endblock');
if (!$ppo->chooseHeading){
	_eTag ('back', array ('url' => _url ('images|admin|cancel', array ('editId' => _request ('editId')))));
}
?>