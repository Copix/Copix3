<h2>{i18n key='params.moduleSelection'}</h2>
<form action="{copixurl dest="admin|parameters|selectModule"}" method="post" name="moduleSelect">
   <select name="choiceModule">
   {foreach key=cle from=$moduleList item=moduleCaption key=moduleId}
      <option value="{$moduleId}" {if $moduleId==$choiceModule}selected="selected"{/if}>{$moduleCaption}</option>
   {/foreach}
   </select>
   <input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
</form>

<h2>{i18n key='params.paramList'}</h2>

{if $error != ''}
  <div class="errorMessage">
  <h1>{i18n key="params.title.error"}</h1>
  {$error}
  </div>
  <br />
{/if}

{if count ($paramsList)}
<table class="CopixTable">
   <thead>
   <tr>
      <th>{i18n key='params.paramsName'}</th>
      <th>{i18n key='params.paramsDefault'}</th>
      <th>{i18n key='params.paramsCurrentValue'}</th>
      <th class="actions">{i18n key='params.paramsOptions'}</th>
   </tr>
   </thead>
   <tbody>
   {foreach from=$paramsList item=params}
      <tr {cycle values=',class="alternate"'}>
         <td>{$params.Caption|escape}</td>
         <td>{$params.DefaultStr|escape}</td>
         {if $params.Name==$editParam}
			<form
				action="{copixurl dest=admin|parameters|valid choiceModule=$choiceModule idFirst=$choiceModule idSecond=$params.Name}"
				method="post">
			<td>
				{if $params.Type == 'bool'}
				<input type="radio" name="value" value="1" id="valueOui" {if $params.Value == 1}checked="checked"{/if} /><label for="valueOui">Oui</label>
				<input type="radio" name="value" value="0" id="valueNon" {if $params.Value == 0}checked="checked"{/if} /><label for="valueNon">Non</label>
				{elseif $params.Type == 'int'}
				<input type="text" name="value" value="{$params.Value|escape}" size="15" />
				{elseif $params.Type == 'select'}
				<select name="value">
					{foreach from=$params.ListValues|toarray item=item key=key}
					<option value="{$key}" {if $params.Value == $key}selected="selected"{/if}>{$item}</option>
					{/foreach}
				</select>
				{elseif $params.Type == 'multiSelect'}
				<select name="value" multiple="multiple" size="3">
					{foreach from=$params.ListValues|toarray item=item key=key}
					<option value="{$key}" {if $params.Value == $key}selected="selected"{/if}>{$item}</option>
					{/foreach}
				</select>
				{else}
				<input type="text" name="value" value="{$params.Value|escape}" size="20" />
				{/if}
			</td>
            <td><input type="image" src="{copixresource path="img/tools/valid.png"}" value="{i18n key="copix:common.buttons.ok"}" title="{i18n key="copix:common.buttons.ok"}" /></form><a href="{copixurl dest="admin|parameters|" choiceModule=$choiceModule}"><img src="{copixresource path="img/tools/cancel.png"}" title="{i18n key="copix:common.buttons.cancel"}" alt="{i18n key="copix:common.buttons.cancel"}" /></a></td>
         {else}
            <td>{$params.ValueStr|escape}</td>
            <td><a href="{copixurl dest="admin|parameters|" choiceModule=$choiceModule editParam=$params.Name}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key='copix:common.buttons.update'}" title="{i18n key='copix:common.buttons.update'}" /></a></td>
         {/if}
      </tr>
   {/foreach}
   </tbody>
</table>
{else}
<p>{i18n key='params.noParam'}</p>
{/if}

<br />
<input type="button" value="{i18n key="copix:common.buttons.back"}" onclick="javascript:window.location='{copixurl dest="admin||"}'" />
