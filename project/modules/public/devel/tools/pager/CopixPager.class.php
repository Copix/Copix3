<?php
/**
* @package		copix
* @author		<o.veujoz@miasmatik.net>, Bertrand Yan
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Pager pour gérer les défilements dans des barres de navigation à partir d'un 
 * ensemble de résultats
 * @package		copix
 * @subpackage	utils
 */
class CopixPager {

    /**
     * Nombre de résultat par page souhaité
     *
     * Valeur par défaut : 10
     * @var int $perPage
     * @see calcul()
     */
   var $perPage;



    /**
     * Le nombre de liens par page souhaité
     *
     * 0 = tous les liens sur la même page
     * Valeur par défaut : 10
     * @var int $delta
     * @see calcul(), parseLoop()
     */
   var $delta;



    /**
     * Le nom du fichier template contenant le code HTML du multipage
     *
     * Ce fichier doit obligatoirement se trouver dans le sous-répertoire "templates" de la classe
     * Valeur par défaut : 'pager1.tpl'
     * @var string $template
     * @see parsetemplate()
     */
   var $template;



    /**
     * Permet de créer une URI personnalisé
     *
     * Utile notamment pour tout ce qui relève de l'ordre de l'URL Rewritting.
     * Doit obligatoirement posséder la variable PAGE.
     * Exemple : mapage-parametre-<#PAGE>.htm
     * Valeur par défaut : chaine vide
     * @var string $tplUri
     * @see getLink()
     */
    var $tplUri;



    /**
     * Permet d'ajouter X colonnes pour la fusion de colonnes (colspan) dans le template
     *
     * Valeur par défaut : 0
     * @var int $addToColspan
     * @see parseColspan()
     */
   var $addToColspan;



    /**
     * Libellé pour la page suivante
     *
     * Se configure dans le fichier 'config.class.php'
     * Se référer au fichier de configuration pour la valeur par défaut
     * @var string $nextPage
     * @see parseNextPage()
     */
   var $nextPage;



    /**
     * Libellé pour la page précédente
     *
     * Se configure dans le fichier 'config.class.php'
     * Se référer au fichier de configuration pour la valeur par défaut
     * @var string $previousPage
     * @see parsePreviousPage()
     */
   var $previousPage;



    /**
     * Libellé pour l'accès à la dernière page
     *
     * Se configure dans le fichier 'config.class.php'
     * Se référer au fichier de configuration pour la valeur par défaut
     * @var string $lastPage
     * @see parseLastPage()
     */
   var $lastPage;



    /**
     * Libellé pour l'accès à la première page
     *
     * Se configure dans le fichier 'config.class.php'
     * Se référer au fichier de configuration pour la valeur par défaut
     * @var string $firstPage
     * @see parseFirstPage()
     */
   var $firstPage;



    /**
     * Toujours afficher le multipage
     *
     * Spécifie s'il faut afficher le multipage lorsque ce dernier n'est pas nécessaire (cas où il y a moins d'enregistrements que
     * la valeur contenue dans $perPage)
     * Valeur par défaut : true
     * @var bool $alwaysShow
     * @see setup()
     */
   var $alwaysShow;



    /**
     * Séparateur de page
     *
     * Définit le séparateur de pages pour les parties "loop".
     * Se configure dans le fichier config.class.php
     * Se référer au fichier de configuration pour la valeur par défaut
     * @var string $separator
     * @see parseLoop()
     */
   var $separator;



    /**
     * Nom de colonne pour le mode "alphabétique"
     *
     * Cette propriété permet de spécifier à la classe sur quelle colonne de la requête doit se jouer le tri.
     * Si cette propriété est précisée, la classe passe automatiquement en mode "index alphabétique".  Pour rester en mode
     * numérique, cette propriété doit rester vide.
     * @var string $alphaColumn
     * @see createSQL(), buildAlphaLikeClause(), calcul(), parseLoop()
     */
    var $alphaColumn;



    /**
     * Regroupement de caractères
     *
     * Prise en compte de x lettres (ex : [A-Z])
     * Valeur par défaut : 1
     * @var int $alphaEncaps
     * @see buildAlphaLikeClause(), calcul(), getAlphaLinkValue()
     */
    var $alphaEncaps;



