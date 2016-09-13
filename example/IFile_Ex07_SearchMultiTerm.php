<?php 
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile.example
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @link       https://github.com/isappit/ifile for the canonical source repository
 * @copyright  Copyright (c) 2011-2016 isApp.it (http://www.isapp.it)
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.2.1
 * 
 */

/**
 * This script is a example how to search terms in the index with "multi-terms" method with Lucene Interface
 * IFile can setting multi search
 * 
 * WARNING:
 * This methos is many fast to parser method
 */ 

error_reporting(E_ALL);
require ("../autoload_prs4.php");
require("../vendor/autoload.php");

use Isappit\Ifile\IFileFactory;
use Isappit\Ifile\Query\IFileQuery;
use Isappit\Ifile\Query\IFileQueryRegistry;

// Define the folder of index. 
// The first time, if the folder exists IFile throw an exception.
$index_path = 'example_ifile_index';

// try/catch
try {
	// instance IFileFactory
	$IFileFactory = IFileFactory::getInstance();
	// define lucene interface
	$ifile = $IFileFactory->getIFileIndexing('lucene', $index_path);
	
	// TEST Query Multi-termine	
	
	// 1. Search sigle term	
	// For multi-terms IFile use IFileQueryRegistry
	$ifileQueryRegistry = new IFileQueryRegistry();
	$ifileQueryRegistry->setQuery('ifile', 'body', IFileQuery::MATCH_REQUIRED);
	
	// define order
	$ifile->setSort('key', SORT_STRING, SORT_DESC);		
	// call Multi-terms method
	$result = $ifile->query($ifileQueryRegistry);
	// print result
	printResult("Search single term (body:ifile)", $result);
		
	// 2. Search multi terms in AND.	
	$ifileQueryRegistry = new IFileQueryRegistry();
	$ifileQueryRegistry->setQuery('ifile', 'body', IFileQuery::MATCH_REQUIRED);
	$ifileQueryRegistry->setQuery('prova', 'body', IFileQuery::MATCH_PROHIBITEN);
	// define order
	$ifile->setSort('key', SORT_STRING, SORT_DESC);		
	// call Multi-terms method
	$result = $ifile->query($ifileQueryRegistry);
	// print result
	printResult("Search multi terms in AND (body:ifile, body:prova)", $result);
	
	// 3. Search multi terms in OR.	
	$ifileQueryRegistry = new IFileQueryRegistry();
	// id not set thirt paramter IFile set the terms as Optional
	$ifileQueryRegistry->setQuery('zend', 'body');
	$ifileQueryRegistry->setQuery('doc', 'body');
	$ifileQueryRegistry->setQuery('txt', 'body');
	$ifileQueryRegistry->setQuery('ifile', 'body');
	// call Multi-terms method
	$result = $ifile->query($ifileQueryRegistry);
	// print result
	printResult("Ricerca di piu' termini in OR (body:zend, body:doc, body:txt, body:ifile)", $result);
		
} catch (Exception $e) {
	echo "Error: ".$e->getMessage();
}

/**
 * Function of utility. Used only for this example.
 * Print result of search
 * 
 * @param strint $type
 * @param array $result_T
 * @return 
 */
function printResult($type, $result) {
 
	echo "Type of search: ".$type; 
	if(!empty($result) && is_array($result)) {
		echo "<br>Result Search:<br>";
		foreach ($result as $hit) {
			$doc = $hit->getDocument();
			echo "File: ".$doc->name." - Chiave: ".$doc->key." - Score: ".$hit->score."<br>";
		}
	}  else {
		echo "<br>Not result returned<br>";
	}
	
	echo "End print for: ($type)<br><br>";
}
?>