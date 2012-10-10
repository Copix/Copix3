<?php

/**
 * CopixTools to create modules, projects...
 * Only ok for linux systems for now...
 * 
 * @author Patrice FERLET <metal3d@copix.org>
 * @package tools
 * @subpackage copixtools
 * @license LGPL 
 * 
 * copix install prjectname /path/to/www
 * copix create module foo -d
 * copix create theme bar
 * copix add param -m foo -n param1 -t bool
 * copix add event -m foo -n EventSend
 * copix add listener -m foo -n Listener
 */


// basics functions
//from: http://www.php.net/manual/en/function.copy.php#77238
function full_copy( $source, $target ){
	if ( is_dir( $source ) ){
		@mkdir( $target );		 
		$d = dir( $source );		 
		while ( FALSE !== ( $entry = $d->read() ) ){
			if ( $entry == '.' || $entry == '..' ){
				continue;
			}
			 
			$Entry = $source . '/' . $entry;
			if ( is_dir( $Entry ) ){
				full_copy( $Entry, $target . '/' . $entry );
				continue;
			}
			copy( $Entry, $target . '/' . $entry );
		}		 
		$d->close();
	}else{
		copy( $source, $target );
	}
}


/**
 * Base Class for CopixTools
 *
 */
class CopixTool {
	protected $_opt;
	protected $_basedir;
	function __construct(){
		$this->_opt = getopt(':m:n:t:dp:');
		$this->_basedir = dirname(__FILE__);
	}

	function output($val){
		if(is_string($val)){
			echo "Value: ".$val."\n";
		}
		else {
			echo "Value:"."\n";
			var_dump($val);
		}

	}
}


/**
 * CopixProjectInstaller is a project installer if you use Copix
 * as a core for multiple projects
 * 
 * Usage: copix.php install projectname /path/to/use
 * will install a new project named "projectname" in /path/to/use/projectname
 * 
 * This will copy and configure: www direcotry, project.inc.php, project.path.inc.php ans config/copix.conf.php
 * Paths will be recreated in project.path.inc.php, index.php, resource.php
 * 
 * A good example is to put copix in /var/www/copix
 * Go into copixdir/tool and configure a new project:
 * $ cd /var/www/copix/tool
 * $ php copix.php install mysite /var/www/html
 * 
 * Now, you can go to: http://127.0.0.1/mysite to configure your new Copix installation
 * 
 * Note: /var/copix/mysite and /tmp/copix/mysite will be created
 *
 */
class CopixProjectInstaller extends CopixTool {
	private $_copixdir = ''; 
	
	public function __construct(){
		parent::__construct();
		$this->_copixdir = 	realpath($this->_basedir.'/../');
	}
	
	public function install($projectname,$to){
		if(is_dir('/proc')){
			$this->_posix_install($projectname,$to);
		}else{
			echo "\nSorry, You're not on posix/unix system, only unix systems are currently supported\n";
		}
	}
	
