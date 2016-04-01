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
namespace Isappit\Ifile\Adapter\Document;

use Isappit\Ifile\Adapter\IFileAdapterAbstract;
use Isappit\Ifile\Adapter\Helpers\AdapterHelper;
use Isappit\Ifile\Exception\IFileAdapterException;
use ZendSearch\Lucene\Document\Docx as Zend_Search_Lucene_Document_Docx;

/**
 * Adapter per il recupero del contenuto dei file DOCX
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileDocument_DOCX extends IFileAdapterAbstract 
{
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Ritorna un oggetto Zend_Search_Lucene_Document
	 *
	 * Implementa il metodo dell'interfaccia Adatpter_Search_Lucene_Document_Interface
	 * 
	 * @return Zend_Search_Lucene_Document
	 */	
	public function loadParserFile()
    {
		// verifica la correttezza del file (DOCX)
		AdapterHelper::checkOpenXML($this->getFilename());
		// recupera i dati del documento
		$doc = Zend_Search_Lucene_Document_Docx::loadDocxFile($this->getFilename());	
		// il body deve essere valorizzato
		if (trim($doc->getFieldValue('body')) == '') {
			throw new IFileAdapterException('Empty body');
		}
		
		return $doc;
    }
}
?> 