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
use Isappit\Ifile\Adapter\Helpers\Word2Txt;
use Isappit\Ifile\Config\IFileConfig;
use Isappit\Ifile\Exception\IFileAdapterException;
use Isappit\Ifile\Servercheck\LuceneServerCheck;

/**
 * Adapter per il recupero del contenuto dei file DOC
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileDocument_DOC extends IFileAdapterAbstract 
{
	
	/**
	 * Path of Antiword Resource
	 * @var string
	 */
	private $antiwordResource;
	
	/**
	 * Path of binary file
	 * @var string
	 */
	private $pathBinaryFile;
	
	/**
	 * Array della configurazione per la doctotxt
	 * @var array
	 */
	private $config;
	
	
	public function __construct() {
		parent::__construct();
		// setta le variabili dei path
		$this->pathBinaryFile 	= IFileConfig::getInstance()->getBinariesPath();
		$this->antiwordResource = $this->pathBinaryFile."resources";	
		$this->config = IFileConfig::getInstance()->getConfig('doctotxt');
	}
	
	/**
	 * Ritorna un oggetto Zend_Search_Lucene_Document
	 *
	 * Implementa il metodo dell'interfaccia Adatpter_Search_Lucene_Document_Interface
	 * 
	 * @return Zend_Search_Lucene_Document
	 * @throws IFileAdapterException
	 */
	public function loadParserFile()
    {
		
		switch ($this->config['type']) {
			case 'ANTIWORD':
				$this->parseAntiword();
				break;				
			case 'COM':
				$this->parseCOM();
				break;
			default:
				$this->parsePHP();					
		}
		
		// il body deve essere valorizzato
		if (!$this->indexValues->issetNotEmpty('body')) {
			throw new IFileAdapterException('Empty body');	
		}
		
		return $this->indexValues->getLuceneDocument();
    }
	
	/**
	 * Cerca di recuperare il contenuto tramite l'utilizzo delle COM
	 * 
	 * @return void
	 * @throws IFileAdapterException
	 */
	private function parseCOM() {
		// verifica che la COM sia richiamabile per il parser dei file DOC
		$serverCheck = LuceneServerCheck::getInstance();
		$serverCheck->serverCheck();
		$reportServerCheck = $serverCheck->getReportCheck();
		// check XPDF 
		$reportCheckCOM = $reportServerCheck['Extension']['com'];
		
		if (!$reportCheckCOM->getCheck()) {
			throw new IFileAdapterException("COM not supported");
		}
		// la libreria non restituisce altre informazioni oltre che il contenuto
    	$doc = new PHPWordLib();
		// utilizza le librerie COM per la lettura del contenuto
		$contents = $doc->LoadFileCOM($this->getFilename());
		
		if ($contents === false) {
			throw new IFileAdapterException('Could not initialise MS Word object');
		}	
		// creazione del Bean
		$this->indexValues = new LuceneDataIndexBean();
		    	
		$this->indexValues->setBody($contents);
	} 
	
	/**
	 * Recupera il contenuto di un file DOC utilizzando le ANTIWORD
	 * 
	 * @return void
	 * @throws IFileAdapterException
	 */
	private function parseAntiword() {
		// verifica che la ANTIWORD sia eseguibile per il parser dei file DOC
		// e verifica che la funzione popen sia installata
		$serverCheck = LuceneServerCheck::getInstance();
		$serverCheck->serverCheck();
		$reportServerCheck = $serverCheck->getReportCheck();
		// check XPDF 
		$reportCheckXPDF = $reportServerCheck['ANTIWORD']['ANTIWORD'];		 
		if (!$reportCheckXPDF->getCheck()) {
			throw new IFileAdapterException("ANTIWORD not executable or supported");
		} 
		// check popen 
		$reportCheckPopen = $reportServerCheck['Function']['popen'];
		if (!$reportCheckPopen->getCheck()) {
			throw new IFileAdapterException("Popen function not exists");
		}
		
		// inizializza l'handle a null
		$handle = null;
		// inizializzo l'encoding a vuoto
		$encoding = "";

		// gestione dell'encodig
		if (!empty($this->config['encoding'])) {
			$encoding = " -m ".$this->config['encoding'].".txt";
		}
		// Custom Executable
		$pathExecutable = $this->config['executable'];

		if (!empty($pathExecutable)) {
			$antiword = $pathExecutable;
			// inserisce la variabile di ambiente per il recupero dei file di mappatura
			putenv("ANTIWORDHOME=".$this->antiwordResource);
			$handle = popen("{$pathExecutable} {$encoding} {$this->getFilename()}", 'r');
		} else if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			// per il SO: WINDOWS
			$antiword = $this->pathBinaryFile . "windows"; 
			// inserisce la variabile di ambiente per il recupero dei file di mappatura
			putenv("ANTIWORDHOME=".$this->antiwordResource);
			//$handle = popen("{$antiword}". DIRECTORY_SEPARATOR ."antiword.exe -m 8859-1.txt {$this->getFilename()}", 'r');			
			$handle = popen("{$antiword}". DIRECTORY_SEPARATOR ."antiword.exe {$encoding} {$this->getFilename()}", 'r');			
		} else if(strtoupper(substr(PHP_OS, 0, 3)) === 'DAR'){
			// per il SO: OSX (DARWIN)
			$antiword = $this->pathBinaryFile . "osx"; 
			// inserisce la variabile di ambiente per il recupero dei file di mappatura
			putenv("ANTIWORDHOME=".$this->antiwordResource);
			$handle = popen("{$antiword}". DIRECTORY_SEPARATOR ."antiword {$encoding} {$this->getFilename()}", 'r');
		}else if(strtoupper(substr(PHP_OS, 0, 3)) === 'LIN'){
			// per il SO: LINUX
			$antiword = $this->pathBinaryFile . "linux"; 
			// inserisce la variabile di ambiente per il recupero dei file di mappatura
			putenv("ANTIWORDHOME=".$this->antiwordResource);
			$handle = popen("{$antiword}". DIRECTORY_SEPARATOR ."antiword {$encoding} {$this->getFilename()}", 'r');
		}else{
			throw new IFileAdapterException("ANTIWORD not supported for this OS: ". strtoupper(substr(PHP_OS, 0, 3))); 
		}	
		
		$contents = '';
		if($handle){
			while (!feof($handle)) {
				set_time_limit(0);
				$contents .= fread($handle, 8192);
		  	}
		}
		// creazione del Bean
		$this->indexValues = new LuceneDataIndexBean();
		$this->indexValues->setBody($contents);
	}
	
	/**
	 * Recupera le informazioni del file DOC e il suo contenuto in formato testuale da script PHP
	 * 
	 * @return void
	 * @throws IFileAdapterException
	 */
	protected function parsePHP() {
		
		// creazione del Bean
		$this->indexValues = new LuceneDataIndexBean();		
		// la libreria non restituisce altre informazioni oltre che il contenuto
    	$doc = new Word2Txt();
		// carica il file
		$contents = $doc->LoadFile($this->getFilename());
		// verifica se il documento e' un DOC
		if ($contents === false) {			
			throw new IFileAdapterException('File is not a DOC');
		}
		
		$this->indexValues->setBody($doc->GetPlainText($contents));	
    }
}
?> 