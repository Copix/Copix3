<h2>{i18n key='pictures.title.deleteTheme'} : {$delTheme->name_tpic}</h2>
{if $count_pict>0}
 <p>{i18n key='pictures.messages.movePicture'}</p>
 <form action="{copixurl dest="pictures|admin|movePicture" id_tpic=$delTheme->id_tpic id_head=$id_head}" method="POST">
  <label for="moveTo">{i18n key='pictures.messages.destination'}</label>
  <select id="moveTo" name="moveTo">
  {foreach from=$themeList item=theme}
     {if $theme->id_tpic neq $delTheme->id_tpic}
        <option value="{$theme->id_tpic}">{$theme->name_tpic}</option>
     {/if}
  {/foreach}
  </select>
  <input type="submit" value="{i18n key="copix:common.buttons.select"}" />
{/if}
  <input type="button" value="{i18n key="copix:common.buttons.delete"}" onclick="javascript:document.location='{copixurl dest="pictures|admin|deleteTheme" id_tpic=$delTheme->id_tpic id_head=$id_head}'" />
  <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location='{copixurl dest="copixheadings|admin|" id_head=$id_head browse="pictures"}'" />
 </form>