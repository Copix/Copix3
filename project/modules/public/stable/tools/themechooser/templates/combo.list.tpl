<form name="themechooser" action="{copixurl dest='themechooser||doSelectTheme'}">
{select name='id_ctpt' values=$arThemes selected=$selectedTheme objectMap='id;name'}
<input type="submit" value={i18n key="copix:common.buttons.valid"}>
</form>