    /**
     * Nom de la variable passée par l'url pour le numéro de la page
     *
     * Cette propriété sert à renommer la variable sur le numéro de page que vous passez par l'url. Ceci permet
     * d'éviter un potentiel conflit si vous disposez déjà d'une variable de même nom dans votre url.
     * Se configure dans le fichier config.class.php
     * Se référer au fichier de configuration pour la valeur par défaut
     * @var string $varUrl
     */
    var $varUrl;



    /**
     * Identifiant de début d'une variable template
     *
     * Sert à renommer les variables templates au bon vouloir du programmeur.
     * Cette propriété indique par quel code commence une variable template
     * Se configure dans le fichier config.class.php
     * Se référer au fichier de configuration pour la valeur par défaut
     * @var string $tplVarBegin
     */
    var $tplVarBegin;



    /**
     * Identifiant de fin d'une variable template
     *
     * Sert à renommer les variables templates au bon vouloir du programmeur.
     * Cette propriété indique par quel code commence une variable template
     * Se configure dans le fichier config.class.php
     * Se référer au fichier de configuration pour la valeur par défaut
     * @var string $tplVarBegin
     */
    var $tplVarEnd;



    /**
     * Chaine ajoutée avant le nom de la page courante
     *
     * Sert à mettre en valeur la page courante
     *
     * Se configure dans le fichier config.class.php
     * Se référer au fichier de configuration pour la valeur par défaut
     * @var string $curPageSpanPre
     */
    var $curPageSpanPre;



    /**
     * Chaine ajoutée après le nom de la page courante
     *
     * Sert à mettre en valeur la page courante
     * Se configure dans le fichier config.class.php
     * Se référer au fichier de configuration pour la valeur par défaut
     * @var string $curPageSpanPost
     */
    var $curPageSpanPost;



    /**
     * Classe CSS pour les liens
     *
     * Se configure dans le fichier config.class.php
     * Se référer au fichier de configuration pour la valeur par défaut
     * @var string $linkClass
     */
    var $linkClass;



    /**
     * Chemin d'accès complet aux templates
     *
     * @access public
     * @var string $linkClass
     * @since 3.1a
     */
    var $tplDir;



    /**
     * Mode dans lequel fonctionnera la classe Multipage
     *
     * Choix possibles : 'arbitraire' || 'alphabetique'
     * @access private
     * @var string $multipage_mode
     */
    var $multipage_mode;



    /**
     * Flag permettant de savoir si l'on est déjà passé dans la méthode setup()
     *
     * Val par défaut : FALSE
     * @access private
     * @var bool $_initialized
     */
    var $_initialized;



    /**
     * Indique si les éléments contenu dans l'url doivent être encodés
     *
     * Valeur par défaut : false
     * @access public
     * @var bool $urlEncode
     */
    var $encodeVarUrl;



    /**
     * Encode les entités HTML des libellés du multipage
     *
     * @access public
     * @var bool $toHtmlEntities
     */
    var $toHtmlEntities;



    /**
     * Permet de switcher entre le mode "jumping/sliding"
     *
     * @access public
     * @var string $display
     */
    var $display;



    /**
     * Chargement du drivers
     *
     * @param string $drivers 'DB'/'Array'
     * @access public
     * @return void
     * @since
      */
    public function Load($options = Array()) {
        //$className = 'multipage_' . StrToLower($driver);
        //require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'drivers' . DIRECTORY_SEPARATOR . StrToLower($driver) . '.class.php');
        //$object = new $className($options);
        Copix::RequireOnce (COPIX_UTILS_PATH.'CopixArrayPager.class.php');
        return new CopixArrayPager($options);
    }

   /**
     * Constructeur
     *
     * Instancie les propriétés avec leur valeurs par défaut
     * @access public
     * @return void
     * @since 1.0
     */
   public function __construct ($options = array()) {
        $this-> template       = '|pager.tpl';
        $this-> tplDir         = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;
        $this-> addToColspan   = 0;
        $this-> tplUri         = '';
        $this-> alphaColumn    = '';
        $this-> alphaEncaps    = 1;
        $this-> aAlpha         = Array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $this-> tpl_data       = '';
        $this-> multipage_mode = 'arbitraire';
        $this-> _initialized   = FALSE;

        foreach ($options as $option => $value) {
            $this-> $option = $value;
        }
   } // end func Multipage



