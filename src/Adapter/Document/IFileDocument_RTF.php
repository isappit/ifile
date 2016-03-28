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
/** PHPWordLib */
require_once 'helpers/class.doc2txt.php';

/**
 * Adapter per il recupero del contenuto dei file RTF.
 * L'Adapter parserizza correttamente solo i documenti RTF generati nella versione minore o uguale alla 1.5.
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileDocument_RTF extends Adapter_Search_Lucene_Document_Abstract 
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
	 * Recupera le informazioni del file RTF e il suo contenuto in formato testuale
	 * 
	 * @return void
	 */
	protected function parse() {
		
		// creazione del Bean
		$this->indexValues = new LuceneDataIndexBean();
		// la libreria non restituisce altre informazioni oltre che il contenuto
    	$doc = new PHPWordLib();
    	$contents = $doc->LoadFile($this->getFilename()); 
		
		if ($contents === false) {
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception('File is not a RTF');
		}	
		    	
		$this->indexValues->setBody($doc->GetPlainText($contents));
    }
}
?> 