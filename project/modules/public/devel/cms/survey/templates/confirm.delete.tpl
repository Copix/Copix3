{i18n key=survey.messages.confirmDelete}&nbsp;{$toDelete->title_svy}
<br />
<input type="button" value="{i18n key=copix:common.buttons.valid}" onclick="javascript:document.location='{copixurl dest="survey|admin|validDelete" id_svy=$toDelete->id_svy}'" />
<input type="button" value="{i18n key=copix:common.buttons.cancel}" onclick="javascript:document.location='{copixurl dest="survey|admin|"}'" />