<?php
namespace Isappit\Ifile\Adapter;

use Isappit\Ifile\Adapter\IfileAdapterInterface;
use Isappit\Ifile\Adapter\Beans\LuceneDataIndexBean;
use Isappit\Ifile\Adapter\Helpers\OpenOffice2Txt;
use Isappit\Ifile\Exception\IFileAdapterException;
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.1.1 Adapter_Search_Lucene_Document_OpenOfficeT.php 2011-01-10 16:56:47
 */

/**
 * Adapter per il recupero del contenuto dei file OpenOffice
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileAdapterOpenOffice extends IfileAdapterInterface 
{
	public function __construct() {
		// verifica che esista dell'estenzione EXIF per il parser dei TAG EXIF
		$serverCheck = LuceneServerCheck::getInstance();
		$serverCheck->serverCheck();
		$reportServerCheck = $serverCheck->getReportCheck();
		
		// check SimpleXML
		$reportCheckSimpleXML = $reportServerCheck['Extension']['SimpleXML'];
		if (!$reportCheckSimpleXML->getCheck()) {
			throw new IFileAdapterException("Extension SimpleXML not found");
		}
		// check DOM
		$reportCheckDom = $reportServerCheck['Extension']['dom'];
		if (!$reportCheckDom->getCheck()) {
			throw new IFileAdapterException("Extension DOM not found");
		}  
		// check ZIP
		$reportCheckZip = $reportServerCheck['Extension']['zip']; 
		if (!$reportCheckZip->getCheck()) {
			throw new IFileAdapterException("Extension ZIP not found");
		} 
		// check ZLIB
		$reportCheckZLib = $reportServerCheck['Extension']['zlib'];
		if (!$reportCheckZLib->getCheck()) {
			throw new IFileAdapterException("Extension ZLIB not found");
		} 
		
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
        // Parserizza il documento
		$this->parse();
		// il body deve essere valorizzato
		if (!$this->indexValues->issetNotEmpty('body')) {
			throw new IFileAdapterException('Empty body');	
		}
        
		return $this->indexValues->getLuceneDocument();
    }
	
	/**
	 * Recupera le informazioni del file OpenOffice e il suo contenuto in formato testuale
	 * 
	 * @return void
	 */
	protected function parse()
	{
		// creazione del Bean
		$this->indexValues = new LuceneDataIndexBean();
		// Converte il contenuto di un file ODT in una stringa
		$doc = new OpenOffice2Txt($this->getFilename());
		// recupera i metadata del file 
		$metadata = $doc->getMetadata();
		// Cicla i metadata e inserisce i dati nell'array dell'indice
		foreach ($metadata as $meta => $metaValue) {
			
			switch ($meta) {
				case 'Title':
					$this->indexValues->setTitle($metadata['Title']);
					break;
				case 'Subject':
					$this->indexValues->setSubject($metadata['Subject']);
					break;
				case 'Creator':
					$this->indexValues->setCreator($metadata['Creator']);
					break;
				case 'Keywords':
					$this->indexValues->setKeywords($metadata['Keywords']);
					break;
				case 'CreationDate':
					$this->indexValues->setCreated($metadata['CreationDate']);
					break;
				case 'Date':
					$this->indexValues->setModified($metadata['ModDate']);						
					break;
			}
		}
		$this->indexValues->setBody($doc->openoffice2txt());
	}
}