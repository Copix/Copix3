<?php
$content = '<table class="CopixTable AddBookmarkTable">';
$content .= '<tr>';
$content .= '<th colspan="2">Dossier</th>';
$content .= '<th colspan="4">Ajouter</th>';
$content .= '<th colspan="2"></th>';
$content .= '</tr>';
foreach ($arElements as $element) {
	$content .= CopixZone::process ('HeadingBookMark', array ('element' => $element, 'treeId' => $treeId, 'filters' => $filters));
}
$content .= '</table>';

if ($heading !== null) {
	$content .= '<br /><center>' . _tag ('button', array ('action' => 'add', 'type' => 'button', 'id' => 'addBookmark', 'caption' => 'Ajouter le dossier courant en favoris')) . '</center>';
	CopixHTMLHeader::addJSDOMReadyCode ("$ ('addBookmark').addEvent ('click', function () { addBookmark (" . $heading . "); })");
}

$id = uniqid ('bookmarkCliker');
echo '<img src="' . _resource ('img/tools/bookmark.png') . '" alt="Favoris" title="Favoris" id="' . $id . '" style="cursor: pointer" />';
_eTag ('copixwindow', array (
	'img' => _resource ('img/tools/bookmark.png'),
	'alt' => 'Favoris',
	'title' => 'Favoris',
	'clicker' => $id,
	'canDrag' => false),
$content);
?>