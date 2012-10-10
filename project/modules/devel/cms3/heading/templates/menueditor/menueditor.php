<?php if($ppo->notify){
	echo _tag("notification", array("title"=>"Enregistrement effectué", "message"=>"Les modifications de menu ont été enregistrées."));
}?>
<form method="post" action="<?php echo _url('heading|menueditor|savemenu'); ?>">
<?php _eTag ('beginblock', array ('title' => 'Séléction de l\'élément', 'isFirst' => true)); ?>
	<table class="CopixVerticalTable">
	  	<tr>
	  		<th width="30%">Editer les menus pour l'élément : </th>
	  		<td>
	  			<?php
				_eTag ('copixzone', array ('process' => 'heading|headingelement/headingelementchooser',
					'linkOnHeading' => true,
					'id' => 'menuElementChooser',
					'selectedIndex' => $ppo->element->public_id_hei,
					'inputElement' => 'public_id',
					'identifiantFormulaire' =>uniqid()
				));
				CopixHTMLHeader::addJSDOMReadyCode("
				$('public_id').addEvent('change', function(){
					new Request.HTML({
						url: '"._url('heading|menueditor|getelementmenuinformations')."',
						evalScripts : true,
						update : $('elementMenuInformations')
					}).post({'public_id_hei':$('public_id').value});
					new Request.HTML({
						url: '"._url('heading|menueditor|getelementinformations')."',
						evalScripts : true,
						update : $('elementInformations')
					}).post({'public_id_hei':$('public_id').value});
				});
				$('public_id').fireEvent('change');
				");
				?>
			</td>
	  	</tr>
	</table>
	<div id="elementInformations"></div>
<?php _eTag ('endblock'); ?>
	<div id="elementMenuInformations"></div>
	<div id="menuedition"></div>
</form>