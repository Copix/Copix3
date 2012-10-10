<table style="width:100%;border-collapse:collapse">
   <tr>
      <td style="border: 1px dotted #000;width:150px;padding:5px;vertical-align:top">{i18n key="admin.messages.chooseHeading"} :
         {i18n key="admin.messages.chooseHeading" assign=text}
         {copixresource assign=img path="img/tools/down.png"}{popupinformation img="$img" handler="onclick" divclass="headingTree" text=$text}
           {$headingsTree}
          {/popupinformation}
      </td>
      <td style="border: 1px dotted #000;padding-top:5px;padding-left:5px;padding-bottom:0px">{i18n key="admin.messages.currentPath"} :
         <ul class="copixArianeLink">{i18n key="admin.messages.chooseHeading" assign=text}
           <li><a href="{copixurl dest="admin|"  browse=$browse}">{i18n key="headings.message.root"}</a></li>
          {foreach from=$path item=dir name=pathLoop}
         	   <li>{if $dir->profileInformation >= PROFILE_CCV_SHOW}<a href="{copixurl dest="admin|" id_head=$dir->id_head browse=$browse}">{/if}{$dir->caption_head}{if $dir->profileInformation >= PROFILE_CCV_SHOW}</a>{/if}</li>
         	{/foreach}
         </ul>
      </td>
   </tr>
   <tr>
      <td style="border: 1px dotted #000;text-align:center;padding:5px;">
          <table style="width:100%">
             <tr>
              {foreach from=$modules item=module name=moduleLoop}
                <td  {if $browse==$module->name}{assign var=description value=$module->longDescription} style="border: 1px solid #000;background-color: #cccccc;" {/if}><a href="{copixurl dest="admin|" id_head=$currentLevel browse=$module->name}"><img  src="{$module->icon}" title="{$module->longDescription}" alt="{$module->shortDescription}" /></a><br />{$module->shortDescription}</td>
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
      </td>
      <td style="border: 1px dotted #000;vertical-align:top;padding:5px;"><h2>{$description}</h2>{$moduleZone}</td>
   </tr>
</table>