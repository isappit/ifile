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
 * This script is a example how to indexed documents manually with Lucene
 */ 
error_reporting(E_ALL);
require ("../autoload_prs4.php");
require("../vendor/autoload.php");

use Isappit\Ifile\IFileFactory;
use Isappit\Ifile\Adapter\Beans\LuceneDataIndexBean;
use ZendSearch\Lucene\Document\Field as Zend_Search_Lucene_Field;

// Define the folder of index. 
// The first time, if the folder exists IFile throw an exception.
$index_path = 'example_ifile_index';


// try/catch
try {
	// instance IFileFactory
	$IFileFactory = IFileFactory::getInstance();
	// define lucene interface
	$ifile = $IFileFactory->getIFileIndexing('lucene', $index_path);
	
	// Text to index
	$text = "I try to indexing this text with IFile Framework.";
	
	// You can use two process for index a document manually.	
	// 1. Use LuceneDataIndexBean: you can set the fields that IFile uses. 
	//    This object define the type of index of the fields automatically.
	// 
	// 2. Instance a Zend_Search_Lucene_Document and define manually the fields
	
	// 1. Exemple for use LuceneDataIndexBean:
	// crea un'istanza di un oggeto LuceneDataIndexBean
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
?>