<?php
namespace Isappit\Ifile\Adapter\Helpers;

/**
 * IFile Framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter/helpers
 * @author 	   http://www.blogseye.com/php-rtf-to-text-converter/
 * @copyright  
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    2.0
 */

/**
 * Permette di recuperare il contenuto di un documento MS Word in formato testo. 
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter/helpers
 * @author     http://www.blogseye.com/php-rtf-to-text-converter/
 * @copyright  
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class Rtf2Txt {
	/*
	 * http://www.blogseye.com/php-rtf-to-text-converter/
	 */
	
	function LoadFile($filename) {
	   $f = fopen($filename, "rb");
	   $contents = fread($f, filesize($filename));
	   fclose($f);
	   
	   if (!$this->CheckRTFFormat($contents)) $isRTF = FALSE; else $isRTF = TRUE;
	
	   if (!$isRTF) return FALSE; else return $contents;
	}
 
	function CheckRTFFormat(&$contents) {
		
	 // This function checks whether the format of the file is RTF (Rich Text Format). Currently supported version is up to 1.5
	   if (substr($contents, 0, 5) == '{\rtf') return TRUE; else return FALSE;
	}
	
	function GetPlainText(&$contents) {
	// we'll try to fix up the parts of the rtf as best we can
	// clean up the file a little to simplify parsing
	$contents=str_replace("\r",' ',$contents); // returns
	$contents=str_replace("\n",' ',$contents); // new lines
	$contents=str_replace('  ',' ',$contents); // double spaces
	$contents=str_replace('  ',' ',$contents); // double spaces
	$contents=str_replace('  ',' ',$contents); // double spaces
	$contents=str_replace('  ',' ',$contents); // double spaces
	$contents=str_replace('} {','}{',$contents); // embedded spaces
	// skip over the heading stuff
	$j=strpos($contents,'{',1); // skip ahead to the first part of the header
	
	$loc=1;
	$t="";
	
	$ansa="";
	$len=strlen($contents);
	$this->getpgraph($contents, $j, $len); // skip by the first paragrap
	
	while($j<$len) {
		$c=substr($contents,$j,1);
		if ($c=="\\") {
			// have a tag
			$tag=$this->gettag($contents, $j, $len);
			if (strlen($tag)>0) {
				// process known tags
				switch ($tag) {
					case 'par':
						$ansa.="\r\n";
					break;
					// ad a list of common tags
					// parameter tags
					case 'spriority1':
					case 'fprq2':
					case 'author':
					case 'operator':
					case 'sqformat':
					case 'company':
					case 'xmlns1':
					case 'wgrffmtfilter':
					case 'pnhang':
					case 'themedata':
					case 'colorschememapping':
						$tt=$this->gettag($contents, $j, $len);
					break;
					case '*':
					case 'info':
					case 'stylesheet':
					// gets to end of paragraph
						$j--;
						$this->getpgraph($contents, $j, $len);
					default:
				// ignore the tag
				}
			}
		} else {
			$ansa.=$c;
		}
		$j++;
	}
	$ansa=str_replace('{','',$ansa);
	$ansa=str_replace('}','',$ansa);
		
	return $ansa;
 }
 
 function getpgraph(&$contents, &$j, &$len) {
	// if the first char after a tag is { then throw out the entire paragraph
	// this has to be nested
	$nest=0;
	while(true) {
		$j++;
		if ($j>=$len) break;
		if (substr($contents,$j,1)=='}') {
			if ($nest==0) return;
			$nest--;
		}
		if (substr($contents,$j,1)=='{') {
			$nest++;
		}
	}
	return;
}
 
 private function gettag(&$contents, &$j, &$len) {
	// gets the text following the / character or gets the param if it there
	$tag='';
	while(true) {
		$j++;
		if ($j>=$len) break;
		$c=substr($contents,$j,1);
		if ($c==' ') break;
		if ($c==';') break;
		if ($c=='}') break;
		if ($c=="\\") {
			$j--;
			break;
		}
		if ($c=="{") {
			//$this->getpgraph();
			break;
		}
		if ((($c>='0')&&($c<='9'))||(($c>='a')&&($c<='z'))||(($c>='A')&&($c<='Z'))||$c=="'"||$c=="-"||$c=="*" ){
			$tag=$tag.$c;
		} else {
			// end of tag
			$j--;
			break;
		}
	}
	return $tag;
 }
	
}
?>