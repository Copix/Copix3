{*
* formulaire d'édition d'un mail .
* params: toEdit : le mail  à éditer.
*}
{if $showErrors}
<div>
   <ul>
   {foreach from=$errors item=message}
     <li>{$message}</li>
   {/foreach}
   </ul>
</div>
{/if}

<form action="{copixurl dest="newsletter|mail|valid"}" method="post" class="copixForm">
<fieldset>
   <table>
      <tr>
        <th>{i18n key=dao.newslettermail.fields.mail_nlm}</th>
        <td><input size="48" type="text" value="{$toEdit->mail_nlm}" name="mail_nlm" /></td>
      </tr>
      <tr>
        <th>{i18n key=dao.newslettermail.fields.valid_nlm}</th>
        <td><input type="radio" value="1" name="valid_nlm" {if $toEdit->valid_nlm eq 1}checked="checked"{/if}/>{i18n key="newsletter.messages.statusEnabled"}
            <input type="radio" value="0" name="valid_nlm" {if $toEdit->valid_nlm eq 0}checked="checked"{/if}/>{i18n key="newsletter.messages.statusDisabled"}</td>
      </tr>
      <tr>
       <th>{i18n key=dao.newslettergroups.fields.name_nlg}</th>
       <td><ul>{foreach from=$groups item=group}
            <li><input type="checkbox" class="checkbox" name="id_nlg[]" value="{$group->id_nlg}" {if $group->checked}checked="checked"{/if} />{$group->name_nlg}</li>
            {/foreach}</td></ul>
      </tr>

   </table>
</fieldset>
   <p class="validButtons">
   <input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
   <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:window.location='{copixurl dest="newsletter|mail|cancelEdit"}'" />
   </p>
</form>
