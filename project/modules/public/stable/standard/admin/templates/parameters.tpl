<h2>{i18n key='params.moduleSelection'}</h2>
<form action="{copixurl dest="admin|parameters|selectModule"}" method="post" name="moduleSelect">
   <select name="choiceModule">
   {foreach key=cle from=$moduleList item=moduleCaption key=moduleId}
      <option value="{$moduleId}" {if $moduleId==$choiceModule}selected="selected"{/if}>{$moduleCaption}</option>
   {/foreach}
   </select>
   <input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
</form>

{if $error != ''}
  <div class="errorMessage">
  <h1>{i18n key="params.title.error"}</h1>
  {$error}
  </div>
  <br />
{/if}

{if count ($paramsList)}
{assign var=exGroup value=null}
{assign var=isFirst value=true}
{foreach from=$paramsList item=params}
{if ($params.Group neq $exGroup)}
{assign var=exGroup value=$params.Group}
{if (!$isFirst)}
   </tbody>
</table>
{/if}
{assign var=isFirst value=false}
<h2>
{if ($params.Group eq 'no-group')}
	{i18n key="params.group.no-group"}
{else}
	{$params.Group}
{/if}
</h2>
<table class="CopixTable">
   <thead>
   <tr>
      <th>&nbsp;{i18n key='params.paramsName'}&nbsp;</th>
      <th>&nbsp;{i18n key='params.paramsDefault'}&nbsp;</th>
      <th>&nbsp;{i18n key='params.paramsCurrentValue'}&nbsp;</th>
      <th class="actions">&nbsp;{i18n key='params.paramsOptions'}&nbsp;</th>
   </tr>
   </thead>
   <tbody>
   {/if}
   
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
            <td width="5px" align="right">
            	<input type="image" src="{copixresource path="img/tools/valid.png"}" value="{i18n key="copix:common.buttons.ok"}" title="{i18n key="copix:common.buttons.ok"}" />
            	</form>
            	<a href="{copixurl dest="admin|parameters|" choiceModule=$choiceModule}"><img src="{copixresource path="img/tools/cancel.png"}" title="{i18n key="copix:common.buttons.cancel"}" alt="{i18n key="copix:common.buttons.cancel"}" /></a>
            	&nbsp;
            </td>
         {else}
            <td>{$params.ValueStr|escape}</td>
            <td width="5px" align="right">
            	<a href="{copixurl dest="admin|parameters|" choiceModule=$choiceModule editParam=$params.Name}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key='copix:common.buttons.update'}" title="{i18n key='copix:common.buttons.update'}" /></a>
            	&nbsp;
            </td>
         {/if}
      </tr>
{/foreach}
</tbody>
</table>
{/if}

<br />
<input type="button" value="{i18n key="copix:common.buttons.back"}" onclick="javascript:window.location='{copixurl dest="admin||"}'" />
