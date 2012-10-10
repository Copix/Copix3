{**
* Template qui permet de choisir dans
* param: list - un tableau contenant des tableaux associatif du type $tab['name'],
*   $tab['libelle'], $tab['img']
* param: zone le nom de la zone, que l'on repasse dans l'url
* param: paste_enable si peut coller.
* param: paste_from si possible de coller, indique l'élément prennant en charge cette copie.
*}
<table>
 {foreach from=$arPortlet key=portletDescription item=portletId}
    <tr {cycle values=',class="alternate"' name="CopixTable"}>
     <td><a href="{copixurl dest='cms|admin|newPortlet' portlet=$portletId templateVar=$templateVar}">{$portletDescription}</a></td>
    </tr>
 {foreachelse}
    <tr><td>{i18n key=admin.error.noPortlet}</td></tr>
 {/foreach}
    {if $pasteEnable}
       <tr {cycle values=',class="alternate"' name="CopixTable"}><td><a href="{copixurl dest="cms|admin|pastePortlet" templateVar=$templateVar}">{i18n key=copix:common.buttons.paste}</td></tr>
    {/if}
</table>
<p><a href="{copixurl dest="cms|admin|edit" kind="1"}">{i18n key="copix:common.buttons.cancel"}</a></p>
