{**
* Page d'administration de l'agenda
*}
{literal}
<script type="text/javascript">
//<![CDATA[
   function validDelete(id){
     //alert("ID: "+id);
     if(confirm("{/literal}{i18n key=schedule.message.confirmdelevent}{literal}")){
         document.location.href="index.php?module=schedule&desc=admin&action=doDelete&id_evnt="+id;
     }
   }
//]]>
</script>
{/literal}
<!--
{if $contribEnabled}
<div class="borderDiv">
   <h2>{i18n key=schedule.title.contrib} </h2>
   <p>{i18n key=schedule.message.contrib}</p>
   <p>
   <input type="button" value="{i18n key=schedule.addevent}" onclick="javascript:document.location.href='{copixurl dest="schedule|admin|getAdminEvent"}'" />
   </p>
</div>
{/if}

{if $validEnabled}
   {if count ($arScheduleValid)}
   <div class="borderDiv">
   <h2>{i18n key=schedule.title.valid}</h2>
   <p>{i18n key=schedule.message.valid}</p>
		<p><table>
      <tr><th>{i18n key="schedule.th.title"}</th>
          <th>{i18n key="copix:common.actions.title"}</th>
      </tr>
         {foreach from=$arScheduleValid item=event key=key}
            <tr>
             <td>{$event->title_evnt}</td>
             <td><input type="button" onclick="javascript:document.location.href='{copixurl dest="schedule|admin|getAdminEvent" id_evnt=$event->id_evnt}'" value="{i18n key=copix:common.buttons.update}" />
                 <input type="button" onclick="javascript:document.location.href='{copixurl dest="schedule|admin|Valid" id_evnt=$event->id_evnt}'" value="{i18n key=copix:common.buttons.ok}" />
                 <input type="button" onclick="javascript:validDelete({$event->id_evnt});" value="{i18n key=schedule.trash}" />
             </td>
            </tr>
         {/foreach}
      </table></p>
  </div>
   {/if}
{/if}



{if $publishEnabled}
	{if count ($arSchedulePublish)}
	<div class="borderDiv">
	<h2>{i18n key=schedule.title.publish}</h2>
	<p>{i18n key=schedule.message.publish}</p>
		<p><table>
      <tr>
        <th>{i18n key=schedule.th.title}</th>
        <th>{i18n key=copix:common.actions.title}</th>
      </tr>
      {foreach from=$arSchedulePublish item=event key=key}
      <tr>
         <td>
             {$event->title_evnt}
         </td>
         <td>
            <input type="button" onclick="javascript:document.location.href='{copixurl dest="schedule|admin|getAdminEvent" id_evnt=$event->id_evnt}'" value="{i18n key=copix:common.buttons.update}">
            <input type="button" onclick="javascript:document.location.href='{copixurl dest="schedule|admin|setInline" id_evnt=$event->id_evnt}'" value="{i18n key=copix:common.buttons.publish}">
            <input type="button" onclick="validDelete({$event->id_evnt});" value="{i18n key=copix:common.buttons.trash}">
         </td>
      </tr>
      {/foreach}
      </table></p>
  </div>
   {/if}
{/if}

{if $moderateEnabled}
   {if count($arScheduleTrash)}
   <div class="borderDiv">
     <h2>{i18n key=schedule.title.trash} </h2>
     <p>{i18n key=schedule.message.trash}</p>
		  <p><table>
			<tr>
			  <th>{i18n key=schedule.th.title}</th>
			  <th>{i18n key=copix:common.actions.title}</th>
			</tr>
			{foreach from=$arScheduleTrash item=trash key=Key name=boucle}
			<tr>
				<td>{$trash->title_evnt}</td>
				<td>
					<input type="button" onclick="javascript:document.location.href='{copixurl dest="schedule|admin|getAdminEvent" id_evnt=$trash->id_evnt}'" value="{i18n key="copix:common.buttons.update"}" />
					<input type="button" onclick="javascript:document.location.href='{copixurl dest="schedule|admin|restore" id_evnt=$trash->id_evnt}'" value="{i18n key=copix:common.buttons.restore}" />
					<input type="button" onclick="jabascript:validDelete({$trash->id_evnt});" value="{i18n key=copix:common.buttons.delete}" />
				</td>
			</tr>
			{/foreach}
			</table></p>
   <p><input type="button" onclick="javascript:document.location.href='{copixurl dest="schedule|admin|emptyTrash"}'" value="{i18n key=schedule.message.emptytrash}" /></p>
   </div>
   {/if}

   
   <div class="borderDiv">
   <h2>{i18n key=schedule.title.online} </h2>
   <p>{i18n key=schedule.message.online}</p>
   <p><input type="button" onclick="javascript:document.location.href='{copixurl dest="schedule|admin|searchEvent"}'" value="Voir"></p>
   </div>
{/if}{* if moderate enable *}

-->

