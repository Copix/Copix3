{if isset($title)}<h2>{$title}</h2>{/if}
{if count($arPath)>0}
   {foreach from=$arPath item="menu" name="boucle"}
      {if !$smarty.foreach.boucle.first} - {/if}
      {if $menu->htmlLink}
         <a {$menu->htmlLink}>{$menu->caption_menu}</a>
      {else}
         {$menu->caption_menu}
      {/if}
   {/foreach}
{/if}