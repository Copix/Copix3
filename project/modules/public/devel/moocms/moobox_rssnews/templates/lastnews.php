<?php
$feed = $rss->feeds[0];
?>
<h2><?php echo $feed->title;?></h2>
<div>
<?php echo $feed->description; ?>
</div>
<em>From: <?php echo $rss->title; ?> - <?php echo $rss->link; ?></em>

