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
 * This script is a example how to indexed a single document with Custom Fields with Lucene
 */ 
error_reporting(E_ALL);
require ("../autoload_prs4.php");
require("../vendor/autoload.php");

use Isappit\Ifile\IFileFactory;
use Isappit\Ifile\Searchengine\IFileIndexingInterface;

// Define the folder of index. 
// The first time, if the folder exists IFile throw an exception.
$index_path = 'example_ifile_index';
// Path of Document
$file	= 'myfiles/customFields/TestAddCustomField.txt';

// try/catch
try {
	// instance IFileFactory
	$IFileFactory = IFileFactory::getInstance();
	// define lucene interface
	$ifile = $IFileFactory->getIFileIndexing('lucene', $index_path);
	// set document
	$ifile->setIndexFile($file);
	// setting Custom Fields
	$ifile->addCustomField('customfield_Keyword', 'my keyword', IFileIndexingInterface::FIELD_TYPE_KEYWORD);
	$ifile->addCustomField('customfield_Text', 'my text', IFileIndexingInterface::FIELD_TYPE_TEXT);
	$ifile->addCustomField('customfield_UnStored', 'my text Unstored', IFileIndexingInterface::FIELD_TYPE_UNSTORED);
	$ifile->addCustomField('customfield_UnIndexed', 'my text Unindexed', IFileIndexingInterface::FIELD_TYPE_UNINDEXED);
	$ifile->addCustomField('customfield_Binary', 'Binary', IFileIndexingInterface::FIELD_TYPE_BINARY);
	
	// WARNING:
	// Not use name of custom fields that IFile define automatically
	// The Custom Fields overwrite the field created from IFile when this parse the document  
	// Example: this field overwrite the field "key" created to IFile
	// $ifile->addCustomField('key', 'mykeyfile', IFile_Indexing_Interface::FIELD_TYPE_KEYWORD);
	
	// get all custom field
	$customFields = $ifile->getCustomField();
	// add document to index
	$doc = $ifile->addDocument();
	// store document
	$ifile->commit();

	echo "The ($file) is correctly indexing<br />";	
	echo "With this custom fields:<br>";
	var_dump($customFields);
	
} catch (Exception $e) {
	echo "Error in document: ($file) - ".$e->getMessage()."<br />";
}
?>