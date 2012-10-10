{**
* Template qui permet de choisir dans une liste de template
*   (pour la gestion de contenu)
* param: list - un tableau contenant des tableaux associatif du type $tab['name'],
*   $tab['libelle'], $tab['img']
* param: url l'url de base on on envera le choix du template.
* param: back_url l'url de retour en cas d'annulation du choix.
*}
<table class="CopixTable">
 <thead>
 <tr>
  <th>{i18n key="template.title.name"}</th>
 </tr>
 </thead>
 {foreach from=$list item=tplName key=tplId}
 <tr {cycle values=',class="alternate"' name="CopixTable"}>
  <td><a href="{copixurl appendFrom=$url template=$tplId}">{$tplName}</a></td>
 </tr>
 {foreachelse}
 <tr>
  <td colspan="2">{i18n key="template.messages.noTemplate"}</td>
 </tr>
 {/foreach}
</table>
<p><a href="{$back_url}"><img src="{copixresource path="img/tools/back.png"}" alt="{i18n key="copix:common.buttons.cancel"}" />{i18n key="copix:common.buttons.cancel"}</a></p>