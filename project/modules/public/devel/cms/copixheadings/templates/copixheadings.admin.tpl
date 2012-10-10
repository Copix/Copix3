<div style="margin: 5px">
<div style="border: 1px dotted #000;">
   <div style="float:left;margin: 5px;margin-top:8px;width:100px;">
      {i18n key="admin.messages.chooseHeading"} :
      {i18n key="admin.messages.chooseHeading" assign=text}
      {copixresource assign=img path="img/tools/down.png"}{popupinformation img="$img" handler="onclick" divclass="headingTree" text=$text}
        {$headingsTree}
       {/popupinformation}
   </div>
   <div style="float:left;padding-left: 15px;padding-top:12px;border-left: 1px dotted #000">
   {i18n key="admin.messages.currentPath"} :
   <ul class="copixArianeLink">{i18n key="admin.messages.chooseHeading" assign=text}
     <li><a href="{copixurl dest="admin|"  browse=$browse}">{i18n key="headings.message.root"}</a></li>
    {foreach from=$path item=dir name=pathLoop}
   	   <li>{if $dir->profileInformation >= PROFILE_CCV_SHOW}<a href="{copixurl dest="admin|" id_head=$dir->id_head browse=$browse}">{/if}{$dir->caption_head}{if $dir->profileInformation >= PROFILE_CCV_SHOW}</a>{/if}</li>
   	{/foreach}
   </ul>
   </div>
   <div style="border: 1px dotted #000;float:left;margin-left:0px;">
   <table style="width:100%;padding-top:5px">
    <tr>
     {foreach from=$modules item=module name=moduleLoop}
       <td   style="text-align:center;{if $browse==$module->name}border: 1px solid #000;background-color: #cccccc;{/if}"><a href="{copixurl dest="admin|" id_head=$currentLevel browse=$module->name}"><img  src="{$module->icon}" title="{$module->longDescription}" alt="{$module->shortDescription}" /></a><br />{$module->shortDescription}</td>
       {if ($smarty.foreach.moduleLoop.iteration mod $moduleLineBreakCount) == 0}
        </tr><tr>
       {/if}
     {/foreach}

     {assign var=missingTd value=$smarty.foreach.moduleLoop.iteration%$moduleLineBreakCount}

     {if $missingTd != $moduleLineBreakCount}
        {section loop=$moduleLineBreakCount-$missingTd name=missingTdLoop}
           <td>&nbsp;</td>
        {/section}
     {/if}
    </tr>
   </table>
   </div>
</div>
<br />
<div style="float:left; margin-left:0px;border: 1px dotted #000;padding: 5px;">
{$moduleZone}
</div>
</div>
