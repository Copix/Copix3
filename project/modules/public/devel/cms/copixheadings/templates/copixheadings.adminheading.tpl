{if $writeEnabled}
   <p><a href="{copixurl dest="admin|create" id_head=$currentLevel}"><img src="{copixresource path="img/tools/new.png"}" alt="{i18n key="admin.buttons.new"}" />{i18n key="admin.buttons.new"}</a></p>
{/if}
{if $pasteEnabled}
	<p><a href="{copixurl dest="admin|paste" id_head=$currentLevel}">{i18n key="admin.command.paste"}</a></p>
{/if}

{if count ($arHeadings)}
<table class="CopixTable">
 <thead>
 <tr>
   <th>{i18n key="copixheadings.fields.caption_head"}</th>
   <th class="actions">{i18n key="admin.titleTab.commands"}</th>
 </tr>
 </thead>
 <tbody>
 {foreach from=$arHeadings item=heading}
    <tr {cycle values=',class="alternate"' name="CopixTable"}>
     <td>{$heading->caption_head|escape:html}</td>
     <td>
        {if $heading->profileInformation >= PROFILE_CCV_WRITE}<a title="{i18n key="admin.command.update"}" href="{copixurl dest="admin|prepareEdit" id_head=$heading->id_head}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="admin.command.update"}" /></a>{/if}
        {if $heading->profileInformation >= PROFILE_CCV_SHOW}<a  title="{i18n key="admin.command.browse"}" href="{copixurl dest="admin|" id_head=$heading->id_head browse="copixheadings"}"><img src="{copixresource path="img/tools/browse.png"}" alt="{i18n key="admin.command.browse"}" /></a>{/if}
        {if $heading->profileInformation >= PROFILE_CCV_WRITE}<a title="{i18n key="admin.command.cut"}" href="{copixurl dest="admin|cut" id_head=$heading->id_head}"><img src="{copixresource path="img/tools/cut.png"}" alt="{i18n key="admin.command.cut"}" /></a>{/if}
        {if $heading->canDelete}<a title="{i18n key="admin.command.delete"}" href="{copixurl dest="admin|delete" id=$heading->id_head}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="admin.command.delete"}" /></a>{/if}
     </td>
    </tr>
 {/foreach}
 </tbody>
</table>
{else}
   <p>{i18n key="headings.messages.noSubHeadings"}</p>
{/if}