<?php 
$lang = CopixI18N::getLang();
$langPath = _resourcePath('|js/langs/'.$lang.'.js');
if(!file_exists($langPath)){
	$lang = 'en';	
}
$ext = EditSyntax::getSyntaxFromExtention($ppo->filePath);
$syntaxes = EditSyntax::getListString();
CopixHTMLHeader::addJSLink(_resource('|js/editarea/edit_area_full.js'));
CopixConfig::instance()->copixhtmlheader_concatJS = false;


$js=<<<EOJS
var ext = '$ext';
var lang = '$lang';
editAreaLoader.init({
	id: "filecontent"	
	,start_highlight: false
	,allow_toggle: false
	,language: lang
	,syntax: ext	
	,toolbar: "search, go_to_line, |, undo, redo, |, select_font, |, syntax_selection, highlight, reset_highlight, |, help"
	,syntax_selection_allow: "$syntaxes"
	,is_multi_files: false
	,show_line_colors: true
});
EOJS;
CopixHTMLHeader::addJSCode($js);
?>

<h2><?php echo _i18n ('fileexplorer.fileType', array ($ppo->type)); ?></h2>
<p><?php echo _tag ('copixicon', array ('type'=>'home', 'href'=>_url ('default', array ('path'=>'./')))), 
 '&nbsp;', 
 _tag ('copixicon', array ('type'=>'refresh', 'href'=>_url ('show', array ('file'=>$ppo->filePath)))),
 '&nbsp;',
 CopixZone::process ('PathExplore', array ('path'=>$ppo->filePath)); ?></p>
<form action="<?php echo _url ('validFileContent', array ('file'=>$ppo->filePath)); ?>" method="POST">
<textarea style="width: 100%;height: 40em;" rows="40" name="filecontent" id="filecontent">
<?php echo _copix_utf8_htmlentities ($ppo->code); ?>
</textarea>
<input type="submit" name="valid" value="<?php echo _i18n ('copix:common.buttons.valid'); ?>" />
<input type="button" name="cancel" value="<?php echo _i18n ('copix:common.buttons.cancel'); ?>" onclick="document.location.href='<?php echo _url ('show', array ('file'=>$ppo->filePath)); ?>'" /> 
</form>