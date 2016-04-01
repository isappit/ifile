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
use Isappit\Ifile\Exception\IFileAdapterException;
use ZendSearch\Lucene\Document\HTML as Zend_Search_Lucene_Document_HTML;

/**
 * Adapter per il recupero del contenuto dei file HTM
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileDocument_HTM extends IFileAdapterAbstract 
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
            
		$doc = Zend_Search_Lucene_Document_HTML::loadHTMLFile($this->getFilename());	
		// il body deve essere valorizzato
		if (trim($doc->getFieldValue('body')) == '') {
			throw new IFileAdapterException('Empty body');
		}
		
		return $doc;
    }		
}
?> 