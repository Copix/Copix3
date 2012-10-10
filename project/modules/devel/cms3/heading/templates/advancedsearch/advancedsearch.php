<?php
CopixHTMLHeader::addCSSLink(_resource('heading|css/cms.css'));
CopixHTMLHeader::addCSSLink(_resource('heading|css/headingadmin.css'));

$imgFolded = _resource ('heading|img/browser_folded.png');
$imgUnfolded = _resource ('heading|img/browser_unfolded.png');
$arHeadingElementTypes = _class ('heading|HeadingElementType')->getList ();


$pagination = CopixZone::process('default|pagination', array(
	'linkBase' => _url('#', array('inheading' => $ppo->options->inheading, 'page'=>'')),
	'current' => $ppo->options->page,
	'max' => $ppo->nbrPages,
	'surround' => 2
));

function replace_accents($str) {
	$str = htmlentities($str, ENT_COMPAT, "UTF-8");
	$str = preg_replace(
		'/&([a-zA-Z])(uml|acute|grave|circ|tilde);/',
		'$1',$str);
	return html_entity_decode($str);
}

_eTag ('error', array ('message' => $ppo->errors));
?>
<?php _eTag ('beginblock', array ('title' => 'Recherche', 'isFirst' => true)); ?>
<form method="post" action="<?php echo _url ('heading|advancedsearch|ShowElements'); ?>" id="formSearchOptions">
	<input type="hidden" name="sort" value="<?php echo $ppo->options->sort; ?>" id="cmsSort" />
	<input type="hidden" name="sortOrder" value="<?php echo $ppo->options->sortOrder; ?>" id="cmsSortOrder" />
	<table class="CopixVerticalTable">
		<tr <?php _eTag ('trclass') ?>>
			<th style="width: 200px">
				<label for="status_hei">Nom</label>
			</th>
			<td style="width: 300px">
				<?php _eTag ('inputtext', array ('name' => 'caption_hei', 'value' => $ppo->options->caption_hei)) ?>
			</td>
			<th style="width: 200px">
				<label>A partir de</label>
			</th>
			<td>
				<?php 
				if(is_null($ppo->options->inheading)){
					echo CopixZone::process ('heading|headingelement/headingelementchooser', array('inputElement'=>'inheading', 'linkOnHeading'=>true, 'arTypes'=>array('heading'), 'showAnchor'=>true));
				}else {
					echo CopixZone::process ('heading|headingelement/headingelementchooser', array('inputElement'=>'inheading', 'linkOnHeading'=>true, 'arTypes'=>array('heading'), 'showAnchor'=>true, 'selectedIndex'=>$ppo->options->inheading));
				}
				?>
			</td>
		</tr>
		<tr <?php _eTag ('trclass') ?>>
			<th>
				<label for="status_hei">Statut</label>
			</th>
			<td>
				<?php 
				if ($ppo->options->status_hei == null){
					_eTag ('select', array ('name' => 'status_hei', 'values' => $ppo->statusOptions, 'strict' => true));
				} else {
					_eTag ('select', array ('name' => 'status_hei', 'values' => $ppo->statusOptions, 'selected' => $ppo->options->status_hei));
				}
				?>
			</td>
			<th>
				<label for="nbrParPage">Résultats</label>
			</th>
			<td>
				<?php _eTag ('inputtext', array ('name' => 'nbrParPage', 'value' => $ppo->options->nbrParPage, 'extra' => 'size="3"')) ?>
				par page
			</td>
		</tr>
		<tr <?php _eTag ('trclass') ?>>
			<th>
				<label for="status_hei">Type</label>
			</th>
			<td>
				<?php 
				if ($ppo->options->type_hei == null){
					_eTag ('multipleselect', array ('name' => 'type_hei', 'values' => $ppo->typesOptions)); 
				} else {
					_eTag ('multipleselect', array ('name' => 'type_hei', 'values' => $ppo->typesOptions, 'selected' => $ppo->options->type_hei));
				}
					?>
			</td>
			<th>
				<label for="status_hei">Identifiant public</label>
			</th>
			<td>
				<?php
				_eTag ('inputtext', array ('name' => 'resolve_public_id', 'value' => $ppo->options->resolve_public_id, 'extra' => 'size="5"'));
				echo '&nbsp;';
				_eTag ('popupinformation', array (), 'Permet de chercher tous les éléments en rapport avec cet identifiant public.');
				?>
			</td>
		</tr>
		<tr>
			<th>
				<label for="content">Contient</label>
			</th>
			<td>
				<?php 
					_eTag ('inputtext', array ('name' => 'content', 'value' => $ppo->options->content));
				?>
			</td>
			<th>
				<label for="url_id_hei">Chemin</label>
			</th>
			<td colspan="3">
				<?php _eTag ('inputtext', array ('name' => 'url_id_hei', 'value' => $ppo->options->url_id_hei)); ?>
				<?php _eTag ('popupinformation', array (), "Affiche tous les éléments dont l'URL acctuelle contient ce texte."); ?>
			</td>
		</tr>
	</table>

	<br />
	<div style="text-align: center">
		<?php _eTag ('button', array ('img' => 'img/tools/search.png', 'caption' => 'Rechercher')) ?>
	</div>
