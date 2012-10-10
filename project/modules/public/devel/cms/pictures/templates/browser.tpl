{literal}
<script type="text/javascript">
   function toggleDisplayAdvancedSearch() {
      if (document.getElementById('advancedSearch').style.display=='none') {
         document.getElementById('advancedSearch').style.display='';
      }else{
         document.getElementById('advancedSearch').style.display='none';
      }
      if (document.getElementById('advancedSearchHeading').style.display=='none') {
         document.getElementById('advancedSearchHeading').style.display='';
      }else{
         document.getElementById('advancedSearchHeading').style.display='none';
      }
      if (document.getElementById('advancedSearchHeadingTitle').style.display=='none') {
         document.getElementById('advancedSearchHeadingTitle').style.display='';
      }else{
         document.getElementById('advancedSearchHeadingTitle').style.display='none';
      }
   }
</script>
{/literal}

{if $popup=='FCKEDITOR'}					
{literal}
<script>
var oEditor = window.parent.InnerDialogLoaded() ;
function addImg( strTmp, alt ){
	oImage = oEditor.FCK.CreateElement( 'IMG' ) ;
	oImage.src = strTmp ;
	oImage.setAttribute( "alt" , alt ) ;		
	parent.window.close();
}
</script>
{/literal}
{/if}

<form action="{copixurl dest="pictures|browser|validParams" back=$back|urlencode select=$select|urlencode popup=$popup id_head=$id_head}" method="post" class="CopixForm">
<table class="CopixTable">
   <thead>
   <tr>
      <th>{i18n key='browser.keyWord'}</th>
      <th>{i18n key='browser.theme'}</th>
   </tr>
   </thead>
   <tfoot>
      <tr>
         <td colspan="2"><input type="submit" value="{i18n key="copix:common.buttons.search"}" /></td>
      </tr>
   </tfoot>
   <tbody>
   <tr>
      <td><input type="text" name="keyWord" value="{$searchParams->keyWord}" size="20" /></td>
      <td>
         {foreach from=$themesList item=themes}
         <input type="checkbox" name="theme[]" value="{$themes->id_tpic}" {foreach from=$searchParams->theme item=theme}{if $theme== $themes->id_tpic}checked="checked"{/if}{/foreach}/>{$themes->name_tpic}
         {foreachelse}
         {i18n key="browser.noTheme"}
         {/foreach}</td>
   </tr>
   <tr>
      <th colspan="2"><a href="#" onclick="Javascript:toggleDisplayAdvancedSearch('');">{i18n key='browser.advancedSearch'}</a></th>
   </tr>
   <tr id="advancedSearch" style="display:none;">
      <td>
         <table>
            <tbody>
            <tr><td>{i18n key='browser.format'}</td>
                <td><select name="format">
                     <option value="all" {if $searchParams->format=='all'}selected="selected"{/if}>{i18n key='browser.all'}</option>
                     {foreach from=$formatList item=format}
                     <option value="{$format}" {if $searchParams->format==$format}selected="selected"{/if}>{$format}</option>
                     {/foreach}
                     </select></td></tr>
            <tr><td>{i18n key='browser.maxWeight'}</td>
                <td><input type="text" name="maxWeight" value="{$searchParams->maxWeight}" size="5" /></td></tr>
            <tr><td>{i18n key='browser.maxWidth'}</td>
                <td><input type="text" name="maxWidth" value="{$searchParams->maxWidth}" size="5" /></td></tr>
            </tbody>
         </table>
      </td>
      <td>
         <table>
            <tr><td>{i18n key='browser.maxHeight'}</td>
                <td><input type="text" name="maxHeight" value="{$searchParams->maxHeight}" size="5" /></td></tr>
            <tr><td>{i18n key='browser.rows'}</td>
                <td><input type="text" name="rows" value="{$searchParams->rows}" size="5" /></td></tr>
            <tr><td>{i18n key='browser.cols'}</td>
                <td><input type="text" name="cols" value="{$searchParams->cols}" size="5" /></td></tr>
         </table>
      </td>
   </tr>
   <tr id="advancedSearchHeadingTitle" style="display:none;">
      <th colspan="2">{i18n key='browser.headings'}</th>
   </tr>
   <tr id="advancedSearchHeading" style="display:none;">
      <td colspan="2">
         <table>
            <tr>
               <td>
                  <table>
                  <tr>
                  {assign var=i value=0}
                  {foreach from=$catList item=categorie}
                  {if $i eq 4}
                  {assign var=i value=0}
                  </tr><tr>
                  {/if}
                   <td><input type="checkbox" name="category[]" value="{$categorie->id_head}" {foreach from=$searchParams->category item=cat}{if $cat==$categorie->id_head}checked="checked"{/if}{/foreach} />{$categorie->caption}</td>
                  {assign var=i value=$i+1}
                  {foreachelse}
                   <td>{i18n key="browser.noCategory"}</td>
                  {/foreach}
                  </tr>
                  </table>
                  </td>
            </tr>
         </table>
      </td>
   </tr>
   </tbody>
