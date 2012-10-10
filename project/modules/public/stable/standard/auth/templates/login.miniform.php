<?php if (!$ppo->user) { ?>
  <form action="<?php echo _url("auth|log|in"); ?>" method="post" id="loginForm">
      <table>
       <tr>
        <td><input type="text" name="login" id="login" size="9"
			value="<?php _etag ('escape', $ppo->login); ?>" /></td>
       </tr>
       <tr>
		<td><input type="password" name="password" id="password" size="9" /> <input
			type="image" src="<?php echo _resource("img/tools/login.png"); ?>"
			value="<?php echo _i18n ("copix:common.buttons.login"); ?>" /></td>
	</tr>
       </table>
       <?php if ($ppo->auth_url_return) { ?>
       <input type="hidden" value="<?php echo htmlentities ($ppo->auth_url_return); ?>" name="auth_url_return" />
<?php } ?>
   </form>
<?php }else{ ?>
    <p><?php echo $ppo->user->getCaption (); ?>
    <a
	href="<?php echo _url("auth|log|out", array ('auth_url_return'=>$ppo->auth_url_return)); ?>">
<img src="<?php echo _resource ("img/tools/logout.png"); ?>"
	alt="<?php echo _i18n('copix:common.buttons.login'); ?>"
	title="<?php echo _i18n ('copix:common.buttons.logout'); ?>" /></a></p>
<?php } ?>