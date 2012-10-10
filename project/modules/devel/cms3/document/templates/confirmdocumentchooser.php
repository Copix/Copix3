<?php echo _tag("notification", array("title"=>"Enregistrement effectué", "message"=>"Les documents ont été ajoutées, vous pouvez maintenant les selectionner à partir de l'onglet \"bibliothèque du CMS\".")); ?>
<br />
<div style="text-align: center;">
	<?php _eTag("button", array("caption"=>"Ajouter d'autres documents", "url"=>_url("heading|element|prepareCreate", array('type'=>"document", 'heading'=>_request('heading'), 'then'=>_url('document|upload|confirmDocumentChooser', array('heading'=>_request('heading')))))))?>
</div>
<?php 
$elements = _request("selectedElementsFromCms");
$publicId = _request("heading");

$elementInfos = explode("|", $elements[0]);
$element = _ioClass("heading|headingelement/headingelementinformationservices")->getById($elementInfos[0], $elementInfos[1]);

if (count($elements) > 1){
	$publicId = $element->parent_heading_public_id_hei;
} else if (count($elements) == 1){
	$publicId = $element->public_id_hei;
}
CopixHTMLHeader::addJSDOMReadyCode("parent.refreshDocumentTrees('".$publicId."');");?>