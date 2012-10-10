<table class="CopixTable">
 <thead>
 <tr>
   <th>{i18n key="admin.titleTab.pageTitle"}</th>
   <th>{i18n key="admin.titleTab.version"}</th>
   <th>{i18n key="admin.titleTab.actions"}</th>
 </tr>
 </thead>
 <tbody>
 {foreach from=$arVersions item=version}
 <tr {cycle values=',class="alternate"'}>
  <td>{$version->title_cmsp}</td>
  <td>{$version->version_cmsp}</td>
  <td>
	 <a href="{copixurl dest="admin|showVersion" id=$version->publicid_cmsp version=$version->version_cmsp}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}" /></a>
     <a href="{copixurl dest="admin|newFromPage" version=$version->version_cmsp id=$version->publicid_cmsp update=1}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
     <a href="{copixurl dest="admin|newFromPage" version=$version->version_cmsp id=$version->publicid_cmsp}"><img src="{copixresource path="img/tools/clone.png"}" alt="{i18n key="copix:common.buttons.copy"}" /></a>
  </td>
 </tr>
 {/foreach}
 </tbody>
</table>