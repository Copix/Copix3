<?php
/**
* @package      copix
* @subpackage   pdf
* @author       Ferlet Patrice
* @copyright	CopixTeam
* @link         http://www.copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

//can be changed or loaded into copix.inc.php or project.inc.php, or into sources...
if (!defined('COPIX_PDF_PATH'))
	define('COPIX_PDF_PATH', COPIX_PATH . 'pdf/');
if (!defined('COPIX_FPDF_PATH'))
	define('COPIX_FPDF_PATH', COPIX_PATH . '../html2fpdf/');
//as require on FPDF librairie
if (!defined('FPDF_FONTPATH'))
	define('FPDF_FONTPATH', COPIX_FPDF_PATH . 'font/');

Copix::RequireOnce (COPIX_FPDF_PATH . '/html2fpdf.php');

/**
 * Overload HTML2PDF class.
 */
class CopixPdf extends HTML2FPDF {

	var $img_ressource = array ();
	var $name;

	/**
	* Create a PDF document
	* @param name of file, orientation P or L , unit (cm, mm, in...), format (letter, A4...)
	* @return void
	*/
	public function __construct ($name = "unnamed.pdf", $orientation = 'P', $unit = 'mm', $format = 'A4') {
		$this->setName($name);
		parent :: HTML2FPDF($orientation, $unit, $format);
	}

	/**
	 * Set Document name
	 * @param nams of Document
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	* Add a new page
	* @param void
	* @return void
	*/
	public function newPage() {
		$this->AddPage();
	}

	/**
	* Insert HTML content to convert in pdf entities
	* @param string html content
	* @return void
	*/
	public function setHtml($html) {
		Copix::RequireOnce (dirname(__FILE__) . '/htmlparser.class.php');
		$html = str_replace("\n", "", $html);
		//force new line after headers
		$html = preg_replace('/<\/h(\d*?)>/','</h\\1><br />',$html);
		$this->WriteHTML($html);
	}

	/**
	* Save pdf to file
	* @param path (if null, pdf will be saved according the filename)
	* @return void
	*/
	public function OutPut($path = null, $dest = 'I') {
		is_null($path) ? $filename = $this->name : $filename = $path . "/" . $this->name;
		foreach ($this->img_ressource as $image) {
			@ unlink($image);
		}
		//$this->_OutPut($filename,"F");
		//Output PDF to some destination
		global $HTTP_SERVER_VARS;

		//Finish document if necessary
		if ($this->state < 3)
			$this->Close();
		//Normalize parameters
		if (is_bool($dest))
			$dest = $dest ? 'D' : 'F';
		$dest = strtoupper($dest);
		switch ($dest) {
			case 'I' :
			case 'D' :
			case 'S' :
				//Return as a string
				return $this->buffer;
			case 'F' :
				//Save to local file
				$f = fopen($this->name, 'wb');
				if (!$f)
					$this->Error('Unable to create output file: ' .
					$this->name);
				fwrite($f, $this->buffer, strlen($this->buffer));
				fclose($f);
				break;
				;
			default :
				$this->Error('Incorrect output destination: ' . $dest);
		}

		return '';
	}

	/**
	* Return title
	* @param none
	* @return string title
	*/
	public function getTitle() {
		return $this->titulo;
	}

	/**
	* Return the pdf content
	* @param void
	* @return pdf buffer
	*/
	public function getBuffer() {
		return $this->OutPut($this->name, "S");
	}

	//You can overload this:
	//Page header
	public function Header() {
		/* An example:
		if ($this->getTitle() != '')
		{
		      //Arial bold 16
		      $this->SetFont('Arial','B',16);
		      //Move to the right
		      $this->Cell(80);
		      //Title (Underlined)
		      $this->SetStyle('U',true);
		      $this->Cell(30,10,$this->titulo,0,0,'C');
		      $this->SetStyle('U',false);
		      //Line break
		      $this->Ln(20);
		      //Return Font to normal
		      $this->SetFont('Arial','',11);*/
		//parent::Header();
	}

