<form action="{copixurl dest="admincategory|refactor"}" method="POST">
<input type="hidden" name="idc" value="{$ppo->idc}">
{i18n key='test.test.refactor'}
{i18n key='test.test.refactor'}{select values=$ppo->arCategories name=new selected=$ppo->arCategories->caption_ctest objectMap="id_ctest;caption_ctest}
<input type="submit" name="send" value="Déplacer les test">
</form>