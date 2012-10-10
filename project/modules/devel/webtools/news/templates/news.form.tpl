{if count ($ppo->arErrors)}
<div class="errorMessage">
<h1>Erreurs</h1>
{ulli values=$ppo->arErrors}
</div>
{/if}

<form action="{copixurl dest="admin|valid"}" method="POST" class="news_form">
<table class="CopixVerticalTable">
 <tr>
  <th>Titre</th>
  <td><input class="news_input" name="title_news" value="{$ppo->toEdit->title_news|escape}" /></td>
 </tr>

 <tr>
  <th>Date</th>
  <td>
  {calendar name=date_news value=$ppo->toEdit->date_news|datei18n}</td>
 </tr>
 <tr>
  <th>Heure</th>
  <td>{html_select_time prefix=news_ use_24_hours=true field_array=heure_news_ar time=$ppo->toEdit->heure_news|time}</td>
 </tr>

 <tr>
  <th>Résumé</th>
  <td><textarea name=summary_news>{$ppo->toEdit->summary_news|escape}</textarea></td>
 </tr>
{if $ppo->multipleRedac}
	<tr>
		<th>Choix du type d'édition :</th>
		<td>
			<ul>
				{foreach from=$ppo->typeRedacAvailable item=redacAvailable}
					<li><a href="{copixurl dest="#" typeRedac=$redacAvailable notxml=true}">{$redacAvailable}</a>{if $redacAvailable eq $ppo->typeRedac}*{/if}</li>
				{/foreach}
			</ul>
		</td>
	</tr>
{/if}

 <tr>
  <th>Contenu</th>
  <td>
  	{if $ppo->typeRedac eq 'text'}
  		<textarea style="height: 300px" name=content_news>{$ppo->toEdit->content_news|strip_tags|escape}</textarea>
  	{elseif $ppo->typeRedac eq 'wiki'}
	  	{wikieditor name=content_news}{$ppo->toEdit->content_news}{/wikieditor}
  	{elseif $ppo->typeRedac eq 'wysiwyg'}
	  	{htmleditor name=content_news content=$ppo->toEdit->content_news}
  	{/if}
	<input type="hidden" name="typeRedac" value="{$ppo->typeRedac}" />
  </td>
 </tr>
 <tr>
  <th>Tags</th>
  <td>
   Séparer les tags par des virgules<br />
   <input type="text" class="news_input" name="tag_list_news" value="{$ppo->tagList}" />
  </td>
 </tr>
</table>

<input type="submit" value="Valider" />
<a href="{copixurl dest="|"}"><input type="button" value="Annuler" /></a>

</form>