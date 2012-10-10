<h2>Appel sans paramètre</h2>
<p>
{ldelim}popupinformation{rdelim}
  Contenu du popup
{ldelim}/popupinformation{rdelim}
</p>

{popupinformation}
  Contenu du popup
{/popupinformation}

<h2>Avec un contenu HTML</h2>
<p>
{ldelim}popupinformation{rdelim}
  &lt;p style="color: #F00; font-weight: bold;"&gt;Test de style&lt;/p&gt;
{ldelim}/popupinformation{rdelim}
</p>

{popupinformation}
  <p style="color: #F00; font-weight: bold;">Test de style</p>
{/popupinformation}


<h2>Avec alt</h2>
<p>
{ldelim}popupinformation alt="Sur l'image"{rdelim}
  Contenu du popup avec alt
{ldelim}/popupinformation{rdelim}
</p>

{popupinformation alt="Sur l'image"}
  Contenu du popup avec alt
{/popupinformation}

<h2>Avec text</h2>
<p>
{ldelim}popupinformation text="Du texte après l'image"{rdelim}
  Contenu du popup avec texte
{ldelim}/popupinformation{rdelim}
</p>

{popupinformation text="Du texte après l'image"}
  Contenu du popup avec texte
{/popupinformation}

<h2>Avec displayimg à false (et donc du texte)</h2>
<p>
{ldelim}popupinformation displayimg=false text="Du texte"{rdelim}
  Contenu du popup avec texte et sans image
{ldelim}/popupinformation{rdelim}
</p>

{popupinformation displayimg=false text="Du texte"}
  Contenu du popup avec texte et sans image
{/popupinformation}

<h2>Avec une autre image</h2>
<p>
{ldelim}copixresource path=img/tools/loupe.png assign=imgPath{rdelim}
{ldelim}popupinformation img=$imgPath{rdelim}
  Contenu du popup avec une image alternative
{ldelim}/popupinformation{rdelim}
</p>

{copixresource path=img/tools/loupe.png assign=imgPath}
{popupinformation img=$imgPath}
  Contenu du popup avec une image alternative
{/popupinformation}

<h2>Avec une classe prédéfinie pour le popup (divclass)</h2>
<p>
{ldelim}copixhtmlheader kind="style"{rdelim}
.divClass {ldelim}
   border: 1px solid #F00;
{rdelim}
{ldelim}/copixhtmlheader{rdelim}

{ldelim}popupinformation divclass=divClass{rdelim}
  Contenu du popup avec une classe css spécifique
{ldelim}/popupinformation{rdelim}
</p>

{copixhtmlheader kind="style"}
.divClass {ldelim}
   border: 1px solid #F00;
{rdelim}
{/copixhtmlheader}

{popupinformation divclass=divClass}
  Contenu du popup avec une classe css spécifique
{/popupinformation}

<h2>Avec un handler sur onclick</h2>
<p>
{ldelim}popupinformation handler=onclick{rdelim}
  Contenu du popup avec onclick
{ldelim}/popupinformation{rdelim}
</p>

{popupinformation handler=onclick}
  Contenu du popup avec onclick
{/popupinformation}

<p><a href="{copixurl dest="default"}">Retour à la page de liste</a></p>