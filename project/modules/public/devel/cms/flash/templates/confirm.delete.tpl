{i18n key=document.messages.confirmDelete}&nbsp;{$toDelete->title_doc}
<br />
<br />
<input type="button" value="{i18n key=copix:common.buttons.valid}" onclick="javascript:document.location='{copixurl dest="document|admin|validDelete" id_doc=$toDelete->id_doc}'" />
<input type="button" value="{i18n key=copix:common.buttons.cancel}" onclick="javascript:document.location='{copixurl dest="document|admin|"}'" />
