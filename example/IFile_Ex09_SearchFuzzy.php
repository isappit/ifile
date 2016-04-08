<?php
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile.example
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright  2011-2013 isApp.it (www.isapp.it)
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.2.1
 * 
 */

/**
 * This script is a example how to search a term in "fuzzy" in the index with Lucene Interface
 * 
 * WARNING:
 * This methos is many fast to parser method
 */ 

error_reporting(E_ALL);
require ("../autoload_prs4.php");
require("../vendor/autoload.php");

use Isappit\Ifile\IFileFactory;
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
	
	// TEST Query Fuzzy		
	// 1. Search Fuzzy term
	// For search IFile use IFileQueryRegistry
	$ifileQueryRegistry = new IFileQueryRegistry();
	$ifileQueryRegistry->setQuery('fil', 'body', null);
	// define order
	$ifile->setSort('key', SORT_STRING, SORT_DESC);		
	// call Phrase Method
	$result = $ifile->queryFuzzy($ifileQueryRegistry);	
	// print result
	printResult("Search Fuzzy (body:fil)", $result);
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