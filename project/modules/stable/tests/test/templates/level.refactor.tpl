<form action="{copixurl dest="adminlevel|refactor"}" method="POST">
<input type="hidden" name="id" value="{$ppo->id}">
{i18n key='copixtest.test.refactor'}
{select values=$ppo->arLevels name=new selected=$ppo->arLevels->caption_level objectMap="id_level;caption_level"}
<input type="submit" name="send" value="Déplacer les test">
</form>
<br />
<a href="{copixurl dest="admin|default"}">
<input type="button" onclick="location.href='{copixurl dest="admin|default"}'" style="width:100px" value="<?php _etag('i18n', array('key' => 'copixtest.historyback')); ?>">
 </a>