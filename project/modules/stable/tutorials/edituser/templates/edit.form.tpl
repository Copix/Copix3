{if count ($ppo->errors) > 0}
	<div class="errorMessage">
		<ul>
			{foreach from=$ppo->errors item=error}
				<li>{$error}</li>
			{/foreach}
		</ul>
	</div>
	<br />
{/if}
<form action="{copixurl dest="edituser||edit"}" method="post">
<table class="CopixVerticalTable">
	<tr>
		<th width="210">Identifiant</th>
		<td>{$ppo->user_id}</td>
	</tr>
	<tr class="alternate">
		<th>Login</th>
		<td><input type="text" name="user_login" size="30" value="{$ppo->user_login}" /></td>
	</tr>
	<tr>
		<th>Mot de passe</th>
		<td><input type="password" name="user_password_1" size="30" /></td>
	</tr>
	<tr class="alternate">
		<th>Mot de passe (confirmation)</th>
		<td><input type="password" name="user_password_2" size="30" /></td>
	</tr>
	<tr>
		<th>Adresse e-mail</th>
		<td><input type="text" name="user_email" size="30" value="{$ppo->user_email}" /></td>
	</tr>
</table>

<br />
<center>
<input type="submit" value="Modifier" />	
</center>
</form>