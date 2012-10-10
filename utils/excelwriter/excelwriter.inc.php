<?php


/*
###############################################
####                                       ####
####    Author : Harish Chauhan            ####
####    Date   : 31 Dec,2004               ####
####    Updated:                           ####
####                                       ####
###############################################

*/

/*
* Class is used for save the data into microsoft excel format.
* It takes data into array or you can write data column vise.
*/

Class ExcelWriter {

	var $filename = "";
	var $author = "";
	var $sheetName = "Feuille 1";
	var $fp = null;
	var $error;
	var $state = "CLOSED";
	var $newRow = false;
	var $data = "";

	/*
	* @Params : $file  : file name of excel file to be created.
	* @Return : On Success Valid File Pointer to file
	* 			On Failure return false	 
	*/

	function ExcelWriter($file = "", $p_author = '', $p_sheetName = 'Feuille1') {
		$this->open($file, $p_author, $p_sheetName);
	}

	/*
	* @Params : $file  : file name of excel file to be created.
	* 			if you are using file name with directory i.e. test/myFile.xls
	* 			then the directory must be existed on the system and have permissioned properly
	* 			to write the file.
	* @Return : On Success Valid File Pointer to file
	* 			On Failure return false	 
	*/
	function open($file, $p_author = '', $p_sheetName = 'Feuille1') {
		/*			if($this->state!="CLOSED")
					{
						$this->error="Error : Another file is opend .Close it to save the file";
						return false;
					}	
					
					if(!empty($file))
					{
						$this->fp=@fopen($file,"w+");
					}
					else
					{
						$this->error="Usage : New ExcelWriter('fileName')";
						return false;
					}	
					if($this->fp==false)
					{
						$this->error="Error: Unable to open/create File.You may not have permmsion to write the file.";
						return false;
					}
					$this->state="OPENED";
					fwrite($this->fp,$this->GetHeader());
					return $this->fp;
					* */
		$this->filename = $file;
		$this->state = "OPENED";
		$this->author = $p_author;
		$this->sheetName = $p_sheetName;
		$this->data = "";
		$this->fwrite($this->GetHeader());
	}

	function close() {
		if ($this->state != "OPENED") {
			$this->error = "Error : Please open the file.";
			return false;
		}
		if ($this->newRow) {
			$this->fwrite("</tr>");
			$this->newRow = false;
		}

		$this->fwrite($this->GetFooter());
		//fclose($this->fp);
		$this->state = "CLOSED";
		return;
	}
	/* @Params : Void
	*  @return : Void
	* This function write the header of Excel file.
	*/

	function GetHeader() {
		$date1 = date("Y-m-d")."T".date("H:s:i")."Z";
		$sName = $this->sheetName;
		$author1 = $this->author;
		$header =<<<EOH
				<html xmlns:o="urn:schemas-microsoft-com:office:office"
				xmlns:x="urn:schemas-microsoft-com:office:excel"
				xmlns="http://www.w3.org/TR/REC-html40">

				<head>
				<meta http-equiv=Content-Type content="text/html; charset=iso-8859-15">
				<meta name=ProgId content=Excel.Sheet>
				<meta name=Generator content="Microsoft Excel 9">
				<!--[if gte mso 9]><xml>
				 <o:DocumentProperties>
				  <o:LastAuthor>${author1}</o:LastAuthor>
				  <o:LastSaved>>${date1}</o:LastSaved>
				  <o:Version>10.2625</o:Version>
				 </o:DocumentProperties>
				 <o:OfficeDocumentSettings>
				  <o:DownloadComponents/>
				 </o:OfficeDocumentSettings>
				</xml><![endif]-->
				<style>
				<!--table
					{mso-displayed-decimal-separator:"\.";
					mso-displayed-thousand-separator:"\,";}
				@page
					{margin:1.0in .75in 1.0in .75in;
					mso-header-margin:.5in;
					mso-footer-margin:.5in;}
				tr
					{mso-height-source:auto;}
				col
					{}
				br
					{mso-data-placement:same-cell;}

            .style0
					{mso-number-format:General;
					text-align:general;
					vertical-align:bottom;
					white-space:nowrap;
					mso-rotate:0;
					mso-background-source:auto;
					mso-pattern:auto;
					color:windowtext;
					font-size:10.0pt;
					font-weight:400;
					font-style:normal;
					text-decoration:none;
					font-family:Arial;
					mso-generic-font-family:auto;
					mso-font-charset:0;
					border:none;
					mso-protection:locked visible;
					mso-style-name:Normal;
					mso-style-id:0;}
				td
					{mso-style-parent:style0;
					padding-top:1px;
					padding-right:1px;
					padding-left:1px;
					mso-ignore:padding;
					color:windowtext;
					font-size:10.0pt;
					font-weight:400;
					font-style:normal;
					text-decoration:none;
					font-family:Arial;
					mso-generic-font-family:auto;
					mso-font-charset:0;
					mso-number-format:General;
					text-align:general;
					vertical-align:bottom;
					border:none;
					mso-background-source:auto;
					mso-pattern:auto;
					mso-protection:locked visible;
					white-space:nowrap;
					mso-rotate:0;}
				.xl24
					{mso-style-parent:style0;
					white-space:normal;}
            .gras
               {
                  font-weight:bold;
               }
				-->
				</style>
				<!--[if gte mso 9]><xml>
				 <x:ExcelWorkbook>
				  <x:ExcelWorksheets>
				   <x:ExcelWorksheet>
					<x:Name>${sName}</x:Name>
					<x:WorksheetOptions>
					 <x:Selected/>
					 <x:ProtectContents>False</x:ProtectContents>
					 <x:ProtectObjects>False</x:ProtectObjects>
					 <x:ProtectScenarios>False</x:ProtectScenarios>
					</x:WorksheetOptions>
				   </x:ExcelWorksheet>
				  </x:ExcelWorksheets>
				  <x:WindowHeight>10005</x:WindowHeight>
				  <x:WindowWidth>10005</x:WindowWidth>
				  <x:WindowTopX>120</x:WindowTopX>
				  <x:WindowTopY>135</x:WindowTopY>
				  <x:ProtectStructure>False</x:ProtectStructure>
				  <x:ProtectWindows>False</x:ProtectWindows>
				 </x:ExcelWorkbook>
				</xml><![endif]-->
				</head>

				<body link=blue vlink=purple>
				<table x:str border=0 cellpadding=0 cellspacing=0 style='border-collapse: collapse;table-layout:fixed;'>
EOH;
		return $header;
	}

	function GetFooter() {
		return "</table></body></html>";
	}

	/*
	* @Params : $line_arr: An valid array 
	* @Return : Void
	*/

	function writeLine($line_arr, $class = "xl24") {
		if ($this->state != "OPENED") {
			$this->error = "Error : Please open the file.";
			return false;
		}
		if (!is_array($line_arr)) {
			$this->error = "Error : Argument is not valid. Supply an valid Array.";
			return false;
		}

		foreach ((array) $line_arr as $col => $width)
			$this->fwrite("<col width=$width style='width:".intval($width * 3 / 4)."pt'>");

		$this->fwrite("<tr>");
		// Le tableau fourni doit �tre associatif, avec la donn�e dans la cl� et la largeur colonne dans la valeur
		// la taille est de 64 dans la classe d'origine
		foreach ((array) $line_arr as $col => $width)
			$this->fwrite("<td class=$class width=$width style='width:".intval($width * 3 / 4)."pt'>$col</td>");

		$this->fwrite("</tr>");

	}

	/*
	* @Params : Void
	* @Return : Void
	*/
	function writeRow() {
		if ($this->state != "OPENED") {
			$this->error = "Error : Please open the file.";
			return false;
		}
		if ($this->newRow == false)
			$this->fwrite("<tr>");
		else
			$this->fwrite("</tr>\n\n<tr>");
		$this->newRow = true;
	}

	/*
	* @Params : $value : Coloumn Value
	* @Return : Void
	*/
	function writeCol($value, $width, $class = "xl24") {
		if ($this->state != "OPENED") {
			$this->error = "Error : Please open the file.";
			return false;
		}
		$this->fwrite("<td class=$class>$value</td>");
	}

	function fwrite($d) {
		if (strlen($d) > 0)
			$this->data .= $d."\r\n";
	}

	function printOut() {
		return new CopixActionReturn(COPIX_AR_DOWNLOAD_CONTENT, $this->data, $this->filename, "application/vnd.ms-excel");
	}
	
	function getData() {
        return $this->data;
	}
    
}
?>