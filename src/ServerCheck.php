<?php
/**
 * IFile
 *
 * This script to check all requirement to use IFile Library
 *
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @link       https://github.com/isappit/ifile for the canonical source repository
 * @copyright  Copyright (c) 2011-2016 isApp.it (http://www.isapp.it)
 * @license	   GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
require ("../autoload_prs4.php");
require ("../../../autoload.php");

use Isappit\Ifile\Config\IFileConfig;
use Isappit\Ifile\Servercheck\LuceneServerCheck;

// Define external configuration file ( if not defined, IFile use: src/Config/xml/IFileConfig.xml )
// $fileConfig = "/Users/isapp/Sites/personal/github/ifile/IFileConfig.xml";

// IMPORTANT:
// if use a external Configuration file is need to set external
// configuration file first to instance LuceneServerCheck
// IFileConfig::setXmlConfig($fileConfig);

// instance LuceneServerCheck
$serverCheck = LuceneServerCheck::getInstance();
// call check
$serverCheck->serverCheck();
// get check object
//$reportCheck = $serverCheck->getReportCheck();

// display result:
if (empty($argv)) {
    // display in HTML format
	$serverCheck->printReportCheckWeb();
} else {
    // display in CommanLine fomrat
	$serverCheck->printReportCheckCLI();
}