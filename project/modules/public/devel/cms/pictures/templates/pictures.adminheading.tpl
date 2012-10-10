{**
* Front page to the pictures actions
*}
{if $moderateEnabled}
<ul class="copixCMSNav">
     <li {if $kind=="general"}class="copixCMSNavSelected"{/if}><a href="{copixurl dest="copixheadings|admin|default" browse="pictures" id_head=$id_head kind="0"}">{i18n key="pictures.title.general"}</a></li>
     <li {if $kind=="properties"}class="copixCMSNavSelected"{/if}><a href="{copixurl dest="copixheadings|admin|default" browse="pictures" id_head=$id_head kind="1"}">{i18n key="pictures.title.properties"}</a></li>
    <li {if $kind=="import"}class="copixCMSNavSelected"{/if}><a href="{copixurl dest="copixheadings|admin|default" browse="pictures" id_head=$id_head kind="2"}">{i18n key="pictures.title.import"}</a></li> 
</ul>
{/if}

{if $kind=="general"}
{if count ($themeList)}
<form id="pictureWorkflow" action="" class="CopixForm" method="post">
<table class="CopixTable">
<thead>
   <tr>
      <th>{i18n key="copix:common.messages.title"}</th>
      <th>{i18n key="copixheadings|workflow.messages.author"}</th>
      <th style="width:23%">{i18n key="copix:common.actions.title"}</th>
   </tr>
