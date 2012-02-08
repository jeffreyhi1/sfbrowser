<?php
/*
* SFBrowser
* Copyright (c) 2008 Ron Valstar http://www.sjeiti.com/
* Dual licensed under the MIT and GPL licenses:
*   http://www.opensource.org/licenses/mit-license.php
*   http://www.gnu.org/licenses/gpl.html
*/

// Destination folder for downloaded files
//$upload_folder = 'data';
// If the browser supports sendAsBinary () can use the array $ _FILES
/*// todo: implement this and refactor js
if(count($_FILES)>0) {
	if( move_uploaded_file( $_FILES['upload']['tmp_name'] , $upload_folder.'/'.$_FILES['upload']['name'] ) ) {
		echo 'done';
	}
	exit();
} else if(isset($_GET['up'])) {
	// If the browser does not support sendAsBinary ()
	if(isset($_GET['base64'])) {
		$content = base64_decode(file_get_contents('php://input'));
	} else {
		$content = file_get_contents('php://input');
	}

	$headers = getallheaders();
	$headers = array_change_key_case($headers, CASE_UPPER);

	if(file_put_contents($upload_folder.'/'.$headers['UP-FILENAME'], $content)) {
		echo 'done';
	}
	exit();
}*/


// FF :: http://localhost/libs/js/sfbrowser/web/sfbrowser/connectors/php/sfbrowser.php?a=uploading&folder=../data/&allow=&deny=php|php3|phtml&resize=null&base64=false
// Ch :: http://localhost/libs/js/sfbrowser/web/sfbrowser/connectors/php/sfbrowser.php?a=uploading&folder=../data/&allow=&deny=php|php3|phtml&resize=null&base64=true

include_once("AbstractSFB.php");
class SFBrowser extends AbstractSFB {
	
	protected $sConnBse = "../../";
	protected $aValidate = array(
		 "fileList"=>	array(0,2,0)	// retreive file list
		,"duplicate"=>	array(0,3,0)	// duplicate file

		,"uploading"=>	array(6,0,0)	// html5 file upload
		,"binload"=>	array(6,0,1)	// html5 binary file upload
		,"upload"=>		array(0,5,1)	// file upload

		,"delete"=>		array(0,3,0)	// file delete
		,"download"=>	array(2,0,0)	// file force download
		,"read"=>		array(0,3,0)	// read txt file contents
		,"rename"=>		array(0,4,0)	// rename file
		,"addFolder"=>	array(0,3,0)	// add folder
		,"moveFiles"=>	array(0,4,0)	// move files
	);
	