    /**
     * Index de départ, index de fin du tableau $aAlpha
     *
     * Calcule les index de départ et de fin pour le tableau $aAlpha en fonction de la page $page
     * @access private
     * @param int $page, page pour laquelle il faut calculer l'index (page courante par défaut)
     * @return array 0 => index du début, 1 => index de fin
     * @since 3.1
     */
    public function getAlphaIndex($page = '') {
        if (empty($page)) $page = $this-> currentPage;

        $IndexStart = ($page * $this-> alphaEncaps) - $this-> alphaEncaps;
        $IndexEnd   = $IndexStart + $this-> alphaEncaps;

        return array($IndexStart, $IndexEnd);
    } // end func getAlphaIndex



   /**
     * Initialisation des propriétés nécessaires au bon déroulement du multipage
     *
     * Les propriétés générées sont les suivantes :
     *     int  nbRecord, le nombre d'enregistrements contenus dans la requête, en tenant compte de la clause LIMIT
     *     int  nbLinks, contient le nombre de liens nécessaire à l'affichage de tous les enregistrements
     *     int  first_pos, contient la position du lien à partir duquel il faudra commencer à afficher les liens vers les autres pages
     *     int  last_pos, contient la position du lien à partir duquel il faudra arrêter d'afficher les liens vers les autres pages
     * @access private
     * @return void
     * @see linkCount()
     * @since 1.0
     */
   public function calcul() {

        // Nombre total d'enregistrements
        $this-> nbRecord = $this-> getNbRecord();

        // Nombre de liens nécessaire à l'affichage de tous les enregistrements
        $this-> nbLinks = $this-> linkCount();

        // Si un petit malin cherche à modifier la variable "page" directement dans l'url
        if ($this-> currentPage > $this-> nbLinks) {
            $_GET[$this-> varUrl] = $this-> nbLinks;
            $this-> setRestriction();
        }

        // Affichage de tous les liens
        if ($this-> delta == 0) $this-> delta = $this-> nbLinks;

        // Suivant le mode d'affichage désiré (sliding||jumping), le calcul diffère
      if ($this-> display == 'sliding') {

            // Position intermédiaire de la page en cours (affichage au milieu)
            $middlePos = floor($this-> delta / 2);

            if ($middlePos < $this-> nbLinks) {

                // Position à partir de laquelle on arrête d'afficher les liens
                if ($this-> currentPage <= $middlePos) $this-> last_pos = abs($this-> currentPage - $middlePos) + $middlePos + ($this-> currentPage + 1);
                else                                   $this-> last_pos = $this-> currentPage + $middlePos;

                // Position à partir de laquelle on commence à afficher les liens
              if (($this-> currentPage + $middlePos) > $this-> nbLinks)   $this-> first_pos = $this-> currentPage - (($this->last_pos + $middlePos) - $this-> nbLinks);
                elseif ($this-> currentPage > $middlePos)                   $this-> first_pos = $this-> currentPage - $middlePos;
              else                                                        $this-> first_pos = 1;

            } else {
                // Il n'y a pas assez d'enregistrements pour afficher autant de liens que souhaité par la propriété delta
                $this-> first_pos = 0;
                $this-> last_pos  = $this-> nbLinks;
            }

        } else {
            // Mode "jumping"
            // Ok, je reconnais, pas la peine de me blâmer, j'ai honteusement pompé le code du Pager de Pear, vu mon incapacité à trouver la bonne formule
            $start = ((($this-> currentPage + (($this-> delta - ($this-> currentPage % $this-> delta))) % $this-> delta) / $this-> delta) - 1) * $this-> delta + 1;
            $this-> first_pos  = max($start, 1);
            $this-> last_pos   = min($start + $this-> delta - 1, $this-> nbLinks);
        }

   } // end func calcul



