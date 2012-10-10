<?php

	$optionsContent = "<div>
		<form id='formOptionArticle$identifiantFormulaire'>
			<input type='hidden' name='editId' value='"._request('editId')."'>
			<input type='hidden' name='portletId' value='$portlet_id'>
			<table class='CopixVerticalTable'>
			<tr><th>Date de création</th>
			<td><input value='1' ".(($options->getParam ('date_create', false)) ? "checked='checked'" : "")." id='check_date_create_".$identifiantFormulaire."' name='date_create' type='checkbox'/><label for='check_date_create_".$identifiantFormulaire."'>Afficher la date de création</label>
			<tr><th>Date de modification</th>
			<td><input value='1' ".(($options->getParam ('date_update', false)) ? "checked='checked'" : "")." id='check_date_update_".$identifiantFormulaire."' name='date_update' type='checkbox'/><label for='check_date_update_".$identifiantFormulaire."'>Afficher la date de modification</label>
			</td></tr>
			<tr><th>Résumé</th>
			<td><input value='1' ".(($options->getParam ('summary', false)) ? "checked='checked'" : "")." id='check_summary_".$identifiantFormulaire."' name='check_summary' type='checkbox'/><label for='check_summary_".$identifiantFormulaire."'>Afficher le résumé</label>
			</td></tr>
			<tr><th>Contenu</th><td>	
			<input value='1' ".(($options->getParam ('content', true)) ? "checked='checked'" : "")." id='check_content_".$identifiantFormulaire."' name='check_content' type='checkbox'/><label for='check_content_".$identifiantFormulaire."'>Afficher le contenu</label>			
			</td></tr>
			".
			($heading ? 
			"<tr><th>Ordre</th><td>"
			._tag("select", array('name'=>"order", 'id'=>'order_'.$identifiantFormulaire, 'emptyShow'=>false, 'values'=>array('display_order_hei'=>'Ordre d\'affichage', 'date_create_hei'=>'Date de création', 'date_update_hei'=>'Date de mise à jour'), 'selected'=>$options->getParam ('order', 'display_order_hei'))).			
			"</td></tr>"
			: '')
			."
			</table>
			<div style='clear: both;text-align: right;'>
				<input type='submit' onclick='updateArticle(\"".$identifiantFormulaire."\", \"".$portlet_id."\", \""._request('editId')."\");$(\"copixWindowArticleOptionMenu".$identifiantFormulaire."\").fireEvent(\"close\");return false;' value='Enregistrer' /> 
			</div>
		</form>
	</div>";

	_etag ('copixwindow', array ('id'=>'copixWindowArticleOptionMenu'.$identifiantFormulaire, 'clicker'=>'options_affichage_'.$identifiantFormulaire, 'title'=>"options d'affichage"), $optionsContent);
	?>
	<a href="#" id="options_affichage_<?php echo $identifiantFormulaire; ?>">
		<img src="<?php echo _resource ('img/tools/config.png'); ?>" alt="" />
	</a>
	<?php 	
	echo " ".CopixZone::process('portal|templateChooser', array('showText'=>false, 'xmlPath'=> CopixTpl::getFilePath("articles|portlettemplates/templates.xml"), 'inputId'=>'template_'.$identifiantFormulaire, 'selected'=>$options->getParam ('template', PortletArticle::DEFAULT_HTML_DISPLAY_TEMPLATE), 'identifiant'=>$identifiantFormulaire, 'module'=>'articles'));	
    echo '<a href="'._url ("portal|admin|moveDownElement", array ('position' => $position, 'editId'=>_request('editId'), 'portal_id'=>$portlet_id)).'">'._tag('copixicon', array('type' => 'movedown')).'</a>';
   	echo '<a href="'._url ("portal|admin|moveUpElement", array ('position' => $position, 'editId'=>_request('editId'), 'portal_id'=>$portlet_id)).'">'._tag('copixicon', array('type' => 'moveup')).'</a>';			   
		
	//code specifique pour le remplacement de titre
	CopixHTMLHeader::addJSDOMReadyCode ("
	$('template_".$identifiantFormulaire."').addEvent('change', function(){
		updateArticle('".$identifiantFormulaire."', '".$portlet_id."', '"._request('editId')."');
		});		
	");
?>