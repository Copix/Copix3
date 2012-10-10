<table class="CopixTable">
	<tr>
		<th><?php _etag ('i18n', 'wsserver.list.wsname'); ?></th>
		<th><?php _etag ('i18n', 'wsserver.list.modulename'); ?></th>
		<th><?php _etag ('i18n', 'wsserver.list.wslink'); ?></th>
		<th><?php _etag ('i18n', 'wsserver.list.wsdllink'); ?></th>
	</tr>
	<?php
	if (isset ($ppo->arWebservices)) {
    	foreach ($ppo->arWebservices as $Webservices) {
    		?>
			<tr>
				<td><?php echo $Webservices->name_wsservices ;?></td>
				<td><?php echo $Webservices->module_wsservices ;?></td>
				<td><a href="<?php echo _url ('wsserver||', array ('wsname'=> $Webservices->name_wsservices)) ;?>">Link</a></td>
				<td><a href="<?php echo _url ('wsserver|default|wsdl', array ('wsname'=> $Webservices->name_wsservices)) ;?>">Link</a></td>
				<td>
					<a href="<?php echo _url ('admin|deleteWsService', array ('id_wsservice' => $Webservices->id_wsservices)) ?>"
						><img src="<?php echo _resource ('img/tools/delete.png') ?>" alt="<?php _i18n ('wsserver.delete') ?>" title="<?php _i18n ('wsserver.delete') ?>"
					/></a>
				</td>
			</tr>
			<?php
    	}
	} else {
    	?>
    	<tr>
    		<td colspan="4"><?php _etag ('i18n', 'wsserver.list.nowslist'); ?></td>
    	</tr>
    	<?php
	}
	?>
</table>

<br />
<input type="button" value="<?php echo _i18n ('wsserver.back'); ?>" onclick="javascript: document.location='<?php echo _url ('admin||') ?>';" />