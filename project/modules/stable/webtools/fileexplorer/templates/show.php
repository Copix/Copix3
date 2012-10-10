<h2><?php echo _i18n ('fileexplorer.fileType', array ($ppo->type)); ?></h2>
<p><?php echo _tag ('copixicon', array ('type'=>'home', 'href'=>_url ('default', array ('path'=>'./')))), 
 '&nbsp;', 
 _tag ('copixicon', array ('type'=>'refresh', 'href'=>_url ('show', array ('file'=>$ppo->filePath)))),
 '&nbsp;',
 CopixZone::process ('PathExplore', array ('path'=>$ppo->filePath)); 
 ?></p>
<div style="border: 1px solid #000; background-color: #ffffff;padding: 5px;">
<?php
$updateProposal = false; 
if ($ppo->image){
	echo '<img src="'._url ('download', array ('file'=>$ppo->filePath)).'" alt="'.$ppo->filePath.'" />';
}elseif ($ppo->document){
	echo '<iframe style="width: 100%;" src="'._url ('download', array ('file'=>$ppo->filePath)).'" /></iframe>';
}else{
	$lang = CopixI18N::getLang();
	CopixHTMLHeader::addJSLink(_resource('|js/editarea/edit_area_full.js'));
	CopixConfig::instance()->copixhtmlheader_concatJS = false;
	$ext = EditSyntax::getSyntaxFromExtention($ppo->filePath);
$js=<<<EOJS
var lang = '$lang';
var ext = '$ext';
if(lang.length == 0){
	lang = 'en';
}
editAreaLoader.init({
	id: "filecontent"	
	,start_highlight: false
	,allow_toggle: true
	, display: 'later'
	,language: lang
	//,syntax: ext	
	,is_multi_files: false
	,is_editable: false
	,show_line_colors: true
});

EOJS;
CopixHTMLHeader::addJSDOMReadyCode($js);
?>
<textarea style="width: 100%;height: 40em;" rows="40" id="filecontent"><?php echo _copix_utf8_htmlentities($ppo->code);?></textarea>
<?php 	
	$updateProposal = $ppo->fileDescription->isWritable (); 
} ?>
</div>
<?php
if ($updateProposal){
	echo _tag ('copixicon', array ('type'=>'update', 'text'=>_i18n ('fileexplorer.fileUpdate'), 'href'=>_url ('show', array ('update'=>'1', 'file'=>$ppo->filePath))));
}
echo CopixZone::process('fileexplorer|permission', array('file' => $ppo->filePath));
?>