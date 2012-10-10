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

<br />
<form action="{copixurl dest="menu|adminmenus|valid"}">
<input type="hidden" name="id_menu" value="{$ppo->toEdit->id_menu}" />
{i18n key="admin.menu_name"}
<input type="text" name="name_menu" value="{$ppo->toEdit->name_menu}" />
<br /><br />
<input type="submit" value="{i18n key="admin.edit"}" />
<input type="button" value="{i18n key="admin.back"}" onclick="document.location='{copixurl dest=menu|adminmenus|}'" />
</form>