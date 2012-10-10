<div style="margin: auto;width: 400px;text-align: center;">
	<br />
	<br />
	<br />
	<br />
	<?php if ($ppo->failed) { ?>
	<div class="errorMessage">
	 <h1><?php echo _i18n ('copix:common.messages.error'); ?></h1>
	 <?php _etag ('ulli', array ('values'=>$ppo->failed)); ?>
	</div>
	<?php } ?>
	<?php if (!$ppo->user || $ppo->noCredential) { ?>
		<?php if ($ppo->createUser) { ?>
		<p><a href="<?php echo _url("auth|usersregister|edit"); ?>">
		<?php echo _i18n('auth|auth.user.create'); ?></a></p>
		<?php } ?>
	  	<form action="<?php echo _url("auth|log|in", ($ppo->noCredential) ? array('noCredential'=>true):array()); ?>" method="post" id="loginForm">
			<div class="bloclogin">
		        <label for="login"><?php echo _i18n('auth|auth.login'); ?></label><br />
		        <input type="text" name="login" id="login" size="9"	value="<?php _etag ('escape', $ppo->login); ?>" />
	
				<label for="password"><?php echo _i18n('auth|auth.password'); ?></label><br />
		        <input type="password" name="password" id="password" size="9" />
		       	   
		       <?php if ($ppo->auth_url_return) { ?>
		          <input type="hidden" value="<?php echo htmlentities ($ppo->auth_url_return); ?>" name="auth_url_return" />
		       <?php } ?>
		       <input type="submit" value="<?php echo _i18n ("copix:common.buttons.login"); ?>" />		       
		       <div style="clear: both;"></div>
	       </div>
	       
	   </form>
	<?php }
	      if ($ppo->user) { 
	?>
	    <p><?php echo _i18n('auth|auth.connectedAs', array ('login'=>$ppo->user->getCaption ())); ?><br />
	    <a
		href="<?php echo _url("auth|log|out", array ('auth_url_return'=>$ppo->auth_url_return)); ?>">
	<img src="<?php echo _resource ("img/tools/logout.png"); ?>"
		alt="<?php echo _i18n('copix:common.buttons.login'); ?>"
		title="<?php echo _i18n ('copix:common.buttons.logout'); ?>" /></a></p>
	<?php } 
	_eTag ('formfocus', array ('id'=>'login'));
	?>
</div>