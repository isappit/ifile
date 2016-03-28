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
 * Adapter per il recupero del contenuto dei file DOC
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileDocument_DOC extends Adapter_Search_Lucene_Document_Abstract 
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
		$this->pathBinaryFile = dirname(__FILE__).DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."binaries".DIRECTORY_SEPARATOR;
		$this->antiwordResource = $this->pathBinaryFile."resources";	
		$this->config = IFileConfig::getInstance()->getConfig('doctotxt');		 
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
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception('Empty body');	
		}
		
		return $this->indexValues->getLuceneDocument();
    }
	
	/**
	 * Cerca di recuperare il contenuto tramite l'utilizzo delle COM
	 * 
	 * @return void
	 */
	private function parseCOM() {
		// verifica che la COM sia richiamabile per il parser dei file DOC
		$serverCheck = LuceneServerCheck::getInstance();
		$serverCheck->serverCheck();
		$reportServerCheck = $serverCheck->getReportCheck();
		// check XPDF 
		$reportCheckCOM = $reportServerCheck['Extension']['com'];
		
		if (!$reportCheckCOM->getCheck()) {
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception("COM not supported");
		}
		// la libreria non restituisce altre informazioni oltre che il contenuto
    	$doc = new PHPWordLib();
		// utilizza le librerie COM per la lettura del contenuto
		$contents = $doc->LoadFileCOM($this->getFilename());
		
		if ($contents === false) {
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception('Could not initialise MS Word object');
		}	
		// creazione del Bean
		$this->indexValues = new LuceneDataIndexBean();
		    	
		$this->indexValues->setBody($contents);
	} 
	
	/**
	 * Recupera il contenuto di un file DOC utilizzando le ANTIWORD
	 * 
	 * @return void
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
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception("ANTIWORD not executable or supported");
		} 
		// check popen 
		$reportCheckPopen = $reportServerCheck['Function']['popen'];
		if (!$reportCheckPopen->getCheck()) {
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception("Popen function not exists");
		}
		
		// inizializza l'handle a null
		$handle = null;
		// inizializzo l'encoding a vuoto
		$encoding = "";

		// gestione dell'encodig
		if (!empty($this->config['encoding'])) {
			$encoding = " -m ".$this->config['encoding'].".txt";
		}
				
		if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
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
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception("ANTIWORD not supported for this OS: ". strtoupper(substr(PHP_OS, 0, 3))); 
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
	 */
	protected function parsePHP() {
		
		// creazione del Bean
		$this->indexValues = new LuceneDataIndexBean();		
		// la libreria non restituisce altre informazioni oltre che il contenuto
    	$doc = new PHPWordLib();
		// carica il file
		$contents = $doc->LoadFile($this->getFilename());
		// verifica se il documento e' un DOC
		if ($contents === false) {			
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception('File is not a DOC');
		}
		
		$this->indexValues->setBody($doc->GetPlainText($contents));	
    }
}
?> 