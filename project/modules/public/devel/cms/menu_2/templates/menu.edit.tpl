
{*
 * formulaire d'édition d'un menu.
 * params: toEdit : le menu à éditer.
 * params: errors: error object
 * params: e - tells if there are some errors.
 * params: arrayAllMenus
 *}
{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl){
   var myForm = document.menuEdit;
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
</div>
{/if}
<p>TEST : {$id_head}</p>
<form action="{copixurl dest="menu_2|admin|valid" id_head=$id_head}" method="post" name="menuEdit">
   <table>
      <tr>
       <th><label for="caption_menu">{i18n key="dao.menu.fields.caption_menu"}</label></th>
       <td><input id="caption_menu" type="text" size="48" name="caption_menu" value="{$toEdit->caption_menu|escape}"/></td>
      </tr>
      
      {if $toEdit->father_menu == null}
      <tr>
       <th><label for="var_name_menu">{i18n key="dao.menu.fields.var_name_menu"}</label></th>
       <td><input id="var_name_menu" type="text" size="48" name="var_name_menu" value="{$toEdit->var_name_menu|escape}"/></td>
      </tr>
      <tr>
       <th>{i18n key="menu.messages.tpl"} : </th>
       <td>{copixtag type='select' name='tpl_menu' values=$arTpl objectMap="" selected=$toEdit->tpl_menu|escape}</td>
      <tr>
      {/if}
      <tr>
       <th><label for="tooltip_menu">{i18n key="dao.menu.fields.tooltip_menu"}</label></th>
       <td><input type="text" id="tooltip_menu" size="48" name="tooltip_menu" value="{$toEdit->tooltip_menu|escape}"/></td>
      </tr>
      <tr>
       <th>{i18n key="menu.title.link"}</th>
       <td><fieldset>
            <input type="radio" name="typelink_menu" value="cmsp" {if ! $cmsOk}disabled="disabled"{/if} {if $toEdit->typelink_menu=='cmsp'}checked=checked{/if} />
            {i18n key="menu.messages.page"}
            {if $cmsId neq null}
               <a href="{copixurl dest="cms||get" id=$cmsId}" target="blank">{$cmsPageName}</a>
            {/if}
            <input type="button" value="{i18n key="copix:common.buttons.select"}" onclick="javascript:doUrl ('{copixurl dest="menu_2|admin|selectPage"}')" {if ! $cmsOk}disabled="disabled"{/if} />
            <br />
            <input type="radio" name="typelink_menu" value="string" {if $toEdit->typelink_menu=='string'}checked=checked{/if} />
            {i18n key="menu.messages.url"}
            <input type="text" size="40" name="url_menu" value="{$toEdit->url_menu|escape}"/>
           </fieldset>
       </td>
      </tr>
      <tr>
         <th>{i18n key="menu.title.target"}</th>
         <td>
            <fieldset>
             <input value="0" type="radio" name="popup_menu" {if $toEdit->popup_menu == 0}checked="checked"{/if} />{i18n key="menu.messages.normal"}<br />
             <input value="1" type="radio" name="popup_menu" {if $toEdit->popup_menu == 1}checked="checked"{/if} />{i18n key="menu.messages.newWindow"}<br />
             <input value="2" type="radio" name="popup_menu" {if $toEdit->popup_menu == 2}checked="checked"{/if} />{i18n key="menu.messages.popup"}<br />
             <table>
              <tr>
               <th>{i18n key="menu.messages.width"} : </th>
               <td><input name="width_menu" type="text" value="{$toEdit->width_menu}" /></td>
              </tr>
              <tr>
               <th>{i18n key="menu.messages.height"} : </th>
               <td><input name="height_menu" type="text" value="{$toEdit->height_menu}" /></td>
              </tr>
              </table>             
            </fieldset>
         </td>
      </tr>
   </table>
   <input type="submit" value="{i18n key="copix:common.buttons.valid"}" />
   <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:window.location='{copixurl dest="menu_2|admin|cancelEdit" id_head=$id_head}'" />
</form>
{formfocus id='caption_menu'}