</table>
</form>

{assign var=i value=0}
{assign var=colspan value=0}
{if count ($pictures)}
<table cellSpacing="5" cellPadding="5" class="CopixTable" style="width:auto;">
   <tr>
   {foreach from=$pictures item=picture key=index}
      {if $i eq $searchParams->cols}
         </tr><tr>
         {assign var=colspan value=$searchParams->cols}
         {assign var=i value=0}
      {/if}
      <td>
         <table class="PictureBrowser" style="width:{$maxX}px;height:{$maxY}px;">
            <tr>
               <td style="width:{$maxX}px;height:{$maxY}px;">
                  {if $popup=='HTMLArea'}
                     <a href="#" onclick="window.opener.{$select}.insertHTML('{if $picture->url_pict}<img src=\'{$picture->url_pict}\'{else}<img src=\'{copixurl dest="pictures||get" id_pict=$picture->id_pict}\'{/if} alt=\'{$picture->name_pict|addslashes}\' border=\'0\'>');window.close();">
                  {elseif $popup=='FCKEDITOR'}
                     <a href="#" onclick="javascript:addImg('{if $picture->url_pict}{$picture->url_pict}{else}{copixurl dest="pictures||get" id_pict=$picture->id_pict}{/if}', '{$picture->name_pict|addslashes}');">
                  {else}
                     {if $select}
                        <a href="{copixurl appendFrom=$select id=$picture->id_pict}">
                     {else}
                        <a href="{copixurl dest="pictures||getFull" id_pict=$picture->id_pict id_head=$id_head}">
                     {/if}
                  {/if}
                 {if $picture->url_pict}
                 <img src="{$picture->url_pict}" alt="{$picture->name_pict}" border="0" height="{$maxY}" width="{$maxX}"/>
                 {else}
                 <img src="{copixurl dest="pictures||get" id_pict=$picture->id_pict width=$maxX height=$maxY}" alt="{$picture->name_pict}" border="0"
                  {if (($picture->x_pict)<($picture->y_pict))}
                     {if (($picture->y_pict)>($maxY))}
                        height="{$maxY}"
                     {/if}
                  {else}
                     {if (($picture->x_pict)>($maxX))}
                        width="{$maxX}"
                     {/if}
                  {/if}/>
                 {/if}
                  </a>
               </td>
            </tr>
             <tr>
               <td class="TitlePict">{$picture->name_pict}</td>
            </tr>
         </table>
      </td>
      {assign var=i value=$i+1}
   {/foreach}
   {if $i<$searchParams->cols}
      {if $colspan eq 0}
         {assign var=colspan value=$i+1}
      {/if}
      <td style="width:90%">&nbsp;</td>
   {/if}
   </tr>
   <tfoot>
   <tr>
      <td style="text-align:center;" colspan="{$colspan}">{$pager}</td>
   </tr>
   </tfoot>
</table>
{else}
 <p>{i18n key=browser.noPicture}</p>
{/if}

{if $back}
 <p><input type="button" onclick="javascript:document.location='{$back}'" value="{i18n key="copix:common.buttons.back"}"/></p>
{/if}
{if $contribEnabled}
 <a href="{copixurl dest="copixheadings|admin|" browse="pictures" id_head=$id_head}">{i18n key='browser.backToAdmin'}</a>
{/if}