{if count ($arScheduleValid) || count ($arSchedulePublish) || count ($arScheduleTrash)}
<div class="borderDiv">
<h2>{i18n key="schedule.title.offline"}</h2>
    <table class="CopixTable">
    <thead>
       <tr>
           <th>{i18n key="schedule.message.title"}</th>
           <th>{i18n key="copix:common.title.status"}</th>
           <th>{i18n key="copix:common.actions.title"}</th>
       </tr>
     </thead>
     <tbody>
         {foreach from=$arScheduleValid item=schedule}
            <tr>
             <td>{$schedule->title_evnt}</td>
             <td>{i18n key="schedule.status.proposed"}</td>
             <td>
<!--                 <a href="{copixurl dest="schedule|default|show" id_news=$news->id_news}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}"/> </a>-->
                 <a href="{copixurl dest="schedule|admin|getAdminEvent" id_evnt=$schedule->id_evnt}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}"/> </a>
              {if $validEnabled}
                 <a href="{copixurl dest="schedule|admin|Valid" id_evnt=$schedule->id_evnt}" title="{i18n key="copix:common.buttons.valid"}"><img src="{copixresource path="img/tools/validate.png"}" alt="{i18n key="copix:common.buttons.valid"}" /></a>
              {/if}
                 <a href="{copixurl dest="schedule|admin|doDelete" id_evnt=$schedule->id_evnt}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.trash"}"/></a>
             </td>
            </tr>
         {/foreach}
         {foreach from=$arSchedulePublish item=schedule}
            <tr>
             <td>{$schedule->title_evnt}</td>
             <td>{i18n key="schedule.status.validated"}</td>
             <td>
<!--              <a href="{copixurl dest="schedule|default|show" id_news=$news->id_news}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}"/> </a>-->
              <a href="{copixurl dest="schedule|admin|getAdminEvent" id_evnt=$schedule->id_evnt}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
              {if $publishEnabled}
                 <a href="{copixurl dest="schedule|admin|setInline" id_evnt=$schedule->id_evnt}" title="{i18n key="copix:common.buttons.publish"}"><img src="{copixresource path="img/tools/publish.png"}" alt="{i18n key="copix:common.buttons.publish"}" /></a>
              {/if}
              <a href="{copixurl dest="schedule|admin|doDelete" id_evnt=$schedule->id_evnt}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.trash"}" /></a>
             </td>
            </tr>
         {/foreach}
      {if $moderateEnabled && count ($arScheduleTrash)}
          {foreach from=$arScheduleTrash item=schedule}
            <tr>
             <td>{$schedule->title_evnt}</td>
             <td>{i18n key="schedule.status.trashed"}</td>
             <td>
<!--              <a href="{copixurl dest="schedule|default|show" id_news=$news->id_news}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}"/> </a>-->
              <a href="{copixurl dest="schedule|admin|getAdminEvent" id_evnt=$schedule->id_evnt}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
              <a href="{copixurl dest="schedule|admin|restore" id_evnt=$schedule->id_evnt}" title="{i18n key="copix:common.buttons.restore"}"><img src="{copixresource path="img/tools/validate.png"}" alt="{i18n key="copix:common.buttons.restore"}" /></a>
              <a href="{copixurl dest="schedule|admin|doDelete" id_evnt=$schedule->id_evnt}" title="{i18n key="copix:common.buttons.delete"}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
             </td>
            </tr>
         {/foreach}
      {/if}
      </tbody>
      <tfoot>
         <tr>
            <td colspan="3">
               {$totalOffline} {i18n key="copix:common.title.offlineElements"}
            </td>
         </tr>
      </tfoot>
      </table>
 </div>
{/if}


{if count ($arScheduleOnline)}
<div class="borderDiv">
<h2>{i18n key="schedule.title.online"}</h2>
    <table class="CopixTable">
    <thead>
       <tr>
           <th>{i18n key="schedule.message.title"}</th>
           <th>{i18n key="copix:common.title.status"}</th>
           <th>{i18n key="copix:common.actions.title"}</th>
       </tr>
    </thead>
    <tbody>
         {foreach from=$arScheduleOnline item=schedule}
            <tr>
             <td>{$schedule->title_evnt}</td>
             <td>{i18n key="schedule.status.published"}</td>
             <td>
<!--            <a href="{copixurl dest="schedule|default|show" id_news=$news->id_news}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}"/> </a>-->
             {if $moderateEnabled}
               <a href="{copixurl dest="schedule|admin|getAdminEvent" id_evnt=$schedule->id_evnt}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
               <a href="{copixurl dest="schedule|admin|doDelete" id_evnt=$schedule->id_evnt}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/trash.png"}" alt="{i18n key="copix:common.buttons.trash"}" /></a>
             {/if}
             </td>
            </tr>
         {/foreach}
    </tbody>
    <tfoot>
      <tr>
         <td colspan="3">
            {$totalOnline} {i18n key="copix:common.title.onlineElements"}
         </td>
      </tr>
    </tfoot>
    </table>
 </div>
{/if}


<p>
	<input type="button" value="{i18n key=copix:common.buttons.back}" onclick="document.location='{copixurl dest="copixheadings|admin|" level=$id_head}'" />
</p>
