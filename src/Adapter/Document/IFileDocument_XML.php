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
use Isappit\Ifile\Adapter\Beans\LuceneDataIndexBean;
use Isappit\Ifile\Exception\IFileAdapterException;

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
class IFileDocument_XML extends IFileAdapterAbstract
{
	public function __construct() {
		// verifica che esista la funzione per eliminare tutti i TAG dal file XML
		$serverCheck = LuceneServerCheck::getInstance();
		$serverCheck->serverCheck();
		$reportServerCheck = $serverCheck->getReportCheck();
		// check strip_tags
		$reportCheckPopen = $reportServerCheck['Function']['strip_tags'];
		if (!$reportCheckPopen->getCheck()) {
			throw new IFileAdapterException("Strip_tags function not exists");
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
			throw new IFileAdapterException('Empty body');	
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
			throw new IFileAdapterException('Error retrieving the contents of the file');
		}
		
		$this->indexValues->setBody(strip_tags($data));
		$this->indexValues->setModified(date ("d/m/Y H:i:s.", @filemtime($this->getFilename())));		
    }
}
?> 