	// SFBrowser
	function __construct() {
		//
		parent::__construct();
		//
		/*$sP = "POST: [";
		$sG = "GET:  [";
		$sF = "FILE: [";
		foreach($_POST as  $k=>$v)	$sP .= $k.":".$v.",";
		foreach($_GET as   $k=>$v)	$sG .= $k.":".$v.",";
		foreach($_FILES as $k=>$v)	$sF .= $k.":".$v.",";
		$sP .= "]";
		$sG .= "]";
		$sF .= "]";
		$sLog  = '_'.date("j-n-Y H:i")."\t\t";
		$sLog .= "ip:".$_SERVER["REMOTE_ADDR"]."\t\t";
		$sLog .= "\n\t\t".$sP."\n\t\t".$sG."\n\t\t".$sF;
		trace($sLog);*/
		//
		if ($this->sAction) {
			switch ($this->sAction) {

				case "fileList": // retreive file list
					$sDir = $this->sConnBse.(isset($_POST["folder"])?$_POST["folder"]:"data/");
					$aFiles = array();
					if ($handle = opendir($sDir)) while (false !== ($file = readdir($handle))) {
						$oFNfo = $this->fileInfo($sDir.$file);
						if ($oFNfo) $aFiles[] = $oFNfo;
					}
					$this->aReturn['msg'] .= "fileListing";
					$this->aReturn['data'] = $aFiles;
				break;

				case "duplicate": // duplicate file
					$sCRegx = "/(?<=(_copy))([0-9])+(?=(\.))/";
					$sNRegx = "/(\.)(?=[A-Za-z0-9]+$)/";
					$oMtch = preg_match( $sCRegx, $this->sSFile, $aMatches);
					if (count($aMatches)>0)	$sNewFile = preg_replace($sCRegx,intval($aMatches[0])+1,$this->sSFile);
					else					$sNewFile = preg_replace($sNRegx,"_copy0.",$this->sSFile);
					while (file_exists($sNewFile)) { // $$ there could be a quicker way
						$oMtch = preg_match( $sCRegx, $sNewFile, $aMatches);
						$sNewFile = preg_replace($sCRegx,intval($aMatches[0])+1,$sNewFile);
					}
					if (copy($this->sSFile,$sNewFile)) {
						$oFNfo = $this->fileInfo($sNewFile);
						$this->aReturn['data'] = $oFNfo;
						$this->aReturn['msg'] .= "duplicated#".$sNewFile;
					} else {
						$this->aReturn['error'] = "notduplicated#".$sNewFile;
					}
				break;

				case "uploading": // html5 file upload
					foreach($_GET as $k=>$v) $_POST[$k] = $v;
				case "upload": // file upload
					$sElName = $this->sAction=="upload"?"fileToUpload":"Filedata";

					$oContents = file_get_contents('php://input');
					$bContents = $oContents!='';

					$bFileError = !$bContents&&!empty($_FILES[$sElName]["error"]);
					$bFileEmpty = !$bContents&&!$bFileError&&(empty($_FILES[$sElName]["tmp_name"])||$_FILES[$sElName]["tmp_name"]=="none");

					if ($bFileError) {
						$this->aReturn['error'] = 'uploadErr'+$_FILES[$sElName]["error"];
					} else if ($bFileEmpty) { ////////////////////////////////////////////////////////////////////////
						$this->aReturn['error'] = "No file was uploaded..";
					} else { /////////////////////////////////////////////////////////////////////////////////////////

						$sDeny =	$_POST["deny"];
						$sAllow =	$_POST["allow"];
						$sResize = 	$_POST["resize"];
						$sFolder = 	$_POST["folder"];

						$this->aReturn['msg'] .= "sFolder_".$sFolder;

						if ($bContents) { // file from php://input
							if (isset($_GET['base64'])) $oContents = base64_decode($oContents);
							$aHeaders = array_change_key_case(getallheaders(), CASE_UPPER);
							$sFile = $aHeaders['UP-FILENAME'];
						} else { // file from $_FILES
							$oFile = $_FILES[$sElName];
							$sFile = $oFile["name"];
						}
						
						$sMime = array_pop(preg_split("/\./",$sFile));//mime_content_type($sDir.$file); //$oFile["type"]; //
						$sFileTo = $sFolder.$sFile;

						// test for existing files with identical name and append a nr
						$iRpt = 1;
						while (file_exists($sFileTo)) {
							$aFile = explode(".",$sFile);
							$aFile[0] .= "_".($iRpt++);
							$sFile = implode(".",$aFile);
							$sFileTo = $sFolder.$sFile;
						}
						$sFileTo = $this->sConnBse.$sFileTo;

						if ($bContents)
						if (!($bContents?file_put_contents($sFileTo,$oContents):move_uploaded_file($oFile["tmp_name"],$sFileTo))) {
							$this->aReturn['error'] .= "Temporary file could not be copied...";
						} else {
							
							$oFNfo = $this->fileInfo($sFileTo);

							$bAllow = $sAllow=="";
							$sFileExt = array_pop(explode(".",$sFile));
							if ($oFNfo) {
								$this->aReturn['msg'] .= $iRpt===1?'fileUploaded':'fileExistsrenamed';
								// check if file is allowed in this session $$$$$$todo: check SFB_DENY
								foreach (explode("|",$sAllow) as $sAllowExt) {
									if ($sAllowExt==$sFileExt) {
										$bAllow = true;
										break;
									}
								}
								foreach (explode("|",$sDeny) as $sDenyExt) {
									if ($sDenyExt==$sFileExt) {
										$bAllow = false;
										break;
									}
								}
							} else {
								$bAllow = false;
							}
							if (!$bAllow) {
								$this->aReturn['error'] = "uploadNotallowed#".$sFileExt;
								@unlink($sFileTo);
							} else {
								if ($sResize&&$sResize!="null"&&$sResize!="undefined"&&($sMime=="jpeg"||$sMime=="jpg")) {
									$aResize = explode(",",$sResize);
									$iToW = $aResize[0];
									$iToH = $aResize[1];
									list($iW,$iH) = getimagesize($sFileTo);
									$fXrs = $iToW/$iW;
									$fYrs = $iToH/$iH;
									if (false) {//just resize
										$fRsz = min($fXrs,$fYrs);
										if ($fRsz<1) {
											$iNW = intval($iW*$fRsz);
											$iNH = intval($iH*$fRsz);
											$oImgN = imagecreatetruecolor($iNW,$iNH);
											$oImg = imagecreatefromjpeg($sFileTo);
											imagecopyresampled($oImgN,$oImg, 0,0, 0,0, $iNW,$iNH, $iW,$iH );
											imagejpeg($oImgN, $sFileTo);
										}
									} else { // crop after resize
										$fRsz = max($fXrs,$fYrs);
										//if ($fRsz<1) {
										if ($fXrs<1||$fYrs<1) {
											$iNW = intval($iW*$fRsz);
											$iNH = intval($iH*$fRsz);
											$iFrX = $iNW>$iToW?($iNW-$iToW)/2:0;
											$iFrY = $iNH>$iToH?($iNH-$iToH)/2:0;
											$iFrW = $iNW>$iToW?$iToW*(1/$fRsz):$iW;
											$iFrH = $iNH>$iToH?$iToH*(1/$fRsz):$iH;
											$oImgN = imagecreatetruecolor($iToW,$iToH);
											$oImg = imagecreatefromjpeg($sFileTo);
											imagecopyresampled($oImgN,$oImg, 0,0, $iFrX,$iFrY, $iToW,$iToH, $iFrW,$iFrH );
											imagejpeg($oImgN, $sFileTo);
										}
									}
									$oFNfo = $this->fileInfo($sFileTo);
								}
								$this->aReturn['data'] = $oFNfo;
							}
						}
					}
				break;

				case "delete": // file delete
					if (count($_POST)!=3||!isset($_POST["folder"])||!isset($_POST["file"])) exit("ku ka");
					if (is_file($this->sSFile)) {
						if (@unlink($this->sSFile))	$this->aReturn['msg'] .= "fileDeleted";
						else					$this->aReturn['error'] .= "fileNotdeleted";
					} else {
						if (@rmdir($this->sSFile))	$this->aReturn['msg'] .= "folderDeleted";
						else					$this->aReturn['error'] .= "folderNotdeleted";
					}
				break;

				case "download":// file force download
					$sZeFile = $this->sConnBse.$this->sSFile;
					if (file_exists($sZeFile)) {
						ob_start();
						//$sType = "application/octet-stream";
						header("Cache-Control: public, must-revalidate");
						header("Pragma: hack");
						header("Content-Type: " . $this->sSFile);
						header("Content-Length: " .(string)(filesize($sZeFile)) );
						header('Content-Disposition: attachment; filename="'.array_pop(explode("/",$sZeFile)).'"');
						header("Content-Transfer-Encoding: binary\n");
						ob_end_clean();
						readfile($sZeFile);
						exit();
					}
				break;

				case "read":// read txt file contents
					$sExt = strtolower(array_pop(explode('.',$this->sSFile)));
					//
					// install extensions and add to php.ini
					// - extension=php_zip.dll
					if ($sExt=="zip") {
						$sDta = "";
						if (!function_exists("zip_open")) {
							$this->aReturn['error'] = "php_zip not installed or enabled";
						} else if ($zip=@zip_open(getcwd()."/".$this->sSFile)) { // 
							while ($zip_entry=@zip_read($zip)) $sDta .=  @zip_entry_name($zip_entry)."\\r\\n"; // zip_entry_filesize | zip_entry_compressedsize | zip_entry_compressionmethod
							@zip_close($zip);
							$this->aReturn['data'] = array(
								 'type'=>'archive'
								,'text'=>$sDta
							);
						}
					} else if ($sExt=="rar") { // - extension=php_rar.dll
						if (!function_exists("rar_open")) {
							$this->aReturn['msg'] .= "php_rar not installed or enabled";
						} else if ($rar_file=@rar_open(getcwd()."/".$this->sSFile)) {
							$entries = @rar_list($rar_file);
							$sDta = '';
							foreach ($entries as $entry) $sDta .=  $entry->getName()."\\r\\n"; // getName | getPackedSize | getUnpackedSize
							@rar_close($rar_file);
							$this->aReturn['data'] = array(
								 'type'=>'archive'
								,'text'=>$sDta
							);
						}
					} else if ($sExt=="pdf") {
						include('class.pdf2text.php');
						$oPdf = new PDF2Text();
						$oPdf->setFilename($this->sSFile);
						$oPdf->decodePDF();
						$sCnt = str_replace(array("\n","\r","\t"),array("\\n","\\n",""),substr($oPdf->output(),0,PREVIEW_BYTES));
						$this->aReturn['data'] = array(
							 'type'=>'ascii'
							,'text'=>$sCnt
						);
					} else if ($sExt=="doc") {
						//////////////////////////////
						// does not seem to be possible
						//////////////////////////////
					} else {
						$oHnd = fopen($this->sSFile, "r");
						$sCnt = preg_replace(array("/\n/","/\r/","/\t/"),array("\\n","\\r","\\t"),addslashes(fread($oHnd, 600)));
						fclose($oHnd);
						$this->aReturn['data'] = array(
							 'type'=>'ascii'
							,'text'=>$sCnt
						);
					}
					$this->aReturn['msg'] .= count($this->aReturn['data'])?'contentsSucces':'contentsFail';
				break;

				case "rename":// rename file
					if (isset($_POST["file"])&&isset($_POST["nfile"])) {
						$sFile = $_POST["file"];
						$sNFile = $_POST["nfile"];

						$sFileExt = array_pop(preg_split("/\./",$sFile));
						$sNFileExt = array_pop(preg_split("/\./",$sNFile));

						$sNSFile = str_replace($sFile,$sNFile,$this->sSFile);
						if (@filetype($this->sSFile)=="file"&&$sFileExt!=$sNFileExt) {
							$this->aReturn['error'] .= "filenameNoext";
						} else if (!preg_match('=^[^/?*;:{}\\\\]+'.(is_dir($this->sSFile)?'=':'\.[^/?*;:{}\\\\]+$='),$sNFile)) {
							$this->aReturn['error'] .= "filenameInvalid";
						} else if ($sFile==$sNFile) {
							$this->aReturn['msg'] .= "filenameNochange";
						} else if ($sNFile=="") {
							$this->aReturn['error'] .= "filenameNothing";
						} else if (file_exists($sNSFile)) {
							$this->aReturn['error'] .= "filenameExists";
						} else {
							if (@rename($this->sSFile,$sNSFile)) $this->aReturn['msg'] .= "filenameSucces";
							else $this->aReturn['error'] .= "filenameFailed";
						}
					}
				break;

				case "addFolder":// add folder
					if (isset($_POST["folder"]))  {
						$sFolderName = isset($_POST["foldername"])?$_POST["foldername"]:"new folder";
						$iRpt = 1;
						$sFolder = $this->sConnBse.$_POST["folder"].$sFolderName;
						while (file_exists($sFolder)) $sFolder = $this->sConnBse.$_POST["folder"].$sFolderName.($iRpt++);
						if (mkdir($sFolder)) {
							$this->aReturn['msg'] .= "folderCreated";
							$oFNfo = $this->fileInfo($sFolder);
							if ($oFNfo) $this->aReturn['data'] = $oFNfo;
							else $this->aReturn['error'] .= "folderFailed";
						} else {
							$this->aReturn['error'] .= "folderFailed";
						}
					}
				break;

				case "moveFiles": // move files
					if (isset($_POST["file"])&&isset($_POST["folder"])&&isset($_POST["nfolder"])) {
						//
						//$sFolder = $_POST["folder"];
						$sNFolder = $_POST["nfolder"];
						$aFiles = explode(",",$_POST["file"]);
						$aMoved = array();
						$aNotMoved = array();
						for ($i=0;$i<count($this->aFiles);$i++) {
							$sFile = $aFiles[$i];
							$this->sSFile = $this->aFiles[$i];
							$sNSFile = str_replace($sFile,$sNFolder."/".$sFile,$this->sSFile);
							if (file_exists($sNSFile)) {
								$this->aReturn['error'] .= "filemoveExists[".$this->sSFile." ".$sNSFile."] ";
								$aNotMoved[] = $sFile;
							} else {
								if (@rename($this->sSFile,$sNSFile)) {
									$this->aReturn['msg'] .= "filemoveSucces";
									$aMoved[] = $sFile;
								} else {
									$this->aReturn['error'] .= "filemoveFailed";
									$aNotMoved[] = $sFile;
								}
							}
						}
						$this->aReturn['data'] = array(
							 'moved'=>$aMoved
							,'notmoved'=>$aNotMoved
							,'newfolder'=>$sNFolder
						);
					}
				break;
			}
			$this->returnJSON($this->aReturn);
		}
	}
}
$oSFBrowser = new SFBrowser;