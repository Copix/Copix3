<h2>Utilisation standard</h2>
{ldelim}select name=select values="1;2;3;4;5"|toarray{rdelim}
<p>{select name=select values="1;2;3;4;5"|toarray}</p>

<h2>Spécifier les clefs/valeurs</h2>
{ldelim}select name=select values="1=>MySQL;2=>Postgres;3=>SQLite;4=>SQLServer;5=>Oracle"|toarray{rdelim}
<p>{select name=select values="1=>MySQL;2=>Postgres;3=>SQLite;4=>SQLServer;5=>Oracle"|toarray}</p>

<h2>Indiquer l'élément sélectionné avec selected</h2>
{ldelim}select name=select selected=2 values="1=>MySQL;2=>Postgres;3=>SQLite;4=>SQLServer;5=>Oracle"|toarray{rdelim}
<p>{select name=select selected=2 values="1=>MySQL;2=>Postgres;3=>SQLite;4=>SQLServer;5=>Oracle"|toarray}</p>

<h2>Changer le libellé de la valeur vide</h2>
{ldelim}select emptyValues="--Aucun--" name=select values="1;2;3;4;5"|toarray{rdelim}
<p>{select emptyValues="--Aucun--" name=select values="1;2;3;4;5"|toarray}</p>

<h2>Changer le libellé et la valeur de la valeur vide</h2>
{ldelim}select emptyValues="KO=>--Aucun--" name=select values="1;2;3;4;5"|toarray{rdelim}
<p>{select emptyValues="KO=>--Aucun--"|toarray name=select values="1;2;3;4;5"|toarray}</p>

<h2>Ne pas afficher de valeur vide</h2>
{ldelim}select emptyShow=false name=select values="1;2;3;4;5"|toarray{rdelim}
<p>{select emptyShow=false name=select values="1;2;3;4;5"|toarray}</p>

<h2>Spécifier un id différent du name</h2>
{ldelim}select name=select id=autrechose values="1;2;3;4;5"|toarray{rdelim}
<p>{select name=select id=autrechose values="1;2;3;4;5"|toarray}</p>

<h2>Utiliser un tableau d'objet et spécifier les clefs / valeurs</h2>
<p>Ici nous avons un tableau d'objet avec les propriétés id/caption. Nous allons indiquer
à la balise que id est la valeur de l'option et que caption est son libellé dans le paramètre
objectMap.</p>

<p>le tableau d'objet est déclaré comme suit :</p> 

<pre>
$arObjects = array ();

$obj = new StdClass ();
$obj-&gt;id = '1';
$obj-&gt;caption = 'libellé 1';
$arObjects[] = $obj;

$obj = new StdClass ();
$obj-&gt;id = '2';
$obj-&gt;caption = 'libellé 2';
$arObjects[] = $obj;
</pre>


{ldelim}select name=select id=autrechose values=$ppo->arObjects objectMap="id;caption"{rdelim}
<p>{select name=select id=autrechose values=$ppo->arObjects objectMap="id;caption"}</p>

<h2>Iterateur d'objets avec spécification de clef / valeurs</h2>
{ldelim}select name=select id=autrechose values=$ppo->iteratorObjects objectMap="id;caption"{rdelim}
<p>{select name=select id=autrechose values=$ppo->iteratorObjects objectMap="id;caption"}</p>

<h2>Paramètre extra pour rajouter des informations à la balise</h2>
{ldelim}select extra='style="background-color: #ccc;"' name=select values="1;2;3;4;5"|toarray{rdelim}
<p>{select extra='style="background-color: #ccc;"' name=select values="1;2;3;4;5"|toarray}</p>


<p><a href="{copixurl dest="default"}">Retour à la page de liste</a></p>