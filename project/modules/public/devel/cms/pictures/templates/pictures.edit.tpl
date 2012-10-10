{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
   var myForm = document.pictureEdit;
   myForm.action = pUrl;
   myForm.submit ();
}
//]]>
</script>
{/literal}

{if $showErrors}
<div class="errorMessage">
 <h1>{i18n key=copix:common.messages.error}</h1>
 {ulli values=$errors}
 {if isset($erreur)}
 <p>{$erreur}</p>
 {/if}
</div>
{/if}

<form name="pictureEdit" action="{copixurl dest="pictures|admin|validPicture"}" method="post" enctype="multipart/form-data" class="copixForm">
<table>
   <tr><th>{i18n key='dao.pictures.fields.name_pict'} *</th>
       <td><input type="text" size="48" name="name_pict" value="{$toEdit->name_pict|escape}" /></td></tr>
   <tr><th>{i18n key='dao.pictures.fields.desc_pict'}</th>
       <td><textarea name="desc_pict" cols="40" rows="5" >{$toEdit->desc_pict|escape}</textarea></td></tr>
   <tr><th>{i18n key='pictures.title.heading'}</th>
       <td><fieldset>
         <table>
            <tr><th colspan="2">{$heading|escape}</th></tr>
            <tr><th>{i18n key='dao.picturesheadings.fields.maxX_cpic'}</th>
                <td>{$headingProperties->maxX_cpic}</td></tr>
            <tr><th>{i18n key='dao.picturesheadings.fields.maxY_cpic'}</th>
                <td>{$headingProperties->maxY_cpic}</td></tr>
            <tr><th>{i18n key='dao.picturesheadings.fields.maxWeight_cpic'}</th>
                <td>{$headingProperties->maxWeight_cpic}</td></tr>
            <tr><th>{i18n key='dao.picturesheadings.fields.format_cpic'}</th>
                <td>{$headingProperties->format_cpic}</td></tr>
         </table>
         </fieldset>
   <tr><th>{i18n key='pictures.title.theme'}</th>
       <td>{if count ($themeList)}
           <fieldset><ul>
           {foreach from=$themeList item=themes}
               <li><input type="checkbox" class="checkbox" name="theme[]" value="{$themes->id_tpic}"{foreach from=$toEdit->theme item=theme}{if $theme == $themes->id_tpic}checked="checked"{/if}{/foreach}/>
                   {$themes->name_tpic}</li>
           {/foreach}
           </ul></fieldset>
           {else}
           <p>{i18n key="pictures.messages.noTheme"}</p>
           {/if}</td>
   </tr>
   {if ($toEdit->id_pict eq 0)}
   <tr><th>{i18n key='pictures.message.picture'}</th>
       <td> <fieldset> 
            {i18n key='pictures.message.file'} ({i18n key='dao.picturesheadings.fields.maxWeight_up'} {$max_upload_size})
            
            <br />
            <input size="35" type="file" name="imageFile">
            <br />
            {i18n key='pictures.messages.or'} {i18n key='pictures.message.path'}
            <br />
            <input size="48" type="text" name="url_pict" value="{$toEdit->url_pict|escape}">
            </fieldset>
       </td>
   </tr>
   {else}
   <tr><td colspan="2">
         {if $toEdit->url_pict}
         <img src="{$toEdit->url_pict}"/>
         {else}
         <img src="{copixurl dest="pictures||get" id_pict=$toEdit->id_pict width=300}"/>
         {/if}
         </td></tr>
   {/if}
</table>
<p class="validButtons">
<input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
<input type="button" value="{$WFLBestActionCaption}" onclick="return doUrl('{copixurl dest=pictures|admin|validPicture doBest=1}')" />
<input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:window.location='{copixurl dest="pictures|admin|cancelEditPicture"}'" />
</p>
</form>
