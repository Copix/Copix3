<h2>Utilisation standard</h2>
{ldelim}inputtext name=texte value="mon texte"{rdelim}
<p>{inputtext name=texte value="mon texte"}</p>

<h2>Utilisation standard avec un identifiant</h2>
{ldelim}inputtext id=texte2 name=texte2 value="mon texte"{rdelim}
<p>{inputtext id=texte2 name=texte2 value="mon texte"}</p>

<h2>Spécifier le nombre de caractères à afficher</h2>
{ldelim}inputtext maxlength="3" id=texte3 name=texte3 value="mon texte"{rdelim}
<p>{inputtext maxlength="3" id=texte3 name=texte3 value="mon texte"}</p>

<h2>Spécifier la taille de l'input text</h2>
{ldelim}inputtext size="8" id=texte4 name=texte4 value="mon texte"{rdelim}
<p>{inputtext size="8" id=texte4 name=texte4 value="mon texte"}</p>

<h2>Indiquer l'élement précédent</h2>
On y passe automatiquement lorsque l'on essaye d'effacer un caractère alors qu'il y a plus rien<br/>
{ldelim}inputtext previous="texte7" id=texte5 name=texte5 value="mon texte"{rdelim}
<p>{inputtext previous="texte7"  id=texte5 name=texte5 value="mon texte"}</p>

<h2>Indiquer l'élement suivant</h2>
On y passe automatiquement lorsque maxlenght caratères ont été tapé<br/>
{ldelim}inputtext maxlength="6" next="texte7" id=texte6 name=texte6 value="mon texte"{rdelim}
<p>{inputtext maxlength="6" next="texte7" id=texte6 name=texte6 value="mon texte"}</p>

<h2>Indiquer l'élement suivant et précédent</h2>
{ldelim}inputtext maxlength="6" previous="texte5" next="texte6" id=texte7 name=texte7 value="mon texte"{rdelim}
<p>{inputtext maxlength="6" previous="texte5" next="texte6" id=texte7 name=texte7 value="mon texte"}</p>

<p><a href="{copixurl dest="default"}">Retour à la page de liste</a></p>