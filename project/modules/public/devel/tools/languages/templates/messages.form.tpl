{mootools}

{literal}
<script type="text/javascript">
function addMessage (key, value) {
  $ ('key').value = key;
  $ ('value').value = value;
  $ ('addMessage').submit ();
}
</script>
{/literal}

{if count ($ppo->arErrors)}
  {if count ($ppo->arErrors) == 1}
    {assign var=title_key value='global.title.error'}
  {else}
    {assign var=title_key value='global.title.errors'}
  {/if}
  <div class="errorMessage">
  <h1>{i18n key="$title_key"}</h1>
  {ulli values=$ppo->arErrors}
  </div>
{/if}

<h2>{i18n key="global.title.addSection"}</h2>
<form action="{copixurl dest="addSection"}" id="addSection" method="post">
<input type="hidden" name="moduleName" id="moduleName" value="{$ppo->moduleName}" />
<input type="hidden" name="file" id="file" value="{$ppo->file}" />
<input type="hidden" name="filemtime" id="file" value="{$ppo->filemtime}" />
{$ppo->mainSection}.<input type="text" name="section" />
<input type="image" src="{copixresource path="img/tools/add.png"}" alt="{i18n key="global.other.add"}" title="{i18n key="global.other.add"}" />
</form>
<br />

{foreach from=$ppo->messagesOrdered key=section item=messages}
<form action="{copixurl dest="edit"}" method="post">
<input type="hidden" name="moduleName" id="moduleName" value="{$ppo->moduleName}" />
<input type="hidden" name="file" id="file" value="{$ppo->file}" />
<input type="hidden" name="filemtime" value="{$ppo->filemtime}" />
<input type="hidden" name="mode" value="doEdit" />
<input type="hidden" name="section" value="{$section}" />

<h2>{$section}</h2>

<table class="CopixVerticalTable">
  {if $section != 'module.xml'}
  <tr>
    <td>{i18n key="global.other.sectionName"}</td>
    <td colspan="2"><input type="text" name="new_section_name" value="{$section}" /></td>
  </tr>
  {/if}
  <tr>
    <td class="alternate" width="170px">{i18n key="global.other.addMessage"}&nbsp;&nbsp;&nbsp;{i18n key="global.other.key"}&nbsp;</td>
    <td class="alternate" nowrap="nowrap" width="10px">      
      <input type="text" id="add_key_{$section}" />
      &nbsp;&nbsp;{i18n key="global.other.value"}&nbsp;
    </td>
    <td><input type="text" id="add_value_{$section}" style="width:100%" /></td>
    </td>
    <td class="alternate" width="20px" align="right" valign="bottom">
      <img src="{copixresource path="img/tools/add.png"}" alt="{i18n key="global.other.add"}" title="{i18n key="global.other.add"}"
        style="cursor:pointer" onclick="javascript: addMessage ('{$section}.' + $('add_key_{$section}').value, $('add_value_{$section}').value)" />
    </td>
  </tr>
</table>
<br />

<table class="CopixVerticalTable">
  {assign var=alternate value=""}
  {foreach from=$messages key=key item=value}
  <tr>
    <td {$alternate} width="150px"><input name="key_{$key}" type="text" value="{$key}" /></td>
    <td {$alternate} align="left">
      <input type="text" name="value_{$key}" value="{$value}" style="width: 100%" />
    </td>
    <td {$alternate} align="right" width="20px">
      <a href="{copixurl dest="delete" moduleName=$ppo->moduleName filemtime=$ppo->filemtime file=$ppo->file message=$section|cat:'.'|cat:$key}"
        ><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="global.other.delete"}" title="{i18n key="global.other.delete"}" border="0"
       /></a>
    </td>
  </tr>
  {if $alternate == ''}
    {assign var=alternate value='class="alternate"'}
  {else}
    {assign var=alternate value=""}
  {/if}
  {/foreach}
</table>

<br />
<div align="center">
  <input type="submit" value="{i18n key="global.other.edit"}" />
</div>
</form>
{/foreach}

<input type="button" value="{i18n key="global.other.back"}" onclick="javascript:document.location='{copixurl dest="languages|"}'" />

<form action="{copixurl dest="addMessage"}" id="addMessage" method="post">
<input type="hidden" name="moduleName" id="moduleName" value="{$ppo->moduleName}" />
<input type="hidden" name="file" id="file" value="{$ppo->file}" />
<input type="hidden" name="filemtime" id="file" value="{$ppo->filemtime}" />
<input type="hidden" name="key" id="key" value="" />
<input type="hidden" name="value" id="value" value="" />
</form>