   /**
     * Nombre de liens
     *
     * Compte le nombre de liens nécessaire à l'affichage de tous les enregistrements en tenant compte du mode
     * de fonctionnement de la classe (alpha-numérique / numérique)
     * @access private
     * @return int nombre de liens
     * @since 3.0
     */
    public function linkCount() {
        if (!empty($this-> alphaColumn))             return ceil(count($this-> aAlpha) / $this-> alphaEncaps);
      else if ($this-> perPage < $this-> nbRecord) return ceil($this-> nbRecord / $this-> perPage);
        else return 1;
    } // end func linkCount



    /**
     *
     *
     * @access private
     * @return int
     * @since 2.0
     */
    public function getColspan() {
        return count($this-> aPage) + $this-> addToColspan;
    } // end func getColspan


    /**
     *
     * @access private
     * @return string
     * @since 2.0
     */
    public function getNextPage() {
        ($this-> currentPage < $this-> nbLinks) ? $link = '<a href="' . $this-> getLink($this-> currentPage + 1) . '" ' . $this-> linkClass . '>'. $this-> nextPage .'</a>' : $link = '';
        return $link;
    } // end func getNextPage


   /**
     * Retourne le lien html pour accéder à la page précédente
     *
     * Si la page courante est la première page, cette méthode retourne une chaîne vide
     *
     * @access private
     * @return string
     * @since 3.1.a
     */
    public function getPreviousPage() {
        ($this-> currentPage != 1) ? $link = '<a href="' . $this-> getLink($this-> currentPage - 1) . '" ' . $this-> linkClass . '>'. $this-> previousPage .'</a>' : $link = '';
        return $link;
    } // end func getPreviousPage



    /**
     * Retourne le lien html d'accès à la première page du template
     */
    public function getFirstPage() {
        return ($this-> currentPage != 1) ? $link = '<a href="' . $this-> getLink(1) . '" ' . $this-> linkClass . '>'. $this-> firstPage .'</a>' : $link = '';
    } // end func getFirstPage



    /**
     * Retourne le lien html pour accéder à la dernière page
     *
     * access private
     * return string
     */
    public function getLastPage() {
        return ($this-> currentPage != $this-> nbLinks) ? $link = '<a href="' . $this-> getLink($this-> nbLinks) .'" ' . $this-> linkClass . '>'. $this-> lastPage .'</a>' : $link = '';
    } // end func getLastPage


    /**
     * Numéro représentant le début des enregistrements affichés
     *
     * access private
     * return int
     */
    public function getFrom() {
        if ($this-> currentPage == 1) return 1;

        $reste = $this-> perPage * $this-> currentPage - $this-> nbRecord;
        ($reste <= 0) ? $r = ($this-> perPage * $this-> currentPage) - $this-> perPage : $r = $this-> nbRecord - ($this-> nbRecord - ($this-> perPage * $this-> currentPage - $this-> perPage));
        return $r + 1;
    } // end func getFrom



    /**
     * Numéro représentant la fin des enregistrements affichés
     *
     * access private
     * return int
     */
    public function getTo() {
        if ($this-> currentPage == $this-> nbLinks) return $this-> nbRecord;

        return $this-> currentPage * $this-> perPage;
    } // end func getTo



    /**
     * Style du lien courant
     *
     * Retourne le lien courant spécifié par $link_name
     * @access private
     * @return string
     */
    public function getCurSpanPage($link_name) {
        return $this-> curPageSpanPre . $link_name . $this-> curPageSpanPost;
    } // end func getCurSpanPage



