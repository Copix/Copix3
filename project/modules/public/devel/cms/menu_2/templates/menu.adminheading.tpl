<h2>{i18n key="menu.title.path"}</h2>

<ul class="copixArianeLink">
{foreach from=$pathMenu item=menu}
   <li><a href="{copixurl dest="copixheadings|admin|" browse='menu_2' id_menu=$menu->id_menu id_head=$id_head}">{$menu->caption_menu}</a></li>
{/foreach}
</ul>

{if $arInheritedMenus !== null}
<table class="CopixTable">
<caption>{i18n key=menu.title.inheritedMenu}</caption>
<thead>
 <tr>
  <th>{i18n key=menu.title.caption_menu}</th>
  <th>{i18n key=menu.title.var_name_menu}</th>
  <th>{i18n key=menu.title.subCount}</th>
  <th>{i18n key=menu.title.id_head}</th>
 </tr>
</thead>
<tbody>
   {if count($arInheritedMenus) > 0}
   {foreach from=$arInheritedMenus item=child name="boucle"}
     <tr {cycle values=',class="alternate"'}>
        <td>{$child->caption_menu}</td>
        <td>{$child->var_name_menu}</td>
        <td>{$child->nbchilds_menu}</td>
        <td><a href='{copixurl dest=# id_head=$child->id_head}'>{i18n key="menu.messages.heading"}</a></td>
     </tr>
   {/foreach}
   {else}
      <tr><td colspan="4">{i18n key="menu.messages.noInheritedMenu"}</td></tr>
   {/if}
</tbody>
</table>
{/if}

<table class="CopixTable">
<caption>{i18n key=menu.title.menuTable}</caption>
<thead>
 <tr>
  <th>{i18n key=menu.title.caption_menu}</th>
  {if $arInheritedMenus !== null}
  <th>{i18n key=menu.title.var_name_menu}</th>
  {/if}
  <th>{i18n key=menu.title.subCount}</th>
  <th>
   {i18n key=copix:common.actions.title}
   {if $currentMenu->userRight eq $adminValue}
      <a href="{copixurl dest=admin|create father_menu=$currentMenu->id_menu id_head=$id_head}"><img src="{copixresource path="img/tools/new.png"}" title="{i18n key=copix:common.buttons.new}" alt="{i18n key=copix:common.buttons.new}"/></a>
      {if $pasteEnabled}
         <a href="{copixurl dest=admin|paste father_menu=$currentMenu->id_menu id_head=$id_head}"><img src="{copixresource path="img/tools/paste.png"}" alt="{i18n key=copix:common.buttons.paste}" title="{i18n key=copix:common.buttons.paste}"/></a>
      {/if}
   {/if}
  </th>
 </tr>
</thead>
<tbody>
   {if count($arMenus) > 0}
   {foreach from=$arMenus item=child name="boucle"}
     <tr {cycle values=',class="alternate"'}>
        <td>{$child->caption_menu}</td>
        {if $arInheritedMenus !== null}
       <td>{$child->var_name_menu}</td>
       {/if}
        <td>{$child->nbchilds_menu}</td>
        <td>
         
         <a href="{copixurl dest=admin|list id_menu=$child->id_menu id_head=$id_head}"><img src="{copixresource path="img/tools/browse.png"}" alt="{i18n key=copix:common.buttons.browse}" title="{i18n key=copix:common.buttons.browse}" /></a>
         <a {$child->htmlLink}>
            <img src="{copixresource path="img/tools/show.png"}" alt="{i18n key=copix:common.buttons.show}" title="{i18n key=copix:common.buttons.show}" />
         </a>
         {if $child->userRight eq $adminValue}
            <a href="{copixurl dest=admin|prepareEdit id_menu=$child->id_menu id_head=$id_head}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key=copix:common.buttons.update}"  title="{i18n key=copix:common.buttons.update}"/></a>
            <a href="{copixurl dest=admin|delete id_menu=$child->id_menu id_head=$id_head}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key=copix:common.buttons.delete}" title="{i18n key=copix:common.buttons.delete}" /></a>
            {if $child->isonline_menu}
               <a href="{copixurl dest=admin|toggleDisplay id_menu=$child->id_menu id_head=$id_head}"><img src="{copixresource path="img/tools/enable.png"}" alt="{i18n key=copix:common.buttons.enable}" title="{i18n key=copix:common.buttons.enable}" /></a>
            {else}
               <a href="{copixurl dest=admin|toggleDisplay id_menu=$child->id_menu id_head=$id_head}"><img src="{copixresource path="img/tools/disable.png"}" alt="{i18n key=copix:common.buttons.disable}" title="{i18n key=copix:common.buttons.disable}" /></a>
            {/if}
         {/if}
         {if $currentMenu->userRight eq $adminValue}
            {if !$smarty.foreach.boucle.first}<a href="{copixurl dest=admin|up id_menu=$child->id_menu id_head=$id_head}"><img src="{copixresource path="img/tools/up.png"}" alt="{i18n key=copix:common.buttons.moveup}" title="{i18n key=copix:common.buttons.moveup}" /></a>{else}&nbsp;&nbsp;&nbsp;&nbsp;{/if} &nbsp;
            {if !$smarty.foreach.boucle.last}<a href="{copixurl dest=admin|down id_menu=$child->id_menu id_head=$id_head}"><img src="{copixresource path="img/tools/down.png"}" alt="{i18n key=copix:common.buttons.movedown}"  title="{i18n key=copix:common.buttons.movedown}" /></a>{else}&nbsp;&nbsp;&nbsp;&nbsp;{/if}
         {/if}
         {if $child->userRight eq $adminValue}
            <a href="{copixurl dest=admin|cut id_menu=$child->id_menu id_head=$id_head}"><img src="{copixresource path="img/tools/cut.png"}" alt="{i18n key=copix:common.buttons.cut}"  title="{i18n key=copix:common.buttons.cut}" /></a>
         {/if}
        </td>
     </tr>
   {/foreach}
   {else}
      <tr><td colspan="3">{i18n key="menu.messages.noMenu"}</td></tr>
   {/if}
</tbody>
</table>