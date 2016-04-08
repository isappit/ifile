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
 * This script is a example how to indexed a single document with Lucene
 */ 
error_reporting(E_ALL);
require ("../autoload_prs4.php");
require("../vendor/autoload.php");

use Isappit\Ifile\IFileFactory;

// Define the folder of index. 
// The first time, if the folder exists IFile throw an exception.
$index_path = 'example_ifile_index';
// Path of Document
$file		= 'myfiles/IFile_Introduzione_1_2.pdf';
// $fileConfig = "/Users/giampaolo/Sites/personal/github/ifile/IFileConfig.xml";

// try/catch
try {
	// setto il nuovo file di configurazione
	// IFileConfig::setXmlConfig($fileConfig);
	// instance IFileFactory
	$IFileFactory = IFileFactory::getInstance();
	// define lucene interface
	$ifile = $IFileFactory->getIFileIndexing('lucene', $index_path);
	// set document
	$ifile->setIndexFile($file);
	// add document to index
	$doc = $ifile->addDocument();
	// store document
	$ifile->commit();	
	
	echo "The ($file) is correctly indexing<br />";	
	
} catch (Exception $e) {
	echo "Error in document: ($file) - ".$e->getMessage();
}
?>