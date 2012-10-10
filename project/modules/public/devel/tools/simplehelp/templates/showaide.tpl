
{if $displayAide}
	{if $popup}
		<a style="background-image: none;" href=# onclick="window.open('{copixurl dest="simplehelp|display|" id_sh=$aide->id_sh}', 'Aide', 'height=400, width=670, scrollbars=yes');">
		{copixpicture resource="img/tools/information.png"}
		</a>
	{else}
	{popupinformation}
		{$aide->contenu_sh}
	{/popupinformation}
	{/if}
{/if}