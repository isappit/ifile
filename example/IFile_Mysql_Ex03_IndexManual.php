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
 * This script is a example how to indexed documents manually with MySql
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
use Isappit\Ifile\Adapter\Beans\LuceneDataIndexBean;
use Isappit\Ifile\Config\IFileConfig;
use ZendSearch\Lucene\Document\Field as Zend_Search_Lucene_Field;

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
	
	// Text to index
	$text = "I try to indexing this text with IFile Framework.";
	
	// You can use two process for index a document manually.	
	// 1. Use LuceneDataIndexBean: you can set the fields that IFile uses. 
	//    This object define the type of index of the fields automatically.
	// 
	// 2. Instance a Zend_Search_Lucene_Document and define manually the fields
	
	// 1. Exemple for use LuceneDataIndexBean:
	// crete LuceneDataIndexBean Instance
	$bean = new LuceneDataIndexBean();	
	// setting the fields
	// the "body" fields is mandatory
	$bean->setBody($text);
	$bean->setCreated('15 November 2013');
	$bean->setCreator('Giampaolo Losito, Antonio di Girolamo');
	$bean->setKeywords("IFile, Lucene, MySql, Search Engine");
	$bean->setModified("15 November 2013");
	$bean->setSubject("Object of document");
	$bean->setDescription("Descriptio of the document");
	$bean->setTitle("Title of the document");
	// get Zend_Search_Lucene_Document from bean
	$doc = $bean->getLuceneDocument();	
	
	// 2. Exemple for use Zend_Search_Lucene_Document
	// $doc = new Zend_Search_Lucene_Document();
	// $doc->addField(Zend_Search_Lucene_Field::UnStored('body', $text));
	
	// custom field
	$doc->addField(Zend_Search_Lucene_Field::Keyword('name', "Manually Document"));	
	$doc->addField(Zend_Search_Lucene_Field::Keyword('key', md5($text)));
	$doc->addField(Zend_Search_Lucene_Field::Keyword('category', 'mytest'));	
	
	// add document to index
	$doc = $ifile->addDocument($doc);
	// store document
	$ifile->commit();

	echo "The document is correctly indexing<br />";
		
} catch (Exception $e) {
	echo "Error: ".$e->getMessage();
}