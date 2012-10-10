{foreach from=$ppo->extends item=extend }
	<tr>
		<th>
			<label for="authextend_{$extend->module}_{$extend->id}" >
				{$extend->caption|escape}{if $extend->required}<span class="required" > *</span>{/if}
				{if  $extend->type=='picture' && $extend->value!=NULL}
					<br /><img src="{copixurl dest='authextend||getpicturevalue' 
					                 id_user=$ppo->id_user id_handler=$ppo->id_handler id_extend=$extend->id 
					                 width=$extend->parameters.width height=$extend->parameters.height }" 
					           alt="{i18n key='authextend|authextend.current_picture'}"   />
					<br />({i18n key='authextend|authextend.current_picture'})
				{/if}
			</label>
		</th>
		<td>
			{* Le champs est de type text *}
			{if $extend->type=='text'}
				<input type="text" id="authextend_{$extend->module}_{$extend->id}" name="authextend_{$extend->module}_{$extend->id}" value="{$extend->value}" maxlength="{$extend->parameters.maxlength}" />
				
				
			{* Le champs est de type image simple *}
			{else if  $extend->type=='picture'}
				<input type="hidden" name="MAX_FILE_SIZE"  value="{$extend->parameters.maxsize}" />
				<input type="file" id="authextend_{$extend->module}_{$extend->id}" name="authextend_{$extend->module}_{$extend->id}" />
				{if $extend->value!=NULL}
					<br />
					<input type="checkbox" id="authextend_{$extend->module}_{$extend->id}_remove" name="authextend_{$extend->module}_{$extend->id}_remove" value="1" />
					<input type="hidden" name="authextend_{$extend->module}_{$extend->id}_exist" value="1" />
					<label for="authextend_{$extend->module}_{$extend->id}_remove" >{i18n key='authextend|authextend.remove_picture'}</label>
				{/if}
			{/if}
		</td>
	</tr>
{/foreach}