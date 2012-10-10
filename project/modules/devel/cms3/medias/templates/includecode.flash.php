<?php
$id = isset($privateId) ? $privateId : uniqid();
if (isset ($media)) {
    $url_media = isset($admin) && $admin ? _url('medias|mediafront|getmedia', array('id_media'=>$media->id_media)) : _url('heading||', array('public_id'=>$media->public_id_hei));
    $url_image_alternative = (!empty ($media->image_media)) ? _url ('heading||', array('public_id'=>$media->public_id_hei, 'image_alternative' => 1)) : '';
    // http://mootools.net/docs/core/Utilities/Swiff
    $params = new CopixParameterHandler();
    $params->setParams($options);
    $variable = $params->getParam ('variable');
    $version = $params->getParam('version', 0, 'numeric', true);
    $typeAffichage = $params->getParam ('typeAffichage', 'window');
    // TODO : parser le texte
    $contenuAlternatif = $params->getParam ('contenuAlternatif', '');
?>
<div id="media_<?php echo $id;?>">
<?php if(!empty ($contenuAlternatif)) {
    echo $contenuAlternatif;
}
elseif (!empty ($url_image_alternative)) { ?>
    <img src="<?php echo $url_image_alternative;?>" width="<?php echo $width;?>" height="<?php echo $height;?>" alt="" />
<?php } ?>
</div>
<script type="text/javascript">
var iRequiredVersion = <?php echo $version;?>;
if (iRequiredVersion <= Browser.Plugins.Flash.version) {
    if (typeof swfobject != 'Swiff') {
        var mySwiff = new Swiff('<?php echo $url_media; ?>', {
            width: <?php echo $width;?>,
            height: <?php echo $height;?>,
            params: {
                wmode: '<?php echo $typeAffichage;?>',
                bgcolor: '#ffffff',
                allowScriptAccess: 'always',
                allowFullScreen: 'false',
                quality: 'high'
            },
            vars: '<?php echo addslashes($variable);?>'.parseQueryString(),
            container: $('media_<?php echo $id;?>')
        });
    }
}
</script>
<?php }?>