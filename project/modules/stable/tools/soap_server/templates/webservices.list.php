<?php 
if (count ($ppo->arWebservices)){
?>
<table class="CopixTable">
	<tr>
		<th><?php _etag ('i18n', 'soap_server.list.wsname'); ?></th>
		<th><?php _etag ('i18n', 'soap_server.list.modulename'); ?></th>
		<th>&nbsp;</th>
	</tr>
<?php
    	foreach ($ppo->arWebservices as $Webservices) {
?>
			<tr <?php _etag ('cycle', array ('values'=>'class="",class="alternate"'))?>>
				<td><?php echo $Webservices->name_webservices ;?></td>
				<td><?php echo $Webservices->file_webservices ;?></td>
				<td>
				<a href="<?php echo _url ('soap_server||', array ('name'=> $Webservices->name_webservices)) ;?>">WS</a>
				&nbsp;&nbsp;
				<a href="<?php echo _url ('soap_server|default|wsdl', array ('name'=> $Webservices->name_webservices)) ;?>">WSDL</a>
				&nbsp;&nbsp;
				<a href="<?php echo _url ('soap_server|default|wsdl', array ('version'=>'1.1', 'name'=> $Webservices->name_webservices)) ;?>">WSDL 1.1</a>
				&nbsp;&nbsp;
				<a href="<?php echo _url ('admin|deleteWsService', array ('id_wsservice' => $Webservices->id_webservices)) ?>"
					><img src="<?php echo _resource ('img/tools/delete.png') ?>" alt="<?php _i18n ('soap_server.delete') ?>" title="<?php _i18n ('soap_server.delete') ?>"
				/></a>
				</td>
			</tr>
<?php
		}//foreach
	echo "</table>";
}else{
   echo '<p>', _i18n ("soap_server.list.nowslist"), '</p>';
}    	
?>
<input type="button" value="<?php echo _i18n ('copix:common.buttons.back'); ?>" onclick="javascript: document.location='<?php echo _url ('admin||') ?>';" />