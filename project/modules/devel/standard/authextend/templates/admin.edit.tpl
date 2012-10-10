<h2>{i18n key='authextend.adminlink'}</h2>

{if $ppo->errors}
	<div class="errorMessage">
 		<h1>{i18n key='copix:common.messages.error'}</h1>
		{ulli values=$ppo->errors}
	</div>
{/if}

<div>
	
	
	<form id="extend_form" action="" method="post" >
		
		<table class="CopixVerticalTable" >
			<tr>
				<th style="width:170px;" >{i18n key='authextend.Type' }</th>
				<th style="width: 90px;" >{i18n key='authextend.Name' }</th>
				<th style="width:300px;" >{i18n key='authextend.Label' }</th>
				<th style="width: 60px;" >{i18n key='authextend.Required' }</th>
				<th style="width: 60px;" >{i18n key='authextend.Active' }</th>
				<th style="width:500px;" >{i18n key='authextend.Options' }</th>
				<th style="width: 90px;" >{i18n key='authextend.Action' }</th>
			</tr>
			
			{foreach from=$ppo->extends key=key item=extend}
				<tr id="action_mode_{$extend->id}" class="action_mode" >
					<td style="vertical-align:top;" >
						<img src="{$extend->iconType}" alt="{$extend->captionType}" />
						{$extend->captionType}
					</td>
					
					<td style="vertical-align:top;" >
						{$extend->name|escape}
					</td>
					
					<td style="vertical-align:top;" >
						{$extend->caption|escape}
					</td>
					
					<td style="vertical-align:top;" >
						{if $extend->required}
							<img src="{copixresource path='/img/tools/enable.png' }" alt="{i18n key='copix:common.buttons.enable'}" />
						{else}
							<img src="{copixresource path='/img/tools/disable.png' }" alt="{i18n key='copix:common.buttons.disable'}" />
						{/if}
					</td>
					<td style="vertical-align:top;" >
						{if $extend->active}
							<img src="{copixresource path='/img/tools/enable.png' }" alt="{i18n key='copix:common.buttons.enable'}" />
						{else}
							<img src="{copixresource path='/img/tools/disable.png' }" alt="{i18n key='copix:common.buttons.disable'}" />
						{/if}
					</td>
					
					<td style="vertical-align:top;" >
						{if $extend->type == 'picture' }
							<div style="clear:both;" >
								<div style="float: left; width:210px;" >{i18n key='authextend.picture_size_max} : </div>
								<div style="float:left;" >{$extend->parameters.maxsize} {i18n key='authextend.Bytes' }</div>
							</div>
								
							<div style="clear:both;" >
								<div style="float: left; width:210px;" >{i18n key='authextend.Width' } : </div> 
								<div style="float:left;" >{$extend->parameters.width} {i18n key='authextend.Pixels' }</div>
							</div>
							
							<div style="clear:both;" >
								<div style="float: left; width:210px;" >{i18n key='authextend.Height' } : </div>
								<div style="float:left;" >{$extend->parameters.height} {i18n key='authextend.Pixels' }</div>
							</div>
								
						{elseif $extend->type == 'text'}
							
							<div style="clear:both;" >
								<div style="float: left; width:210px;" >{i18n key='authextend.char_size_max' } : </div>
								<div style="float:left;" >{$extend->parameters.maxlength}</div>
							</div>
							<div style="clear:both;" >
								<div style="float: left; width:210px;" >{i18n key='authextend.filtre} : </div>
								{assign var=keyFilter value=$extend->parameters.filter }
								<div style="float:left;" >{$ppo->arFiltersText.$keyFilter}</div>
							</div>
							
						{/if}
					</td>
					
					<td style="vertical-align:top;" >
						<a href="" onclick="editMode ('{$extend->id}'); return false;" >
							<img src="{copixresource path='/img/tools/update.png' }" alt="{i18n key='copix:copixform.button.edit'}" />
						</a>
						
						<a href="{copixurl dest='authextend|admin|delete' id=$extend->id }" >
							<img src="{copixresource path='/img/tools/trash.png' }" alt="{i18n key='copix:copixform.button.delete'}" />
						</a>
						
						{if $key > 0 }
							<a href="{copixurl dest='authextend|admin|up' id=$extend->id }" >
								<img src="{copixresource path='/img/tools/up.png' }" alt="{i18n key='copix:common.buttons.up'}" />
							</a>
						{else}
							<span style="font-size:1px;padding-left:13px;" >&nbsp;</span>
						{/if}
						
						{if $key < count($ppo->extends)-1 }
							<a href="{copixurl dest='authextend|admin|down' id=$extend->id }" >
								<img src="{copixresource path='/img/tools/down.png' }" alt="{i18n key='copix:common.buttons.down'}" />
							</a>
						{/if}
					</td>
				</tr>
				<tr id="edit_mode_{$extend->id}" class="edit_mode" style="display:none;" >
					<td style="vertical-align:top;" >
						<img src="{$extend->iconType}" alt="{$extend->captionType}" />
						{$extend->captionType}
						<input type="hidden" name="type_{$extend->id}" value="{$extend->type}" />
					</td>
					
					<td style="vertical-align:top;" >
						<input type="text" name="name_{$extend->id}" maxlength="50" style="width:97%;" value="{$extend->name|escape}" />
					</td>
					
					<td style="vertical-align:top;" >
						<input type="text" name="caption_{$extend->id}" maxlength="255" style="width:97%;" value="{$extend->caption|escape}" />
					</td>
					
					<td style="vertical-align:top;" >
						<input type="hidden" name="required_cb_{$extend->id}" value="0" />
						<input type="checkbox" name="required_{$extend->id}" value="1" {if $extend->required}checked="checked"{/if} />
					</td>
					
					<td style="vertical-align:top;" >
						<input type="hidden" name="active_cb_{$extend->id}" value="0" />
						<input type="checkbox" name="active_{$extend->id}" value="1" {if $extend->active}checked="checked"{/if} />
					</td>
					
					<td style="vertical-align:top;" >
						{if $extend->type == 'picture' }
							<div style="clear:both;" >
								<div style="float: left; width:210px;" > {i18n key='authextend.picture_size_max} : </div>
								{capture name=name}maxsize_{$extend->id}{/capture}
								{spinbutton name=$smarty.capture.name value=$extend->parameters.maxsize min=10 max=$ppo->MAX_FILE_SIZE integer=true style="width:80px;" }
								<div style="float:left;" > {i18n key='authextend.Bytes' }</div>
							</div>
								
							<div style="clear:both;" >
								<div style="float:left; width:210px;" > {i18n key='authextend.Width' } : </div>
								{capture name=name}width_{$extend->id}{/capture}
								{spinbutton name=$smarty.capture.name value=$extend->parameters.width min=1 integer=true style="width:80px;" } 
								<div style="float:left;" > {i18n key='authextend.Pixels' }</div>
							</div>
								
							<div style="clear:both;" >
								<div style="float:left; width:210px;" > {i18n key='authextend.Height' } : </div>
								{capture name=name}height_{$extend->id}{/capture}
								{spinbutton name=$smarty.capture.name value=$extend->parameters.height min=1 integer=true style="width:80px;" } 
								<div style="float:left;" > {i18n key='authextend.Pixels' }</div>
							</div>
						{elseif $extend->type == 'text'}
							<div style="clear:both;" >
								<div style="float: left; width:210px;" > {i18n key='authextend.char_size_max} : </div>
								{capture name=name}maxlength_{$extend->id}{/capture}
								{spinbutton name=$smarty.capture.name value=$extend->parameters.maxlength min=1 max=255 integer=true style="width:80px;" }
							</div>
							<div style="clear:both;" >
								<div style="float: left; width:210px;" >{i18n key='authextend.filtre} : </div>
								{capture name=name}filtersText_{$extend->id}{/capture}
								<div style="float: left;" >{select name=$smarty.capture.name values=$ppo->arFiltersText emptyShow=false selected=$extend->parameters.filter }</div>
							</div>
						{/if}
					</td>
					
					<td style="vertical-align:top;" >
						<a href="" onclick="editMode (); return false;" >
							<img src="{copixresource path='/img/tools/cancel.png' }" alt="{i18n key='copix:common.buttons.cancel'}" />
						</a>
						<input type="image" src="{copixresource path='/img/tools/ok.png' }" value="{i18n key='copix:common.buttons.ok'}" />
					</td>
				</tr>
			{/foreach}
			<tr id="insert_new_title" >
				<th colspan="7" >{i18n key='authextend.insert_input' }</th>
			</tr>
			
			<tr id="insert_new" >
				<td style="vertical-align:top;" >
					{select name="type" id="selectType" values=$ppo->arType emptyShow=false selected=$ppo->form->type }
				</td>
				
				<td style="vertical-align:top;" >
					<input type="text" name="name" maxlength="50" style="width:97%;" value="{$ppo->form->name|escape}" />
				</td>
				
				<td style="vertical-align:top;" >
					<input type="text" name="caption" maxlength="255" style="width:97%;" value="{$ppo->form->caption|escape}" />
				</td>
				
				<td style="vertical-align:top;" >
					<input type="hidden" name="checkbox_cb" value="0" />
					<input type="checkbox" name="required" value="1" {if $ppo->form->required}checked="checked"{/if} />
				</td>
				
				<td style="vertical-align:top;" >
					<input type="hidden" name="active_cb" value="0" />
					<input type="checkbox" name="active" value="1" {if $ppo->form->active}checked="checked"{/if} />
				</td>
				
				<td style="vertical-align:top;" >
					
					<div class="extend_type extend_type_picture" >
						
						<div style="clear:both;" >
							<div style="float: left; width:210px;" > {i18n key='authextend.picture_size_max} : </div>
							{spinbutton name='maxsize' value=$ppo->form->maxsize min=10 max=$ppo->MAX_FILE_SIZE integer=true style="width:80px;" }
							<div style="float:left;" > {i18n key='authextend.Bytes' }</div>
						</div>
							
						<div style="clear:both;" >
							<div style="float:left; width:210px;" > {i18n key='authextend.Width' } : </div>
							{spinbutton name='width' value=$ppo->form->width min=1 integer=true style="width:80px;" } 
							<div style="float:left;" > {i18n key='authextend.Pixels' }</div>
						</div>
							
						<div style="clear:both;" >
							<div style="float:left; width:210px;" > {i18n key='authextend.Height' } : </div>
							{spinbutton name='height' value=$ppo->form->height min=1 integer=true style="width:80px;" } 
							<div style="float:left;" > {i18n key='authextend.Pixels' }</div>
						</div>
						
					</div>
					<div class="extend_type extend_type_text" >
						<div style="clear:both;" >
							<div style="float: left; width:210px;" > {i18n key='authextend.char_size_max} : </div>
							{spinbutton name='maxlength' value= $ppo->form->maxlength min=1 max=255 integer=true style="width:80px;" }
						</div>
						<div style="clear:both;" >
							<div style="float: left; width:210px;" >{i18n key='authextend.filtre} : </div>
							<div style="float: left;" >{select name="filtersText" values=$ppo->arFiltersText emptyShow=false selected=$ppo->form->filtersText }</div>
						</div>
					</div>
				</td>
				<td style="vertical-align:top;" >
					<input type="hidden" name="adder" value="1" />
					<input type="image" name="add" src="{copixresource path='img/tools/add.png'}" value="{i18n key='copix:common.buttons.add'}" />
				</td>
			</tr>
			
		</table>
		
	</form>
	
</div>

{foreach from=$ppo->extends item=extend}

{/foreach}
<br />

{back url='admin||'}

{literal}
<script type="text/javascript">
	//<!--
		var typeChoice = function() {
			$$('.extend_type').each( function (elem) {
				elem.setStyle ('display', 'none');
			});
			$$('.extend_type_' + $('selectType').value).each( function (elem) {
				elem.setStyle ('display', 'block');
			});
		};
		
		var editMode = function (id) {

			$$('.action_mode').each (function (el) {
				el.setStyle('display', '');
			});
			$$('.edit_mode').each (function (el) {
				el.setStyle('display', 'none');
			});
			
			if  (id) {
				$('extend_form').action='{/literal}{copixurl dest='authextend|admin|update'}{literal}?id='+id;
				$('insert_new_title').setStyle('display', 'none');
				$('insert_new').setStyle('display', 'none');
				$('edit_mode_'+id).setStyle('display', '');
				$('action_mode_'+id).setStyle('display', 'none');
			} else {
				$('extend_form').action='';
				$('insert_new_title').setStyle('display', '');
				$('insert_new').setStyle('display', '');
			}
			return false;
		};
		
		$('selectType').addEvent ('change', function (elem) {
			typeChoice ();
		});
		
		typeChoice ();
		
		
	//!-->
</script>
{/literal}

