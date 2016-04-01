<?php
/**
 * IFile framework
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    2.0
 */

namespace Isappit\Ifile\Adapter;

/**
 * Elemento di interfaccia per gli Adapter
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
interface IFileAdapterInterface {
	/**
	 * Ritorna un oggetto Zend_Search_Lucene_Document per l'indicizzazione  
	 * 
	 * @return Zend_Search_Lucene_Document
	 */
	function loadParserFile();
	
	/**
	 * Ritorna un oggetto Zend_Search_Lucene_Document per l'indicizzazione  
	 * Il metodo permette di gestire parser diversi da File
	 * 
	 * @param mixed $property
	 * @return Zend_Search_Lucene_Document
	 */
	function loadParserCustom($property);
	
	/**
	 * Setta il path del file da parserizzare
	 * 
	 * @param string $filename
	 * @return void 
	 */
	function setFilename($filename);
	
	/**
	 * Ritorna il path del file da parserizzare
	 *  
	 * @return string 
	 */
	function getFilename();
}
?>