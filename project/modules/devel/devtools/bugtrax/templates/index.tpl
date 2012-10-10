{literal}
<style type="text/css">
.bugrow:hover {
	cursor: pointer;
}
</style>
{/literal}
<h2>Bugs</h2>

<p>
<a href="{copixurl dest="bugtrax||newbug"}"><img src="{copixresource path="img/tools/new.png"}" alt="New"/> Add Bug</a>
</p>
<p>
<table class="CopixTable">
<tr>
	<th>&nbsp;</th>
	<th>ID</th>
	<th>Severity</th>
	<th>Group</th>
	<th>Title</th>
	<th>Date</th>
	<th>State</th>
</tr>
{foreach from=$ppo->bugs item=bug}
<tr class="bugrow" style="background-color: {$bug->color}" onclick="javascript:document.location.href='{copixurl dest="bugtrax||showbug" id_bug=$bug->id_bug}';">
	<td><a href="{copixurl dest="bugtrax||showbug" id_bug=$bug->id_bug}"><img src="{copixresource path="img/tools/show.png"}" alt="Show" /></a></td>
	<td>{$bug->id_bug}</td>
	<td>{$bug->severity_bug}</td>
	<td>{$bug->heading->heading_bughead} - {$bug->heading->version_bughead}</td>
	<td>{$bug->name_bug}</td>
	<td>{$bug->date_bug} - modified on {$bug->modificationdate_bug}</td>
	<td>{$bug->state_bug}</td>
</tr>
{/foreach}
</table>
</p>
<p>
<a href="{copixurl dest="bugtrax||newbug"}"><img src="{copixresource path="img/tools/new.png"}" alt="New"/> Add Bug</a>
</p>
