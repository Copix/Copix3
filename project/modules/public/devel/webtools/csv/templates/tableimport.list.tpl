{mootools}

<br />

<table class="CopixTable">
	<thead>
		<tr>
			<th>{i18n key='csv.table'}</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$ppo->tabTable item=objTable}
		    <tr {cycle values=",class='alternate'"}>
		      <td>{$objTable|escape}</td>
			  {copixurl dest="import|choosefile" nomTable=$objTable assign=nameUrl}
			  <td>{copixicon type=select href=$nameUrl}</td>
		      
		{/foreach}
	</tbody>
</table>

<br />

<input type="button" value="{i18n key='csv.return'}" onclick="javascript:document.location.href='{copixurl dest="admin|default|"}'" />