	//Page footer
	public function Footer() {
		/* An example:
		//Position at 1.0 cm from bottom
		$this->SetY(-10);
		//Copyright //especial para esta versão
		$this->SetFont('Arial','B',9);
		$this->SetTextColor(0);
		//Arial italic 9
		$this->SetFont('Arial','I',9);
		//Page number
		$this->Cell(0,10,$this->PageNo().'/{nb}',0,0,'C');
		//Return Font to normal
		$this->SetFont('Arial','',11);*/
		// décommenter s'il doit y avoir un footer : par défaut, affiche le n° de page
		//parent::Footer();	
	}

	//overloaded for better work

	public function Image($file, $x, $y, $w = 0, $h = 0, $type = '', $link = '', $paint = true) {
		//Put an image on the page
		if (!isset ($this->images[$file])) {
			//Patrice Ferlet add:
			//try to open and get type, this is to get 'inline' images
			//construct host
			$last_path = dirname($_SERVER["SCRIPT_NAME"]) . '/';
			$uri = $_SERVER["HTTP_HOST"] . $last_path;

			if (!preg_match(',^http:,', $file) //not http protocole
			and !preg_match(',^' . $uri . ',', $file) //not hostname used
			and !preg_match(';^\.{1,2}/;', $file)) { //not ./ or ../ path used
				$file = 'http://' . $uri . $file;
			}
			//print_r($file); exit;
			if (!$img = fopen($file, "r"))
				return null;
			$buff = fgets($img);
			fclose($img);
			//try first file line, code is tested to get type.
			if (preg_match('/^GIF/i', $buff)) {
				$type = 'gif';
			}
			elseif (preg_match('/PNG/i', $buff)) {
				$type = 'png';
			}
			elseif (preg_match('/^ÿØÿà/', $buff)) {
				$type = 'jpg';
			} else
				$type = '';

			//First use of image, get info
			if ($type == '') {
				$pos = strrpos($file, '.');
				if (!$pos)
					$this->Error('Image file has no extension and no type was specified: ' .
					$file);
				$type = substr($file, $pos +1);
			}
			$type = strtolower($type);
			$mqr = get_magic_quotes_runtime();
			set_magic_quotes_runtime(0);

			//we get copy, and we recreate images to unload alphablended png files etc...
			if ($type == 'jpg' or $type == 'jpeg') {
				$fromimg = COPIX_CACHE_PATH . "/" . uniqid("_img_fo_pdf_") . ".jpg";
				$this->img_ressource[] = $fromimg;
				copy($file, $fromimg);
				$file = $fromimg;
				$info = $this->_parsejpg($file);
			}
			elseif ($type == 'png') {
				$newimg = COPIX_CACHE_PATH . "/" . uniqid("_img_fo_pdf_") . ".png";
				$fromimg = COPIX_CACHE_PATH . "/" . uniqid("_img_fo_pdf_") . ".png";
				$this->img_ressource[] = $newimg;
				$this->img_ressource[] = $fromimg;

				//create a white background for alpha channels
				copy($file, $fromimg);
				$ressource = imagecreatefrompng($fromimg);
				$sizeX = imagesx($ressource);
				$sizeY = imagesy($ressource);
				$white = imagecreate($sizeX, $sizeY);
				$back = imagecolorallocate($white, 255, 255, 255);
				imagefilledrectangle($white, 0, 0, $sizeX, $sizeY, $back);
				$ressource2 = imagecopymerge($white, $ressource, 0, 0, 0, 0, $sizeX, $sizeY, 100);
				imagepng($white, $newimg);

				$file = $newimg;
				$type = "png";
				$info = $this->_parsepng($newimg);
				//$info=$this->_parsepng($file);
			}
			elseif ($type == 'gif') { //EDITEI - updated
				$fromimg = COPIX_CACHE_PATH . "/" . uniqid("_img_fo_pdf_") . ".gif";
				$this->img_ressource[] = $fromimg;
				copy($file, $fromimg);
				$file = $fromimg;
				$info = $this->_parsegif($file);
			} else {
				//Allow for additional formats
				$mtd = '_parse' . $type;
				if (!method_exists($this, $mtd))
					$this->Error('Unsupported image type: ' .
					$type);
				$info = $this-> $mtd ($file);
			}
			set_magic_quotes_runtime($mqr);
			$info['i'] = count($this->images) + 1;
			$this->images[$file] = $info;
		}
		//end of Patrice's modifications, now we continue as original script did:
		else
			$info = $this->images[$file];
		//Automatic width and height calculation if needed
		if ($w == 0 and $h == 0) {
			//Put image at 72 dpi
			$w = $info['w'] / $this->k;
			$h = $info['h'] / $this->k;
		}
		if ($w == 0)
			$w = $h * $info['w'] / $info['h'];
		if ($h == 0)
			$h = $w * $info['h'] / $info['w'];

		$changedpage = false; //EDITEI

		//Avoid drawing out of the paper(exceeding width limits). //EDITEI
		if (($x + $w) > $this->fw) {
			$x = $this->lMargin;
			$y += 5;
		}

		//Avoid drawing out of the page. //EDITEI
		$tMargin = 0;
		if (($y + $h) > $this->fh) {
			$this->AddPage();
			$y = $tMargin +10; // +10 to avoid drawing too close to border of page
			$changedpage = true;
		}

		$outstring = sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q', $w * $this->k, $h * $this->k, $x * $this->k, ($this->h - ($y + $h)) * $this->k, $info['i']);

		if ($paint) //EDITEI
			{
			$this->_out($outstring);
			if ($link)
				$this->Link($x, $y, $w, $h, $link);
		}

		//Avoid writing text on top of the image. //EDITEI
		if ($changedpage)
			$this->y = $y + $h;
		else
			$this->y = $y + $h;

		//Return width-height array //EDITEI
		$sizesarray['WIDTH'] = $w;
		$sizesarray['HEIGHT'] = $h;
		$sizesarray['X'] = $x; //Position before painting image
		$sizesarray['Y'] = $y; //Position before painting image
		$sizesarray['OUTPUT'] = $outstring;
		return $sizesarray;
	}

