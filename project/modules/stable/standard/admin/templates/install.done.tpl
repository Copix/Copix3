<div  style="border: 2px solid #FF0000; margin: 5px; padding: 5px;"> 
<p>{i18n key=install.messages.logpassdone}</p>
<p>{i18n key=install.messages.changepass}</p>

<p><strong>login</strong> : {$ppo->loginInformations.login} <br />
<strong>mot de passe </strong>:  {$ppo->loginInformations.password} </p>
</div>

{copixurl dest="admin||" assign=url}
{copixzone process="auth|loginform" auth_url_return=$url}