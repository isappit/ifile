<?php
namespace Isappit\Ifile\Adapter\Document;

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

/** Adatpter_Search_Lucene_Document_Abstract */
require_once 'Adapter_Search_Lucene_Document_Abstract.php';

/**
 * Adapter per il recupero del contenuto dei file XML.
 * L'adapter recupera solo il testo presente all'interno dei TAG
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileDocument_XML extends Adapter_Search_Lucene_Document_Abstract
{
	public function __construct() {
		// verifica che esista la funzione per eliminare tutti i TAG dal file XML
		$serverCheck = LuceneServerCheck::getInstance();
		$serverCheck->serverCheck();
		$reportServerCheck = $serverCheck->getReportCheck();
		// check strip_tags
		$reportCheckPopen = $reportServerCheck['Function']['strip_tags'];
		if (!$reportCheckPopen->getCheck()) {
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception("Strip_tags function not exists");
		}
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
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception('Empty body');	
		}
        
		return $this->indexValues->getLuceneDocument();
    }
	
	/**
	 * Recupera le stringhe del file XML 
	 * 
	 * @return void
	 */
	protected function parse() {
		
		// istanzia la classe per la parserizzazione dei file XML
		// creazione del Bean
		$this->indexValues = new LuceneDataIndexBean();
		// recupero il contenuto del file 
		$data = @file_get_contents($this->getFilename());
		
		if ($data === false) {
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception('Error retrieving the contents of the file');
		}
		
		$this->indexValues->setBody(strip_tags($data));
		$this->indexValues->setModified(date ("d/m/Y H:i:s.", @filemtime($this->getFilename())));		
    }
}
?> 