</form>
<?php _eTag ('endblock'); ?>
<?php _eTag ('beginblock', array ('title' => 'Résultats', 'isFirst' => true)); ?>
<?php if (count ($ppo->elements) == 0) { ?>
	Aucun élément ne correspond à vos critères de recherche.
<?php } else { ?>

	<div style="text-align: center">
		<?php 
		if ($ppo->export){
			echo CopixZone::process("headingelementexport", array('nbElements'=>$ppo->nbElements));
		}
		echo $pagination; 
		?>
	</div>
	<br />

	<table class="CopixTable">
		<thead>
			<tr>
				<th onclick="submitSort('caption_hei');" class="cmsSort">
					<?php if ($ppo->options->sort == "caption_hei"){
						echo '<img src="'._resource($ppo->options->sortOrder == 'DESC' ? 'img/tools/up.png' : 'img/tools/down.png').'" />';
					}
					?>
					Nom
				</th>
				<th onclick="submitSort('hierarchy_hei');" class="cmsSort">
					<?php if ($ppo->options->sort == "hierarchy_hei"){
						echo '<img src="'._resource($ppo->options->sortOrder == 'DESC' ? 'img/tools/up.png' : 'img/tools/down.png').'" />';
					}
					?>
					Chemin
				</th>
				<th onclick="submitSort('author_caption_update_hei');" class="cmsSort">
					<?php if ($ppo->options->sort == "author_caption_update_hei"){
						echo '<img src="'._resource($ppo->options->sortOrder == 'DESC' ? 'img/tools/up.png' : 'img/tools/down.png').'" />';
					}
					?>
					Modifié par
				</th>
				<th onclick="submitSort('date_update_hei');" class="cmsSort">
					<?php if ($ppo->options->sort == "date_update_hei"){
						echo '<img src="'._resource($ppo->options->sortOrder == 'DESC' ? 'img/tools/up.png' : 'img/tools/down.png').'" />';
					}
					?>
					Modifié le
				</th>
				<?php
					if (!is_numeric($ppo->options->status_hei)){
						?><th onclick="submitSort('status_hei');" class="cmsSort">
							<?php if ($ppo->options->sort == "status_hei"){
								echo '<img src="'._resource($ppo->options->sortOrder == 'DESC' ? 'img/tools/up.png' : 'img/tools/down.png').'" />';
							}
							?>
							Statut
						</th>
						<?php
					}
				?>
			</tr>
		</thead>
		<tbody>
		<?php
		$trClass = null;
		$elementsHELT = array ();
		foreach ($ppo->elements as $index => $element) {
			$path = explode('-', $element->hierarchy_hei);
			$breadcrumb = array ();
			foreach ($path as $id => $value) {
				$elementPath = _ioClass('heading|headingelementinformationservices')->get ($value);
				$visibility = _ioClass ('headingelementinformationservices')->getVisibility ($value, $foo);
				$breadcrumb[$value] = $visibility ? $elementPath->caption_hei : '<span style="color:grey">'.$elementPath->caption_hei.'</span>';
			}
			$data = $element->caption_hei;
			?>
			<tr <?php _eTag ('trclass', array ('id' => 'results')) ?>>
				<td>
					<?php if (in_array($element->type_hei, array('page', 'image')) && $element->status_hei == HeadingElementStatus::PUBLISHED){ ?>
					<a href="<?php echo _url ('heading||', array ('public_id' => $element->public_id_hei)) ?>" title="Voir <?php echo '['.$element->type_hei.'] '.$element->caption_hei; ?>">
					<?php } else { ?>
					<a href="<?php echo _url ('heading|element|prepareEdit', array ('type' => $element->type_hei, 'id' => $element->id_helt, 'heading'=>$element->parent_heading_public_id_hei)); ?>" title="Editer <?php echo '['.$element->type_hei.'] '.$element->caption_hei; ?>">
					<?php } ?>
						<img src="<?php echo _resource($arHeadingElementTypes[$element->type_hei]['icon']); ?>" alt="<?php echo $element->type_hei; ?>" width="18px" height="18px" />
						<?php echo $ppo->options->caption_hei ? str_replace($ppo->options->caption_hei, "<span style='color:#5F5F5F;font-weight:bold;'>".$ppo->options->caption_hei.'</span>', $element->caption_hei) : $element->caption_hei; ?>
					</a>
				</td>
				<td>
					<?php
						foreach ($breadcrumb as $key=>$caption){
							if ($key == 0){
								echo '<a href="'._url('heading|element|', array('heading'=>$key)).'" title="Racine du site"><img src="'._resource("heading|img/url.png").'" alt="Racine du site" /></a>';
								if ($key != $element->public_id_hei){
									echo '&nbsp;<img src="'._resource("img/tools/next.png").'" alt=">" />&nbsp;';
								}
							} else {
								if($key != $element->public_id_hei){
									echo '<a href="'._url('heading|element|', array('heading'=>$key)).'">'.$caption.'</a>&nbsp;<img src="'._resource("img/tools/next.png").'" />&nbsp;';
								} else {
									echo '<label for="title_'.$element->id_helt.'">'.$element->caption_hei.'</label>';
								}
							}
						}
					?>
				</td>
				<td><?php echo $element->author_caption_update_hei; ?></td>
				<td><?php echo CopixDateTime::yyyymmddhhiissToDateTime ($element->date_update_hei); ?></td>
				<?php
				if (!is_numeric($ppo->options->status_hei)){
					?><td><span class="status<?php echo $element->status_hei ?>"><?php echo _class('HeadingElementStatus')->getCaption(intval($element->status_hei)); ?></span></td>
					<?php
				}
				?>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<br />

	<div style="text-align: center">
		<?php echo $pagination ?>
	</div>
	<?php
}

CopixHTMLHeader::addJSCode("
function submitSort(sort){
	$('cmsSort').value = sort;
	$('cmsSortOrder').value = $('cmsSortOrder').value == 'DESC' ? 'ASC' : 'DESC';
	$('formSearchOptions').submit();
}
");

//_eTag ('back', array ('url' => _url ('admin||')));
?>
<?php _eTag ('endblock'); ?>