<a href="javascript: void (0);" onclick="Copix.get_developerBar ('<?php echo $idBar ?>').showVars ('Get'); return false;">$_GET</a>
&nbsp;&nbsp;<a href="javascript: void (0);" onclick="Copix.get_developerBar ('<?php echo $idBar ?>').showVars ('Post'); return false;">$_POST</a>
&nbsp;&nbsp;<a href="javascript: void (0);" onclick="Copix.get_developerBar ('<?php echo $idBar ?>').showVars ('Cookie'); return false;">$_COOKIE</a>
&nbsp;&nbsp;<a href="javascript: void (0);" onclick="Copix.get_developerBar ('<?php echo $idBar ?>').showVars ('Session'); return false;">$_SESSION</a>
&nbsp;&nbsp;<a href="javascript: void (0);" onclick="Copix.get_developerBar ('<?php echo $idBar ?>').showVars ('Server'); return false;">$_SERVER</a>
<?php
$showVars = array ('get' => 'Get', 'post' => 'Post', 'cookie' => 'Cookie', 'server' => 'Server');
foreach ($showVars as $key => $divName) {
	?>
	<div id="<?php echo $idBar ?>Vars<?php echo $divName ?>" style="display: none">
		<table class="CopixTable">
			<tr>
				<th>Variable</th>
				<th>Valeur</th>
			</tr>
			<?php
			$alternate = null;
			foreach ($values[$key] as $var => $value) {
				$alternate = ($alternate == null) ? 'class="alternate"' : null;
				?>
				<tr <?php echo $alternate ?>>
					<td><?php echo $var ?></td>
					<td><?php echo DeveloperBar::dump ($value) ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>
<?php } ?>

<div id="<?php echo $idBar ?>VarsSession" style="display: none">
	<?php foreach ($values['session'] as $namespace => $session) { ?>
		<h2><?php echo $namespace ?></h2>
		<table class="CopixTable">
			<tr>
				<th>Variable</th>
				<th>Valeur</th>
			</tr>
			<?php
			$alternate = null;
			foreach ($session as $sessionName => $sessionValue) {
				$alternate = ($alternate == null) ? 'class="alternate"' : null;
				?>
				<tr <?php echo $alternate ?>>
					<td><?php echo $sessionName ?></td>
					<td><?php echo $sessionValue ?></td>
				</tr>
			<?php } ?>
		</table>
	<?php } ?>
</div>