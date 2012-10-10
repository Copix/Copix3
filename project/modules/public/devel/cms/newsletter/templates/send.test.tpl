<h2>{i18n key="newsletter.title.sendTest" titlePage=$newsletter->title_cmsp}</h2>
{if $error}
{$error}
{/if}
<form action="{copixurl dest="newsletter|admin|sendTest" id=$newsletter->publicid_cmsp}" method="post">
{i18n key="newsletter.messages.mailTest"} :
<input type="text" name="test_mail" value="" />
<input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
<input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location='{copixurl dest="copixheadings|admin|" level=$newsletter->id_head browse="newsletter"}'" />
</form>