    /**
     * Retourne dans un tableau tous les liens à afficher pour le multipage
     *
     * Retourne le lien courant spécifié par $link_name
     * @access private
     * @param string $repeat Morceau du template contenant la zone à répéter
     * @return string
     */
    public function getAllPage() {
        $z     = 0;
        $page  = '';
        $liens = '';

        While ($z < $this-> nbLinks) {
            $z++;

            if ((($z >= $this-> first_pos) && ($z <= $this-> last_pos)) || ($this-> delta == $this-> nbLinks) ) {

                ($this-> multipage_mode == 'alphabetique') ? $linkName = $this-> getAlphaLinkValue($z) : $linkName = $z;

                if ($z != 1 && $z != $this-> first_pos)   $page = $this-> separator;

                if ($z == $this-> currentPage) $page .= $this-> getCurSpanPage($linkName);
                else                       $page .= '<a href="'. $this-> getLink($z) . '" ' . $this-> linkClass . '>'. $linkName .'</a>';

                //$liens .= preg_replace($this-> getTplPattern('PAGE'), $page, $repeat);
                $liens .= $page;
                // Pour la méthode getAll() & getColspan
                $this-> aPage[] = $page;

             // AJOUT DAVID DURET 2004-02-09 10:50
                $this-> result[] = array(
                   'PageNumber'   => $z,
                   'PageIsCurrent'   => ( $z == $this-> currentPage ) ? TRUE : FALSE,
                   'PageUri'      => $this-> getLink($z),
                   'PageLabel'      => $linkName,
                   'PageLink'      => ''
                );
         }
      }

        return $liens;
    } // end func getAllPage


    /**
     * Nom du lien en mode alpha-numérique
     *
     * Retourne le nom du lien $index (numéro de la page) lorsque la classe fonctionne en mode alphanumérique.
     * Elle se base sur les propriétés $alphaEncaps et $aAlpha.
     * @access private
     * @return string nom du lien
     * @since 3.1
     */
    public function getAlphaLinkValue($index) {

        if ($this-> alphaEncaps == 1) return $this-> aAlpha[$index-1];

        $return     = '[';
        $aIndex     = $this-> getAlphaIndex($index);
        $IndexStart = $aIndex[0];
        $IndexEnd   = $aIndex[1];

        unset($aIndex);

        While ($IndexStart != $IndexEnd) {

            if (!IsSet($this-> aAlpha[$IndexStart])) break;

            $return .= $this-> aAlpha[$IndexStart];
            $IndexStart++;

            if ($IndexStart < $IndexEnd && IsSet($this-> aAlpha[$IndexStart])) $return .= '-';
        }

        $return .= ']';

        return $return;
    } // end func getAlphaLinkValue



    /**
     * Lien pour la page spécifiée
     *
     * Retourne l'URI complète pour accèder à la page $num_page. Le retour diffère en fonction de la propriété $tplUri.
     * @param  int num_page, numéro de la page pour laquelle il faut créer le lien
     * @access private
     * @return string URI d'accès à la page
     * @since 2.0
     */
    public function getLink($num_page) {
        if (!empty($this-> tplUri)) return preg_replace($this-> getTplPattern('PAGE'), $num_page, $this-> tplUri);
        else                        return $this-> page_file . $this-> varUrl . '=' . $num_page;
    } // end func getLink



    /**
     * Lit le fichier passé en paramètre et retourne son contenu
     *
     * @param string $file le chemin d'accès complet au fichier (inclu le nom du fichier)
     * @access private
     * @return string $date le contenu du fichier
     * @exception bool false
     * @since 1.0
     */
   public function getFile($file) {
      if (!Is_File($file)) return false;

      $fp   = @fopen($file, 'r');
      $data = @fread($fp, filesize($file));
            @fclose($fp);

      return $data;
   } // end func getFile



    /**
     * Génère l'expression régulière propre au nom de la variable $var_name
     *
     * La génération se base sur les propriétés "$tplVarBegin" & "$tplVarEnd" et sur la chaine passée en paramètre
     * @access private
     * @param string $var_name le nom de la variable du template
     * @return string pattern
     * @since 3.1a
     */
    public function getTplPattern($var_name) {
        return '/' . str_replace('/', '\/', preg_quote($this-> tplVarBegin)) . '(' . $var_name .')' . str_replace('/', '\/', preg_quote($this-> tplVarEnd)) . '/';
    } // end func getTplPattern



    /**
     * Génère l'expression régulière pour récuperer la zone à répéter dans le template
     *
     * La génération se base sur les propriétés "$tplVarLoopBegin" & "$tplVarLoopEnd"
     * @access private
     * @return string pattern
     * @since 3.1a
     */
    public function getTplLoopPat() {
        return '/' . str_replace('/', '\/', preg_quote($this-> tplVarLoopBegin)) . '(.*)' . str_replace('/', '\/', preg_quote($this-> tplVarLoopEnd)) . '/';
    } // end func getTplLoopPat



