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
 * This script is a example how to use MySqli interface for working with index
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
require("../vendor/autoload.php");

use Isappit\Ifile\IFileFactory;
use Isappit\Ifile\Config\IFileConfig;

// Create connection at MySql
// IFile works only with MySqli Interfaceù
// First to test, you must create "example_ifile_index" DB in the your MySql
$connection = @new mysqli('127.0.0.1', 'root', '', 'example_ifile_index', 3306);					
if (mysqli_connect_error()) {die(" - ".mysqli_connect_error()." - ");}

// Define external configuration file ( if not defined, IFile use: src/Config/xml/IFileConfig.xml )
// $fileConfig = "/Users/isapp/Sites/personal/github/ifile/IFileConfigMySql.xml";

// try/catch
try {
	// IMPORTANT:
	// if use a external Configuration file is need to set external
	// configuration file first to instance IFileFactory
	// IFileConfig::setXmlConfig($fileConfig);
	
	// instance IFileFactory
	$IFileFactory = IFileFactory::getInstance();
	// define mysqli interface
	$ifile = $IFileFactory->getIFileIndexing('mysqli', $connection);
	
	// TEST: Delete a document that exist
	// echo "delete (ID = 1)<br />: ";
	// echo $ifile->delete(1); 
	
	// TEST: Delete a document that not exist
	// echo "delete (id no range): ";
	// echo $ifile->delete(100000);
	
	// TEST: Delete all document
	// echo "delete all document: ";
	// echo $ifile->deleteAll(true);
	
	// TEST: Delete fisically the index
	// echo "delete index: ";
	// echo $ifile->deleteAll(false);
	
	// TEST: Optimize index (use optimize only if present deleted documents in index)
	// echo "optimize: ";
	// echo $ifile->optimize();
	
	// TEST: Numero di documenti (compresi quelli marcati come cancellati) 
	echo "count: ";
	echo $ifile->count()."<br />";
	 
	// TEST: Number of document (in this number not is present the delete document)
	echo "numDocs: ";
	echo $ifile->numDocs()."<br />";
	 
	// TEST: Verify if exists deleted documents
	echo "hasDeletions: ";
	echo $ifile->hasDeletions()."<br />";
	 
	// TEST: Verify if term is present in the fild
	echo "hasTerm (Term:file, Field:body): ";
	echo $ifile->hasTerm('file', 'body')."<br />";
	 
	// TEST: Verify if term is present in the index
	echo "hasTerm (Term:file, Field:): ";
	echo $ifile->hasTerm('file')."<br />";
	 
	// TEST: Return default field
	echo "getDefaultSearchField: ";
	echo $ifile->getDefaultSearchField()."<br />";
	 
	// TEST: Setting default field
	echo "setDefaultSearchField (field:body): ";
	echo $ifile->setDefaultSearchField('body')."<br />";
	
	// TEST2: Return default field
	echo "getDefaultSearchField after setting: ";
	echo $ifile->getDefaultSearchField()."<br />";
	
	// TEST2: Verify if term is present in the index (for only default field)
	echo "hasTerm (Term:file, Field:) (setDefaultSearchField:body): ";
	echo $ifile->hasTerm('file')."<br />";
	
	// TEST3: Setting an field of default that not exist 
	echo "setDefaultSearchField: ";
	echo $ifile->setDefaultSearchField('notexist')."<br />"; 
	
	// TEST3: Verify if term is present in the index (for only default field that not exist)
	echo "hasTerm (Term:file, Field:) (setDefaultSearchField:not exist): ";
	echo $ifile->hasTerm('file')."<br />";
	
	// TEST: Verify if the document is deleted
	// For Mysqli interface the ID of the documents start to 0
	echo "isDeleted (ID:1) : ";
	echo $ifile->isDeleted(1)."<br />";
	
	// TEST: Delete index and return all document deleted	 
	//echo "undeletedAll: ";
	//echo $ifile->undeletedAll()."<br>";
	 
	// TEST: Setting Result limit at 10
	echo "setResultLimit (10): ";
	echo $ifile->setResultLimit(10)."<br />";
	
	// TEST: Setting Result limit at all 
	echo "getResultLimit: ";
	echo $ifile->getResultLimit()."<br />"; 
	
	// TEST: Return the list of Fields present in the indicx (All)
	echo "getFieldNames: <pre>";
	echo print_r($ifile->getFieldNames(), 1)."</pre><br />"; 
	
	// TEST: Return the list of Fields in the indicx (only "indexing" fields)
	echo "getFieldNames (only indexed): <pre>";
	echo print_r($ifile->getFieldNames(true), 1)."</pre><br />";
	
	// TEST: Return a single document from ID 
	// For Mysqli interface the ID of the documents start to 0
	echo "getDocument (ID:1): <pre>";
	echo print_r($ifile->getDocument(1), 1)."</pre><br />";
	 
	// TEST2: Return a single document from ID (ID not present)
	//echo "getDocument (id no range): <pre>";
	//echo print_r($ifile->getDocument(100), 1)."</pre><br />";
	
	// TEST: Return all document (without the deleted documents)
		echo "getAllDocument (without delete document): <pre>";
	echo print_r($tot = $ifile->getAllDocument(), 1)."</pre><br />";
	echo "Total document indexing: ".count($tot)."<br />";
	
	// TEST: Return all document (with the deleted documents)
	echo "getAllDocument (with delete document): <pre>";
	echo print_r($tot = $ifile->getAllDocument(true), 1)."</pre><br />";
	echo "Total document: ".count($tot)."<br />";
	
	// TEST: Return terms present in the field
	echo "Terms in the field (Title): <pre>";
	echo print_r($ifile->getTermsForField('title'), 1)."</pre><br />";
	
	// TEST: Return all terms present in the index
	echo "Terms in the index: <pre>";
	echo print_r($ifile->terms(), 1)."</pre><br />";
	
	
} catch (Exception $e) {
	echo "Error: ".$e->getMessage();
}
?>