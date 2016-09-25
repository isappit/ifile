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
 * This script is a example how to indexed more documents with Lucene
 */ 
error_reporting(E_ALL);
require ("../autoload_prs4.php");
require("../../../autoload.php");

use Isappit\Ifile\IFileFactory;

// Define the folder of index. 
// The first time, if the folder exists IFile throw an exception.
$index_path = 'example_ifile_index';
// Folder of Documents
$directory  = "myfiles";

// first try for catch the errors of interface 
try {
	
	// instance IFileFactory
	$IFileFactory = IFileFactory::getInstance();
	// define lucene interface
	$ifile = $IFileFactory->getIFileIndexing('lucene', $index_path);
	
	// array of files
	$files = array();	
	// get files in folder
	if ($handle = opendir($directory)) {
		while ($file = readdir($handle)) {
			if (!is_dir("{$directory}/{$file}")) {
				if ($file != "." & $file != "..") {
					$files[] = "{$directory}/{$file}";
				}
			}
		}
	}
	closedir($handle);
	
	foreach ($files as $file) {
		// second try for catch the error in index process of documents		 
		try {
			// set document
			$ifile ->setIndexFile($file);
			// add document to index
			$doc = $ifile->addDocument();
			// store document
			$ifile->commit();
			
			echo "The ($file) is correctly indexing<br />";
			
		} catch (exception $e) {
			echo "Error in document: ($file) - ".$e->getMessage()."<br />";
		}
	}
	
} catch (Exception $e) {
	echo "Generic Error: ".$e->getMessage()."<br />";
}