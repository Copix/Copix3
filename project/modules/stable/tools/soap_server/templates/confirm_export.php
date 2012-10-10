<ul>
<?php
   echo '<li>', _i18n ('soap_server.url'), ' : <a href="'.$ppo->url.'">', $ppo->url, '</a>', '</li>';
   echo '<li>', _i18n ('soap_server.url_wsdl'), ' : <a href="'.$ppo->url_wsdl.'">', $ppo->url_wsdl, '</a>', '</li>';
   echo '<li>', _i18n ('soap_server.url_wsdl_1_1'), ' : <a href="'.$ppo->url_wsdl_1_1.'">', $ppo->url_wsdl_1_1, '</a>', '</li>';
?>
</ul>
<a href="<?php echo _url ('admin||'); ?>"><input type="button" value="<?php echo _i18n ('copix:common.buttons.back'); ?>" onclick="document.location.href='<?php echo _url ('admin||'); ?>'" /></a>