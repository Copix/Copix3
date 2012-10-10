<form action='<?php echo _url('auth|dynamic|record'); ?>' method="POST" >
<input type="hidden" name="id_group" value="<?php echo $ppo->id_group; ?>" />
<input type="hidden" name="handler_group" value="<?php echo $ppo->handler_group; ?>" />
<input type="hidden" name="url_return" value="<?php echo $ppo->url_return; ?>" />
<ul>
<?php
    
    foreach ($ppo->list as $dc) {
        echo '<li>';
        echo $dc->record->name_dc;
        echo '<a href="'._url('auth|dynamic|delete',array('id_dc'=>$dc->record->id_dc,"id_group"=>$ppo->id_group,'handler_group'=>$ppo->handler_group,'url_return'=>$ppo->url_return)).'"><img src="'._resource('img/tools/delete.png').'" /></a>';
        if (count($dc->data) <= 0) {
            echo '<input type="checkbox" name="bool['.$dc->record->id_dc.']" value="1" '.$dc->checked.' /><input type="hidden" name="value[]" value="'.$dc->record->id_dc.'" />';
        } else {
            echo '<ul>';
            foreach ($dc->data as $value) {
                echo '<li>'.$value->value_dcv.'('.$value->level_dcv.')';
                echo '<a href="'._url('auth|dynamic|delete',array('id_dcv'=>$value->id_dcv,"id_group"=>$ppo->id_group,'handler_group'=>$ppo->handler_group,'url_return'=>$ppo->url_return)).'"><img src="'._resource('img/tools/delete.png').'" /></a>';
                echo '<input type="checkbox" name="bool['.$dc->record->id_dc.'|'.$value->id_dcv.']" value="1" '.$value->checked.' /><input type="hidden" name="value[]" value="'.$dc->record->id_dc.'|'.$value->id_dcv.'" />';
    			echo '</li>';
            }
            echo '</ul>';
        }
        echo '</li>';
    }
?>
</ul>
<input type="submit" value="<?php _etag ('i18n', "copix:common.buttons.save"); ?>" />
<input type="button" value="<?php _etag ('i18n', 'copix:common.buttons.back'); ?>" onclick="javascript:document.location.href='<?php echo $ppo->url_return; ?>'">
</form>