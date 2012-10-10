<?php
$id = isset($privateId) ? $privateId : uniqid();
if (isset ($media)) {
	$url_player = _resource('medias|dewplayer-vol.swf', null, true);
	$url_media = isset($admin) && $admin ? _url('medias|mediafront|getmedia', array('id_media'=>$media->id_media)) : _url('heading||', array('public_id'=>$media->public_id_hei));
?>
<object type="application/x-shockwave-flash" data="<?php echo $url_player; ?>?mp3=mp3/<?php echo $url_media; ?>" width="240" height="20" id="dewplayer<?php echo $id; ?>">
	<param name="wmode" value="transparent" />
	<param name="movie" value="<?php echo $url_player; ?>?mp3=mp3/<?php echo $url_media; ?>" />
</object>
<?php }?>
