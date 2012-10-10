<form action="{copixurl dest="cache|valid"}" method="POST">
<table class="CopixTable">
 <tr>
  <th>{i18n key="cache.name"}</th>
  <td>{$ppo->cache.name}</td>
 </tr>
	<tr class="alternate">
		<th>{i18n key="cache.strategy"}</th>
		<td>
		<table>
			<tr>
				<td>{i18n key=cache.standard}</td>
				<td>{select name=strategy values=$ppo->arStrategies
				selected=$ppo->cache.strategy objectMap="id;caption"}</td>
			</tr>
			<tr>
				<td>{i18n key=cache.custom}</td>
				<td><input type="text" value="" name="strategy_class" /></td>
			</tr>
		</table>
		</td>
	</tr>

	<tr >
		<th>{i18n key="cache.link"}</th>
		<td>
		<table>
			{if is_array ($ppo->asLinked)} 
			 {foreach from=$ppo->asLinked item=linked}
			 <tr>
	           <td>{$linked}</td>
	           <td><a href="{copixurl dest="cache|removeLink" linkToRemove=$linked}"> <img src="{copixresource path="img/tools/delete.png"}" /></a></td>
	         </tr>
	  		 {/foreach}
			{/if}
			<tr>
				<td>{select name=link values=$ppo->arLink
				selected=$ppo->cache.link }</td>
				<td><input type=image src="{copixresource path="img/tools/add.png"}" /></td>
			</tr>
		</table>
		</td>
	</tr>
 
 <tr class="alternate">
 	<th>{i18n key="cache.dir"}</th>
		<td>
		<table>
			<tr>
				<td><input type="text" value="{$ppo->cache.dir}" name="dir" /></td>
			</tr>
		</table>
		</td> 	
 </tr>
 
  <tr class="alternate">
 	<th>{i18n key="cache.duration"}</th>
		<td>
		{if ($ppo->cache.duration == 0)}
		 {i18n key="cache.infinity"}
	    {/if}
		<table>
			<tr>
				<td><input size="5" type="text" value="{$ppo->cache.duration}" name="duration" /></td>
          		<td><input type=image src="{copixresource path="img/tools/add.png"}" /></td>
			</tr>
		</table>
		</td> 	
 </tr>
 
 <tr>
  <th>{i18n key="cache.enabled"}</th>
  <td><input type="checkbox" value="1" name="enabled" {if $ppo->cache.enabled}checked="checked"{/if} /></td>
 </tr>
</table>
<input type="submit" value="{i18n key="copix:common.buttons.valid"}" name="save" /> 
<a href="{copixurl dest="admin|cache|admin"}"><input type="button" value="{i18n key="copix:common.buttons.back"}" /></a>
</form>