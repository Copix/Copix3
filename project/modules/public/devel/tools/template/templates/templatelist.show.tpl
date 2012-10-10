{copixhtmlheader kind="jsCode"}
function getSelectedTemplateTheme (){ldelim}
   var themeSelectBox;
   themeSelectBox = document.getElementById ('selectedTheme');
   return themeSelectBox.value;
{rdelim}

function updateUrlWithSelectedTheme (object){ldelim}
   object.href = object.href + getSelectedTemplateTheme ();
{rdelim}

{/copixhtmlheader}
<form id="templateList" action="{copixurl dest=template||}" class="CopixForm" method="post">
<table>
 <tr>
  <th>{i18n key=template.dao.id_ctpt}</th><td>{select id="selectedTheme" name="selectedTheme" values=$themeList objectMap="id_ctpt;caption_ctpt" selected=$selectedTheme extra="onChange=this.form.submit()"}
  <a href="{copixurl dest="theme|create"}"><img src="{copixurl}img/tools/new.png" /></a>
  <a href="{copixurl dest="theme|prepareEdit" id_ctpt=""}" onclick="updateUrlWithSelectedTheme(this)"><img src="{copixurl}img/tools/update.png" /></a>
  <a href="{copixurl dest="theme|delete" id_ctpt=""}" onclick="updateUrlWithSelectedTheme(this)"><img src="{copixurl}img/tools/delete.png" /></a>
  </td>
 </tr>
 <tr>
  <th>{i18n key=template.dao.modulequalifier_ctpl}</th><td>{select name="selectedQualifier" values=$qualifierList objectMap="name;description" selected=$selectedQualifier extra="onChange=this.form.submit()"}</td>
 </tr>
 </table>
 <input type="submit" value="{i18n key=copix:common.buttons.search}">
 </form>
<table class="CopixTable">
<thead>
     <tr>
        <th>{i18n key="template.dao.qualifier_ctpl"}</th>
        <th>{i18n key="template.dao.caption_ctpl"}</th>
        <th style="width:28%">{i18n key="copix:common.actions.title"}</th>
     </tr>
  </thead>
  <tbody>
  {foreach from=$templateList item=template}
     <tr {cycle values=',class="alternate"'}>
        <td>{$template->qualifier_ctpl}</td>
        <td>{$template->caption_ctpl}</td>
        <td><a href="{copixurl dest="template|admin|delete"      id_ctpl=$template->id_ctpl}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixurl}img/tools/delete.png" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="template|admin|download"    id_ctpl=$template->id_ctpl}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixurl}img/tools/show.png" alt="{i18n key="copix:common.buttons.show"}"/> </a>
            <a href="{copixurl dest="template|admin|prepareEdit" id_ctpl=$template->id_ctpl}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixurl}img/tools/update.png" alt="{i18n key="copix:common.buttons.update"}" /></a>
        </td>
     </tr>
  {foreachelse}
     <tr {cycle values=',class="alternate"'}>
        <td colspan="3">{i18n key="template.messages.noTemplateHere"}</td>
     </tr>
  {/foreach}
  </tbody>
</table>

<a href="{copixurl dest="template|admin|create" modulequalifier_ctpl=$selectedQualifier theme=$selectedTheme}" title="{i18n key=copix:common.buttons.new}"><img src="{copixurl}img/tools/update.png" alt="" />{i18n key=copix:common.buttons.update}</a>
<a href="{copixurl dest="template|admin|create" modulequalifier_ctpl=$selectedQualifier theme=$selectedTheme dynamicTemplate=1}" title="{i18n key=copix:common.buttons.new}"><img src="{copixurl}img/tools/new.png" alt="" />{i18n key=copix:common.buttons.new}</a>