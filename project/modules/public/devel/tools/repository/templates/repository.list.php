<table class="CopixTable">
	<tr>
        	<th><?php _etag ('i18n', 'repository.list.filename'); ?></th>
                <th><?php _etag ('i18n', 'repository.list.uploader'); ?></th>
                <th><?php _etag ('i18n', 'repository.list.nbdownload'); ?></th>
                <th><?php _etag ('i18n', 'repository.list.uploaddate'); ?></th>
                <th>&nbsp;</th>
        </tr>
<?php
if (isset ($ppo->arStoredFile)) {
    foreach ($ppo->arStoredFile as $storedFile) {
    ?>
        <tr>
                <td align="center"><?php echo $storedFile->storedfile_name ;?></td>
                <td align="center"><?php echo $storedFile->storedfile_uploader ;?></td>
                <td align="center"><?php echo $storedFile->storedfile_nbdownload ;?></td>
                <td align="center"><?php echo CopixDateTime::ISODateTimeToDateTime($storedFile->storedfile_uploaddate) ;?></td>
                <td align="center"><a href="<?php echo _url ('repository|repository|download', array ('id'=> $storedFile->storedfile_id)) ;?>"><?php _etag ('i18n','repository.list.download');?></a></td>
        </tr>
<?php
    }
} else {
    ?>
    <tr>
        <td colspan="5"><?php _etag ('i18n', 'repository.list.nofile'); ?></td>
    </tr>
    <?php
}
?>
</table>

