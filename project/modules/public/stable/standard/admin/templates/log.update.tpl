{if count ($ppo->arErrors)}
  {if count ($ppo->arErrors) == 1}
    {assign var=title_key value='logs.error'}
  {else}
    {assign var=title_key value='logs.errors'}
  {/if}
  <div class="errorMessage">
  <h1>{i18n key="$title_key"}</h1>
  {ulli values=$ppo->arErrors}
  </div>
{/if}

{mootools}

{literal}
<script type="text/javascript">
function onChangeType (type) {
	$('configEmail').style.display = (type.value == 'email') ? '' : 'none';
}
</script>
{/literal}

<form action="{copixurl dest="log|valid"}" method="POST">
<table class="CopixTable">
	<tr>
		<th>{i18n key="logs.name"}</th>
		<td>{$ppo->log.name}</td>
	</tr>
	<tr class="alternate">
		<th>{i18n key="logs.strategy"}</th>
		<td>
			<table>
				<tr>
					<td>{i18n key=logs.standard}</td>
					<td>{select name=strategy values=$ppo->arStrategies	selected=$ppo->log.strategy objectMap="id;caption" extra="onchange=onChangeType(this)"}</td>
				</tr>
				<tr id="configEmail" {if $ppo->log.strategy neq 'email'} style="display: none" {/if}>
					<td>{i18n key="logs.email"}</td>
					<td><input type="text" name="email" value="{$ppo->log.email}" /></td>
				</tr>
				<tr>
					<td>{i18n key=logs.custom}</td>
					<td><input type="text" value="" name="strategy_class" /></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<th>{i18n key="logs.handle"}</th>
		<td>
			{if !is_array ($ppo->log.handle)}
		 	{i18n key="logs.handleAll"}
	    	{/if}
			<table>
				{if is_array ($ppo->log.handle)} 
		 		{foreach from=$ppo->log.handle item=handle}
		 		<tr>
           			<td>{$handle}</td>
           			<td><a href="{copixurl dest="log|removeHandle" handle=$handle}"> <img src="{copixresource path="img/tools/delete.png"}" /></a></td>
         		</tr>
  		 		{/foreach}
				{/if}
         		<tr>
          			<td><input type="text" name="handle" /></td>
          			<td><input type=image src="{copixresource path="img/tools/add.png"}" /></td>
		 		</tr>
			</table>
			
			{if !is_array ($ppo->log.handle)}
		 	{i18n key="logs.handleExcept"}	    
			<table>
				{if is_array ($ppo->log.handleExcept)} 
		 		{foreach from=$ppo->log.handleExcept item=handle}
		 		<tr>
           			<td>{$handle}</td>
           			<td><a href="{copixurl dest="log|removeHandleExcept" handleExcept=$handle}"> <img src="{copixresource path="img/tools/delete.png"}" /></a></td>
         		</tr>
  		 		{/foreach}
				{/if}
         		<tr>
          			<td><input type="text" name="handleExcept" /></td>
          			<td><input type=image src="{copixresource path="img/tools/add.png"}" /></td>
		 		</tr>
			</table>
			{/if}
	 	</td>
	</tr>
	<tr class="alternate">
		<th>{i18n key="logs.from"}</th>
		<td>{select name="level" values=$ppo->arLevel objectMap="id;caption"
		selected=$ppo->log.level}</td>
	</tr>
	<tr class="alternate">
		<th>{i18n key="logs.enabled"}</th>
		<td><input type="checkbox" value="1" name="enabled" {if $ppo->log.enabled}checked="checked"{/if} /></td>
	</tr>
</table>

<input type="submit" value="{i18n key="copix:common.buttons.valid"}" name="save" /> 
<a href="{copixurl dest="admin|log|admin"}"><input type="button" value="{i18n key="copix:common.buttons.back"}" /></a>
</form>