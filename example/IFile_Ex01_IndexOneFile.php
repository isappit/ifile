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
 * This script is a example how to indexed a single document with Lucene
 */ 
error_reporting(E_ALL);
require ("../autoload_prs4.php");
require("../../../autoload.php");

use Isappit\Ifile\IFileFactory;
use Isappit\Ifile\Config\IFileConfig;

// Define the folder of index. 
// The first time, if the folder exists IFile throw an exception.
$index_path = 'example_ifile_index';
// Path of Document
$file		= 'myfiles/IFile_Introduzione_1_2.pdf';
// Define external configuration file ( if not defined, IFile use: src/Config/xml/IFileConfig.xml )
// $fileConfig = "/Users/isapp/Sites/personal/github/ifile/IFileConfig.xml";

// try/catch
try {
	// IMPORTANT: 
	// if use a external Configuration file is need to set external 
	// configuration file first to instance IFileFactory
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