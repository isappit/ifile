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
 * This script is a example how to search a "prhase" in the index with Lucene Interface
 * 
 * WARNING:
 * This methos is many fast to parser method
 */ 

error_reporting(E_ALL);
require ("../autoload_prs4.php");
require("../../../autoload.php");

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
	
	// TEST Query Phrase		
	// 1. Search Exact Phrase	
	// For search IFile use IFileQueryRegistry
	$ifileQueryRegistry = new IFileQueryRegistry();
	$ifileQueryRegistry->setQuery('come utilizzare ifile', 'body', IFileQuery::MATCH_REQUIRED);
	// define order
	$ifile->setSort('key', SORT_STRING, SORT_DESC);		
	// call Phrase Method
	$result = $ifile->queryPhrase($ifileQueryRegistry);	
	// print result
	printResult("Search Phrase (body:come utilizzare ifile)", $result);
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