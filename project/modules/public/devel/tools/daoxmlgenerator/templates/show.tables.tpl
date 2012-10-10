<h2>{i18n key="daoxmlgenerator.select.title"}</h2><br />
<form method="post" action="{copixurl dest=showXML}">
<table witdh="100%">
<tr>
   <td valign="top">
   <b>Tables:</b><br/><br/>
      <select name="tablename[]" size={$nbr} multiple>
      {foreach from=$tables item=table}
      <option value={$table}>{$table}</option>
      {/foreach}
      </select><br/>
   </td>
   <td valign="top">
   <b>Options: </b><br />
   <input type="checkbox" name="xmlheader" value="1" {if $xmlheader or $xmlheader==""}checked{/if}> Inclure la balise XML en ent&ecirc;te ?<br />
   <hr>
   <b>Type:</b><br />
   <input type="radio" name="xmltype" value="dao" {if $xmltype=="dao"}checked{/if} disabled/>Copix DAO V0<br />
   <input type="radio" name="xmltype" value="daov1" {if $xmltype=="daov1"}checked{/if} disabled/>Copix DAO V1<br />
   <input type="radio" name="xmltype" value="daov2" {if $xmltype=="daov2" or $xmltype==""}checked{/if} />Copix DAO V2<br />
   <!-- <input type="radio" name="xmltype" value="struct">XML structure ?<br /> -->
   <br />
   {i18n key="daoxmlgenerator.encoding"} :
   <select name="iso" >
      <option value="iso-8859-1" {if $iso=="iso-8859-1" or $iso==""} selected {/if}>iso-8859-1</option>
      <option value="iso-8859-7" {if $iso=="iso-8859-7"} selected {/if}>iso-8859-7</option>
      <option value="iso-8859-9" {if $iso=="iso-8859-9"} selected {/if}>iso-8859-9</option>
      <option value="iso-8859-15" {if $iso=="iso-8859-15"} selected {/if}>iso-8859-15</option>
      <option value="utf-8" {if $iso=="utf-8"} selected {/if}>utf-8</option>
      <option value="utf-16" {if $iso=="utf-16"} selected {/if}>utf-16</option>

      <option value="cp-866" {if $iso=="cp-866"} selected {/if}>iso-8859-1</option>
      <option value="sjis" {if $iso=="sjis"} selected {/if}>sjis</option>
      <option value="euc-kr" {if $iso=="euc-kr"} selected {/if}>euc-kr</option>

      <option value="win1250" {if $iso=="win1250"} selected {/if}>win1250</option>
      <option value="win1251" {if $iso=="win1251"} selected {/if}>win1251</option>
      <option value="win1252" {if $iso=="win1252"} selected {/if}>win1252</option>
      <option value="win1256" {if $iso=="win1256"} selected {/if}>win1256</option>
    </select>
    <br />
    <input type="submit" name="submitTables" value="{i18n key="daoxmlgenerator.get.xml"}">
   </td>
</tr>
</table>
</form>