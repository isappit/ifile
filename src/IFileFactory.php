<?php
namespace Isappit\Ifile;

use Isappit\Ifile\Config\IFileConfig;
use Isappit\Ifile\Exception\IFileException;
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    2.0
 */

/**
 * Istance dinamically an object of type:
 * Adapter_Search_Lucene_Document_Interface or IFileIndexingInterface
 *
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileFactory {
	// error_reporting(E_PARSE);
	// Eliminati i NOTICE. 
	// Questo perche' in alcuni casi sia la ICONV 
	// utilizzata dal processo di indicizzazione da parte del framework ZEND 
	// ritornano un Notice dovuto a caratteri sporchi nei documenti
	// Elimando il NOTICE non si capisce se il testo e' stato realmente indicizzato
	// dato che la ICONV tronca il testo al carattere sporco
	// @TODO  
	// verificare se e' possibile per la ICONV continuare il processo
	// di encoding senza fermarsi anche su caratteri non corretti	
	/**
	 * Instanse IFileFactory
	 * 
	 * @var IFileFactory
	 */
	private static $_instance;
	
	/**
	 * Name directory of the file
	 * 
	 * @var string
	 */
	private $dirname;
	
	/**
	 * Private construct for the Singleton pattern 
	 *
	 * @return void 
	 */
	private function __construct() {
		$this->dirname = dirname(__FILE__).DIRECTORY_SEPARATOR;		
	}
	
	/**
	 * Return ionstanse of the IFileFactory object
	 * 
	 * @return IFileFactory  
	 */
	static function getInstance() {
		if (self::$_instance == null) 
			self::$_instance = new IFileFactory();			
			
		return self::$_instance;		
	}
	
	/**
	 * Return an instance of IFile_Indexing_Interface object
	 * 
	 * @throws IFileException
	 * @return IFile_Indexing_Interface
	 * @throws ReflectionException, IFileException 
	 */
	public function getIFileIndexing($type, $resource, $xmlConfig = null) {
		
		// get name class
		$className = "IFileIndexing".ucfirst(strtolower($type));
		$pathFile  = $this->dirname.'Searchengine/'.$className.'.php';
		
		// check if file exists
		if (!file_exists($pathFile)) {
			throw new IFileException('Type of indexing is not allowed');
		} 
		
		$namespace = __NAMESPACE__.'\\Searchengine\\'.$className;
		// Reflection		
		$reflection = new \ReflectionClass($namespace);
		
		$found = false;
		// get interfaces of the class
		$interfaces = $reflection->getInterfaces();
		// check that class implement the ActionInterface
		foreach($interfaces as $interface) {
			if ($interface->getName() == __NAMESPACE__.'\\Searchengine\\IFileIndexingInterface') 
			{
				$found = true;
				break;	
			}				 
		} 
		if(!$found) {
			throw new IFileException('The class does not implement IFileIndexingInterface');
		}
		
		// setto il nuovo file di configurazione
		if ($xmlConfig != null) {
			IFileConfig::setXmlConfig($xmlConfig);	
		}
		
		// return object
		return $reflection->newInstance($resource);
	}
	
	/**
	 * Retunr Adatpter_Search_Lucene_Document_Interface object
	 * 
	 * @throws IFileException
	 * @return Adatpter_Search_Lucene_Document_Interface
	 * @throws ReflectionException, IFileException
	 */
	public function getAdapterSearchLuceneDocument($ext) {
		
		// get name class
		$className =  "Adapter_Search_Lucene_Document_".strtoupper($ext);
		$pathFile  = $this->dirname.'adapter'.DIRECTORY_SEPARATOR.$className.'.php';

		// check if file exists 
		if (!file_exists($pathFile)) {
			throw new IFileException('Type of file extension is not allowed');
		} 

		// require class
		// require_once($pathFile);

		// Reflection		
		$reflection = new ReflectionClass($className);
		$found = false;
		// get interfaces of the class
		$interfaces = $reflection->getInterfaces();
		// check that class implement the ActionInterface			
		foreach($interfaces as $interface) {
			if ($interface->getName() == 'Adapter_Search_Lucene_Document_Interface') 
			{
				$found = true;
				break;	
			}				 
		} 
		if(!$found) {
			throw new IFileException('The class does not implement Adapter_Search_Lucene_Document_Interface');
		}
		
		// ritorna l'oggetto
		return $reflection->newInstance();
	}
}
