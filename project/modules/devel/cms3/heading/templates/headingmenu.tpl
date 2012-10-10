{copixresource path="img/tools/next.png" assign=img}

{if count($arHeadings)}
 {popupinformation handler=clickdelay img=$img divclass=cmsBreadcrumbPopupinfo}
 <ul>
  {foreach from=$arHeadings item=value key=cle}
 	 <li><a href="{copixurl dest="heading|element|" heading=$value->public_id_hei}"> {$value->caption_hei} </a></li>
  {/foreach}
  </ul>
 {/popupinformation}
{/if}