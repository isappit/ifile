<?php
/**
 * IFile Framework
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Bogomil Alexandrov, Peter Litov
 * @copyright  Copyright (c) Motion Ltd. 2004
 * @license    GNU General Public License
 * @version    2.0
 */
namespace Isappit\Ifile\Adapter\Helpers;

/**
 * Permette di recuperare il contenuto di un documento MS Word in formato testo. 
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author     Bogomil Alexandrov, Peter Litov
 * @copyright  Copyright (c) Motion Ltd. 2004
 * @license    GNU General Public License
 */
class Word2Txt {
	
/*
  Word2Txt version 2.0

  Description: The Word2Txt is a simple (but quite usefull) converter that converts MS Word and RTF files to plain text. Some restrictions apply however:
   - Currently supports version of MS Word 97 or newer (97/2000/XP/2003/... etc)
   - Currently supports RTF (Rich Text Format) versions up to 1.5 (the currently latest version)
   - If the file is saved without accepting track changes the output text might not be fully correct
   - The file must be fully saved, i.e. if the file was fast saved then only part of the document will be retrieved
   - No international character sets support for MS Word files - only ANSI
   - Text in text boxes or other special controls is not retrieved when converting from MS Word

   Hope to have those supported at some time in the future...

   Written by: Bogomil Alexandrov, Peter Litov
   20.11.2004
   (C) Motion Ltd. 2004. All rights reserved!
   Sofia, Bulgaria
   http://www.motion-bg.com

   Can be distributed under GNU, but a contribution from your site (in the form of link to http://www.motion-bg.com) is highly appreciated!
*/

 function LoadFile($filename) {
   $f = fopen($filename, "rb");
   $contents = fread($f, filesize($filename));
   fclose($f);

   if (!$this->CheckWordFormat($contents)) $isWord = FALSE; else $isWord = TRUE;
   if (!$this->CheckRTFFormat($contents)) $isRTF = FALSE; else $isRTF = TRUE;

   if (!$isWord && !$isRTF) return FALSE; else return $contents;
 }
 
 /**
  * Load file using COM extension
  * 
  * @return boolean
  */
 function loadFileCOM($filename) {
 	
	$word = new COM("word.application");
	$word->Documents->Open($filename);
	
	// Extract content.
	$contents = (string) $word->ActiveDocument->Content;
	
	$word->ActiveDocument->Close(false);
	
	$word->Quit();
	$word = null;
	unset($word);
	
	return $contents;
 }

 function CheckWordFormat(&$contents) {
   // This function checks the format of the file. Currently only MS Word 97/2000/XP/2003/... (Windows) format using ANSI character set is supported, so for now we will return false on all others!
   if (@ord($contents[512]) != 236  && @ord($contents[513]) != 165) return FALSE;
   if (@ord($contents[530]) != 0) return FALSE;
   if (@ord($contents[532]) > 0) return FALSE;
   if (@ord($contents[546]) != 98 && @ord($contents[547]) != 106) return FALSE;

   return TRUE;
 }

 function CheckRTFFormat(&$contents) {
 // This function checks whether the format of the file is RTF (Rich Text Format). Currently supported version is up to 1.5
   if (substr($contents, 0, 6) == '{\rtf1') return TRUE; else return FALSE;
 }

 function GetWordPlainText(&$contents) {
 // This function will return the text of a MS Word document in a plain text format. Note: This will only get the main text for now, no text boxes, etc.
   $s1 = (@ord($contents[536]) + (@ord($contents[537]) << 8) + (@ord($contents[538]) << 16) + (@ord($contents[539]) << 24)) + 512;
   $s2 = (@ord($contents[540]) + (@ord($contents[541]) << 8) + (@ord($contents[542]) << 16) + (@ord($contents[543]) << 24)) + 512;
   if ($s2 > $s1) {
     $plaintext = substr($contents, $s1, $s2-$s1);

     $ss1 = strpos($plaintext, chr(19));
     if ($ss1) {
        $ss2 = strpos($plaintext, chr(21), $ss1);
        $plaintext = substr_replace($plaintext, '', $ss1, $ss2-$ss1+1);
     }
     $plaintext = str_replace(chr(0), "", $plaintext);
     $plaintext = str_replace(chr(1), "", $plaintext);
     $plaintext = str_replace(chr(2), "", $plaintext);
     $plaintext = str_replace(chr(3), "", $plaintext);
     $plaintext = str_replace(chr(4), "", $plaintext);
     $plaintext = str_replace(chr(5), "", $plaintext);
     $plaintext = str_replace(chr(6), "", $plaintext);
     $plaintext = str_replace(chr(7), "   ", $plaintext);
     $plaintext = str_replace(chr(8), "", $plaintext);
     $plaintext = str_replace(chr(11), "\n", $plaintext);
     $plaintext = str_replace(chr(12), "\n\n", $plaintext);
     $plaintext = str_replace(chr(13), "\n", $plaintext);
     $plaintext = str_replace(chr(14), "   ", $plaintext);
     $plaintext = str_replace(chr(15), "", $plaintext);
     $plaintext = str_replace(chr(16), "", $plaintext);
     $plaintext = str_replace(chr(17), "", $plaintext);
     $plaintext = str_replace(chr(18), "", $plaintext);
     $plaintext = str_replace(chr(20), "   ", $plaintext);
     $plaintext = str_replace(chr(22), "", $plaintext);
     $plaintext = str_replace(chr(23), "", $plaintext);
     $plaintext = str_replace(chr(24), "", $plaintext);
     $plaintext = str_replace(chr(25), "", $plaintext);
     $plaintext = str_replace(chr(26), "", $plaintext);
     $plaintext = str_replace(chr(27), "", $plaintext);
     $plaintext = str_replace(chr(28), "", $plaintext);
     $plaintext = str_replace(chr(29), "", $plaintext);
     $plaintext = str_replace(chr(30), "", $plaintext);
     $plaintext = str_replace(chr(31), "-", $plaintext);
     $plaintext = str_replace(chr(160), " ", $plaintext);

     return $plaintext;
   } else return false;
 }

