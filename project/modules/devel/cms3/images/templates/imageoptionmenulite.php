<?php
_eTag ('mootools', array ('plugin'=> array ('lightbox')));

// Nécessaire pour le moment car la zone doit être chargée depuis ici, car l'autload ne charge pas la zone danz l'écran diaporama
echo '<div style="display:none;">'.CopixZone::process ('heading|headingelement/headingelementchooser', array('selectedIndex'=>$options->getParam ('link', null), 'inputElement'=>'link_'.$identifiantFormulaire)).'</div>';

echo " ".CopixZone::process ('portal|templateChooser', array('xmlPath'=> CopixTpl::getFilePath("images|portlettemplates/templates.xml"), 'inputId'=>'template_'.$identifiantFormulaire, 'selected'=>$options->getParam ('template', PortletImage::DEFAULT_HTML_DISPLAY_IMAGE_TEMPLATE), 'identifiant'=>$identifiantFormulaire, 'module'=>'images'));

echo '<a href="'._url ("portal|admin|moveDownElement", array ('position' => $position, 'editId'=>_request('editId'), 'portal_id'=>$portlet_id)).'">'._tag('copixicon', array('type' => 'movedown')).'</a>';
echo '<a href="'._url ("portal|admin|moveUpElement", array ('position' => $position, 'editId'=>_request('editId'), 'portal_id'=>$portlet_id)).'">'._tag('copixicon', array('type' => 'moveup')).'</a>';
		
?>
