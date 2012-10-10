{if $ppo->sending eq 'true'}
	{if $ppo->errors}
	<div class="errorMessage">
	<h1>{i18n key="copix:common.messages.error"}</h1>
	<ul>
		{foreach from=$ppo->errors item=error}
			<li>{$error}</li>
		{/foreach}
	</ul>
	</div>
	{else}
	<h1>{i18n key="email.sending"}</h1>
	{/if}
{/if}
{if $ppo->infomail.enable eq 0}
<p>
	{i18n key="email.warning"}
	<a href="{copixurl dest="admin|parameters|edit" choiceModule="default"}">{i18n key="email.configure"}</a>
</p>
{else}
	<p>
	{i18n key="email.method"} : {$ppo->infomail.method}
	{if $ppo->infomail.method eq "smtp"}
	<br/>L'hôte smtp que vous avez spécifié est {$ppo->infomail.smtp}, si le mail n'arrive pas vérifiez bien que cet hôte est bon.
	{/if}
	{if $ppo->mail.from eq ""}
		Attention il n'y a pas d'adresse par défaut correcte pour l'envoie
	{/if}
	</p>
	<br/>
	<form action="{copixurl dest="admin|email|send"}"  method="post">
		<table>
			<tr>
				<th><label for="mailfrom">{i18n key="email.From"}</label></th>
				<td>{inputtext id="mailfrom" name="mailfrom" size="50" value=$ppo->mail.from}</td>
			</tr>
			<tr>
				<th><label for="mailfromname">{i18n key="email.FromName"}</label></th>
				<td>{inputtext id="mailfromname" name="mailfromname" size="50" value=$ppo->mail.fromname}</td>
			</tr>
			<tr>
				<th><label for="maildest">{i18n key="email.Dest"}</label></th>
				<td>{inputtext id="maildest" name="maildest" size="50" value=$ppo->mail.dest}</td>
			</tr>
			<tr>
				<th><label for="mailcc">{i18n key="email.CC"}</label></th>
				<td>{inputtext id="mailcc" name="mailcc" size="50" value=$ppo->mail.cc}</td>
			</tr>
			<tr>
				<th><label for="mailcci">{i18n key="email.CCi"}</label></th>
				<td>{inputtext id="mailcci" name="mailcci" size="50" value=$ppo->mail.cci}</td>
			</tr>
		</table>
		<br/>
		<table>
			<tr>
				<th><label for="mailtitle">{i18n key="email.Title"}</label></th>
				<td>{inputtext id="mailtitle" name="mailtitle" size="60" value=$ppo->mail.subject}</td>
			</tr>
			<tr>
				<th><label for="mailmsg">{i18n key="email.Message"}</label></th>
				<td><textarea id="mailmsg" name="mailmsg" rows="5" cols="60">{$ppo->mail.msg}</textarea></td>
			</tr>
		</table>
		<p align="center">
			<input type="submit" value="{i18n key="copix:common.buttons.send"}" />
		</p>
	</form>
{/if}
{back url="admin||"}