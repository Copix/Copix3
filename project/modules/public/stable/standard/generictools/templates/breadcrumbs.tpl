{**
* Template par défaut du fil d'Ariane (CopixBreadcrumbs)
*
* @param $arBreadcrumbsItems : tableau de BreadcrumbsItem
* @param $title              : titre du fil d'ariane (facultatif)
*}
{assign var=nbBreadcrumbsItems value=$arBreadcrumbsItems|@count}
{if $nbBreadcrumbsItems > 0}
    <ul class="breadCrumbs">
        {if $title}
            <li class="rootCrumb">{$title|escape:"htmlall"}</li>
        {/if}
        {counter start=0 assign=i}
        {foreach from=$arBreadcrumbsItems item=breadcrumbsItem}
            {counter}
            {if $i < $nbBreadcrumbsItems}
                <li><a href="{$breadcrumbsItem->link}" title="{$breadcrumbsItem->text|escape:"htmlall"}">{$breadcrumbsItem->text|escape:"htmlall"}</a> &gt;</li>
            {else}
                <li class="lastCrumb">{$breadcrumbsItem->text|escape:"htmlall"}</li>
            {/if}
        {/foreach}
    </ul>
{/if}

{*
Elements de style exemple à rajouter dans la CSS
.breadCrumbs {
   font-size: 0.8em;
   list-style-type: none;
   margin: 0 0 10px 0;
   padding: 0;
   text-align: right;
}

.breadCrumbs li {
   display: inline;
}

#breadCrumbs li.lastCrumb {
   font-weight: bold;
}

#breadCrumbs li.rootCrumb {}
*}