	//overloaded to make some functions in accord to name copix name's convention
	public function usePre($bool = true) {
		parent :: UsePRE($bool);
	}

	public function useTitle($bool = true) {
		parent :: UseTitle($bool);
	}

	public function setTitle($title) {
		parent :: SetTitle($title);
		$this->titulo = $title;
		$this->title = $title;
	}

	public function useCss($bool = true) {
		parent :: UseCSS($bool);
	}

	//changing path
	public function ReadCSS($html) {
		//! @desc CSS parser
		//! @return string

		/*
		* This version ONLY supports:  .class {...} / #id { .... }
		* It does NOT support: body{...} / a#hover { ... } / p.right { ... } / other mixed names
		* This function must read the CSS code (internal or external) and order its value inside $this->CSS.
		*/

		$match = 0; // no match for instance
		$regexp = ''; // This helps debugging: showing what is the REAL string being processed

		//CSS external
		$regexp = '/<link rel="stylesheet".*?href="(.*?)"\s*?.*?\/?>/si';
		$CSSext = array ();
		$match = preg_match_all($regexp, $html, $CSSext);
		$ind = 0;

		while ($match) {
			$file = file($CSSext[1][$ind]);
			$CSSextblock = implode('', $file);

			//Get class/id name and its characteristics from $CSSblock[1]
			$extstyle = array ();
			$regexp = '/[.# ]([^.]+?)\\s*?\{(.+?)\}/s'; // '/s' PCRE_DOTALL including \n
			preg_match_all($regexp, $CSSextblock, $extstyle);

			//Make CSS[Name-of-the-class] = array(key => value)
			$regexp = '/\\s*?(\\S+?):(.+?);/si';
			$extstyleinfo = array ();
			for ($i = 0; $i < count($extstyle[1]); $i++) {
				preg_match_all($regexp, $extstyle[2][$i], $extstyleinfo);
				$extproperties = $extstyleinfo[1];
				$extvalues = $extstyleinfo[2];
				for ($j = 0; $j < count($extproperties); $j++) {
					//Array-properties and Array-values must have the SAME SIZE!
					$extclassproperties[strtoupper($extproperties[$j])] = trim($extvalues[$j]);
				}
				$this->CSS[$extstyle[1][$i]] = $extclassproperties;
				$extproperties = array ();
				$extvalues = array ();
				$extclassproperties = array ();
			}
			$match--;
			$ind++;
		} //end of match

		$match = 0; // reset value, if needed

		//import CSS
		$regexp = '/import\s*url\((.*?)\)/si';
		$CSSext = array ();
		$match = preg_match_all($regexp, $html, $CSSext);
		$ind = 0;

		while ($match) {
			$CSSext[1][$ind] = str_replace('"', '', $CSSext[1][$ind]);
			$CSSext[1][$ind] = str_replace("'", '', $CSSext[1][$ind]);
			$file = file($CSSext[1][$ind]);
			$CSSextblock = implode('', $file);

			//Get class/id name and its characteristics from $CSSblock[1]
			$extstyle = array ();
			$regexp = '/[.# ]([^.]+?)\\s*?\{(.+?)\}/s'; // '/s' PCRE_DOTALL including \n
			preg_match_all($regexp, $CSSextblock, $extstyle);

			//Make CSS[Name-of-the-class] = array(key => value)
			$regexp = '/\\s*?(\\S+?):(.+?);/si';
			$extstyleinfo = array ();
			for ($i = 0; $i < count($extstyle[1]); $i++) {
				preg_match_all($regexp, $extstyle[2][$i], $extstyleinfo);
				$extproperties = $extstyleinfo[1];
				$extvalues = $extstyleinfo[2];
				for ($j = 0; $j < count($extproperties); $j++) {
					//Array-properties and Array-values must have the SAME SIZE!
					$extclassproperties[strtoupper($extproperties[$j])] = trim($extvalues[$j]);
				}
				$this->CSS[$extstyle[1][$i]] = $extclassproperties;
				$extproperties = array ();
				$extvalues = array ();
				$extclassproperties = array ();
			}
			$match--;
			$ind++;
		} //end of match

		$match = 0; // reset value, if needed

		//CSS internal
		//Get content between tags and order it, using regexp
		$regexp = '/<style.*?>(.*?)<\/style>/si'; // it can be <style> or <style type="txt/css">
		$CSSblock = array ();
		$style = array ();
		$styleinfo = array ();
		$match = preg_match($regexp, $html, $CSSblock);

		if ($match) {
			//Get class/id name and its characteristics from $CSSblock[1]
			$regexp = '/[.#]([^.]+?)\\s*?\{(.+?)\}/s'; // '/s' PCRE_DOTALL including \n
			preg_match_all($regexp, $CSSblock[1], $style);

			//Make CSS[Name-of-the-class] = array(key => value)
			$regexp = '/\\s*?(\\S+?):(.+?);/si';

			for ($i = 0; $i < count($style[1]); $i++) {
				preg_match_all($regexp, $style[2][$i], $styleinfo);
				$properties = $styleinfo[1];
				$values = $styleinfo[2];
				for ($j = 0; $j < count($properties); $j++) {
					//Array-properties and Array-values must have the SAME SIZE!
					$classproperties[strtoupper($properties[$j])] = trim($values[$j]);
				}
				$this->CSS[$style[1][$i]] = $classproperties;
				$properties = array ();
				$values = array ();
				$classproperties = array ();
			}

		} // end of match

		//print_r($this->CSS);// Important debug-line!

		//Remove CSS (tags and content), if any
		$regexp = '/<style.*?>(.*?)<\/style>/si'; // it can be <style> or <style type="txt/css">
		$html = preg_replace($regexp, '', $html);

		return $html;
	}
}
?>