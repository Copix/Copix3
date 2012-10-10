<form action="{copixurl dest="menu|adminitems|valid"}" method="post">
<input type="hidden" name="id_parent" value="{$ppo->toEdit->id_parent_item}" />
<input type="hidden" name="id_menu" value="{$ppo->toEdit->id_menu}" />
<input type="hidden" name="id_item" value="{$ppo->toEdit->id_item}" />

{if count ($ppo->arErrors)}
<br />
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

<table>
  <tr>
    <td>{i18n key="admin.name_item"}&nbsp;</td>
    <td><input type="text" name="name_item" id="name_item" value="{$ppo->toEdit->name_item}" /></td>
  </tr>
  <tr>
    <td>{i18n key="admin.link_item"}&nbsp;</td>
    <td><input type="text" name="link_item" value="{$ppo->toEdit->link_item}" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="submit" value="{$ppo->submit_caption}" />
      <input type="button" value="{i18n key="copix:common.buttons.back"}" onclick="document.location='{copixurl dest="menu|adminitems|" id_menu=$ppo->toEdit->id_menu}'" />
    </td>
  </tr>
</table>
</form>

{formfocus id="name_item"}