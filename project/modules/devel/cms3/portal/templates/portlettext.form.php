<?php 
	_eTag('mootools', array('plugins'=>'copixformobserver'));
	CopixHTMLHeader::addJSLink(_resource('portal|js/tools.js'));
	CopixHTMLHeader::addCSSLink(_resource('portal|styles/style.css'));
	//initialisation des variables
	$identifiantFormulaire = $portlet->getRandomId ();	
	$params = new CopixParameterHandler();
	$options = $portlet->getOptions ();	
	$params->setParams($options);
?>
<div id="div_<?php echo $identifiantFormulaire;?>">
<?php
	if (isset($portlet->_arHeadingElementsError) && count($portlet->_arHeadingElementsError)) {
		_etag('ulli', array ('values' => $portlet->_arHeadingElementsError, 'extras' => 'class="error"'));
	}
?>
<select id="editor<?php echo $identifiantFormulaire;?>" name="editor<?php echo $identifiantFormulaire;?>">
	<option value="<?php echo CmsEditorServices::WIKI_EDITOR; ?>" <?php echo $params->getParam('editor', CmsEditorServices::WIKI_EDITOR) == CmsEditorServices::WIKI_EDITOR ? "selected='selected'" : ''; ?>>Wiki</option>
	<option value="<?php echo CmsEditorServices::WYSIWYG_EDITOR; ?>" <?php echo $params->getParam('editor', CmsEditorServices::WIKI_EDITOR) == CmsEditorServices::WYSIWYG_EDITOR ? "selected='selected'" : ''; ?>>Wysiwyg</option>
</select>
<form id="textform_<?php echo $identifiantFormulaire;?>" style="display:<?php echo $params->getParam('editor', CmsEditorServices::WIKI_EDITOR) == CmsEditorServices::WYSIWYG_EDITOR ? 'none' : '' ?>">
<?php 	
	echo CopixZone::process('cms_editor|cmswikieditor', array('name'=>'text_'.$identifiantFormulaire, 'text'=>$params->getParam('text')));
?>
</form>
<form id="wysiwygform_<?php echo $identifiantFormulaire;?>" style="display:<?php echo $params->getParam('editor', CmsEditorServices::WIKI_EDITOR) == CmsEditorServices::WIKI_EDITOR ? 'none' : '' ?>">
<?php 	
	echo CopixZone::process('cms_editor|cmswysiwygeditor', array('name'=>'html_'.$identifiantFormulaire, 'value'=>$params->getParam('html'))); 
?>
</form>
</div>
<?php
CopixHTMLHeader::addJSDOMReadyCode("
$('editor".$identifiantFormulaire."').addEvent('change', function(){
	$('wysiwygform_".$identifiantFormulaire."').setStyle('display', $('editor".$identifiantFormulaire."').value == ".CmsEditorServices::WIKI_EDITOR." ? 'none' : '');
	$('textform_".$identifiantFormulaire."').setStyle('display', $('editor".$identifiantFormulaire."').value != ".CmsEditorServices::WIKI_EDITOR." ? 'none' : '');
	updateEditor('".$portlet->getRandomId()."', '"._request('editId')."', $('editor".$identifiantFormulaire."').value);
});

var formTextIdentifiantObserver".$identifiantFormulaire." = new CopixFormObserver ('textform_".$identifiantFormulaire."', {
      onChanged : function (){
         updateText($('text_".$identifiantFormulaire."').value, '".$portlet->getRandomId()."', '"._request('editId')."', ".CmsEditorServices::WIKI_EDITOR.");
      }
   });
");
CopixHTMLHeader::addJSCode("
function updateWysiwygformhtml_".$identifiantFormulaire."(text){
	updateText(text, '".$portlet->getRandomId()."', '"._request('editId')."', ".CmsEditorServices::WYSIWYG_EDITOR.");
}");
?>