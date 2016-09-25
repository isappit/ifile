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
 * This script is a example how to use Lucene interface for working with index
 */ 
error_reporting(E_ALL);
require ("../autoload_prs4.php");
require("../../../autoload.php");

use Isappit\Ifile\IFileFactory;

// Define the folder of index. 
// The first time, if the folder exists IFile throw an exception.
$index_path = 'example_ifile_index';

// try/catch
try {
	// instance IFileFactory
	$IFileFactory = IFileFactory::getInstance();
	// define lucene interface
	$ifile = $IFileFactory->getIFileIndexing('lucene', $index_path);
	
	// TEST: Delete a document that exist
 	//echo "delete (ID = 1)<br />: ";
 	//echo $ifile->delete(1); 
 	//$ifile->commit();
 	//die();
	
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
 	//echo "optimize: ";
 	//echo $ifile->optimize();
 	//die();
	
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
	echo "setDefaultSearchField (field:notexist): ";
	// echo "not implemented<br />";
	echo $ifile->setDefaultSearchField('notexist')."<br />"; 
	
	// TEST3: Verify if term is present in the index (for only default field that not exist)
	echo "hasTerm (Term:file, Field:) (setDefaultSearchField:not exist): ";
	echo $ifile->hasTerm('file')."<br />";
	
	// TEST: Verify if the document is deleted
	// For Lucene interface the ID of the documents start to 0
	echo "isDeleted (ID:0) : ";
	echo $ifile->isDeleted(0)."<br />";
	
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
	echo "getDocument (ID:0): <pre>";
	echo print_r($ifile->getDocument(0), 1)."</pre><br />";
	 
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