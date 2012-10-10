<?php
$options = $portlet->getOptions ();
// Nombre d'éléments à afficher
$iNbItem = intval ($portlet->getOption ('nb_item'));
if ($iNbItem == 0) {
    $iNbItem = 4;
}
// Titre de la portlet
$title = $portlet->getOption ('title', false);
?>
<?php if ($title) {?>
<h1><?php _eTag('escape', $title);?></h1>
<?php }?>
<ul>
    <?php
    // affichage des x items les plus récents
    for($i = 0; ($i < $iNbItem) && ($feeds_iterator->valid()); $i++) {
        $oFeed = $feeds_iterator->current();
    ?>
    <li>
        <?php echo CopixDateTime::yyyymmddToDate (CopixDateTime::GMTToyyyymmdd ($oFeed->pubDate));?>
        -
        <a href="<?php echo $oFeed->link;?>" target="_blank">
        <?php echo $oFeed->title;?>
        </a>
    </li>
    <?php
        $feeds_iterator->next();
    }?>
</ul>