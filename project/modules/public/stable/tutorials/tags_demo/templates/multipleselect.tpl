<h2>Utilisation standard</h2>
{ldelim}multipleselect name=multipleselect values="1;2;3;4;5"|toarray{rdelim}
<p>{multipleselect name=multipleselect values="1;2;3;4;5"|toarray}</p>

<h2>Spécifier les clefs/valeurs</h2>
{ldelim}multipleselect name=multipleselect values="1=>MySQL;2=>Postgres;3=>SQLite;4=>SQLServer;5=>Oracle"|toarray{rdelim}
<p>{multipleselect name=multipleselect2 values="1=>MySQL;2=>Postgres;3=>SQLite;4=>SQLServer;5=>Oracle"|toarray}</p>

<h2>Indiquer l'élément sélectionné avec selected</h2>
{ldelim}multipleselect name=multipleselect selected=2 values="1=>MySQL;2=>Postgres;3=>SQLite;4=>SQLServer;5=>Oracle"|toarray{rdelim}
<p>{multipleselect name=multipleselect3 selected=2 values="1=>MySQL;2=>Postgres;3=>SQLite;4=>SQLServer;5=>Oracle"|toarray}</p>

<h2>Indiquer les éléments sélectionnés avec selected</h2>
{ldelim}multipleselect name=multipleselect selected="2;3;4"|toarray values="1=>MySQL;2=>Postgres;3=>SQLite;4=>SQLServer;5=>Oracle"|toarray{rdelim}
<p>{multipleselect name=multipleselect10 selected="2;3;4"|toarray values="1=>MySQL;2=>Postgres;3=>SQLite;4=>SQLServer;5=>Oracle"|toarray}</p>


<h2>Changer le libellé de la valeur vide</h2>
{ldelim}multipleselect emptyValues="--Aucun--" name=multipleselect values="1;2;3;4;5"|toarray{rdelim}
<p>{multipleselect emptyValues="--Aucun--" name=multipleselect4 values="1;2;3;4;5"|toarray}</p>

<h2>Changer le libellé et la valeur de la valeur vide</h2>
{ldelim}multipleselect emptyValues="KO=>--Aucun--" name=multipleselect values="1;2;3;4;5"|toarray{rdelim}
<p>{multipleselect emptyValues="KO=>--Aucun--"|toarray name=multipleselect5 values="1;2;3;4;5"|toarray}</p>

<h2>Ne pas afficher de valeur vide</h2>
{ldelim}multipleselect emptyShow=false name=multipleselect values="1;2;3;4;5"|toarray{rdelim}
<p>{multipleselect emptyShow=false name=multipleselect6 values="1;2;3;4;5"|toarray}</p>

<h2>Spécifier un id différent du name</h2>
{ldelim}multipleselect name=multipleselect id=autrechose values="1;2;3;4;5"|toarray{rdelim}
<p>{multipleselect name=multipleselect7 id=autrechose values="1;2;3;4;5"|toarray}</p>

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


{ldelim}multipleselect name=multipleselect id=autrechose values=$ppo->arObjects objectMap="id;caption"{rdelim}
<p>{multipleselect name=multipleselect8 id=autrechose values=$ppo->arObjects objectMap="id;caption"}</p>

<h2>Paramètre extra pour rajouter des informations à la balise</h2>
{ldelim}multipleselect extra='style="background-color: #ccc;"' name=multipleselect values="1;2;3;4;5"|toarray{rdelim}
<p>{multipleselect extra='style="background-color: #ccc;"' name=multipleselect9 values="1;2;3;4;5"|toarray}</p>


<p><a href="{copixurl dest="default"}">Retour à la page de liste</a></p>