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
 * This script is a example how to search with a term in "fuzzy" in the index with MiSqli Interface
 * 
 * WARNING:
 * This methos is many fast to parser method
 * 
 * IMPORTANT:
 * For MySql is important define in the configuration (
 *  - default: IFileConfig.xml
 *  - you can configure external XML file and set this with static method: IFileConfig::setXmlConfig($fileConfig);
 * Define Table name that IFile must used
 * The fields: name, path, filename as "Text":
 * 
   <table-name collation="utf8_general_ci">ifile_index_table</table-name>
   ...
   <zend-document>
		<fields>			
			<field name="name" type="Text" />
			<field name="path" type="Text" />
			<field name="filename" type="Text" />			
		</fields>		
	</zend-document>
 */

error_reporting(E_ALL);
require ("../autoload_prs4.php");
require("../../../autoload.php");

use Isappit\Ifile\IFileFactory;
use Isappit\Ifile\Config\IFileConfig;
use Isappit\Ifile\Query\IFileQueryRegistry;

// Create connection at MySql
// IFile works only with MySqli Interface
// First to test, you must create "example_ifile_index" DB in the your MySql
$connection = @new mysqli('127.0.0.1', 'root', '', 'example_ifile_index', 3306);
if (mysqli_connect_error()) {die(" - ".mysqli_connect_error()." - ");}

// Define external configuration file ( if not defined, IFile use: src/Config/xml/IFileConfig.xml )
// $fileConfig = "/Users/isapp/Sites/personal/github/ifile/IFileConfigMySql.xml";

// try/catch
try {

    // if use a external Configuration file is need to set external
    // configuration file first to instance IFileFactory
    // IFileConfig::setXmlConfig($fileConfig);

	// instance IFileFactory
	$IFileFactory = IFileFactory::getInstance();
	// define mysqli interface
	$ifile = $IFileFactory->getIFileIndexing('mysqli', $connection);
	
	// TEST Query Phrase		
	// 1. Search Exact Phrase	
	// For search IFile use IFileQueryRegistry
	$ifileQueryRegistry = new IFileQueryRegistry();
	$ifileQueryRegistry->setQuery('ifile', 'body', null);
	// define order
	$ifile->setSort('key', SORT_STRING, SORT_DESC);		
	// call Phrase Method
	$result = $ifile->queryFuzzy($ifileQueryRegistry);	
	// print result
	printResult("Search Fuzzy (body:ifile)", $result);
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