 function ExtractRTFBlock($blockname, &$contents) {

   $blockname = '{\\' . $blockname;
   $s1 = strpos($contents, $blockname);
   $s2 = strpos($contents, '}}', $s1);
   return substr($contents, $s1, $s2-$s1+1);
 }

 function RemoveRTFBlock($blockname, &$contents) {

   $blockname = '{\\' . $blockname;
   $s1 = strpos($contents, $blockname);
   $s2 = strpos($contents, '}}', $s1);
   $part1 = substr($contents, 0, $s1-1);
   $part2 = substr($contents, $s2+1);
   return $part1 . $part2;
 }

 function GetRTFPlainText(&$contents) {
   $s1 = strpos($contents, '{', 1);
   $rtf_header = substr($contents, 0, $s1);
   if (substr($rtf_header, 0, 6) == '{\rtf1') $rtf_version = 1; else return FALSE;

   if (strpos($rtf_header, '\ansi')) $rtf_charset = 'ansi';
   if (strpos($rtf_header, '\mac')) $rtf_charset = 'mac';
   if (strpos($rtf_header, '\pc')) $rtf_charset = 'pc';

   $count = preg_match_all("(\\\ansicpg([0-9]+))", $rtf_header, $res);
   if ($count > 0) $rtf_codepage = $res[1][0];

   $count = preg_match_all("(\\\uc([0-9]{1}))", $rtf_header, $res);
   if ($count > 0) $rtf_unicodebytes = $res[1][0];

   $count = preg_match_all("(\\\deflang([0-9]+))", $rtf_header, $res);
   if ($count > 0) $rtf_deflang = $res[1][0];

   $count = preg_match_all("(\\\deflangfe([0-9]+))", $rtf_header, $res);
   if ($count > 0) $rtf_deflangfe = $res[1][0];

   $contents = $this->RemoveRTFBlock('fonttbl', $contents);
   $contents = $this->RemoveRTFBlock('colortbl', $contents);
   $info_block = $this->ExtractRTFBlock('info', $contents);
   $contents = $this->RemoveRTFBlock('info', $contents);
   $s1 = 1;
   while ($s1 > 0) {
     $contents = $this->RemoveRTFBlock('*', $contents);
     $s1 = strpos($contents, '\*');
   }

   $plain_text = $contents;
   $plain_text = str_replace("\n", "", $plain_text);
   $plain_text = str_replace("\r", "", $plain_text);
   $plain_text = str_replace('\pard', "\dummy", $plain_text);

   $plain_text = preg_replace("/(\\\\'([0-9a-zA-Z]*))/e", 'chr(hexdec("\\1"))', $plain_text); // Convert hexdecimal symbols into letters
   $plain_text = preg_replace("(\\\par([ ]){0,1})", "\n", $plain_text); // Convert \par to \n
   $plain_text = preg_replace("(\\\(sn|sv)+([a-zA-Z0-9 -:_\\\\])*([ ]){0,1})", "", $plain_text); // Clean up all unused control symbols
   $plain_text = preg_replace("(\\\([a-z])+([-0-9])*([ ]){0,1})", "", $plain_text); // Clean up all unused control symbols

   $plain_text = str_replace("{", "", $plain_text);
   $plain_text = str_replace("}", "", $plain_text);
   $plain_text = str_replace("  ", " ", $plain_text);

   return $plain_text;
 }

 function GetPlainText(&$contents) {
   if ($this->CheckWordFormat($contents)) return $this->GetWordPlainText($contents);
   if ($this->CheckRTFFormat($contents)) return $this->GetRTFPlainText($contents);
   return FALSE;
 }

}
?>