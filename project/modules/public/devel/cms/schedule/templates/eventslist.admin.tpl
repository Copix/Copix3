{literal}
<script type="text/javascript">
//<![CDATA[
function validDelete(id){
	if(confirm("{/literal}{i18n key=schedule.message.confirmdelevent}{literal}","DELETE ?")){
		 document.location.href="index.php?module=schedule&desc=admin&action=doDelete&id_evnt="+id;
	}
}
//]]>
</script>
{/literal}

<a href="{copixurl dest="schedule|admin|"}">Retour à la liste de catégories</a><br />

{foreach from=$tabEvents key=keyEvents item=objEvents}
<h2>{$objEvents->title_evnt|stripslashes}&nbsp;&nbsp;</h2>
<br>
<a href="{copixurl dest="schedule|admin|getAdminEvent" id_evnt=$objEvents->id_evnt}">Modifier</a>
- <a href="#" onclick="validDelete();">Supprimer</a>
<br>
<p>{$objEvents->author_evnt}</p>

{/foreach}
<br /><a href="{copixurl dest="schedule|admin|getAdminEvent"}"><input type="button" value="Ajouter un événement"></a>
<br /><a href="{copixurl dest="schedule|admin|"}">Retour à la liste de catégories</a>

