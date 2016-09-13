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
 * This script is a example how to search a grop of terms in boolean method with Lucene Interface
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
	
	// TEST Query Boolean
	// For Boolean Search IFile use IFileQueryRegistry
	
	// Case 1. Search term present in first group AND present or not present in second group
	// First group
	$ifileQueryRegistry_01 = new IFileQueryRegistry();
	$ifileQueryRegistry_01->setQuery('prova', 'body', IFileQuery::MATCH_REQUIRED);
	$ifileQueryRegistry_01->setQuery('txt', 'body', IFileQuery::MATCH_REQUIRED);
	// Second group
	$ifileQueryRegistry_02 = new IFileQueryRegistry();
	$ifileQueryRegistry_02->setQuery('doc', 'body', IFileQuery::MATCH_REQUIRED);
	// Setting query
	$ifileQueryRegistry =  new IFileQueryRegistry();
	$ifileQueryRegistry->setQuery($ifileQueryRegistry_01, null, IFileQuery::MATCH_REQUIRED);
	$ifileQueryRegistry->setQuery($ifileQueryRegistry_02, null, IFileQuery::MATCH_OPTIONAL);
	// define order
	$ifile->setSort('key', SORT_STRING, SORT_DESC);		
	// call Boolean method 
	$result = $ifile->queryBoolean($ifileQueryRegistry);
	// print result
	printResult("Case 1: ", $result);
	
	// Case 2. Search term present or in first group OR in second group
	// First group
	$ifileQueryRegistry_01 = new IFileQueryRegistry();
	$ifileQueryRegistry_01->setQuery('prova', 'body', IFileQuery::MATCH_REQUIRED);
	$ifileQueryRegistry_01->setQuery('txt', 'body', IFileQuery::MATCH_REQUIRED);
	// Second gruppo
	$ifileQueryRegistry_02 = new IFileQueryRegistry();
	$ifileQueryRegistry_02->setQuery('doc', 'body', IFileQuery::MATCH_REQUIRED);
	// Setting query
	$ifileQueryRegistry =  new IFileQueryRegistry();
	$ifileQueryRegistry->setQuery($ifileQueryRegistry_01, null, IFileQuery::MATCH_OPTIONAL);
	$ifileQueryRegistry->setQuery($ifileQueryRegistry_02, null, IFileQuery::MATCH_OPTIONAL);
	// define order
	$ifile->setSort('key', SORT_STRING, SORT_DESC);		
	// call Boolean method 
	$result = $ifile->queryBoolean($ifileQueryRegistry);
	// print result
	printResult("Case 2: ", $result);
	
	
	// Case 3. Search all term present in first group AND NOT present in second group
	// First group
	$ifileQueryRegistry_01 = new IFileQueryRegistry();
	$ifileQueryRegistry_01->setQuery('prova', 'body', IFileQuery::MATCH_REQUIRED);
	$ifileQueryRegistry_01->setQuery('txt', 'body', IFileQuery::MATCH_REQUIRED);
	// Second group
	$ifileQueryRegistry_02 = new IFileQueryRegistry();
	$ifileQueryRegistry_02->setQuery('doc', 'body', IFileQuery::MATCH_REQUIRED);
	// Setting query
	$ifileQueryRegistry =  new IFileQueryRegistry();
	$ifileQueryRegistry->setQuery($ifileQueryRegistry_01, null, IFileQuery::MATCH_REQUIRED);
	$ifileQueryRegistry->setQuery($ifileQueryRegistry_02, null, IFileQuery::MATCH_PROHIBITEN);
	// define order
	$ifile->setSort('key', SORT_STRING, SORT_DESC);		
	// call Boolean method 
	$result = $ifile->queryBoolean($ifileQueryRegistry);
	// print result
	printResult("Case 3: ", $result);
	
	// Case 4. Search term (optional) present in first group AND NOT present in second group	
	// Second group
	$ifileQueryRegistry_01 = new IFileQueryRegistry();
	$ifileQueryRegistry_01->setQuery('prova', 'body', IFileQuery::MATCH_REQUIRED);
	$ifileQueryRegistry_01->setQuery('txt', 'body', IFileQuery::MATCH_REQUIRED);
	// Second group
	$ifileQueryRegistry_02 = new IFileQueryRegistry();
	$ifileQueryRegistry_02->setQuery('doc', 'body', IFileQuery::MATCH_REQUIRED);
	// Setting query
	$ifileQueryRegistry =  new IFileQueryRegistry();
	$ifileQueryRegistry->setQuery($ifileQueryRegistry_01, null, IFileQuery::MATCH_OPTIONAL);
	$ifileQueryRegistry->setQuery($ifileQueryRegistry_02, null, IFileQuery::MATCH_PROHIBITEN);
	// define order
	$ifile->setSort('key', SORT_STRING, SORT_DESC);		
	// call Boolean method 
	$result = $ifile->queryBoolean($ifileQueryRegistry);
	// print result
	printResult("Case 4: ", $result);
	
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