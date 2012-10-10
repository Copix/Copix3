<?php
CopixHTMLHeader::addJSLink (_resource ('portal|js/tools.js'));
CopixHTMLHeader::addCSSLink (_resource ('portal|styles/style.css'));
CopixHTMLHeader::addCSSLink (_resource ('heading|css/headingadmin.css'));
_eTag ('mootools', array ('plugins' => 'resize'));

CopixHTMLHeader::addJSDOMReadyCode("
new Resizing('description_hei',{'min':25,'max':400, 'userpreference': 'articles|descriptionHeight'});

$('editor').addEvent('change', function(){
	document.location.href = '"._url('articles|admin|changeeditor')."?editId="._request('editId')."&'+$('articleForm').toQueryString ();
});
");

$imgSrc = _resource('img/tools/help.png');
if (isset ($ppo->error)) {
	_eTag ('error', array ('message' => $ppo->error));
}

_eTag ('beginblock', array ('title' => 'Informations générales', 'isFirst' => true));
?>
<form id="articleForm" action="<?php echo CopixUrl::get ("admin|valid", array("editId" => $ppo->editId)); ?>" enctype="multipart/form-data" method="POST">
<input type="hidden" name="publish" id="publish" value="0" />
<input type="hidden" name="published_date" id="published_date" />
<input type="hidden" name="end_published_date" id="end_published_date" />
<table class="CopixVerticalTable">
	<tr <?php _eTag ('trclass') ?>>
		<th style="width: 100px">Nom</th>
		<th class="help"><?php _eTag('popupinformation', array('width' => '420', 'alt' => "Aide", 'img' => $imgSrc), "Le nom de l'article sera utilisé lors des demandes de téléchargement."); ?></th>
		<td colspan="2"><input type="text" name="caption_hei" class="inputText" value="<?php echo htmlentities($ppo->editedElement->caption_hei, ENT_COMPAT, 'UTF-8'); ?>" style="width: 99%" /></td>
	</tr>
	<?php if ($ppo->chooseHeading !== false){ ?>
	<tr <?php _eTag ('trclass') ?>>
		<th style="width: 100px">Dossier</th>
		<th class="help"><?php _eTag('popupinformation', array('width' => '420', 'alt' => "Aide", 'img' => $imgSrc), "Dossier dans lequel sera enregistré l'article"); ?></th>
		<td colspan="2">
			<?php 
			echo CopixZone::process ('heading|headingelement/headingelementchooser', array('inputElement'=>'parent_heading_public_id_hei', 'linkOnHeading'=>true, 'arTypes'=>array('heading'), 'selectedIndex'=>$ppo->editedElement->parent_heading_public_id_hei));
			?>
		</td>
	</tr>
	<?php }?>
	<tr <?php _eTag ('trclass') ?>>
		<th>Editeur</th>
		<th class="help"></th>
		<td colspan="2">
			<select id="editor" name="editor_article">
				<?php $editor = isset($ppo->editedElement->editor_article) ? $ppo->editedElement->editor_article : CmsEditorServices::WIKI_EDITOR; ?>
				<option value="<?php echo CmsEditorServices::WIKI_EDITOR; ?>" <?php echo $editor == CmsEditorServices::WIKI_EDITOR ? "selected='selected'" : ""; ?>>Wiki</option>
				<option value="<?php echo CmsEditorServices::WYSIWYG_EDITOR; ?>" <?php echo $editor == CmsEditorServices::WYSIWYG_EDITOR ? "selected='selected'" : ""; ?>>WYSIWYG</option>
			</select>
		</td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th>Description</th>
		<th class="help">
			<?php
			_eTag ('popupinformation', array('width' => '450', 'alt' => "Aide", 'img' => $imgSrc),
			"Cette description sera utilisée par les moteurs de recherche lors du référencement, ainsi que par le moteur de recherche interne de CopixCMS, lorsqu'il présentera les résultats de sa recherche.");
			?>
		</th>
		<td>
			<div id="articleDescription" style="display: <?php echo (CopixUserPreferences::get ('articles|showDescription', true)) ? 'block' : 'none' ?>">
				<textarea class="cmsElementDescription" id="description_hei" name="description_hei"
				style="height: <?php echo CopixUserPreferences::get ('articles|descriptionHeight') ?>px"><?php echo $ppo->editedElement->description_hei; ?></textarea>
			</div>
		</td>
		<td style="width: 20px"><?php _eTag ('showdiv', array ('id' => 'articleDescription', 'userpreference' => 'articles|showDescription', 'alternate' => '(Description cachée)')) ?></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th class="last">R&eacute;sum&eacute;</th>
		<th class="help last"></th>
		<td>
			<div id="articleSummary" style="display: <?php echo (CopixUserPreferences::get ('articles|showSummary', true)) ? 'block' : 'none' ?>">
				<?php
				$height = CopixUserPreferences::get ('articles|summaryHeight');
				switch ($editor){
					case CmsEditorServices::WIKI_EDITOR :
						echo CopixZone::process ('cms_editor|cmswikieditor', array('name'=>'summary_article', 'text'=>$ppo->editedElement->summary_article, 'height' => $height));
						break;

					case CmsEditorServices::WYSIWYG_EDITOR :
						echo CopixZone::process ('cms_editor|cmswysiwygeditor', array('name'=>'summary_article', 'text'=>$ppo->editedElement->summary_article, 'height' => $height));
						break;
				}
				?>
			</div>
		</td>
		<td style="width: 20px"><?php _eTag ('showdiv', array ('id' => 'articleSummary', 'userpreference' => 'articles|showSummary', 'alternate' => '(Résumé caché)')) ?></td>
	</tr>
</table>
<?php _eTag ('endblock') ?>

<?php _eTag ('beginblock', array ('title' => 'Contenu')) ?>
<?php
$height = CopixUserPreferences::get ('articles|contentHeight');
switch ($editor){
	case CmsEditorServices::WIKI_EDITOR :
		echo CopixZone::process ('cms_editor|cmswikieditor', array('name'=>'content_article', 'text'=>$ppo->editedElement->content_article, 'height' => $height));
		break;

	case CmsEditorServices::WYSIWYG_EDITOR :
		echo CopixZone::process ('cms_editor|cmswysiwygeditor', array('theme'=>$ppo->theme, 'name'=>'content_article', 'text'=>$ppo->editedElement->content_article, 'height' => $height));
		break;
}

_eTag ('endblock');
echo CopixZone::process ('heading|headingelement/HeadingElementButtons', array ('showBack'=>!$ppo->popup, 'form' => 'articleForm', 'actions' => array ('savedraft', 'savepublish', 'saveplanned'), 'element'=>$ppo->editedElement));
?>
</form>