<?php 
CopixHTMLHeader::addJSLink (_resource('cms_editor|js/tools.js'), array ('concat' => false));
CopixHTMLHeader::addJSLink (_resource('js/tiny_mce/tiny_mce_src.js'), array ('concat' => false));
CopixHTMLHeader::addCSSLink (_resource ('cms_editor|styles/cmseditor.css'));

_eTag('mootools', array('plugins'=>"resize"));
//Necessaire pour le plugin image du tiny_mce pour l'appel ajax du bouton imageChooser
?>
<input type="hidden" id="heading_<?php echo $name ?>" value="<?php echo CopixSession::get('heading', _request('editId')); ?>" />
<textarea id="<?php echo $name ?>" name="<?php echo $name ?>" style="width:99%; height: <?php echo $height ?>px"><?php echo $text; ?></textarea>

<?php
$styleSheetList = explode(';', CopixConfig::get('cms_editor|tinymce_stylesheet'));
$paths = array();
foreach ($styleSheetList as $styleSheet){
	if ($styleSheet && CopixResource::exists ($styleSheet)) {
		$paths[] = _resource($styleSheet, $theme);
	}
}
$paths = implode(',', $paths);

$jsCode = <<<EOF
function initFunction(ed) {
   tinyMCE.dom.Event.add(ed.getWin(), "blur", function(e){
        ed.cmsChange(ed.getContent());
   });
}

tinyMCE.init({
	language : "fr",
	/*skin : "o2k7",
	skin_variant : "silver",*/
	mode : "exact",
	oninit : function(){
		new Resizing('$name\_ifr',{'min':100,'max':800, 'userpreference': 'articles|contentHeight'});
	},
	setup : function(ed) {	
		ed.onInit.add(function(ed){initFunction(ed)});
		ed.cmsChange = function(content){
	        updateWysiwygform$name(content);
	    };
      	ed.onChange.add(function(ed, l) {
	      	try {      		
	      		//fonction à definir si on veut capturer l'evenement
	      		ed.cmsChange(ed.getContent());
	        } catch (err) {}
      	});
   	},
	elements : '$name',
	content_css : '$paths',
	theme : "copixcms",
	relative_urls : false,
	convert_urls : false,
	plugins : "table,elementchooser,imagechooser,paste",
	theme_copixcms_buttons1_add : "elementChooserLink,elementChooserImage",
	theme_copixcms_buttons2_add : "pasteword,|,tablecontrols",
	table_styles : "Header 1=header1;Header 2=header2;Header 3=header3",
	table_cell_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
	table_row_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
	table_cell_limit : 1000,
	table_row_limit : 500,
	table_col_limit : 500,
	extended_valid_elements : "img[class|src|border|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|thumb_title|thumb_title_pos|thumb_keep_proportions|public_id|thumb_show_image|thumb_galery_id]"

});



EOF;
CopixHTMLHeader::addJSCode($jsCode);
?>
<?php if ($preview) { ?>
	<br />
	<?php _eTag ('button', array ('img' => 'img/tools/show.png', 'caption' => 'Prévisualiser', 'id' => 'preview_button_' . $name, 'type' => 'button')) ?>
	<div id="preview<?php echo $name;?>" class="cmsEditorPreview"></div>
	<?php
	CopixHTMLHeader::addJSDOMReadyCode("
	$('preview_button_".$name."').addEvent('click', function (){
			cmsWysiwygPreview('".$name."', tinyMCE.get('".$name."').getContent());
			$ ('preview".$name."').setStyle ('display', 'block');
		});
	");
	?>
<?php } ?>
