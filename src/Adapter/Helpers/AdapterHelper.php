<?php
/**
 * IFile Framework
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.0 AdapterHelper.php 2011-01-17 16:09:56
 */
namespace Isappit\Ifile\Adapter\Helpers;

/**
 * Classe di utility
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999 
 */
class AdapterHelper {
	
	/**
	 * Ritorna la decodifica dei codici di errore della libreria ZipArchive 
	 * 
	 * @param int $code
	 * @return string
	 */
	static function getZipError($code) {
		
		$zipError 	  = array();
		$zipError[0]  = "No error";
		$zipError[1]  = "Multi-disk zip archives not supported";
		$zipError[2]  = "Renaming temporary file failed";
		$zipError[3]  = "Closing zip archive failed";
		$zipError[4]  = "Seek error";
		$zipError[5]  = "Read error";
		$zipError[6]  = "Write error";
		$zipError[7]  = "CRC error";
		$zipError[8]  = "Containing zip archive was closed";
		$zipError[9]  = "No such file";
		$zipError[10] = "File already exists";
		$zipError[11] = "Can't open file";
		$zipError[12] = "Failure to create temporary file";
		$zipError[13] = "Zlib error";
		$zipError[14] = "Malloc failure";
		$zipError[15] = "Entry has been changed";
		$zipError[16] = "Compression method not supported";
		$zipError[17] = "Premature EOF";
		$zipError[18] = "Invalid argument";
		$zipError[19] = "Not a zip archive";
		$zipError[20] = "Internal error";
		$zipError[21] = "Zip archive inconsistent";
		$zipError[22] = "Can't remove file";
		$zipError[23] = "Entry has been deleted";
		
		if (isset($zipError[$code]))
			return $zipError[$code]; 
		else	
			return $zipError[20]; 
	}
	
	/**
	 * Verifica se il documento e' un documento OpenXML
	 *
	 * @param string $filename percorso del file
	 * @throws Adapter_Search_Lucene_Exception 
	 * @return void
	 */
	static function checkOpenXML($filename) {
		// Create new ZIP archive
	    $zip = new ZipArchive;
		$code = $zip->open($filename);
		// Verifica se il file e' sato aperto correttamente
	    if (true !== $code) {
	    	require_once 'Helper_Exception.php';
			throw new Helper_Exception(AdapterHelper::getZipError($code));			
	    }
		// verifica l'esistenza fel file .rels
		if($zip->getFromName('_rels/.rels') === false) {
			require_once 'Helper_Exception.php';
			throw new Helper_Exception(AdapterHelper::getZipError(21));
		}
		// chiude lo zip
		$zip->close();
	}
}
?>