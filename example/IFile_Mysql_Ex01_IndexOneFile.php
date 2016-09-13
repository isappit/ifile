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
 * This script is a example how to indexed a single document with MySql
 * 
 * IMPORTANT:
 * For MySql is important define in the configuration (
 *  - default: IFileConfig.xml
 *  - you can configure external XML file and set this with static method: IFileConfig::setXmlConfig($fileConfig);
 * Define Table name that IFile must used 
 * The fields: name, path, filename as "Text":
 * 
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
// IFile for now works only with MySqli Interface
// First to test, you must create "example_ifile_index" DB in the your MySql
$connection = new mysqli('127.0.0.1', 'root', '', 'example_ifile_index', 3306);					
if (mysqli_connect_error()) {die(" - ".mysqli_connect_error()." - ");}
// Path of Document
$file	= 'myfiles/IFile_Introduzione_1_2.pdf';
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