    /**
     * Evalue la présence d'une variable dans le fichier template
     *
     * La propriété $tpl_data doit exister avant l'appel à cette méthode.
     * La chaine passée en paramètre correspond à l'indice du tableau tplVarName[] défini dans le fichier de configuration
     * @access private
     * @return int nombre d'occurence de $search dans $tpl_data
     * @see getTplPattern()
     */
    public function PregMatch($search) {
        return preg_match($this-> getTplPattern($search), $this-> tpl_data);
    } // end func PregMatch



    /**
     * Evalue la présence d'une zone à répeter dans le fichier template
     *
     * La propriété $tpl_data doit exister avant l'appel à cette méthode.
     * @access private
     * @return int nombre d'occurence de la zone à répeter dans $tpl_data
     * @see getTplLoopPat()
     */
    public function PregMatchLoop() {
        return preg_match($this-> getTplLoopPat(), $this-> tpl_data, $test);
    } // end func PregMatchLoop



    /**
     * Renvoie un tableau contenant toutes les propriétés de la classe
     *
     * Sert à ceux qui ne veulent pas passer par les templates de la classe
     * @return array
     * @access public
     */
    public function getAll() {
        $this-> setup();

        $array = Array(
            $this-> tplVarName['NBRECORD']      => $this-> nbRecord,
            $this-> tplVarName['NEXT_PAGE']     => $this-> getNextPage(),
            $this-> tplVarName['PREVIOUS_PAGE'] => $this-> getPreviousPage(),
            $this-> tplVarName['FIRST_PAGE']    => $this-> getFirstPage(),
            $this-> tplVarName['LAST_PAGE']     => $this-> getLastPage(),
            $this-> tplVarName['CURRENT_PAGE']  => $this-> currentPage,
            $this-> tplVarName['TOTAL_PAGE']    => $this-> nbLinks,
            $this-> tplVarName['LIMIT']         => $this-> perPage,
            $this-> tplVarName['FROM']          => $this-> getFrom(),
            $this-> tplVarName['TO']            => $this-> getTo()
        );

        if (!IsSet($this-> aPage)) $this-> getAllPage();

        // AJOUT DAVID DURET 2004-02-09 10:50
      $array['RawPages'] = $this-> result;

        $array[$this-> tplVarName['PAGE']] = $this-> aPage;

        // Le parsing de la variable colspan ne peut avoir lieu qu'après l'appel à la méthode getAllPage() (dû à l'instanciation de la propriété aPage)
        $array[$this-> tplVarName['COLSPAN']] = $this-> getColspan();

        return $array;
    } // end func getAll



   /**
     * Lance les fonctions pour parser le template
     *
     * Cherche et remplace dans le template les variables connues en lançant les traitements associés
     * @access private
     * @see getFile(), parseLoop(), parseColspan(), parseNbRecord(), parseNextPage(), parsePreviousPage(), parseFirstPage(), parseLastPage(), parseCurrentPage(), parseTotalPage()
     * @since 2.0
     */
   public function parsetemplate() {
      $tpl = & new CopixTpl ();
      $tpl->assign ('LOOP'          , $this-> getAllPage());
      $tpl->assign ('COLSPAN'       , $this-> getColspan());
      $tpl->assign ('NBRECORD'      , $this-> nbRecord);
      $tpl->assign ('NEXT_PAGE'     , $this-> getNextPage());
      $tpl->assign ('PREVIOUS_PAGE' , $this-> getPreviousPage());
      $tpl->assign ('FIRST_PAGE'    , $this-> getFirstPage());
      $tpl->assign ('LAST_PAGE'     , $this-> getLastPage());
      $tpl->assign ('CURRENT_PAGE'  , $this-> currentPage);
      $tpl->assign ('TOTAL_PAGE'    , $this-> nbLinks);
      $tpl->assign ('LIMIT'         , $this-> perPage);
      $tpl->assign ('FROM'          , $this-> getFrom());
      $tpl->assign ('TO'            , $this-> getTo());
      $this-> tpl_data =  $tpl-> fetch ($this-> template);
   } // end func parsetemplate



