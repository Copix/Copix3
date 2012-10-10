<table class="CopixTable">
	<tr>
		<th><?php _etag ('i18n', 'wsserver.list.wsname'); ?></th>
		<th><?php _etag ('i18n', 'wsserver.list.modulename'); ?></th>
		<th><?php _etag ('i18n', 'wsserver.list.filename'); ?></th>
		<th></th>
	</tr>
	<?php
	if (isset ($ppo->arWebservices)) {
    	foreach ($ppo->arWebservices as $Webservices) {
    		?>
			<tr>
				<td><?php echo $Webservices->name_wsservices ;?></td>
				<td><?php echo $Webservices->module_wsservices ;?></td>
				<td><?php echo $Webservices->file_wsservices ;?></td>
				<td>
					<a href="<?php echo _url ('wsserver||', array ('wsname'=> $Webservices->name_wsservices)) ?>"
						><img src="<?php echo _resource ('img/tools/link.png') ?>" alt="<?php echo _i18n ('wsserver.list.wslink') ?>" title="<?php echo _i18n ('wsserver.list.wslink') ?>"
					/></a>
					<a href="<?php echo _url ('wsserver|default|wsdl', array ('wsname'=> $Webservices->name_wsservices)) ?>"
						><img src="<?php echo _resource ('wsserver|img/wsdl.png') ?>" alt="<?php echo _i18n ('wsserver.list.wsdllink') ?>" title="<?php echo _i18n ('wsserver.list.wsdllink') ?>"
					/></a>
					<a href="<?php echo _url ('wsserver|admin|test', array ('webservice'=> $Webservices->id_wsservices)) ?>"
						><img src="<?php echo _resource ('wsserver|img/testxml.png') ?>" alt="<?php echo _i18n ('wsserver.list.test') ?>" title="<?php echo _i18n ('wsserver.list.test') ?>"
					/></a>
					<a href="<?php echo _url ('wsserver|admin|deleteWsService', array ('id_wsservice' => $Webservices->id_wsservices)) ?>"
						><img src="<?php echo _resource ('img/tools/delete.png') ?>" alt="<?php echo _i18n ('wsserver.delete') ?>" title="<?php echo _i18n ('wsserver.delete') ?>"
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
<a href="<?php echo _url ('wsserver|admin|manageWebServices') ?>"
	><img src="<?php echo _resource ('img/tools/add.png') ?>" alt="<?php echo _i18n ('wsserver.add') ?>" title="<?php echo _i18n ('wsserver.add') ?>" />
	<?php echo _i18n ('wsserver.list.addWebservice') ?>
</a>

<br /><br />
<input type="button" value="<?php echo _i18n ('wsserver.back'); ?>" onclick="javascript: document.location='<?php echo _url ('admin||') ?>';" />