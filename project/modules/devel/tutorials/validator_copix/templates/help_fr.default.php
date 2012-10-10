<h2>Tutorial</h2>
<p>Lors de la sortie de Copix 3.0.3 sont apparus les validateurs. Cet article à pour but de vous présenter comment mettre en place un validateur pour votre site</p>

<h3 >Objectifs</h3>
<p>J’ai un formulaire simple avec&nbsp;: Non, Prénom, date de naissance, numéro de téléphone et j’aimerais le valider, et renvoyer un message d’erreur si les informations ne sont pas correctement remplies. Dans le principe on commence par créer le template, <i >default.form.php</i>, de la page :</p>

<div style="text-align: left;" class="codeBox">
<?php _eTag ('geshi', array ('lang'=>'php', 
'content'=>'<form action="<?php echo _url ("module|default|valid");?>">
Nom : <input type="text" name="nom"/<br/>
Prénom : <input type="text" name="prenom"/<br/>
Date de naissance : <input type="text" name="datenaissance"/><br/>
Téléphone : <input type="text" name="telephone"/><br/>
</form>')); ?>

</div>
<p>Ce code est placé dans un fichier form.php du répertoire templates de mon module et je l’intègre dans mon actiongroup default, fichier actiongroups/default.actiongroup.php du module, méthode processDefault&nbsp;:</p>
<div style="text-align: left;" class="codeBox">
<?php _eTag ('geshi', array ('lang'=>'php','content'=>'public function processDefault (){
   $ppo = new CopixPPO ();
   return _arPPO ($ppo, \'default.form.php\');
}')); ?></div>
<p>Maintenant dans l’idée si je veux créer un validateur qui va vérifier si Nom et Prénom sont des chaines de caractères, la date de naissance est une date et le téléphone un téléphone. Pour celà Copix met à ma disposition le mécanisme des <a href="http://svn.copix.org/wiki/CopixValidator" class="spip_out">validateurs</a> qu’il suffit d’implémenter.</p>

<h3 >Solutions avec des validateurs simples</h3>
<p>En passant par des validateur simple, on valide toutes les valeurs individuellement en utilisant le raccourci _validator ()&nbsp;; comme ceci&nbsp;:</p>

<div style="text-align: left;" class="codeBox">
<?php _eTag ('geshi', array ('lang'=>'php', 
'content'=>'$arError = array();

if (_validator (\'notempty\')->check (_request(\'nom\') !== \'true\')){
	$arError[] = \'Nom de l\\\'utilisateur obligatoire\';
}

if (_validator (\'notempty\')->check (_request(\'prenom\') !== \'true\')){
	$arError[] = \'Prénom de l\\\'utilisateur obligatoire\';
}

if (_validator (\'date\')->check (_request(\'date\') !== \'true\')){
	$arError[] = \'Erreur sur la date de naissance\';
}')); ?>
</div>
<p>Il possible de valider une valeur à l’aide de plusieurs validateurs, par exemple pour vérifier qu’une valeur est bien remplie. On utilise dans ce cas les <a href="http://svn.copix.org/wiki/CopixCompositeValidator" class="spip_out">validateurs composites</a>. Par exemple pour tester que votre téléphone est bien rempli et correspond à un numéro de télphone il convient de faire</p>

<div style="text-align: left;"  class="codeBox">
<?php _eTag ('geshi', array ('lang'=>'php', 
'content'=>'if (_ctValidator ()->attach (_validator (\'notEmpty\'))->attach (_validator (\'phone\'))->check (_request(\'telephone\')) !== \'true\'){
	$arError[] = \'Erreur sur le téléphone\';
}'));?>
</div>
<p>Le code ainsi réalisé permet de créer le tableau d’erreur qui peut être affiché. Pour se faciliter la vie et permettre d’étendre plus simplement l’étendue de nos vérifications, il est aussi possible (et souvent préférable) de créer un seul validateur qui permettra de vérifier l’ensemble des variabes de votre formulaire&nbsp;:</p>

<h3 >Les validateurs complexes </h3>
<p>Dans l’idée il convient de créer un validateur composé des validateurs énoncés. On utilise les <a href="http://svn.copix.org/wiki/CopixComplexTypeValidator" class="spip_out">validateurs complexes</a> qui s’occuperont de rassembler les validateurs simples et de les attacher à une donnée à vérifier.</p>

<div style="text-align: left;"  class="codeBox">
<?php _eTag ('geshi', array ('lang'=>'php', 'content'=>'// Création de mon validateur complexe
$validateur = _ctValidateur (); // _ctValidator équivaut à un new CopixComplexTypeValidator ();'));?>
</div>
<p>Le type de validateur complexe fournit une méthode attachTo permettant de lier une valeur à vérifier à un validateur. Le premier argument étant le validateur, le deuxième le chemin vers la ou les variables à vérifier. Pour reprendre l’exemple plus haut, voilà comment on pourrait faire&nbsp;:</p>
<div style="text-align: left;"  dir="ltr"><code><br>
$validateur-&gt;attachTo (_validator ('notempty'), array ('nom', 'prenom'); <br>
$validateur-&gt;attachTo (_validator ('date'), 'datenaissance');<br>
$validateur-&gt;attachTo (_ctValidator -&gt;attach (_validator (notEmpty'))-&gt;attach (_validator ('phone'), 'telephone');<br>

<br>
</code></div>
<p>La vérification peut alors se faire de la manière suivante&nbsp;:</p>

<div style="text-align: left;"  dir="ltr"><code><br>
$res = $validateur -&gt;check (CopixRequest::asArray ());<br>
</code></div>
<h3 >Personnaliser les messages d’erreur</h3>
<p>Dans l’exemple précédent, $res peut contenir plusieuts types de valeurs. Tout d’abord la valeur booléene "true" indiquant que tout s’est bien passé. Dans ce cas pas de soucis vous pouvez afficher votre page. Si par contre une ou plusieurs validations ont échoué, $res contiendra un objet de type CopixErrorObject permettant de récuperer les informations sur les erreurs.</p>