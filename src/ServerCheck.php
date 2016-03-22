<?php
namespace Isappit\Ifile;
/**
 * IFile framework
 * 
 * Lo script serve solo a verificare se sono installate tutte le librerie necessarie al  
 * corretto funzionamento di IFile all'interno dell'ambiente dove questa verra' utilizzata.
 * 
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license	   GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.0 serverCheck.php 2011-01-18 22:11:43
 */

/** LuceneServerCheck */
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'servercheck/LuceneServerCheck.php';

// instanzia la classe LuceneServerCheck
$serverCheck = LuceneServerCheck::getInstance();
// richiama il metodo di controllo
$serverCheck->serverCheck();
// recupera l'array degli oggetti di check
//$reportCheck = $serverCheck->getReportCheck();

// presenta a video il risultato del controllo:
// in formato tabellare se richiamato da browser
// in linea se richiamato da shell
if (empty($argv)) {
	$serverCheck->printReportCheckWeb();
} else {	
	$serverCheck->printReportCheckCLI();
}
?>