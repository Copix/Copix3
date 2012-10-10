<div style="text-align:center; width:400px;font-size:16px;">
	<h3>Supprimer cet élément ?</h3>
	Etes vous sûr de vouloir supprimer cet élément ?	
	<br />
	<div>
		<button id="delete<?php echo $randomId; ?>">Supprimer</button>
		<a id="cancel<?php echo $randomId; ?>" href="javascript:;" onclick="javascript:$('copixwindowremoveportlet<?php echo $randomId; ?>').fireEvent('close');">Annuler</a>
	</div>
</div>
<?php 
CopixHTMLHeader::addJSDOMReadyCode("
	$('delete$randomId').addEvent('click', function(){
		$('copixwindowremoveportlet$randomId').fireEvent('close');
		showWaitMessage();
		window.location.href = '" ._url ('admin|deletePortlet', array ('id'=>$randomId, 'editId'=>$editId))."';
	});
");
?>