</thead>
<tbody>
   <!--- ONLINE PICTURE -->
   {if count($arPicturePublish)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.online"} ({$arPicturePublish|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $publishEnabled}<!--<img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copixheadings|workflow.messages.deleteAll"}"/>-->{/if}</th>
   </tr>
   {foreach from=$arPicturePublish item=picture}
   <tr {cycle values=',class="alternate"' name="arPicturePublish"}>
      <td>{popupinformation text=$picture->name_pict}
            {if $picture->url_pict}
            <img src="{$picture->url_pict}"
            {else}
            <img src="{copixurl dest="pictures||get" id_pict=$picture->id_pict width="300"}"
            {/if}
            alt="{$picture->name_pict}" /><br />
            {if !$picture->url_pict}
            {i18n key="dao.pictures.fields.x_pict"} : {$picture->x_pict}px<br />
            {i18n key="dao.pictures.fields.y_pict"} : {$picture->y_pict}px<br />
            {/if}
            {if $picture->desc_pict}{i18n key="copix:common.messages.desc"} : {$picture->desc_pict}<br />{/if}
            {i18n key="copixheadings|workflow.messages.publishBy" param1=$picture->statusauthor_pict param2=$picture->statusdate_pict|datei18n}
            {if $picture->statuscomment_pict}{i18n key="copixheadings|workflow.messages.withComment" param=$picture->statuscomment_pict}{/if}
          {/popupinformation}
          {$picture->name_pict}</td>
      <td>{$picture->author_pict}</td>
      <td>{if $publishEnabled}
            <a href="{copixurl dest="pictures|admin|deletePicture" id_pict=$picture->id_pict}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="pictures|admin|cut" id_pict=$picture->id_pict}" title="{i18n key="copix:common.buttons.cut"}"><img src="{copixresource path="img/tools/cut.png"}" alt="{i18n key="copix:common.buttons.cut"}" /></a>
            <a href="{copixurl dest="pictures|admin|prepareEditPicture" id_pict=$picture->id_pict}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}

   <!--- DRAFT PICTURE -->
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.draft"} ({$arPictureDraft|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th><a href="{copixurl dest="admin|createPicture" id_head=$id_head}" title="{i18n key="pictures.buttons.addPicture"}"><img src="{copixresource path="img/tools/new.png"}" alt="{i18n key="pictures.buttons.addPicture"}" /></a>
          {if $pasteEnabled}
           <a href="{copixurl dest="admin|paste" id_head=$id_head}"><img src="{copixresource path="img/tools/paste.png"}" alt="{i18n key="copix:common.buttons.paste"}" title="{i18n key="copix:common.buttons.paste"}" /></a>
          {/if}</th>
   </tr>
   {foreach from=$arPictureDraft item=picture}
   <tr {cycle values=',class="alternate"' name="arPictureDraft"}>
      <td>{popupinformation text=$picture->name_pict}
            {if $picture->url_pict}
            <img src="{$picture->url_pict}"
            {else}
            <img src="{copixurl dest="pictures||get" id_pict=$picture->id_pict width="300"}"
            {/if}
            alt="{$picture->name_pict}" /><br />
            {if !$picture->url_pict}
            {i18n key="dao.pictures.fields.x_pict"} : {$picture->x_pict}px<br />
            {i18n key="dao.pictures.fields.y_pict"} : {$picture->y_pict}px<br />
            {/if}
            {if $picture->desc_pict}{i18n key="copix:common.messages.desc"} : {$picture->desc_pict}<br />{/if}
            {i18n key="copixheadings|workflow.messages.createBy" param1=$picture->statusauthor_pict param2=$picture->statusdate_pict|datei18n}
            {if $picture->statuscomment_pict}{i18n key="copixheadings|workflow.messages.withComment" param=$picture->statuscomment_pict}{/if}
          {/popupinformation}
          {$picture->name_pict}</td>
      <td>{$picture->author_pict}</td>
      <td>{if $contribEnabled}
            {copixurl dest="pictures|admin|statusTrashPicture" id_pict=$picture->id_pict assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="pictureWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="pictures|admin|prepareEditPicture" id_pict=$picture->id_pict}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="pictures|admin|statusProposePicture" id_pict=$picture->id_pict assign=urlPropose}
            <a href="#" onclick="{jssubmitform href=$urlPropose form="pictureWorkflow"}" title="{i18n key="copix:common.buttons.propose"}"><img src="{copixresource path="img/tools/propose.png"}" alt="{i18n key="copix:common.buttons.propose"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_pict_{$picture->id_pict}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {foreachelse}
   <tr>
      <td colspan="3">{i18n key="copixheadings|workflow.messages.noDraft"}</td>
   </tr>
   {/foreach}

   <!--- REFUSE PICTURE -->
   {if count($arPictureRefuse)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.refuse"} ({$arPictureRefuse|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $contribEnabled}<!--<img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copixheadings|workflow.messages.deleteAll"}">-->{/if}</th>
   </tr>
   {foreach from=$arPictureRefuse item=picture}
   <tr {cycle values=',class="alternate"' name="arPictureRefuse"}>
      <td>{popupinformation text=$picture->name_pict}
            {if $picture->url_pict}
            <img src="{$picture->url_pict}"
            {else}
            <img src="{copixurl dest="pictures||get" id_pict=$picture->id_pict width="300"}"
            {/if}
            alt="{$picture->name_pict}" /><br />
            {if !$picture->url_pict}
            {i18n key="dao.pictures.fields.x_pict"} : {$picture->x_pict}px<br />
            {i18n key="dao.pictures.fields.y_pict"} : {$picture->y_pict}px<br />
            {/if}
            {if $picture->desc_pict}{i18n key="copix:common.messages.desc"} : {$picture->desc_pict}<br />{/if}
            {i18n key="copixheadings|workflow.messages.refuseBy" param1=$picture->statusauthor_pict param2=$picture->statusdate_pict|datei18n}
            {if $picture->statuscomment_pict}{i18n key="copixheadings|workflow.messages.withComment" param=$picture->statuscomment_pict}{/if}
          {/popupinformation}
          {$picture->name_pict}</td>
      <td>{$picture->author_pict}</td>
      <td>{if $contribEnabled}
            {copixurl dest="pictures|admin|statusTrashPicture" id_pict=$picture->id_pict assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="pictureWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="pictures|admin|prepareEditPicture" id_pict=$picture->id_pict}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_pict_{$picture->id_pict}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}

   <!--- TOVALID PICTURE -->
   {if count($arPicturePropose)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.messages.toValid"} ({$arPicturePropose|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $validEnabled}<!--
            <img src="{copixresource path="img/tools/valid.png"}" alt="{i18n key="copixheadings|workflow.messages.validAll"}"/>
            <img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copixheadings|workflow.messages.refuseAll"}"/>
           -->{/if}</th>
   </tr>
   {foreach from=$arPicturePropose item=picture}
   <tr {cycle values=',class="alternate"' name="arPicturePropose"}>
      <td>{popupinformation text=$picture->name_pict}
            {if $picture->url_pict}
            <img src="{$picture->url_pict}"
            {else}
            <img src="{copixurl dest="pictures||get" id_pict=$picture->id_pict width="300"}"
            {/if}
            alt="{$picture->name_pict}" /><br />
            {if !$picture->url_pict}
            {i18n key="dao.pictures.fields.x_pict"} : {$picture->x_pict}px<br />
            {i18n key="dao.pictures.fields.y_pict"} : {$picture->y_pict}px<br />
            {/if}
            {if $picture->desc_pict}{i18n key="copix:common.messages.desc"} : {$picture->desc_pict}<br />{/if}
            {i18n key="copixheadings|workflow.messages.proposeBy" param1=$picture->statusauthor_pict param2=$picture->statusdate_pict|datei18n}
            {if $picture->statuscomment_pict}{i18n key="copixheadings|workflow.messages.withComment" param=$picture->statuscomment_pict}{/if}
          {/popupinformation}
          {$picture->name_pict}</td>
      <td>{$picture->author_pict}</td>
      <td>{if $validEnabled}
            {copixurl dest="pictures|admin|statusTrashPicture" id_pict=$picture->id_pict assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="pictureWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="pictures|admin|prepareEditPicture" id_pict=$picture->id_pict}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixurl}img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="pictures|admin|statusValidPicture" id_pict=$picture->id_pict assign=urlValid}
            <a href="#" onclick="{jssubmitform href=$urlValid form="pictureWorkflow"}" title="{i18n key="copix:common.buttons.valid"}"><img src="{copixresource path="img/tools/valid.png"}" alt="{i18n key="copix:common.buttons.valid"}" /></a>
            {copixurl dest="pictures|admin|statusRefusePicture" id_pict=$picture->id_pict assign=urlRefuse}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="pictureWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_pict_{$picture->id_pict}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}

   <!--- TOPUBLISH PICTURE -->
   {if count($arPictureValid)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.messages.toPublish"} ({$arPictureValid|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $publishEnabled}<!--
            <img src="{copixresource path="img/tools/publish.png"}" alt="{i18n key="copixheadings|workflow.messages.publishAll"}"/>
            <img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copixheadings|workflow.messages.refuseAll"}"/>
           -->{/if}</th>
   </tr>
   {foreach from=$arPictureValid item=picture}
   <tr {cycle values=',class="alternate"' name="arPictureValid"}>
      <td>{popupinformation text=$picture->name_pict}
            {if $picture->url_pict}
            <img src="{$picture->url_pict}"
            {else}
            <img src="{copixurl dest="pictures||get" id_pict=$picture->id_pict width="300"}"
            {/if}
            alt="{$picture->name_pict}" /><br />
            {if !$picture->url_pict}
            {i18n key="dao.pictures.fields.x_pict"} : {$picture->x_pict}px<br />
            {i18n key="dao.pictures.fields.y_pict"} : {$picture->y_pict}px<br />
            {/if}
            {if $picture->desc_pict}{i18n key="copix:common.messages.desc"} : {$picture->desc_pict}<br />{/if}
            {i18n key="copixheadings|workflow.messages.validBy" param1=$picture->statusauthor_pict param2=$picture->statusdate_pict|datei18n}
            {if $picture->statuscomment_pict}{i18n key="copixheadings|workflow.messages.withComment" param=$picture->statuscomment_pict}{/if}
          {/popupinformation}
          {$picture->name_pict}</td>
      <td>{$picture->author_pict}</td>
      <td>{if $publishEnabled}
            {copixurl dest="pictures|admin|statusTrashPicture" id_pict=$picture->id_pict assign=urlTrash}
            <a href="#" onclick="{jssubmitform href=$urlTrash form="pictureWorkflow"}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            <a href="{copixurl dest="pictures|admin|prepareEditPicture" id_pict=$picture->id_pict}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
            {copixurl dest="pictures|admin|statusPublishPicture" id_pict=$picture->id_pict assign=urlPublish}
            <a href="#" onclick="{jssubmitform href=$urlPublish form="pictureWorkflow"}" title="{i18n key="copix:common.buttons.publish"}"><img src="{copixresource path="img/tools/publish.png"}" alt="{i18n key="copix:common.buttons.publish"}" /></a>
            {copixurl dest="pictures|admin|statusRefusePicture" id_pict=$picture->id_pict assign=urlRefuse notxml="true"}
            <a href="#" onclick="{jssubmitform href=$urlRefuse form="pictureWorkflow"}" title="{i18n key="copix:common.buttons.refuse"}"><img src="{copixresource path="img/tools/refuse.png"}" alt="{i18n key="copix:common.buttons.refuse"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_pict_{$picture->id_pict}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}

   <!--- TRASH PICTURE -->
   {if count($arPictureTrash)}
   <tr>
      <th colspan="2">{i18n key="copixheadings|workflow.status.trash"} ({$arPictureTrash|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
      <th>{if $contribEnabled}<!--<img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copixheadings|workflow.messages.deleteAll"}"/>-->{/if}</th>
   </tr>
   {foreach from=$arPictureTrash item=picture}
   <tr {cycle values=',class="alternate"' name="arPictureTrash"}>
      <td>{popupinformation text=$picture->name_pict}
            {if $picture->url_pict}
            <img src="{$picture->url_pict}"
            {else}
            <img src="{copixurl dest="pictures||get" id_pict=$picture->id_pict width="300"}"
            {/if}
            alt="{$picture->name_pict}" /><br />
            {if !$picture->url_pict}
            {i18n key="dao.pictures.fields.x_pict"} : {$picture->x_pict}px<br />
            {i18n key="dao.pictures.fields.y_pict"} : {$picture->y_pict}px<br />
            {/if}
            {if $picture->desc_pict}{i18n key="copix:common.messages.desc"} : {$picture->desc_pict}<br />{/if}
            {i18n key="copixheadings|workflow.messages.trashBy" param1=$picture->statusauthor_pict param2=$picture->statusdate_pict|datei18n}
            {if $picture->statuscomment_pict}{i18n key="copixheadings|workflow.messages.withComment" param=$picture->statuscomment_pict}{/if}
          {/popupinformation}
          {$picture->name_pict}</td>
      <td>{$picture->author_pict}</td>
      <td>{if $contribEnabled}
            <a href="{copixurl dest="pictures|admin|deletePicture" id_pict=$picture->id_pict}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
            {copixurl dest="pictures|admin|statusDraftPicture" id_pict=$picture->id_pict assign=urlDraft}
            <a href="#" onclick="{jssubmitform href=$urlDraft form="pictureWorkflow"}" title="{i18n key="copix:common.buttons.restore"}"><img src="{copixresource path="img/tools/restore.png"}" alt="{i18n key="copix:common.buttons.restore"}" /></a>
          {i18n key="copixheadings|workflow.messages.addComment" assign=text}
          {copixresource assign=url path="img/tools/comments.png"}
          {popupinformation text=$text img=$url handler="onclick" divclass="statusComment"}
               <input type="text" name="statuscomment_pict_{$picture->id_pict}" value=""/>
          {/popupinformation}
          {/if}
      </td>
   </tr>
   {/foreach}
   {/if}
</tbody>
</table>
</form>
{/if}
{if $kind=="properties"}
<p>
{i18n key="pictures.warning.missingTheme"}
</p>
{/if}
<p>{i18n key="pictures.messages.getOnline"} <a href="{copixurl dest="pictures|browser|" id_head=$id_head}" />{i18n key="pictures.messages.here"}</a>
{/if}

{if $kind=="properties"}
   {if $moderateEnabled}
   {if $showErrors}
    <div class="errorMessage">
     <h1>{i18n key=copix:common.messages.error}</h1>
      {if isset($erreur)}
        <ul><li>{$erreur}</li></ul>
      {else}
        {ulli values=$errors}
      {/if}
     </div>
   {/if}
   <h2>{i18n key="pictures.title.theme"}</h2>
   {if count ($themeList)}
    <table class="CopixTable">
       <thead>
          <tr>
              <th>{i18n key="pictures.messages.title"}</th>
              <th class="actions">{i18n key="copix:common.actions.title"}</th>
          </tr>
       </thead>
       <tbody>
            {foreach from=$themeList item=theme}
               {if $toEdit->id_tpic eq $theme->id_tpic}
               <form action="{copixurl dest="pictures|admin|validTheme"}" method="post">
                <tr {cycle values=',class="alternate"'}>
                <td><input type="text" name="name_tpic" value="{$toEdit->name_tpic|escape}" /></td>
                <td>
                 <input type="image" src="{copixresource path="img/tools/valid.png"}" value="{i18n key="copix:common.buttons.ok"}" />
                 <a href="{copixurl dest="pictures|admin|cancelEditTheme"}"><img src="{copixresource path="img/tools/cancel.png"}" alt="{i18n key="copix:common.buttons.cancel"}" /></a>
                </td>
               </tr>
               </form>
               {else}
               <tr {cycle values=',class="alternate"'}>
                <td>{$theme->name_tpic|escape:html}</td>
                <td>
                 <a href="{copixurl dest="pictures|admin|prepareEditTheme" id_tpic=$theme->id_tpic id_head=$id_head}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
                 <a href="{copixurl dest="pictures|admin|prepareDelTheme" id_tpic=$theme->id_tpic id_head=$id_head}" title="{i18n key="copix:common.buttons.delete"}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
                </td>
               </tr>
               {/if}
            {/foreach}
       </tbody>
    </table>
   {else}
   <p>
   {i18n key="pictures.messages.noTheme"}
   </p>
   {/if}
   {if $toEdit}
      {if $toEdit->id_tpic > 0}
      {else}
      <h2>{i18n key="pictures.titlePage.createTheme"}</h2>
      <form action="{copixurl dest="pictures|admin|validTheme"}" method="post">
         <table class="CopixTable">
            <tr><th>{i18n key='dao.picturesthemes.fields.name_tpic'} *</th>
                <td><input type="text" name="name_tpic" value="{$toEdit->name_tpic|escape}" /></td></tr>
         </table>
         <p>
         <input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
         <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:window.location='{copixurl dest="pictures|admin|cancelEditTheme"}'" />
         </p>
      </form>
      {/if}
   {else}
   <a href="{copixurl dest="admin|createTheme" id_head=$id_head}" title="{i18n key="copix:common.buttons.new"}"><img src="{copixresource path="img/tools/new.png"}" alt="{i18n key="copix:common.buttons.new"}" />{i18n key="copix:common.buttons.new"}</a>
   {/if}
   <h2>{i18n key="pictures.title.properties"}</h2>
   <table class="CopixTable">
      <thead>
         <tr>
            <th>{i18n key='dao.picturesheadings.fields.maxX_cpic'}</th>
            <th>{i18n key='dao.picturesheadings.fields.maxY_cpic'}</th>
            <th>{i18n key='dao.picturesheadings.fields.maxWeight_cpic'}</th>
            <th>{i18n key='dao.picturesheadings.fields.format_cpic'}</th>
         </tr>
      </thead>
      <tbody>
      <tr><td>{$headingProperties->maxX_cpic}</td>
          <td>{$headingProperties->maxY_cpic}</td>
          <td>{$headingProperties->maxWeight_cpic}</td>
          <td>{$headingProperties->format_cpic}</td>
      </tr>
      </tbody>
   </table>
   <a href="{copixurl dest="admin|prepareEditProperties" id_head=$id_head}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" />{i18n key="copix:common.buttons.update"}</a>
   {/if}
{/if}
{if $kind=="import"}
    <table class="CopixTable">
       <thead>
          <tr>
              <th>{i18n key="browser.theme"}</th>
          </tr>
       </thead>
       <tbody>
	   		<form action="{copixurl dest="pictures|admin|import"}" method="post">
				<input type="hidden" name="id_head" value="{$id_head}"/>
            {foreach from=$themeList item=theme}
               <tr {cycle values=',class="alternate"'}>
                <td><input type="checkbox" class="checkbox" name="theme[]" value="{$theme->id_tpic}" />{$theme->name_tpic|escape:html}</td>
               </tr>
            {/foreach}
	   <tr>
       <td><input type="submit" value="{i18n key="copix:common.buttons.ok"}" /></td>
	   </tr>
	   </form>
	   </tbody>
	</table>
{/if}
