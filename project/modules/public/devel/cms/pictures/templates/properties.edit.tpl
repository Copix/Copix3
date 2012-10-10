{if $showErrors}
<div class="errorMessage">
<h1>{i18n key="copix:common.messages.error"}</h1>
   <ul>
   {foreach from=$errors item=message}
     <li>{$message}</li>
   {/foreach}
   {if $invalidParams}
   <li>{i18n key='pictures.messages.wrongMaxX'}{$invalidParams->maxX}</li>
   <li>{i18n key='pictures.messages.wrongMaxY'}{$invalidParams->maxY}</li>
   <li>{i18n key='pictures.messages.wrongMaxWeight'}{$invalidParams->maxWeight}</li>
   <li>{i18n key='pictures.messages.wrongMaxFormat'}
    {foreach from=$invalidParams->invalidFormat item=format}
      {$format}&nbsp;
    {/foreach}
   {/if}
   </ul>
</div>
{/if}
<form action="{copixurl dest="pictures|admin|validProperties"}" method="post">

<table class="copixTable">
   <tr><th>{i18n key='dao.picturesheadings.fields.caption_head'} *</th><td>{$heading}</td>
   </tr>
   <tr><th>{i18n key='dao.picturesheadings.fields.maxX_cpic'} *</th><td><input type="text" name="maxX_cpic" size="10" value="{$toEdit->maxX_cpic|escape}" /></td>
   </tr>
   <tr><th>{i18n key='dao.picturesheadings.fields.maxY_cpic'} *</th><td><input type="text" name="maxY_cpic" size="10" value="{$toEdit->maxY_cpic|escape}" /></td>
   </tr>
   <tr><th>{i18n key='dao.picturesheadings.fields.maxWeight_cpic'} *</th><td><input type="text" name="maxWeight_cpic" size="10" value="{$toEdit->maxWeight_cpic|escape}" /></td>
   </tr>
   <tr><th>{i18n key='dao.picturesheadings.fields.format_cpic'} *</th><td>
      <ul>
         {foreach from=$formatList item=format}
         <li><input type="checkbox" name="format[]" value="{$format}" {foreach from=$editCatFormat item=formatCat}{if $formatCat==$format}checked="checked"{/if}{/foreach}/>
         {$format}</li>
         {/foreach}
      </ul>
      </td>
   </tr>
</table>
<input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
<input type="button" onclick="javascript:window.location='{copixurl dest="copixheadings|admin|" id_head=$toEdit->id_head browse="pictures" kind="1"}'" value="{i18n key="copix:common.buttons.cancel"}" />
</form>
