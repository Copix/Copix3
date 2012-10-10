<input type="hidden" name="exCols" id="exCols" value="<?php echo $cols ?>" />
<table>
	<tr>
		<td>Colonnes :&nbsp;</td>
		<td><?php _eTag ('select', array ('name' => 'cols', 'values' => array (1 => 1, 2 => 2), 'selected' => $cols, 'emptyShow' => false)) ?></td>
	</tr>
	<tr>
		<td>Blocs :&nbsp;</td>
		<td>
			<?php
			$blocs = array(
				'publish' => array ('Dernières publications', 'heading|dashBoardShowPublishs'),
				'actions' => array ('Historique', 'heading|dashBoardShowHistory'),
				'drafts' => array ('Brouillons', 'heading|dashBoardShowDrafts'),
				'contentstats' => array ('Statistiques', 'heading|dashBoardShowStats'),
				'bookmarks' => array ('Favoris', 'heading|dashBoardShowBookmarks'),
				'explore' => array ('Navigation', 'heading|dashBoardShowNavigation')
			);
			foreach ($blocs as $id => $infos) {
				$checked = (CopixUserPreferences::get ($infos[1])) ? 'checked="checked"' : null;
				echo '<input type="checkbox" name="bloc_' . $id . '" id="bloc_' . $id . '" ' . $checked . ' />';
				echo '<label for="bloc_' . $id . '"> ' . $infos[0] . '</label>';
				echo '&nbsp;&nbsp;&nbsp;';
				CopixHTMLHeader::addJSDOMReadyCode ("
					$ ('bloc_$id').addEvent ('change', function (pEl) {
						$ ('$id').setStyle ('display', pEl.target.checked ? 'block' : 'none');
						Copix.savePreference ('$infos[1]', pEl.target.checked ? 1 : 0);
					});
				");
			}
			?>
		</td>
	</tr>
</table>