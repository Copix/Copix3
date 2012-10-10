<br />
<h2>{i18n key=admin.title_list_menus}</h2>
<br />

<table class="CopixVerticalTable">
  {assign var=alternate value=""}
  {foreach from=$ppo->arrMenus item=menu}  
  <tr>
    <td {$alternate}>{$menu->name_menu}</td>
    <td {$alternate} width="60">
      <a href="{copixurl dest="menu|adminmenus|edit" id_menu=$menu->id_menu}" title="{i18n key=admin.edit}"
        ><img src="{copixresource path=img/tools/select.png}" alt="{i18n key=admin.edit}" title="{i18n key=admin.edit}"
      /></a>
      <a href="{copixurl dest="menu|adminitems|" id_menu=$menu->id_menu}" title="{i18n key=admin.edititems}"
        ><img src="{copixresource path=img/tools/update.png}" alt="{i18n key=admin.edititems}" title="{i18n key=admin.edititems}"
      /></a>
      <a href="{copixurl dest="menu|adminmenus|delete" id_menu=$menu->id_menu}" title="{i18n key=admin.delete}"
        ><img src="{copixresource path=img/tools/delete.png}" alt="{i18n key=admin.delete}" title="{i18n key=admin.delete}"
      /></a>
    </td>
  </tr>
  {if $alternate == ''}
    {assign var=alternate value='class="alternate"'}
  {else}
    {assign var=alternate value=""}
  {/if}
  {/foreach}
</table>

<br />
<form action="{copixurl dest="menu|adminmenus|valid"}" method="post">
<h2>{i18n key=admin.addmenu}</h2>
<br />

{if count ($ppo->arErrors)}
  {if count ($ppo->arErrors) == 1}
    {assign var=title_key value='admin.error'}
  {else}
    {assign var=title_key value='admin.errors'}
  {/if}
  <div class="errorMessage">
  <h1>{i18n key="$title_key"}</h1>
  {ulli values=$ppo->arErrors}
  </div>
{/if}

<input type="text" name="name_menu" size="30" />
<input type="image" src="{copixresource path=img/tools/add.png}" title="{i18n key=admin.add}" />
</form>

<br />
<input type="button" value="{i18n key=admin.back}" onclick="document.location='{copixurl dest=admin||}'" />
<br />