{if $ppo->testConnection === true}
	<font color="green">{i18n key="database2.testConnectionOK"}</font>
	<br />
	{if $ppo->copixIsInstalled}
		{i18n key="database2.testConnection.copixIsInstalled"}
	{else}
		{i18n key="database2.testConnection.copixIsNotInstalled"}
		<script type="text/javascript">
		$('installCopix').style.display = '';
		canAddProfile = true;
		</script>
	{/if}
{else}
	<font color="red">{$ppo->testConnection}</font>
{/if}