   /**
     * Affichage du multipage
     *
     * Affiche directement en sortie le contenu du template après traitement
     * @access public
     * @return void
     * @since 1.0
     */
   public function pMultipage() {
        $this-> setup();
      echo $this-> tpl_data;
   } // end func pMultipage



   /**
     * Renvoie le résultat du template une fois parsé
     *
     * @access public
     * @return string le template une fois parsé
     * @since 1.0
     */
   public function getMultipage(){
        // On balance les traitements
        $this-> setup();
      return $this-> tpl_data;
   } // end func getMultipage



   /**
     * Reconstruction de l'url de la page en cours
     *
     * @access private
     * @return string URL
     */
   public function buildUrl() {
      $params = $_GET;
        $uri    = basename($_SERVER['PHP_SELF']);

      foreach ($params as $key => $value) {
            if ($this-> encodeVarUrl === TRUE) $value = urlencode($value);

         if ($key != $this-> varUrl) $queryString[] = $key . '=' . $value;
      }

      if (IsSet($queryString) && count($queryString) > 0 ) $uri .= '?' . implode('&amp;', $queryString) . '&';
      else $uri .= '?';

        return $uri;
   } // end func buildUrl



   /**
     * Lit le fichier de configuration et en exploite le contenu
     *
     * @see buildUrl()
     * @return void
     * @access private
     * @since 1.0
     */
   public function &config() {
        // Lecture du fichier de configuration
        $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'CopixPager.config.php'; 

        $options = array ();
        require ($file);//prépare $options
        // Rappatriement des données en provenance du fichier de configuration
        $this->setConfig($options['PARAMS']);
        $this->setConfig($options['PAGER']);
        $this->setConfig($options['ADVANCED']);
        //$this->setConfig($options['TPL_VAR_NAME'], 'tplVarName');

        // Passage en entités HTML
        if ($this-> toHtmlEntities === TRUE) $this-> encodeHtml();

      // Nom du fichier
      $this-> page_file = $this-> buildUrl();

        // Ajout de l'attribut html "class" pour les liens
        if (!empty($this-> linkClass)) $this-> linkClass = ' class="' . $this-> linkClass . '" ';

      // Initialisation des valeurs par défaut :
      $this-> nbLinks = 1;

        if (!IsSet($_GET[$this-> varUrl]) || $_GET[$this-> varUrl] == 0){
        	$_GET[$this-> varUrl] = 1;
        }

        return $options;

   } // end func config


    /**
     * Convertit les caractères spéciaux en entités HTML
     *
     * Uniquement pour les variables pouvant contenir des libellés
     * @access private
     * @return void
     * @since 3.2a
     */
    public function encodeHtml() {

        function easyEncode(&$var) {
            $var = htmlentities($var);
        }

        // Encodage des variables
        easyEncode($this-> nextPage);
        easyEncode($this-> previousPage);
        easyEncode($this-> lastPage);
        easyEncode($this-> firstPage);
    }



    /**
     * Instancie les propriétés contenu dans le paramètre $array
     *
     * @param array $array
     * @param string $put_in_array Permet d'ajouter les données dans une propriété de type tableau qui prendra le contenu de cette variable comme nom du tableau
     * @return void
     */
    public function setConfig($array, $put_in_array = '') {

        if (!Empty($put_in_array) && !isSet($this-> $put_in_array)) $this-> $put_in_array = Array();

        foreach ((array)$array as $option => $value) {

            if (isSet($this-> $put_in_array)) {
                $test =& $this-> $put_in_array;
                if (Empty($test[$option])) $test[$option] = $value;
            }
            else if (!IsSet($this-> $option)) $this-> $option = $value;
        }

        if (isSet($test)) unset($test);
    } // end func setConfig



