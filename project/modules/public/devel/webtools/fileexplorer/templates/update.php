<h2><?php echo _i18n ('fileexplorer.fileType', array ($ppo->type)); ?></h2>
<p><?php echo _tag ('copixicon', array ('type'=>'home', 'href'=>_url ('default', array ('path'=>'./')))), 
 '&nbsp;', 
 _tag ('copixicon', array ('type'=>'refresh', 'href'=>_url ('show', array ('file'=>$ppo->filePath)))),
 '&nbsp;',
 CopixZone::process ('PathExplore', array ('path'=>$ppo->filePath)); ?></p>
<div style="border: 1px solid #000; background-color: #ffffff;padding: 5px;">
<form action="<?php echo _url ('validFileContent', array ('file'=>$ppo->filePath)); ?>" method="POST">
<textarea style="width: 100%;" rows=40 name="filecontent">
<?php echo _copix_utf8_htmlentities ($ppo->code); ?>
</textarea>
<input type="submit" name="valid" value="<?php echo _i18n ('copix:common.buttons.valid'); ?>" />
<input type="button" name="cancel" value="<?php echo _i18n ('copix:common.buttons.cancel'); ?>" onclick="document.location.href='<?php echo _url ('show', array ('file'=>$ppo->filePath)); ?>'" /> 
</form>