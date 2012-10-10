<h2><?php echo _i18n ('fileexplorer.fileType', array ($ppo->type)); ?></h2>
<p><?php echo _tag ('copixicon', array ('type'=>'home', 'href'=>_url ('default', array ('path'=>'./')))), 
 '&nbsp;', 
 _tag ('copixicon', array ('type'=>'refresh', 'href'=>_url ('show', array ('file'=>$ppo->filePath)))),
 '&nbsp;',
 CopixZone::process ('PathExplore', array ('path'=>$ppo->filePath)); ?></p>
<div style="border: 1px solid #000; background-color: #ffffff;padding: 5px;">
<?php 
if ($ppo->image){
	echo '<img src="'._url ('download', array ('file'=>$ppo->filePath)).'" alt="'.$ppo->filePath.'" />';
}elseif ($ppo->document){
	echo '<iframe style="width: 100%;" src="'._url ('download', array ('file'=>$ppo->filePath)).'" /></iframe>';
}else{
	echo $ppo->code;
	$updateProposal = $ppo->fileDescription->isWritable (); 
} ?>
</div>

<?php
if ($updateProposal){
	echo _tag ('copixicon', array ('type'=>'update', 'text'=>_i18n ('fileexplorer.fileUpdate'), 'href'=>_url ('show', array ('update'=>'1', 'file'=>$ppo->filePath))));
}
?>