    /**
     * Teste le paramètrage de la classe.
     *
     * En cas d'erreur, arrête le script
     * @access private
     * @return void
     */
    public function testParams() {
        if (!is_int($this-> perPage) || $this-> perPage <= 0) trigger_error('Propriété <b>perPage</b> mal configurée <br>', E_USER_ERROR);
        if ((!is_int($this-> delta) || $this-> delta < 0)) trigger_error('Propriété <b>delta</b> mal configurée <br>', E_USER_ERROR);
        //if (!empty($this-> template) && !is_file($this-> tplDir . $this-> template)) trigger_error('template <b>' . $this-> tplDir . $this-> template .'</b> introuvable <br>', E_USER_ERROR);
        if (!is_int($this-> addToColspan)) trigger_error('Propriété <b>addToColspan</b> mal configurée <br>', E_USER_ERROR);
        if (!is_bool($this-> alwaysShow)) trigger_error('Propriété <b>alwaysShow</b> mal configurée <br>', E_USER_ERROR);
        if (!is_string($this-> alphaColumn)) trigger_error('Propriété <b>alphaColumn</b> mal configurée <br>', E_USER_ERROR);
        if (!is_int($this-> alphaEncaps) || $this-> alphaEncaps <= 0) trigger_error('Propriété <b>alphaEncaps</b> mal configurée <br>', E_USER_ERROR);
        if (!empty($this-> tplUri) && (!is_string($this-> tplUri) || !preg_match($this-> getTplPattern('PAGE'), $this-> tplUri))) trigger_error('Propriété <b>tplUri</b> mal configurée. Vérifiez la présence de la variable PAGE telle qu\'elle est configurée dans le fichier de configuration<br>', E_USER_ERROR);
        if (empty($this-> varUrl) || !is_string($this-> varUrl)) trigger_error('Propriété <b>varUrl</b> mal configurée. Vérifiez son paramètrage dans le fichier de configuration <br>', E_USER_ERROR);
        if (!is_bool($this-> toHtmlEntities)) trigger_error('Propriété <b>toHtmlEntities</b> mal configurée <br>', E_USER_ERROR);
        if (!is_bool($this-> encodeVarUrl)) trigger_error('Propriété <b>encodeVarUrl</b> mal configurée <br>', E_USER_ERROR);
        if ($this-> display != 'sliding' && $this-> display != 'jumping') trigger_error('Propriété <b>display</b> mal configurée <br>', E_USER_ERROR);

    } // end func testParams



    /**
     * Définit la propriété firstline & instancie correctement la valeur de varUrl
     *
     * @access private
     * @since 3.2
     * @return void
     */
    public function setRestriction() {
        ($_GET[$this-> varUrl] == '' || (int) $_GET[$this-> varUrl] < 0) ? $this-> currentPage = 1 : $this-> currentPage = $_GET[$this-> varUrl];

      if (($_GET[$this-> varUrl] == 1) || (!$_GET[$this-> varUrl])) $this-> firstline = 0;
      elseif ($_GET[$this-> varUrl] == 2)                           $this-> firstline = $this-> perPage;
      else                                                          $this-> firstline = $this-> perPage * $_GET[$this-> varUrl] - $this-> perPage;
    }



    /**
     * Paramètre la classe suivant le fichier "config.php".
     *
     * Crée la connexion à la base
     * Instancie la propriété $aSql
     * @access private
     * @return void
     * @since 1.0
     */
   public function setup() {

        if ( FALSE === $this-> _initialized ) {
            $this-> config();
            $this-> testParams();
            $this-> setRestriction();
            $this-> init();
            $this-> calcul();

            // Il est possible de ne pas avoir spécifié de template si l'on passe par la méthpode GetAll()
            if (!empty($this-> template)) {

                // La variable alwaysShow détermine si l'on affiche ou pas le template
                // dans le cas où il n'y a qu'une seule page de résultat
                if ($this-> alwaysShow === true) $this-> parsetemplate();
                elseif ($this-> nbLinks != 1)     $this-> parsetemplate();
            }

            $this-> data = $this-> getRecords();
            $this-> close();

            $this->_initialized = TRUE;
        }
   } // end func setup



    /**
     * Affiche les propriétés de l'objet Multipage
     *
     * @access private
     * @return void
     */
    public function viewObject() {
        print '<xmp>';
        print_r($this);
        print '</xmp>';
    } // end func viewObject

} // end class Multipage
?>