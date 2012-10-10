<?php
$css = "
textarea {
	width: 100%;
}
.result {
	background: #ffc;
	border: 1px solid #ccc;
}";
CopixHTMLHeader::addStyle ($css);
?>
<h1><?php echo $ppo->TITLE_PAGE; ?></h1>
<p>Pour reprendre un exemple de code, cliquer dessus et le copier en faisant CTRL+A puis CTRL+C. Il suffit ensuite de le coller avec CTRL+V et de l'éditer.</p>

<h2>Titres</h2>
<textarea style="height:6em;"># titre
## titre
...
###### titre</textarea>

<h4>Résultat</h4>
<div class="result">
	<h1>titre</h1>

	<h2>titre</h2>

	<p>...</p>

	<h6>titre</h6>
</div>


<h2>Liens</h2>
<textarea>[texte du lien](cms:12345 "Infobulle (facultative)")</textarea>

<h4>Résultat</h4>
<div class="result">
	<p><a href="cms:12345" title="Infobulle (facultative)">Texte du lien</a></p>
</div>

<h2>Images</h2>
<textarea>![texte alternatif](cms:12345 "Infobulle (facultative)")</textarea>

<h3>Note sur l'accessibilité</h3>
<ul>
	<li>Si l'image contient du texte, le texte alternatif doit reprendre ce texte</li>
	<li>Si l'image n'apporte aucune information, le texte alternatif doit être vide.</li>
	<li>Si l'image est dans un lien, le texte alternatif doit indiquer la cible du lien (sauf si le lien contient déjà du texte pour ça)</li>
</ul>

<h2>Listes</h2>
<h3>Listes à puces</h3>
<textarea> * élément de liste à puce
 * autre élément de liste à puce
 - autre manière de créer une puce</textarea>

<h4>Résultat</h4>
<div class="result">
	<ul>
		<li>élément de liste à puce</li>
		<li>autre élément de liste à puce</li>
		<li>autre manière de créer une puce</li>
	</ul>
</div>

<h3>Listes numérotées</h3>
<textarea> 1. élément de liste numérotée
 2. autre élément de liste numérotée
</textarea>

<h4>Résultat</h4>
<div class="result">
	<ol>
		<li>élément de liste numérotée</li>
		<li>autre élément de liste numérotée</li>
	</ol>
</div>

<h3>Imbriquer des listes</h3>
<textarea style="height:8em"> 1. élément de liste numérotée
 2. autre élément de liste numérotée
    - Liste à puce imbriquée
	- Liste à puce imbriquée
 3. suite de la liste numérotée
</textarea>

<h4>Résultat</h4>
<div class="result">
	<ol>
		<li>élément de liste numérotée</li>
		<li>autre élément de liste numérotée
			<ul>
				<li>Liste à puce imbriquée</li>
				<li>Liste à puce imbriquée</li>
			</ul>
		</li>
		<li>suite de la liste numérotée</li>
	</ol>
</div>

<h3>Listes de définition</h3>
<textarea style="height:12em">Terme à définir

:    Définition du terme

Autre terme

:    Définition de l'autre terme
     - Liste à puce imbriquée
     - Liste à puce imbriquée...</textarea>

<h4>Résultat</h4>
<div class="result">
	<dl>
		<dt>Terme à définir</dt>
			<dd>
				<p>Définition du terme</p>
			</dd>
		<dt>Autre terme</dt>
			<dd>
				<p>Définition de l'autre terme</p>
				<ul>
					<li>Liste à puce imbriquée</li>
					<li>Liste à puce imbriquée...</li>
				</ul>
			</dd>
	</dl>
</div>

<h2>Citations</h2>
<textarea style="height:10em">Texte normal

>   Début de paragraphe en citation
>
>   Autre paragraphe de citation

Paragraphe normal</textarea>

<h4>Résultat</h4>
<div class="result">
	<p>Texte normal</p>
	<blockquote>
	  <p>Début de paragraphe en citation</p>
	  
	  <p>Autre ligne</p>
	</blockquote>
	<p>Paragraphe normal</p>
</div>

<h2>Abréviations</h2>
<textarea>Texte incluant une abréviation (HTML)

*[HTML]: HyperText Markup Language</textarea>

<h4>Résultat</h4>
<div class="result">
	<p>Texte incluant une abréviation (<abbr title="HyperText Markup Language">HTML</abbr>)</p>
</div>