 <?php echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => 'Notes d\'administration', 'icon' => _resource ('heading|img/togglers/comments.png'))) ?>

<div class="element">
	<div class="elementContent">
		<textarea name="comment_hei" class="comment_hei" rows="10"><?php echo $record->comment_hei ?></textarea>
	</div>
</div>