{if count ($arTemplate)}
	{foreach from=$arTemplate key=module item=templates}
	<h2>{$module}</h2>
     <ul>
	  {foreach from=$templates key=templateid item=template}
       <li><a title="{$templateid}" href="{copixurl dest="admin|validForm" standardTemplate=$templateid editId=$editId}">{$template}</a></li>
      {/foreach}
     </ul>
    {/foreach}
{/if}

{if count ($modifiedTemplates)}
<hr />
	{foreach from=$modifiedTemplates key=theme item=modules}
		<h2>{$theme}</h2>
		     <ul>		
		{foreach from=$modules key=module item=templates}
			<h3>{$module}</h3>
			     <ul>
				{foreach from=$templates item=modifiedTemplate}
			       <li><a title="{$modifiedTemplate->qualifier_ctpl}" href="{copixurl dest="admin|validForm" standardTemplate=$modifiedTemplate->qualifier_ctpl sourceTemplate=$modifiedTemplate->publicid_ctpl editId=$editId}">{$modifiedTemplate->caption_ctpl}</a></li>
				{/foreach}
				</ul>
		{/foreach}
			</ul>
    {/foreach}
	</ul>
{/if}

{if count ($newTemplates)}
<hr />
	{foreach from=$newTemplates key=theme item=modules}
		<h2>{$theme}</h2>
		     <ul>		
		{foreach from=$modules key=module item=templates}
			<h3>{$module}</h3>
			     <ul>
					{foreach from=$templates item=newTemplate}
				       <li><a title="{$newTemplate->modulequalifier_ctpl}" href="{copixurl dest="admin|validForm" modulequalifier_ctpl=$newTemplate->modulequalifier_ctpl standardTemplate='' sourceTemplate=$newTemplate->id_ctpl editId=$editId}">{$newTemplate->caption_ctpl}</a></li>
				    {/foreach}
				</ul>
		{/foreach}
			</ul>
    {/foreach}
	</ul>
{/if}