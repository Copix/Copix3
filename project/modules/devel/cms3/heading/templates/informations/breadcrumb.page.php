 <?php echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => 'Fil d\'ariane', 'icon' => _resource ('heading|img/togglers/breadcrumb.png'))) ?>

<div class="element">
	<div class="elementContent">
		<table class="CopixVerticalTable">
			<tr>
				<th style="width: 40px">Type</th>
				<td>
					<?php
					_eTag ('radiobutton', array (
						'name' => 'breadcrumb_type_page',
						'values' => array (
							PageServices::BREADCRUMB_TYPE_AUTO => 'Rubriques parentes',
							PageServices::BREADCRUMB_TYPE_NONE => 'Aucun',
						),
						'selected' => $page->breadcrumb_type_page
					))
					?>
				</td>
			</tr>
		</table>
	</div>
</div>