	private function _posix_install($projectname,$to){
		
		$uid = `echo \$UID`;
		if($uid!=0){
			die ('Sorry, you have to be root to install new copix project\n');
		}
		
		
		echo "Create a project named $projectname to $to\n";
		$own = array();
		while(count($own)<2){
			echo "Give owner and group for apache (as apache:apache): ";
			$own = fgets(STDIN);
			if(trim($own)=="") $own = "apache:apache";
			$own = trim($own);			
			$own = explode(":",$own);
		}
		$projectpath = $to.'/'.$projectname;
		
		//prepare system
		`mkdir -p /var/log/copix && chown $own[0]:$own[1] /var/log/copix`;
		`mkdir -p /var/log/copix/$projectname`;
		`mkdir -p /var/cache/copix && chown $own[0]:$own[1] /var/cache/copix`;
		`mkdir -p /var/cache/copix/$projectname`;
		`mkdir -p /var/copix && chown $own[0]:$own[1] /var/copix`;
		`mkdir -p /tmp/copix && chown $own[0]:$own[1] /tmp/copix`;
		
		
		//copy needed project files
		`cp -r ../www $projectpath`;
	    `cp -r ../project/config $projectpath/config`;
		`cp -r ../var /var/copix/$projectname`;
		`cp -r ../temp /tmp/copix/$projectname`;
		
		`chown $own[0]:$own[1] /var/cache/copix/$projectname`;
		`chown $own[0]:$own[1] /var/log/copix/$projectname`;
		`chown $own[0]:$own[1] /var/copix/$projectname`;
		`chown $own[0]:$own[1] /tmp/copix/$projectname`;
		
		copy('../project/project.inc.php', $projectpath.'/config/project.inc.php');

		//now, modify paths into sources
		//index.php:
		$index = file_get_contents($projectpath.'/index.php');
		
		$index = str_replace('require ($path . \'/../utils/copix/copix.inc.php\');',
							 'require (\''.$this->_copixdir.'/utils/copix/copix.inc.php\');',
							 $index);
		$index = str_replace('require ($path . \'/../project/project.inc.php\');',
							 'require ($path . \'/config/project.inc.php\');',
							 $index);
		$index = str_replace('$coord = new ProjectController ($path . \'/../project/config/copix.conf.php\');',
							 '$coord = new ProjectController ($path . \'/config/copix.conf.php\');',
							 $index);
		$f = fopen($projectpath.'/index.php',"w");
		fwrite($f,$index,strlen($index));
		fclose($f);
		//index.php written
		
		//project path
		$f = fopen($projectpath.'/config/project.path.inc.php','w');
		$string = "<?php
/**
* @package              copix
* @subpackage   core
* @author               Croes Gérald, Jouanneau Laurent
* @copyright    2001-2006 CopixTeam
* @link                 http://copix.org
* @license              http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
define ('PROJECT_NAME' , '".$projectname."');
define ('COPIX_PROJECT_PATH', '".$this->_copixdir."/project/');
define ('COPIX_TEMP_PATH',    '/tmp/copix/'.PROJECT_NAME.'/');
define ('COPIX_VAR_PATH',     '/var/copix/'.PROJECT_NAME.'/');
define ('COPIX_LOG_PATH',     '/var/log/copix/'.PROJECT_NAME.'/');
define ('COPIX_CACHE_PATH',   '/var/cache/copix/'.PROJECT_NAME.'/');
?>";
		fwrite($f,$string,strlen($string));
		fclose($f);
		//project written
		
		
		//resource
		$resource = file_get_contents($projectpath.'/resource.php');		
		$resource = str_replace('require (dirname (__FILE__).\'/../utils/copix/copix.inc.php\');',
								'require (\''.$this->_copixdir.'/utils/copix/copix.inc.php\');',
								$resource);
								
		$resource = str_replace('require (dirname (__FILE__).\'/../project/project.path.inc.php\');',
								'require (dirname (__FILE__).\'/config/project.path.inc.php\');',
								$resource);
								
		$f = fopen ($projectpath.'/resource.php','w');
		fwrite($f,$resource,strlen($resource));
		fclose($f);
		
		
		//change owner		
		//better for linux:
		
		`chown -R $own[0]:$own[1] /tmp/copix/$projectname`;
		`chown -R $own[0]:$own[1] /var/copix/$projectname`;
		`chown -R $own[0]:$own[1] $projectpath`;
		
		echo "\n\nProject $projectname written into $projectpath.\n";
	}


}

/**
 * Module Creation class
 *
 */
class CopixModuleCreation extends CopixTool{
	private $_devel;
	private $_modulename;

	function __construct($name=false){
		parent::__construct();
		$this->_modulename = $name;
		$this->_devel = false;
		var_dump($this->_opt);
		if(isset($this->_opt['d'])){
			$this->_devel = true;

		}
	}

	function createModule(){
		$this->output("Creation: ".$this->_modulename);
		if($this->_devel){
			$this->output("en mode devel");
		}
	}


	function addParam(){
		$module = $this->_opt['m'];
		$param = $this->_opt['n'];
		$type = $this->_opt['t'];

		if(isset($this->_opt["m"])){
			$this->output("Add Param for module $module: $param type $type");
		}

	}


	function addListener(){

	}


	function addEvent(){

	}
}


/**
 * Main function 
 *
 * @param array $argv (from command line)
 */

function main($argv){	
	switch ($argv[1]){
		case 'install':
			$action = new CopixProjectInstaller();
			$action->install($argv[2],$argv[3]);
			break;
		case "create":
			if($argv[2]=="module"){
				$action = new CopixModuleCreation($argv[3]);
				$action->createModule();
			}
			else if($argv[2]=="theme"){
				$action = new CopixThemeCreation($argv[3]);
				$action->createTheme();
			}
			break;
		case "add":
			if($argv[2]=="param"){
				$action = new CopixModuleCreation;
				$action->addParam();
			}
			else if($argv[2]=="listener"){
				$action = new CopixModuleCreation;
				$action->addListener();

			}
			else if($argv[2]=="event"){
				$action = new CopixModuleCreation;
				$action->addEvent();
			}
			break;
	}


}

main($argv);
?>