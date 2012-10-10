<h2>{$bug->name_bug}</h2>
{literal}
<style>
.CopixTable {
	
}

.CopixTable th{
	width: 20%
}

.CopixTable th, .CopixTable td{
	vertical-align: top;
	padding: 8px;
}

.CopixTable td.bugdesc {
	border: 1px solid black;
	font-family: Monospace;
}
</style>
{/literal}
<p>
<a href="{copixurl dest="bugtrax||"}"><img src="{copixresource path="img/tools/back.png"}" /> {i18n key="copix:common.buttons.back"}</a>
</p>
<p>
<table class="CopixTable">
<tr>
	<th>Créé le</th>
	<td>{$ppo->bug->date_bug}</td>
</tr>
<tr>
	<th>Modifié le</th>
	<td>{$ppo->bug->modificationdate_bug}</td>
</tr>
<tr>
	<th>Etat</th>
	<td>{$ppo->bug->state_bug}</td>
</tr>
<tr>
	<th>Créé par</th>
	<td>{$ppo->bug->author_bug}</td>
</tr>
<tr>
	<th>Rapport</th>
	<td class="bugdesc">{$ppo->bug->description_bug|htmlentities|nl2br}</td>
</tr>
</table>
</p>
<p>
<a href="{copixurl dest="bugtrax||"}"><img src="{copixresource path="img/tools/back.png"}" /> {i18n key="copix:common.buttons.back"}</a>
</p>
<p>
<h2>Commentaires</h2>
{copixzone process="comments|comment" id="module;group;action;id_bug" required=false}
</p>
<p>
<a href="{copixurl dest="bugtrax||"}"><img src="{copixresource path="img/tools/back.png"}" /> {i18n key="copix:common.buttons.back"}</a>
</p>
