<?php
/**
 * @package		webtools
 * @subpackage	wikirenderer
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author		Julien SALLEYRON
 * @link		http://www.copix.org
 */

/**
 * Actiongroup pour tester le renderer
 *
 */
class ActionGroupDefault extends CopixActionGroup {
	
	public function processDefault () {
		return _arPpo(new CopixPPO(array ('text'=>CopixSession::get('text'))), 'test.tpl');
	}
	
	public function processLaunchRender () {
		CopixSession::set('text', _request('text'));
		$timer = new CopixTimer();
		$timer->start();
		_classInclude('wikirenderer|componentparsehandler');
		$components = ComponentParseHandler::getInstallComponents();
		$timer = new CopixTimer ();
		$text = _request('text');
		$timer->start();
		$tokenizer = _class ('wikirenderer|tokenizer');
		$tokens = $tokenizer->getTokens ($text, $components);
		
		$tokenTime = $timer->stop();
		$timer->start();
		$renderer = _ioClass('wikirenderer|tokenrenderer');
		$data = $renderer->render($tokens);
		$renderTime = $timer->stop();
		$tpl = new CopixTpl ();
		$errors = '';
		if ($tokenizer->getErrors()->countErrors() > 0) {
			$errors = '<br />Erreurs : <ul><li>'.$tokenizer->getErrors()->asString('</li><li>').'</li></ul>';
		}
		$tpl->assign ('MAIN', $errors.'Nombre de caractère : '.strlen($text).'<br />Nombre de composants : '.count($components).'<br />Temps de tokenize'.(($tokenizer->isFromCache()) ? '[CACHE]' : '').' : '.$tokenTime.'<br />Temps de rendu '.(($renderer->isFromCache()) ? '[CACHE]' : '').': '.$renderTime.'<br /><hr />'.$data);
		return _arDisplay($tpl);
			}
	public function processTestPerf () {
		$timer = new CopixTimer();
		$timer->start();
		_classInclude('wikirenderer|componentparsehandler');
		$components = ComponentParseHandler::getInstallComponents();
		$timer = new CopixTimer ();
		$text = $this->test1;
		$timer->start();
		$tokenizer = _class ('wikirenderer|tokenizer');
		$tokens = $tokenizer->getTokens ($text, $components, false);
		
		$tokenTime = $timer->stop();
		$timer->start();
		$renderer = _ioClass('wikirenderer|tokenrenderer');
		$data = $renderer->render($tokens);
		$renderTime = $timer->stop();
		$tpl = new CopixTpl ();
		$errors = '';
		if ($tokenizer->getErrors()->countErrors() > 0) {
			$errors = '<br />Erreurs : <ul><li>'.$tokenizer->getErrors()->asString('</li><li>').'</li></ul>';
		}
		$tpl->assign ('MAIN', $errors.'Nombre de caractère : '.strlen($text).'<br />Nombre de composants : '.count($components).'<br />Temps de tokenize'.(($tokenizer->isFromCache()) ? '[CACHE]' : '').' : '.$tokenTime.'<br />Temps de rendu '.(($renderer->isFromCache()) ? '[CACHE]' : '').': '.$renderTime.'<br /><hr />'.$data);
		return _arDisplay($tpl);
	}
	
	
	public function processAdminComponents () {
		_classInclude('wikirenderer|componentparsehandler');
		$ppo = new CopixPPO ();
		$ppo->components = ComponentParseHandler::getComponents();
		@include (COPIX_VAR_PATH . 'config/wiki_component.conf.php');
		$ppo->installedComponents = isset($wiki_components) ? $wiki_components : array ();
		return _arPpo ($ppo, 'admincomponent.tpl');
	}
	
	public function processSaveInstallComponents () {
		$arComponents = _request('components');
		
		$path = COPIX_VAR_PATH . 'config/wiki_component.conf.php';
		
		$generator = new CopixPHPGenerator ();
	    $str = $generator->getPHPTags ($generator->getVariableDeclaration ('$wiki_components', $arComponents));
		$file = new CopixFile ();
		$file->write ($path, $str);
		
		return _arRedirect (_url ('wikirenderer||admincomponents'));
	}
	
	
	private $test1 = 
	
