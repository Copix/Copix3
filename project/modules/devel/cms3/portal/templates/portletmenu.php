<?php
$hasOptions = false;
$templateNb = 0;
if (($xml = simplexml_load_file ($xmlPath))) {
	$templateNb = count($xml->children());
	foreach ($xml->children() as $node){
		// check s'il y a ades options, on est obligé de faire le test pour tous les noeuds car c'est chargé en ajax
		if($node->options){
			$hasOptions = true;
		}
	}
}

if($templateNb >1 || $hasOptions){
?>
<div id="portletMenu<?php echo $portletRandomId; ?>" class="portletMenu">
	Visuel du bloc 
	<?php
	echo CopixZone::process ('portal|templateChooser', array ('xmlPath'=> $xmlPath, 'selected'=>$template, 'inputId'=>'portletTemplate'.$portletRandomId, 'portletId' => $portletRandomId,  'identifiant'=>$portletRandomId, 'module'=>$module, 'textBouton'=>'', 'img'=>'heading|img/general_view.png'));
	?>
</div>
<?php
}
CopixHTMLHeader::addJSCode ("
function updatePortlet".$portletRandomId."(){
	var myHTMLRequest = new Request.HTML({
			url:'"._url ('portal|ajax|updatePortlet')."'
			}).post({
				'template':$('portletTemplate$portletRandomId').value,
				'portletId' : '".$portletRandomId."',
				'editId' : '"._request('editId')."'
				});
}
");

CopixHTMLHeader::addJSDOMReadyCode ("
if($('portletTemplate$portletRandomId')){
    $('portletTemplate$portletRandomId').addEvent('change', function(){
                updatePortlet".$portletRandomId."();
                if(typeof updateToolBar =='function'){
                    updateToolBar('".$portletRandomId."', '"._request ('editId')."');
                }
		});
}
");

?>