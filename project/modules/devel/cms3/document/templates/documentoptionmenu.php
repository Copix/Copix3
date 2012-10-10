<?php
$pTypeElement = '';
	$optionsContent = "<table class='CopixVertical'><tr><th>Ouverture du fichier</th><td><form id='formOptionDocument$identifiantFormulaire'>
	<input type='hidden' name='editId' value='"._request('editId')."'>
	<input type='hidden' name='portletId' value='$portlet_id'>
	<input ".(($options->getParam ('content_disposition', 'inline') == 'inline') ? "checked='checked'" : "")." ".($options->getParam ('file_document', true) ? '' : "disabled='disabled'")." value='inline' name='content_disposition' id='inline".$identifiantFormulaire."' type='radio'/><label for='inline".$identifiantFormulaire."'>Ouvrir dans le navigateur</label><br />
	<input ".(($options->getParam ('content_disposition') == 'attachement') ? "checked='checked'" : "")." ".($options->getParam ('file_document', true) ? '' : "disabled='disabled'")." value='attachement' name='content_disposition' id='attachement".$identifiantFormulaire."' type='radio'/><label for='attachement".$identifiantFormulaire."'>Forcer le telechargement</label><br />
	</form>
	</td></tr></table>
	
	<div style='clear: both;text-align: right;'>
	<input type='submit' onclick='updateDocument(\"".$identifiantFormulaire."\", \"".$portlet_id."\", \""._request('editId')."\");$(\"copixWindowDocumentOptionMenu".$identifiantFormulaire."\").fireEvent(\"close\");return false;' value='Enregistrer' /> 
	</div>";
?>
<a href="#" id="options_affichage_<?php echo $identifiantFormulaire; ?>">
	<img src="<?php echo _resource ('img/tools/config.png'); ?>" alt="" />
</a>
<?php 
_etag ('copixwindow', array ('id'=>'copixWindowDocumentOptionMenu'.$identifiantFormulaire, 'clicker'=>'options_affichage_'.$identifiantFormulaire, 'title'=>"options d'affichage"), $optionsContent);
echo " ".CopixZone::process('portal|templateChooser', array('showText'=>false, 'xmlPath'=> CopixTpl::getFilePath("document|portlettemplates/templates.xml"), 'inputId'=>'template_'.$identifiantFormulaire, 'selected'=>$options->getParam ('template', PortletDocument::DEFAULT_HTML_DISPLAY_TEMPLATE), 'identifiant'=>$identifiantFormulaire));
echo  "</td><td style='text-align:center'>";					
echo '<a href="'._url ("portal|admin|moveDownElement", array ('position' => $position, 'editId'=>_request('editId'), 'portal_id'=>$portlet_id)).'">'._tag('copixicon', array('type' => 'movedown')).'</a>';
echo '<a href="'._url ("portal|admin|moveUpElement", array ('position' => $position, 'editId'=>_request('editId'), 'portal_id'=>$portlet_id)).'">'._tag('copixicon', array('type' => 'moveup')).'</a>';
?>