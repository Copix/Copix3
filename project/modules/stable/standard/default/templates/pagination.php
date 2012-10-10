<?php if ($ppo->max > 1) { ?>
	<div class="pagination">
	<?php if ($ppo->showBounds) { ?>
		<?php if ($ppo->currentPage == 1) { ?>
			<span class="current"><?php echo $ppo->firstPage; ?></span>
		<?php } else { ?>
			<a href="<?php echo $ppo->linkBase; ?>1"><?php echo $ppo->firstPage; ?></a>
		<?php } ?>
	<?php } ?>
	<?php if ($ppo->showNext) { ?>
		<?php if ($ppo->currentPage == 1) { ?>
			<span class="current"><?php echo $ppo->previousPage; ?></span>
		<?php } else { ?>
			<a href="<?php echo $ppo->linkBase.($ppo->currentPage-1); ?>"><?php echo $ppo->previousPage; ?></a>
		<?php } ?>
	<?php } ?>
	
	<?php if ($ppo->loopStart > 1) { ?>
	    <a href="<?php echo $ppo->linkBase; ?>1">1</a>
		<?php if ($ppo->loopStart > 2) { ?>
			<span class="ellipse">...</span>
		<?php } ?>
	<?php } ?>
	
	<?php for ($i = $ppo->loopStart; $i <= $ppo->loopEnd; $i++ ) { ?>
		<?php if ($i != $ppo->currentPage) { ?>
			<a href="<?php echo $ppo->linkBase.$i; ?>"><?php echo $i; ?></a>
		<?php } else { ?>
			<span class="current"><?php echo $ppo->currentPage; ?></span>
		<?php } ?>
	<?php } ?>
	
	<?php if ($ppo->loopEnd != $ppo->max) { ?>
		<?php if ($ppo->loopEnd + 1 != $ppo->max) { ?>
			<span class="ellipse">...</span>
		<?php } ?>
		<a href="<?php echo $ppo->linkBase.$ppo->max; ?>"><?php echo $ppo->max; ?></a>
	<?php } ?>
	
	<?php if ($ppo->showNext) { ?>
		<?php if ($ppo->currentPage == $ppo->max) { ?>
			<span class="current"><?php echo $ppo->nextPage; ?></span>
		<?php } else { ?>
			<a href="<?php echo $ppo->linkBase.($ppo->currentPage+1); ?>"><?php echo $ppo->nextPage; ?></a>
		<?php } ?>
	<?php } ?>
	<?php if ($ppo->showBounds) { ?>
		<?php if ($ppo->currentPage == $ppo->max) { ?>
			<span class="current"><?php echo $ppo->lastPage; ?></span>
		<?php } else { ?>
			<a href="<?php echo $ppo->linkBase.$ppo->max; ?>"><?php echo $ppo->lastPage; ?></a>
		<?php } ?>
	<?php } ?>
	</div>
<?php } ?>