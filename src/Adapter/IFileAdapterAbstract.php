<?php
namespace Isappit\Ifile\Adapter;

use Isappit\Ifile\Adapter\IfileAdapterInterface;
use Isappit\Ifile\Adapter\Beans\DocumentDataIndexBean;
use Isappit\Ifile\Adapter\Helpers\AdapterHelper;
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
 * @version    2.0
 */

/**
 * Classe astratta che implementa l'interfaccia per gli Adapter.
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
abstract class IFileAdapterAbstract implements IfileAdapterInterface {
	
	/**
	 * @var msgError
	 */
	private $msgError = '';
	
	/**
	 * @var string
	 */
	private $filename = '';
	
	/**
	 * Bean dei dati da indicizzare
	 * 
	 * @var LuceneDataIndexBean 
	 */
	protected $indexValues = null;
	
	/**
	 *  
	 * @return void 
	 */
	public function __construct() {}
	
	/**
	 * Implementa l'interfaccia  
	 * 
	 * @return ZendSearch\Lucene\Document
	 */
	public function loadParserFile() {
		return null;
	}
	
	/**
	 * Ritorna un oggetto Zend_Search_Lucene_Document per l'indicizzazione  
	 * 
	 * @return ZendSearch\Lucene\Document
	 */
	public function loadParserCustom($property) {
		return null;
	}
	
	/**
	 * Implementa l'interfaccia
	 * 
	 * @see IFileAdapterInterface
	 * @throws Lucene_Exception
	 */
	public function setFilename($filename) {
		
		// controlla che sia un file
		if(is_dir($filename) || !is_file($filename)) {
			throw new IFileAdapterException('File does not exist or is corrupted');
		}
		
		$this->filename = $filename;
	}
	
	/**
	 * Implementa l'interfaccia
	 * 
	 * @see IFileAdapterInterface
	 */
	public function getFilename() {
		return $this->filename;
	}
	
	/**
	 * Metodo astratto per il processo di parserizzazione del file
	 * che non sono gestiti da Zend Framework
	 * 
	 * @return void
	 */
	protected function parse() {
		return;	
	}	
}
?>