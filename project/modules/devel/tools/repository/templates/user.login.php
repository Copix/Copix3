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
      <table>
       <tr>
        <th><?php echo _i18n('auth|auth.login'); ?></th>
        <td><input type="text" name="login" id="login" size="9"
			value="<?php _etag ('escape', $ppo->login); ?>" /></td>
       </tr>
       <tr>
        <th><?php echo _i18n('auth|auth.password'); ?></th>
        <td><input type="password" name="password" id="password" size="9" /></td>
       </tr>
       <?php if($ppo->ask_rememberme){ ?>
       <tr>
        <th><?php echo _i18n('auth|auth.rememberme'); ?></th>
        <td><input type="checkbox" name="rememberme" id="rememberme" value="yes" /></td>
       </tr>
       <?php } ?>
       </table>
       <?php if ($ppo->auth_url_return) { ?>
          <input type="hidden" value="<?php echo htmlentities ($ppo->auth_url_return); ?>" name="auth_url_return" />
       <?php } ?>
       <input type="submit" value="Login">
   </form>
<?php }
      if ($ppo->user) { 
?>
    <?php echo _i18n('auth|auth.connectedAs', array ('login'=>$ppo->user->getCaption ())); ?><br />
    <form action="<?php echo _url("auth|log|out", array ('auth_url_return'=>$ppo->auth_url_return)); ?>" method="get">
    <p>Action : <br/>
    <a href="<?php echo _url ('repository|file|list'); ?>">Liste des fichiers</a><br/>
  	<a href="<?php echo _url ('repository|file|upload'); ?>">Ajouter un fichier</a></p>
    
    <input type="submit" value="Logout">
    </form>
<?php } ?>