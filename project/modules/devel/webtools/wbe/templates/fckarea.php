<?php
require_once (CopixModule::getPath ('wbe').'www/js/fckeditor/fckeditor.php');

$oFCKeditor = new FCKeditor('FCKeditor1') ;
$oFCKeditor->BasePath = _resource ('|js/fckeditor/');
$oFCKeditor->Value = $ppo->content ;
$oFCKeditor->Create() ;
?>