	'= Bienvenue dans le projet Copix =

Ici, vous pouvez : 
 * [http://svn.copix.org/browser Parcourir les sources]
 * [http://svn.copix.org/newticket Reporter un bug]
 * [http://svn.copix.org/roadmap Consulter l\'état d\'avancement des futures version]
 * [http://svn.copix.org/timeline Suivre les évolutions au quotidien]

Si vous avez un problème d\'utilisation ou souhaitez discuter autours du projet, utilisez plutôt le [http://forum.copix.org forum de discussions]. 

Si vous souhaitez télécharger la dernière version stable de Copix, consultez la page des [http://www.copix.org/index.php/wiki/telechargements téléchargements]

Vous pouvez également vous abonner aux [http://lists.copix.org/ listes de diffusion] pour suivre par email des rapports d\'évolution des tickets et des sources.

= Travailler avec les sources ? =

L\'adresse du SVN de Copix est {{{ http://svn.copix.org/svn/ }}}. Ce SVN est public en lecture, vous n\'êtes pas tenu de vous identifier pour pouvoir réaliser un checkout.

Ligne de commande nécessaire pour récupérer Copix :
{{{ svn co http://svn.copix.org/svn/ }}}

= Règles d\'utilisation de Subversion =

Avant de modifier les sources et de travailler avec Subversion, veuillez prendre connaissance des [http://www.copix.org/index.php/wiki/Utilisation_de_Subversion règles d\'utilisation] et des [http://www.copix.org/index.php/wiki/Normes_de_developpement normes de développement].

== Problèmatique ==
L\'idée de Soap est de permettre d\'exporter des classes appellées par des clients.

Basiquement un serveur peut s\'écrire de la manière suivante :
{{{
#!php
<?php
$server = new SoapServer ("some.wsdl");
$server->setClass("Foo");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $server->handle();
} else {
    echo "Ce serveur SOAP peut gérer les fonctions suivantes : ";
    $functions = $server->getFunctions();
    foreach($functions as $func) {
        echo $func . "
";
    }
}

class Foo {
    /**
     * @param $x int
     * @param $y int
     * @return int
     */
    function Add($x, $y)  {
	return $x+$y;
    }
}
?>
}}}
Un client peut donc appeller la fonction Add.

{{{
#!php
<?php
$client = new SoapClient("some.wsdl");

$res = $client->Add(1,1);
echo $res;

?>
}}}
== Intégration dans copix ==
Dans Copix pour l\'instant le module WSServer permet de traiter les requêtes et de générer le fichier WSDL en fonction de la classe exporté.

Par contres des questions se posent concernant la classe exporté.

Pour le moment celle-ci est de type CopixActionGroup, ceci permet de simplifier, dans le module WSServer, l\'identification et le chargement de la classe. Par contre les fonctions de la classe exportée sont directement interpretés lors qu\'un client les appelle. On ne passe donc pas par la controleur et les classes exportées peuvent être indépendantes de Copix.

L\'idée serait de trouver un moyen d\'exporter les classes de telle manière que l\'on passe par le controleur, permettant ainsi d\'utiliser l\'ensemble du framework Copix. Dans ce cas on pourrait envisager une fonction qui traiterait les paramètres, identifierait le module a utiliser et renverrait un résultat.

Exemple de client :
{{{
#!php
<?php
$client = new SoapClient("http://www.monsite.com/index.php/wsserver/default/wsdl/");

$res = $client->process("module","desc","action",array("param"));
echo $res;

?>
}}}

Qu\'en pensez vous?

= Introduction =

Les objets "ActionGroup" ont pour objectif de regrouper les actions par typologie (actions d\'administration, actions front office, ....)

Ces objets sont stockés dans les répertoires actiongroups/ des modules.

Les fichiers qui déclarent les ActionGroup sont en minuscules et se nomment "nom.actiongroup.php".

Dans un fichier "nom.actiongroup.php" on trouvera la déclaration de la classe "ActionGroupNom"

exemple

{{{
#!php
class ActionGroupNom extends CopixActionGroup {
}
}}}

CopixActionGroup est la classe de base des ActionGroup.

= Les actions =
Le rôle principal des ActionGroup est de répondre à la demande de l\'internaute, demande qui arrive sous la forme d\'une URL.

Les Actions sont implémentées dans les ActionGroups par des méthodes préfixées de "process".

{{{
#!php
class ActionGroupExemple extends CopixActionGroup {
   function processUneAction (){
      //..code d\'une action
   }
}
}}}

== Routage par défaut ==
Les \'\'ActionGroup\'\' sont sollicités par un processus de routage qu\'il est important de bien comprendre pour la suite.

Pour rappel, les [wiki:ComprendreLaFormeDesURL URL Copix] (générées et traitées avec CopixUrl) sont composées de 3 éléments :
   * Le module (qui va déterminer le "chemin d\'exécution")
   * Le groupe (qui va déterminer l\' \'\'ActionGroup\'\' à utiliser)
   * L\'action (qui va déterminer la méthode à appeler dans l\'ActionGroup)

=== Exemple 1 ===
\'\'index.php?module=mon_module&group=mon_groupe&action=mon_action\'\'

Ici, on va instancier l\' \'\'ActionGroup\'\' \'\'ActionGroupMon_Groupe\'\' situé dans le module \'\'mon_module\'\' (emplacement exact : \'\'chemin_du_module/mon_module/actiongroup/mon_groupe.actiongroup.php\'\') et exécuter la méthode \'\'processMon_Action ()\'\'

=== Exemple 2 ===
\'\'index.php?module=mon_module&group=mon_groupe&action=mon_autre_action\'\'

Ici, on va instancier l\' \'\'ActionGroup\'\' \'\'ActionGroupMon_Groupe\'\' situé dans le module \'\'mon_module\'\' (emplacement exact : \'\'chemin_du_module/mon_module/actiongroup/mon_groupe.actiongroup.php\'\') et exécuter la méthode \'\'processMon_Autre_Action ()\'\'

=== Exemple 3 ===
\'\'index.php?module=mon_module&group=mon_autre_groupe&action=mon_action\'\'

Ici, on va instancier l\' \'\'ActionGroup\'\' \'\'ActionGroupMon_Autre_Groupe\'\' situé dans le module \'\'mon_module\'\' (emplacement exact : \'\'chemin_du_module/mon_module/actiongroup/mon_autre_groupe.actiongroup.php\'\') et exécuter la méthode \'\'processMon_Autre_Action ()\'\'

\'\'\'Note : \'\'\' La casse des modules, groupes et actions n\'a pas d\'importance, Copix utilisera systématiquement des minuscules.

= Les méthodes spéciales =
== beforeAction ($actionName) ==
La méthode beforeAction est systématiquement appelée avant le traitement à proprement parlé de votre méthode "process". Elle reçoit en paramètre le nom de l\'action demandée par l\'utilisateur.

Cette méthode est donc une bonne opportunité pour réaliser des opérations groupées pour votre \'\'ActionGroup\'\'.

Par exemple, si vous voulez protéger l\'ensemble des pages de votre ActionGroup pour qu\'elles soient accessibles aux seuls administrateurs, il suffit de faire :

{{{
#!php
class ActionGroupProtege extends CopixActionGroup {
   public function beforeAction ($pActionName) {
      CopixAuth::getCurrentUser ()->assertCredential (\'basic:registered\');
   }
}
}}}

\'\'\' Note: \'\'\' Si vous levez une exception dans votre méthode beforeAction, la méthode "process" (de l\'action demandée) ne sera pas exécutée. (Si vous implémentez catchActionErrors, cette méthode aura la main)

Si vous décidez de retourner un CopixActionReturn depuis cette action, alors l\'action initialement demandée ne sera pas exécutée et Copix prendra directement en compte votre retour.

Exemple :
{{{
#!php
class ActionGroupDefault extends CopixActionGroup {
   public function beforeAction ($pActionName) {
      if (in_array ($pActionName, array (\'action1\', \'action2\', \'action3\'))){
         //Si l\'action demandée est 1 2 ou 3, alors on redirige l\'utilisateur vers une autre URL
         return _arRedirect (_url (\'module|group|action\'));
      }
   }
}
}}}

\'\'\' NOTE \'\'\' : Si votre méthode beforeAction a retourné un code, alors l\'action d\'origine n\'est pas exécutée. Le processus continue ensuite normalement avec afterAction qui reçoit en paramètre "beforeAction" comme nom d\'action et le code retour de beforeAction comme données.

== afterAction ($actionName, $toReturn) ==

La méthode afterAction est appelée systématiquement après l\'appel à votre méthode process.

Elle reçoit en paramètre le nom de l\'action dont on a demandé l\'exécution ($actionName) et le retour apporté par la méthode de l\'action (processXXX).


\'\'\' Note: \'\'\' Si une exception est levée dans beforeAction ou dans votre Action, la méthode afterAction ne sera pas exécutée.

Si votre méthode afterAction retourne un CopixActionReturn, alors ce dernier sera préféré à celui émis à l\'origine par l\'Action.

Exemple 
{{{
#!php
class ActionGroupDefault extends CopixActionGroup {
   public function afterAction ($pActionName, $pActionReturn) {
      //On va transformer la sortie en PDF
      if ($pActionReturn->code == CopixActionReturn::PPO){
         $tpl = new CopixTpl ();

         $tpl->assign (\'ppo\', $pActionReturn->data);
         $contenu = $tpl->fetch ($pActionReturn->more);
         $contenu = _class (\'moduleconversion|pdf\')->renderPdf ($contenu);
         
         return _arContent ($contenu, array (\'filename\'=>\'archive.zip\'));
      }
   }
}
}}}

== catchActionExceptions ($e) ==
Lorsqu\'une exception est levée dans beforeAction, afterAction ou votre Action, cette dernière est transmise à la méthode catchActionExceptions.

Cette méthode vous permet de traiter les erreurs spécifiques à votre module et d\'y réagir convenablement.

Si votre méthode ne gère pas l\'exception en question, il faudra la relancer avec "throw $e".

exemple

{{{
#!php
function catchActionExceptions ($e){
   if ($e instanceof ExceptionQueJeGere){
      //traitement
   }else{
      throw $e;//exception non gérée ici
   }
}
}}}
== otherAction ($actionName) ==
Lorsque l\'internaute demande une action à votre ActionGroup alors que celui-ci ne sait pas la gérer, c\'est cette méthode qui intercepte la demande. La méthode otherAction reçoit le nom de l\'action qui avait été demandée par l\'utilisateur.

Par défaut, si vous n\'implémentez pas cette méthode dans votre ActionGroup, Copix retournera un 404.

= Demander l\'exécution d\'une Action via la méthode process =

Il vous est possible de demander à Copix d\'exécuter une action via la méthode statique CopixActionGroup::process (\'nomAction\', $parametresAction);

Vous ne devriez réserver cette possibilité qu\'à des fins exceptionnelles, lorsque vous avez besoin de faire appel directement à une action tierce. Un bon exemple d\'utilisation est le module [wiki:Description generictools] avec par exemple son [wiki:GetConfirm message de confirmation].

La description de l\'action à utiliser sera de la forme : nomModule|nomGroupe::nomAction

Exemple d\'utilisation 

{{{
#!php
//... des conditions nous amènent à demander confirmation d\'un message
   return CopixActionGroup::process (\'generictools|Messages::getConfirm\',
                                    array (\'message\'=>\'Êtes vous sûr de cela ?\',
                                           \'confirm\'=>_url (\'module|actionOui\'),
                                           \'cancel\'=>_url (\'module|actionNon\')));
}}}

= Voir aussi =
 * Le tutorial [wiki:HelloYou Hello You !]
 * les [wiki:PrincipesDeBase Principes de base]

= Avant de commencer =
Nous vous conseillons de lire le tutoriel [wiki:HelloYou Hello You !] ainsi que [wiki:PrincipesDeBase Principes de base] avant de commencer la lecture de ce document.

= Qu\'est-ce qu\'une Action ? =

Les actions dans Copix sont des réponses à une URL. A une demande correspond une Action.

Les actions sont implémentées dans des objets ActionGroup.

Exemple :

{{{
#!php
class ActionGroupExemple extends CopixActionGroup {
   //L\'action par défaut
   public function processDefault (){
   }
   //Action "UneAction"
   public function processUneAction (){
   }
   //Action "UneAutreAction"
   public function processUneAutreAction (){
   }
   //Action "UneTroisiemeAction"
   public function processUneTroisiemeAction (){
   }
}
}}}

Vous êtes libre de faire ce que bon vous semble dans les actions et devez à la fin de chacune retourner une réponse de type "CopixActionReturn" pour indiquer à Copix la façon dont terminer le processus (affichage, téléchargement, redirection, ...)

= Les différents codes retour =

== Affichage avec CopixActionReturn::PPO ==

Le type PPO est utilisé pour afficher un contenu (souvent (X)HTML).

{{{
#!php
public function processAffiche (){
   $ppo = new CopixPpo ();
   $ppo->TITLE_PAGE = \'Titre de la page\' ;
   $ppo->nom = \'Le nom à afficher\';
   return new CopixActionReturn (CopixActionReturn::PPO, $ppo, \'template_a_utiliser.tpl\');
   //ou alors, plus rapide à écrire
   return _arPpo ($ppo, \'template_a_utiliser.tpl\');
}
}}}

Tout sur [wiki:CopixActionReturnPPO CopixActionReturn::PPO]

== Redirection avec CopixActionReturn::REDIRECT ==

Le type REDIRECT est utilisé pour rediriger l\'utilisateur vers une autre URL.

{{{
#!php
public function processAffiche (){
   return new CopixActionReturn (CopixActionReturn::REDIRECT, \'http://www.yahoo.fr\');
   //ou alors, plus rapide à écrire
   return _arRedirect (\'http://www.yahoo.fr\');
}
}}}

Tout sur [wiki:CopixActionReturnREDIRECT CopixActionReturn::REDIRECT]

== Téléchargement d\'un fichier avec CopixActionReturn::FILE ==

Le type FILE est utilisé pour présenter à l\'utilisateur un fichier existant sur le serveur.

{{{
#!php
public function processDownload (){
   return new CopixActionReturn (CopixActionReturn::FILE, \'/tmp/fichier_a_telecharger.pdf\');
   //ou alors, plus rapide à écrire
   return _arFile (\'/tmp/fichier_a_telecharger.pdf\');
}
}}}

Tout sur [wiki:CopixActionReturnFILE CopixActionReturn::FILE]

== Téléchargement d\'un contenu avec CopixActionReturn::CONTENT ==

Le type CONTENT est utilisé pour présenter à l\'utilisateur un contenu binaire. On s\'en sert lorsque le contenu est généré à la volée et n\'est pas conservé sur le serveur (une image, un pdf, ...).

{{{
#!php
public function processDownloadContent (){
   $image = new UneClasseQuiGenereUneImage ();
   return new CopixActionReturn (CopixActionReturn::CONTENT, $image->generateJpg ());
   //ou alors, plus rapide à écrire
   return _arContent ($image->generateJpg ());
}
}}}

Tout sur [wiki:CopixActionReturnCONTENT CopixActionReturn::CONTENT]

== Ne rien faire de plus avec CopixActionReturn::NONE ==

Le type NONE est rarement utilisé et est réservé pour des occasions très spécifiques ou on demande à Copix de ne rien faire après l\'action elle-même (on considère ainsi que l\'action a effectué elle-même toutes les opérations nécessaires à la réponse de l\'utilisateur).

{{{
#!php
public function processDownloadContent (){
   //des traitements divers
   return new CopixActionReturn (CopixActionReturn::NONE);//rien d\'autre à faire, ni affichage ni rien
   //ou alors, plus rapide à écrire
   return _arNone ();
}
}}}

== Retourner un code HTTP avec CopixActionReturn::HTTPCODE ==

Le type HTTPCODE est également rarement utilisé, il permet de retourner un simple entête HTTP comme retour au navigateur client.

{{{
#!php
public function processNotFound (){
   return new CopixActionReturn (CopixActionReturn::HTTPCODE, CopixHTTPHeader::get404 (), "Page introuvable");
}
}}}

= Voir aussi =
 * Les ActionGroup


= Présentation =

CopixAJAX définit des méthodes statiques permettant de faciliter la programmation AJAX avec Copix.

== Déterminer si on traite une requête AJAX ==

Cela peut être fait facilement avec CopixAJAX::isAJAXRequest().

{{{
boolean CopixAJAX::isAJAXRequest()
}}}

Retourne true si l\'on est dans le traitement d\'une requête AJAX.

== Gestion de sessions AJAX ==

CopixAJAX introduit un nouveau concept : les sessions AJAX. 

L\'idée est de partager des données entre les différents appels AJAX d\'une même page, tout en évitant les conflits entre plusieurs versions de la page (dans des onglets différents du navigateur par exemple).

Lors de la génération initiale d\'une page (i.e. requête non-AJAX), un identifiant de session AJAX (mais pas encore la session elle-même) est généré pour la page. Toutes les requêtes AJAX qui seront effectuées par cette page communiquerons ce même identifiant à Copix.

Dès lors que l\'on fait appel à la méthode CopixAJAX::getSession(), la session AJAX est créée au sein de la session PHP classique. Par la suite, les données seront accessibles uniquement avec ce même identifiant.

Le système inclut un "garbage collector" pour supprimer les sessions obsolètes.

\'\'\'A noter :\'\'\' une session AJAX différente étant créée à chaque génération d\'une page, un rechargement réinitialise la session AJAX.

=== Méthodes ===

==== CopixAJAX::getSessionId ====

{{{
string CopixAJAX::getSessionId()
}}}

Renvoie un identifiant de session AJAX : soit l\'identifiant en cours, soit un identifiant tout neuf.

==== CopixAJAX::getSession ====

{{{
CopixAJAXSession CopixAJAX::getSession()
}}}

Crée, ou récupère, la session AJAX de la page.

L\'utilisation de cette méthode implique l\'utilisation du framework Javascript.

Pour l\'utilisation de CopixAJAXSession, je vous invite à consulter la [http://phpdoc.copix.org/copix/utils/CopixAJAXSession.html PHPDoc].

= Exemple  =

L\'utilisation au niveau PHP est très simple :

{{{
#!php
<?php
class ActionGroupExemple extends CopixActionGroup {

  public function processStartPage() {
    // Initialise la session AJAX de la page.
    $sessionAJAX = CopixAJAX::getSession();
    // Assigne une valeur
    $sessionAJAX->maValeur = 5;

    // N.B: on pourrait aussi écrire : CopixAJAX::getSession()->set(\'maValeur\', 5);
  }

  public function processActionAJAX() {
    // Récupère la session AJAX
    $sessionAJAX = CopixAJAX::getSession();
    // Récupère la valeur
    $maValeur = $sessionAJAX->maValeur;

    // N.B: on pourrait aussi écrire : maValeur = CopixAJAX::getSession()->get(\'maValeur\');
  }

}
?>
}}}

L\'utilisation de cette méthode implique le chargement du framework AJAX.
	';
}
?>