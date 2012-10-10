{if count ($ppo->arErrors)}
  {if count ($ppo->arErrors) == 1}
    {assign var=title_key value='copix:common.messages.error'}
  {else}
    {assign var=title_key value='copix:common.messages.errors'}
  {/if}
  <div class="errorMessage">
  <h1>{i18n key="$title_key"}</h1>
  {ulli values=$ppo->arErrors}
  </div>
{/if}

<h2>{i18n key=admin.title_list_menus}</h2>
<table class="CopixVerticalTable">
  {assign var=alternate value=""}
  {foreach from=$ppo->arrMenus item=menu}  
  <tr {cycle values=',class="alternate"'}>
  {if ($ppo->editedMenu) && ($ppo->editedMenu->id_menu == $menu->id_menu)}
   <form action="{copixurl dest="adminmenus|valid"}" method="post">
    <td><input type="text" name="name_menu" id="name_menu_edit" value="{$ppo->editedMenu->name_menu}" /></td>
    <td width="60"><input type="image" src="{copixresource path=img/tools/valid.png}" />
    {copixurl dest="adminmenus|cancel" assign=cancel}
    {copixicon type="cancel" href=$cancel}
    </td>
    </td>
   </form>
  {else}
    <td>{$menu->name_menu}</td>
    <td width="60">
      {copixurl dest="menu|adminmenus|edit" id_menu=$menu->id_menu assign=update} 
      {copixicon type="update" href=$update}
      
      {copixurl dest="menu|adminitems|" id_menu=$menu->id_menu assign=show}
      {copixicon type="show" href=$show}

      {copixurl dest="menu|adminmenus|delete" id_menu=$menu->id_menu assign=delete}
      {copixicon type="delete" href=$delete}
    </td>
  {/if}
  </tr>
  {/foreach}
</table>

<h2>{i18n key=admin.addmenu}</h2>

<form action="{copixurl dest="menu|adminmenus|valid"}" method="post">
<input type="text" name="name_menu" id="name_menu" size="30" />
<input type="image" src="{copixresource path=img/tools/add.png}" title="{i18n key=copix:common.buttons.add}" value="{i18n key=copix:common.buttons.add}" />
</form>

<br />
<input type="button" value="{i18n key=copix:common.buttons.back}" onclick="document.location='{copixurl dest=admin||}'" />
<br />

{if ($ppo->editedMenu)}
{formfocus id="name_menu_edit"}
{else}
{formfocus id="name_menu"}
{/if}