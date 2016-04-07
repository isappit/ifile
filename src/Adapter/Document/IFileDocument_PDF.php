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
use Isappit\Ifile\Config\IFileConfig;
use Isappit\Ifile\Config\Helpers\IFileXpdfConfig;
use Isappit\Ifile\Exception\IFileAdapterException;
use Isappit\Ifile\Servercheck\LuceneServerCheck;

/**
 * Adapter per il recupero del contenuto dei file PDF
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileDocument_PDF extends IFileAdapterAbstract 
{
	/**
	 * Percorso del file PDF
	 * 
	 * @var string
	 */
	private $pathBinaryFile;
	
	/**
	 * Congigurazione IFile 
	 * @var IFileConfig
	 */
	private $iFileConfig;
		
	public function __construct() {
		// Recupera i dati di configurazione 		
		$this->ifileConfig = IFileConfig::getInstance();	
				
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
	 * Recupera le informazioni del file PDF e il suo contenuto in formato testuale
	 *
	 * @return void
	 */
	protected function parse()
	{
		// creazione del Bean
		$this->indexValues = new LuceneDataIndexBean();
		
		/**
		 * Converte il contenuto di un file PDF in una stringa
		 */
		$this->getTxtFromBinaries();

		return true;
	}
	
	/**
	 * Recupera i meta tag ed il contenuto di un file PDF utilizzando le XPFD
	 * 
	 * Ritorna null se si e' verificato un errore nella lettura del file da parte
	 * della XPDF o true se non vi sono stati errori.
	 *  
	 * @return mixed
	 */
	private function getTxtFromBinaries() {
		
		// verifica che la XPDF sia eseguibile per il parser dei file PDF
		// e verifica che la funzione popen sia installata
		$serverCheck = LuceneServerCheck::getInstance();
		$serverCheck->serverCheck();
		$reportServerCheck = $serverCheck->getReportCheck();
		// check XPDF 
		$reportCheckXPDF = $reportServerCheck['XPDF']['PDFTOTEXT'];		 
		if (!$reportCheckXPDF->getCheck()) {
			throw new IFileAdapterException("XPDF not executable");
		}		
		
		// check popen 
		$reportCheckPopen = $reportServerCheck['Function']['popen'];
		if (!$reportCheckPopen->getCheck()) {
			throw new IFileAdapterException("Popen function not exists");
		}
		// definizione dei path
		$pathBinaryFile = $pathInfoBinaryFile = dirname(__FILE__)."../Helpers/binaries/";
		$configXpdf = $configInfoXpdf = dirname(__FILE__)."../Helpers/binaries/xpdfrc/xpdfrc";
		
		$original_name 	= $this->getFilename();
		$so = $this->_stremingOutput();
		$executableSO 	= $so['so'];
		$executableInfo = (isset($so['info'])) ? $so['info'] : false ;
		$outputStreming = $so['output'];
		
		
		// XPDF configurazione
		// opw
		$opwConfig = IFileXpdfConfig::decodeOpw($this->ifileConfig->getXpdf('opw'));
		$opw = (!empty($opwConfig)) ? "-opw \"$opwConfig\"" : "";
		// custom XPDF
		$pdftotextConfig = $this->ifileConfig->getXpdf('pdftotext');
		$pdfinfoConfig 	 = $this->ifileConfig->getXpdf('pdfinfo');
		$custom_pdftotext = false;
		$custom_pdfinfo   = false;
		
		// pdftotext executable
		if (!empty($pdftotextConfig['executable'])) {
			$custom_pdftotext = true;
			if (file_exists($pdftotextConfig['executable'])) {
				$pathBinaryFile = $pdftotextConfig['executable'];
			}	
		}
		// pdftotext xpdfrc
		if (!empty($pdftotextConfig['xpdfrc'])) {
			if (file_exists($pdftotextConfig['xpdfrc'])) {
				$configXpdf = $pdftotextConfig['xpdfrc'];
			}
		}
		
		// pdfinfo executable
		if (!empty($pdfinfoConfig['executable'])) {
			$custom_pdfinfo = true;
			if (file_exists($pdfinfoConfig['executable'])) {
				$pathInfoBinaryFile = $pdfinfoConfig['executable'];
			}	
		}
		// pdfinfo xpdfrc
		if (!empty($pdfinfoConfig['xpdfrc'])) {
			if (file_exists($pdfinfoConfig['xpdfrc'])) {
				$configInfoXpdf = $pdfinfoConfig['xpdfrc'];
			}
		}
		
		// verifica se gli eseguibili hanno i permessi
		if ($executableInfo || $custom_pdfinfo) {
			// check XPDFINFO 
			$reportCheckXPDF = $reportServerCheck['XPDF']['PDFINFO'];		 
			if (!$reportCheckXPDF->getCheck()) {
				throw new IFileAdapterException("XPDF INFO not executable");
			}	
		}
		// IMPORTANTE:::::
		// vengono inibiti tutti i tipi di errori pertanto se si 
		// verificano errori da shell il contenuto rimane vuoto. 
		// Questo impedisce di parserizzare contenuti non corretti.
		$handle = null;
		// gestione della pdfinfo		
		if ($executableInfo && !$custom_pdfinfo) {
			$handle = popen($pathInfoBinaryFile . "{$executableInfo} {$opw} -cfg {$configInfoXpdf} \"{$this->getFilename()}\" ", 'r');
		} elseif ($custom_pdfinfo) {
			$handle = popen($pathInfoBinaryFile .  " {$opw} -cfg {$configInfoXpdf} \"{$this->getFilename()}\" ", 'r');
		} elseif ($custom_pdftotext) {
			$handle = popen($pathBinaryFile .  " {$opw} -cfg {$configXpdf} -q -htmlmeta \"{$this->getFilename()}\" {$outputStreming}", 'r');
		} else {
			$handle = popen($pathBinaryFile .  "{$executableSO} {$opw} -cfg {$configXpdf} -q -htmlmeta \"{$this->getFilename()}\" {$outputStreming}", 'r');
		}
				
		$contents = '';
		if($handle){
			while (!feof($handle)) {
				set_time_limit(0);
				$contents .= fread($handle, 8192);
				// gestione per eventuali bafferizzazioni dell'output
				// da altri applicazioni (vedi Joomla!)
				$ob = ob_list_handlers();
				if (!empty($ob)) ob_flush();				
		  	}
		}
		
		// se si utilizza la pdfinfo allora il processo e' diverso
		// la pdfinfo ottimizza, in termini di tempo e risorse, il 
		// recupero delle informazioni del PDF 
		if ($executableInfo || $custom_pdfinfo) {
			if (!empty($contents)) { 
				$information = preg_split ('/$\R?^/m', $contents);
				foreach($information as $info) {
					list($meta, $value) = explode(":", $info);
					
					$value = trim($value);
					
					switch (strtolower(trim($meta))) {
						case 'title':
							$this->indexValues->setTitle($value);
							break;
						case 'subject':
							$this->indexValues->setSubject($value);
							break;
						case 'author':
							$this->indexValues->setCreator($value);
							break;
						case 'keywords':
							$this->indexValues->setKeywords($value);
							break;
						case 'creationdate':							
							$reg = '/'.$meta.':/';
							$infoDate = preg_split ($reg, $info);
							if (isset($infoDate[1])) {
								$value = trim($infoDate[1]);							
							}															
							$this->indexValues->setCreated($value);
							break;
						case 'moddate':
							$reg = '/'.$meta.':/';
							$infoDate = preg_split ($reg, $info);
							if (isset($infoDate[1])) {
								$value = trim($infoDate[1]);							
							}								
							$this->indexValues->setModified($value);
							break;
						case 'pages':
							$this->indexValues->setPages($value);						
							break;
					}
					
				}	
			}			
		} else {
			// ritorna null se il contenuto e' vuoto
			// il content potrebbe essere vuoto dovuto ad errori 
			// di permessi di esecuzione della pdftotext 
			if (empty($contents)) return null;
					
			// permette di disabilitare la visualizzazione
			// degli errori creati dalla LIBXML - PHP 5.1.0
			libxml_use_internal_errors(true);
			// pulizia del buffer degli errori
			libxml_clear_errors();
			// istanzio un oggetto DOM
			$dom = new \DOMDocument();
			//carico il file HTML
			if (!$dom->loadHTML($contents)) {
				$errors = libxml_get_errors();
				throw new IFileAdapterException('XPDF not return HTML meta-data');
			}
			
			// creo un oggetto Xpath
			$xpath = new \DOMXPath($dom);
			// recupero i nodi dei meta dati
			$nodeMeta = $xpath->query("//html/head/meta");
			// recupero le informazioni del file PDF	
			foreach($nodeMeta as $meta) {
				switch ($meta->getAttribute('name')) {
					case 'Title':
						$this->indexValues->setTitle($meta->getAttribute('content'));
						break;
					case 'Subject':
						$this->indexValues->setSubject($meta->getAttribute('content'));
						break;
					case 'Author':
						$this->indexValues->setCreator($meta->getAttribute('content'));
						break;
					case 'Keywords':
						$this->indexValues->setKeywords($meta->getAttribute('content'));
						break;
					case 'CreationDate':
						$this->indexValues->setCreated($meta->getAttribute('content'));
						break;
					case 'Date':
						$this->indexValues->setModified($meta->getAttribute('content'));						
						break;
				}
			}
			unset($dom);
			unset($xpath);
		}
		
		// unsetto gli oggetti per recuperare risorse
		unset($handle);		
		
		$handle2 = null;
		
		if ($custom_pdftotext) {
			$handle2 = popen($pathBinaryFile .  " {$opw} -cfg {$configXpdf} -q \"{$this->getFilename()}\" {$outputStreming}", 'r');
		} else {
			$handle2 = popen($pathBinaryFile .  "{$executableSO} {$opw} -cfg {$configXpdf} -q \"{$this->getFilename()}\" {$outputStreming}", 'r');
		}
		
		$contents = '';
		if($handle2){
			while (!feof($handle2)) {
				set_time_limit(0);
				$contents .= fread($handle2, 8192);						
		  	}
		}
		
		$this->indexValues->setBody($contents);
		
		return true;
	}
		
	/**
	 * Return Output and SO (pdfinfo, pdftotext) 
	 * @return string
	 */
	private function _stremingOutput() {
		
		$so = array();
		$server = $this->ifileConfig->getConfig("server");
		$serverbit = $server['bit'];
		 
		if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			// executable
			$so['so'] = "windows/pdftotext.exe";
			$so['info'] = "windows/pdfinfo.exe";
			// if server is at 64 bit
			if ($serverbit == '64') {
				$so['so'] = "windows/bin64/pdftotext.exe";
				$so['info'] = "windows/bin64/pdfinfo.exe";
			}
			// output			
			$so['output'] = " - 2>nul";			
		}else if(strtoupper(substr(PHP_OS, 0, 3)) === 'FRE'){
			// executable
			$so['so'] = "freebsd/pdftotext";
			// output
			$so['output'] = " - 2>/dev/null";
		}else if(strtoupper(substr(PHP_OS, 0, 3)) === 'DAR'){
			// executable
			$so['so'] = "osx/pdftotext";
			// output
			$so['output'] = " - 2>/dev/null";
		}else if(strtoupper(substr(PHP_OS, 0, 3)) === 'LIN'){
			// executable
			$so['so'] = "linux/pdftotext";
			$so['info'] = "linux/pdfinfo";
			// if server is at 64 bit
			if ($serverbit == '64') {
				$so['so'] = "linux/bin64/pdftotext";
				$so['info'] = "linux/bin64/pdfinfo";
			}
			// output
			$so['output'] = " - 2>/dev/null";
		}else{
			// executable
			$so['so'] = "custom/pdftotext";
			// output
			$so['output'] = " - 2>/dev/null";
		}
		
		return $so;
	}
}