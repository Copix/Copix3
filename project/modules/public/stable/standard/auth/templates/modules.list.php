<form action='<?php echo _url('auth|module|record'); ?>' method="POST" >
<input type="hidden" name="id_group" value="<?php echo $ppo->id_group; ?>" />
<input type="hidden" name="handler_group" value="<?php echo $ppo->handler_group; ?>" />
<input type="hidden" name="url_return" value="<?php echo $ppo->url_return; ?>" />
<ul>
<?php

    foreach ($ppo->list as $module) {
        echo '<li>';
        echo $module->name;
        if (isset($module->delete) && $module->delete) {
            echo '<a href="'._url('auth|module|deleteModule',array('moduleToDelete'=>$mc->name,"id_group"=>$ppo->id_group,'handler_group'=>$ppo->handler_group,'url_return'=>$ppo->url_return)).'"><img src="'._resource('img/tools/delete.png').'" /></a>';
        }
        if (count($module->data) > 0) {
            echo '<ul>';
            foreach ($module->data as $mc) {
                echo '<li>';
                echo $mc->record->name_mc;
                if (isset($mc->delete) && $mc->delete) {
                    echo '<a href="'._url('auth|module|delete',array('id_mc'=>$mc->record->id_mc,"id_group"=>$ppo->id_group,'handler_group'=>$ppo->handler_group,'url_return'=>$ppo->url_return)).'"><img src="'._resource('img/tools/delete.png').'" /></a>';
                }                
                if (count($mc->data) <= 0) {
                    echo '<input type="checkbox" name="bool['.$mc->record->id_mc.']" value="1" '.$mc->checked.' /><input type="hidden" name="value[]" value="'.$mc->record->id_mc.'" />';
                } else {
                    echo '<ul>';
                    foreach ($mc->data as $value) {
                        echo '<li>'.$value->value_mcv.'('.$value->level_mcv.')';
                        if (isset($value->delete) && $value->delete) {
                            echo '<a href="'._url('auth|module|delete',array('id_mcv'=>$value->id_mcv,"id_group"=>$ppo->id_group,'handler_group'=>$ppo->handler_group,'url_return'=>$ppo->url_return)).'"><img src="'._resource('img/tools/delete.png').'" /></a>';
                        }
                        echo '<input type="checkbox" name="bool['.$mc->record->id_mc.'|'.$value->id_mcv.']" value="1" '.$value->checked.' /><input type="hidden" name="value[]" value="'.$mc->record->id_mc.'|'.$value->id_mcv.'" />';
        				echo '</li>';
                    }
                    echo '</ul>';
                }
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