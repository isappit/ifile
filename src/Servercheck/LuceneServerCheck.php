<?php
namespace Isappit\Ifile\Servercheck;

use Isappit\Ifile\Config\IFileConfig;
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage servercheck
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    2.0
 */

/**
 * Verifica se ci sono tutti i requisiti per utilizzare la libreria
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage servercheck
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class LuceneServerCheck {
	/**
	 * Istanza di LuceneServerCheck
	 * 
	 * @var LuceneServerCheck
	 */
	private static $_instance;
	/**
	 * Controllo esecuzione della check
	 * 
	 * @var boolean
	 */
	private static $_check = false;
	/**
	 * Configurazione di IFile
	 * 
	 * @var boolean
	 */
	private $_ifileConfig = null;
    /**
     * Percorso dei file Binari
     */
    private $_binariesPath = null;

	/**
	 * Libreria di Zend Lucene 
	 */	
	const ZEND_SEARCH_LUCENE = 'ZendSearch\\Lucene\\Lucene';
	/**
	 * Libreria di Zend PDF
	 */
	const ZEND_PDF = 'Zend/Pdf.php';
	/**
	 * Versione Zend 
	 */
	const ZENDVERSION = '1.10.1';
	/**
	 * Versione Zend Last
	 */
	const TOZENDVERSION = '1.12.1';
	/**
	 * Prefisso Adapter Document
	 */
	const ADAPTER_DOCUMENT = 'IFileDocument_';
	/**
	 * Percorso degli Adapters
	 */
	const ADAPTERS_PATH = 'Adapter/Document/';
	/**
	 * XPDF installato nel sistema
	 */
	const BINARIES_DEFAULT = '/usr/bin/pdftotext';	
	/**
	 * ANTIWORD per Windows 
	 */	
	const BINARIES_WIN_DOC = 'windows/antiword.exe';
	
	/**
	 * ANTIWORD per Linux
	 */	
	const BINARIES_LIN_DOC = 'linux/antiword';
	
	/**
	 * ANTIWORD per OS
	 */	
	const BINARIES_OSX_DOC = 'osx/antiword';
	
	/**
	 * XPDF per Windows 
	 */	
	const BINARIES_WIN = 'windows/pdftotext.exe';
	/**
	 * XPDF per Windows 64bit
	 */	
	const BINARIES_WIN_64 = 'windows/bin64/pdftotext.exe';
	/**
	 * XPDF INFO per Windows 
	 */	
	const BINARIES_INFO_WIN = 'windows/pdfinfo.exe';
	/**
	 * XPDF INFO per Windows 
	 */	
	const BINARIES_INFO_WIN_64 = 'windows/bin64/pdfinfo.exe';
		
	/**
	 * XPDF per OSX 
	 */	
	const BINARIES_OSX = 'osx/pdftotext';
	/**
	 * XPDF per FREEBSD 
	 */	
	const BINARIES_FRE = 'freebsd/pdftotext';	
	
	/**
	 * XPDF per Linux 
	 */
	const BINARIES_LIN = 'linux/pdftotext';	
	/**
	 * XPDF per Linux 64bit
	 */
	const BINARIES_LIN_64 = 'linux/bin64/pdftotext';
	
	/**
	 * XPDF INFO per Linux 
	 */
	const BINARIES_INFO_LIN = 'linux/pdfinfo';	
	/**
	 * XPDF INFO per Linux 64bit
	 */
	const BINARIES_INFO_LIN_64 = 'linux/bin64/pdfinfo';
	
	/**
	 * XPDF per universal 
	 */
	const BINARIES_UNV = 'custom/pdftotext';
		
	/**
	 * Versione minima di PHP 
	 */	
	const PHPVERSION = '5.3.0';
	
	/**
	 * Array di oggetti ReportCheck
	 * 
	 * @var array
	 */
	private $registry = array();	
	/**
	 * Array dei path include configurati nel php.ini
	 * 
	 * @var array
	 */
	private $include_path = array();
	/**
	 * Stringa dei permessi
	 * 
	 * @var string
	 */
	private $configmod = '';
	/**
	 * Lista delle estensioni consentite
	 *
	 * @var array
	 */
	private $extensionsAllows = array();
	
	/**
	 * Costruttore privato per la gestione del Singleton
	 */
	private function __construct() {
		// recupero la configurazine di IFile
		$this->_ifileConfig  = IFileConfig::getInstance();
        $this->_binariesPath = $this->_ifileConfig->getBinariesPath();
	}
	
	/**
	 * Ritorna una istanza dell'oggetto LuceneServerCheck
	 * 
	 * @return LuceneServerCheck  
	 */
	static function getInstance() {
		if (self::$_instance == null) { 
			self::$_instance = new LuceneServerCheck();
		}
		
		return self::$_instance;		
	}
	
	/**
	 * Verifica tutti i requisiti richiesti
	 * 
	 * @return void 
	 */
	public function serverCheck() {
		
		if (!self::$_check) {
			$this->checkZendFramework();
			$this->checkPCRE();
			$this->checkServer();
			$this->checkPermissionXPDF();
			$this->checkPermissionINFOXPDF();
			$this->checkPermissionANTIWORD();
			$this->checkPHPVersion();
			$this->checkPHPLib();
			$this->checkPHPFunction();
			$this->checkExtensionsAllows();
			
			self::$_check = true; 
		}
	}
	
	/**
	 * Ritorna il registro degli oggetti ReportCheck
	 * 
	 * @return array
	 */
	public function getReportCheck() {
		return $this->registry;
	}
	
	public function printReportCheckCLI() {
		require "theme/cli.phtml";
	}
	
	/**
	 * Presenta a video i risultati nel nuovo formato HTML
	 *
	 * @return void
	 */
	public function printReportCheckWeb() {
		require "theme/web.phtml";
	}
	
	/**
	 * Presenta a video i risultati in formato HTML
	 *  
	 * @return void
	 */
	public function printReportCheck() {
		
		echo "<html>\n";
		echo "<head>\n";
            echo "<title>IFile Server Check</title>";
			echo "<style>\n";
				echo "body {text-align: center;}";
				echo "table {margin:auto;border-top:1px solid #000;border-left:1px solid #000;}\n";
				echo "td, th {padding:4px;border-bottom:1px solid #000; border-right:1px solid #000}\n";
			echo "</style>\n";
		echo "</head>\n";
		echo "<body>\n";
		echo "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
		echo "<thead>";
			echo "<tr style =\"background-color:#ccc;text-align:center;\">";
				echo "<th>Component</th>\n";
				echo "<th>Check</th>\n";
				echo "<th>Requirements</th>\n";
				echo "<th>Info</th>\n";
				echo "<th>Use</th>\n";
				echo "<th>WebSite</th>\n";
			echo "</tr>\n"; 
		
		echo "</thead>";
		
		echo "<tbody>";
		foreach($this->registry as $caption => $check) {
			
			echo "<tr><td style =\"text-align:center;\" colspan=\"6\"><strong>".$caption."</strong></td></tr>\n";
			foreach($check as $obj){
				$background = ($obj->getCheck()) ? '#8f8' : '#f88';
				echo "<tr style=\"background-color:{$background}\">";
				echo "<td><strong>".$obj->getLabel()."</strong></td>\n";
				echo "<td>".$obj->getMessage()."</td>\n";
				echo "<td>".$obj->getRequire()."</td>\n";
				echo "<td width=\"25%\">".$obj->getInfo()."</td>\n";
				echo "<td width=\"20%\">".$obj->getInfoUse()."</td>\n";
				echo "<td><a href=\"".$obj->getSite()."\" target=\"_blanck\">".$obj->getSite()."</a></td>\n";
				echo "</tr>\n"; 
			}			
		}
		echo "</tbody>";
		echo "</table>";
		echo "</body>\n";
		echo "</html>\n";
	}
	
	/**
	 * Ritorna l'array delle estensioni solo se richiamata im metodo "serverCheck"
	 * 
	 * @return array
	 */
	public function getExtensionsAllowed() {
		if (empty($this->extensionsAllows)) {
			$this->readExtensionsAllows();
		}
		
		return $this->extensionsAllows;
	}
	
	/**
	 * 
	 * Verifica se il server e' a 32 o 64 BIT
	 * 
	 * @return 
	 */
	private function getServerBit() {
		$int = "9223372036854775807";
		$int = intval($int);
		if ($int == 9223372036854775807) {
		  /* 64bit */
		  return "64bit";
		} elseif ($int == 2147483647) {
		  /* 32bit */
		  return "32bit";
		} else {
		  /* error */
		  return "Not defined";
		} 
	}
	
	/**
	 * Legge la directory degli Adapter e setta le estensioni consentite.
	 * 
	 * @return void 
	 */
	private function readExtensionsAllows() {
		// @TODO da rivedere il recupero del Path
		$dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.LuceneServerCheck::ADAPTERS_PATH;
		
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if (is_file($dir.$file) && strpos($file, LuceneServerCheck::ADAPTER_DOCUMENT) !== false) {
						// delete extension				
						$filename = preg_replace('/\.[^.]*$/', '', $file);
						// get extension file indexing
						$extension = preg_replace('/'.LuceneServerCheck::ADAPTER_DOCUMENT.'/', '', $filename);
						$this->extensionsAllows[strtolower($extension)] = strtolower($extension);
					}
				}
				closedir($dh);
			}
		}
	}
	
	/**
	 * Verifica le estensioni dei file consentiti
	 * 
	 * @return void
	 */
	private function checkExtensionsAllows() {
		
		if (empty($this->extensionsAllows)) {
			$this->readExtensionsAllows();
		}
		
		$reportCheck = new ReportCheck(true, 'Extensions', 'OK', "Not defined", implode(", ", $this->extensionsAllows), 'http://ifile.isapp.it', 'All extensions files allowed for Automatic Indexing');
		$this->pushReportCheck('Extensions allowed', 'Extensions', $reportCheck);
		
	}
	
	/**
	 * Verifica che la versione di PHP sia uguale o superiore alla 5.1.0
	 * 
	 * @return void
	 */
	private function checkServer() {
		// server 32/64bit
		$server = $this->getServerBit();
		$use = 'Server type';
		// inizializza l'oggetto per il report
		$reportCheck = new ReportCheck(true, 'Server', $server, 'Not defined' , 'Note: If the OS is 64bit but PHP running a 32 bit, the check will return (32 bit)', 'http://www.php.net/manual/en/install.php', $use);
				
		$this->pushReportCheck('SERVER', 'Server', $reportCheck);
	}
	
	/**
	 * Verifica l'esistenza delle librerie di lucene della ZEND FRAMEWORK
	 * 
	 * @return void 
	 */
	private function checkZendFramework() {
		// inizializza l'oggetto per il report
		$reportCheckLucene = new ReportCheck(false, 'Zend Search Lucene', 'Not present', 'Version Not Defined', 'Install Zend Search Lucene', 'https://github.com/zendframework/ZendSearch', 'Used by Lucene and MySqli Interface');		
		
		if (class_exists(self::ZEND_SEARCH_LUCENE)) {
			$reportCheckLucene->setCheck(true);
			$reportCheckLucene->setMessage('Exists');
			$reportCheckLucene->setInfo('Zend Search Lucene is installed in vendor');
		}
		
		$this->pushReportCheck('Zend Framework', 'Search Lucene', $reportCheckLucene);
	} 
	
	/**
	 * Verifica se si hanno i permessi per utilizzare le XPDF
	 *  
	 * @return void 
	 */
	private function checkPermissionXPDF() {
		// inizializza l'oggetto per il report
		$reportCheck = new ReportCheck(false, 'XPDF Binaries File', 'Unexecutable', 'CHMOD 0755' , 'For more information visit web site', 'http://www.foolabs.com', 'Used only for PDF file parser');
		// configurazione di IFile
		//$ifileConfig = IFileConfig::getInstance();
		// server
		$server = $this->_ifileConfig->getConfig("server");
		$serverbit = $server['bit'];
		// pdftotext personalizzata
		$pdftotextConfig = $this->_ifileConfig->getXpdf('pdftotext');

		$customXPDF = false;
		// controlla se esiste una configurazione di una XPDF personalizzata
		if (!empty($pdftotextConfig['executable'])) {
			$customXPDF = true;
			$path  = $pdftotextConfig['executable'];
			$perms = $this->checkPermits($path, "0755", false, $customXPDF);
		}else if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$path  = LuceneServerCheck::BINARIES_WIN;
			if ($serverbit == '64') {
				$path  = LuceneServerCheck::BINARIES_WIN_64;
			}
			$perms = $this->checkPermits($path, "0755");
		}else if(strtoupper(substr(PHP_OS, 0, 3)) === 'FRE'){
			$perms = $this->checkPermits(LuceneServerCheck::BINARIES_FRE, "0755");	
			$path  = LuceneServerCheck::BINARIES_FRE;		
		}else if(strtoupper(substr(PHP_OS, 0, 3)) === 'DAR'){			
			$perms = $this->checkPermits(LuceneServerCheck::BINARIES_OSX, "755", true);
			$path  = LuceneServerCheck::BINARIES_OSX;	
		}else if(strtoupper(substr(PHP_OS, 0, 3)) === 'LIN'){
			$path  = LuceneServerCheck::BINARIES_LIN;
			if ($serverbit == '64') {
				$path  = LuceneServerCheck::BINARIES_LIN_64;
			}
			$perms = $this->checkPermits($path, "0755");
				
		}else{
			$perms = $this->checkPermits(LuceneServerCheck::BINARIES_UNV, "0755");
			$path  = LuceneServerCheck::BINARIES_UNV;
		}
		
		$infoPath = ($customXPDF) ? $path : $this->_binariesPath.$path;
        $permissionInfo = ($this->configmod) ? "Permission [ ".$this->configmod." ]" : "Permission not defined";
		
		if (!$perms) {									
			$reportCheck->setMessage('Unexecutable');	
			$reportCheck->setInfo($permissionInfo. ' ('.$infoPath.') - Please verify if binaries XPDF (OS: '.strtoupper(substr(PHP_OS, 0, 3)).') exists and set permission to 0755');
		} else {
			$reportCheck->setCheck(true);
			$reportCheck->setMessage('Executable');
			$reportCheck->setInfo($permissionInfo.' ('.$infoPath.')');
		}
		
		$this->pushReportCheck('XPDF', 'PDFTOTEXT', $reportCheck);
	}
	
	/**
	 * Verifica se si hanno i permessi per utilizzare le XPDF
	 *  
	 * @return void 
	 */
	private function checkPermissionINFOXPDF() {
		// inizializza l'oggetto per il report
		$reportCheck = new ReportCheck(false, 'XPDF INFO Binaries File', 'Unexecutable', 'CHMOD 0755' , 'For more information visit web site', 'http://www.foolabs.com', 'Used only for PDF file parser');
		$supported = true;
		// configurazione di IFile
		//$ifileConfig = IFileConfig::getInstance();
		// server
		$server = $this->_ifileConfig->getConfig("server");
		$serverbit = $server['bit'];
		// pdfinfo personalizzata
		$pdfinfoConfig = $this->_ifileConfig->getXpdf('pdfinfo');
		
		$customXPDF = false;
		// controlla se esiste una configurazione di una XPDF INFO personalizzata
		if (!empty($pdfinfoConfig['executable'])) {
			$customXPDF = true;
			$path  = $pdfinfoConfig['executable'];
			$perms = $this->checkPermits($path, "0755", false, $customXPDF);	
		} else if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$path  = LuceneServerCheck::BINARIES_INFO_WIN;
			if ($serverbit == '64') {
				$path  = LuceneServerCheck::BINARIES_INFO_WIN_64;
			}
			$perms = $this->checkPermits($path, "0755");
			
		}else if(strtoupper(substr(PHP_OS, 0, 3)) === 'LIN'){
			$path  = LuceneServerCheck::BINARIES_INFO_LIN;
			if ($serverbit == '64') {
				$path  = LuceneServerCheck::BINARIES_INFO_LIN_64;
			}
			$perms = $this->checkPermits($path, "0755");
				
		}else{
			$supported = false;			
		}
		
		if (!$supported) {
			$reportCheck->setMessage('Unsupported');	
			$reportCheck->setRequire('Not defined');	
			$reportCheck->setInfo('XPDF INFO Binaries File isn\'t supported - for '.strtoupper(substr(PHP_OS, 0, 3)));
		} else {
			$infoPath = ($customXPDF) ? $path : $this->_binariesPath.$path;
            $permissionInfo = ($this->configmod) ? "Permission [ ".$this->configmod." ]" : "Permission not defined";

			if (!$perms) {									
			$reportCheck->setMessage('Unexecutable');
                $reportCheck->setInfo($permissionInfo. ' ('.$infoPath.') - Please verify if binaries XPDF (OS: '.strtoupper(substr(PHP_OS, 0, 3)).') exists and set permission to 0755');
			} else {
				$reportCheck->setCheck(true);
				$reportCheck->setMessage('Executable');
                $reportCheck->setInfo($permissionInfo.' ('.$infoPath.')');
			}
		}
		
		
		$this->pushReportCheck('XPDF', 'PDFINFO', $reportCheck);
	}
	
	/**
	 * Verifica se si hanno i permessi per utilizzare le ANTIWORD
	 *  
	 * @return void 
	 */
	private function checkPermissionANTIWORD() {
		// inizializza l'oggetto per il report
		$reportCheck = new ReportCheck(false, 'ANTIWORD Binaries File', 'Unexecutable', 'CHMOD 0755' , 'For more information visit web site', 'http://www.winfield.demon.nl/', 'Used only for DOC file parser');
		// verifica se antiword e' supportato da IFile
		$supported  = true;
        $customExec = false;
        // Executable
        $pathExecutable = $this->_ifileConfig->getDocToTxt('executable');

        if (!empty($pathExecutable)) {
            $customExec = true;
            $path  = $pathExecutable;
            $perms = $this->checkPermits($path, "0755", false, $customExec);
        } else if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$perms = $this->checkPermits(LuceneServerCheck::BINARIES_WIN_DOC, "0755");
			$path  = LuceneServerCheck::BINARIES_WIN_DOC;
		} else if(strtoupper(substr(PHP_OS, 0, 3)) === 'DAR'){			
			$perms = $this->checkPermits(LuceneServerCheck::BINARIES_OSX_DOC, "755", true);
			$path  = LuceneServerCheck::BINARIES_OSX_DOC;	
		}else if(strtoupper(substr(PHP_OS, 0, 3)) === 'LIN'){
			$perms = $this->checkPermits(LuceneServerCheck::BINARIES_LIN_DOC, "0755");
			$path  = LuceneServerCheck::BINARIES_LIN_DOC;	
		} else {
			$supported = false;
		}
		
		if (!$supported) {
				$reportCheck->setMessage('Unsupported');	
				$reportCheck->setInfo('ANTIWORD Binaries File isn\'t supported - for '.strtoupper(substr(PHP_OS, 0, 3)). ' system operation in "ifile-binaries" folder. Check configuration for use COM or PHP parser or configure a custom executable');
		} else {

            $execPath = ($customExec) ? $path : $this->_binariesPath.$path;
            $permissionInfo = ($this->configmod) ? "Permission [ ".$this->configmod." ]" : "Permission not defined";
			if (!$perms) {									
				$reportCheck->setMessage('Unexecutable');
                $reportCheck->setInfo($permissionInfo. ' ('.$execPath.') - Please verify if binaries ANTIWORD (OS: '.strtoupper(substr(PHP_OS, 0, 3)).') exists and set permission to 0755');
			} else {
				$reportCheck->setCheck(true);
				$reportCheck->setMessage('Executable');
                $reportCheck->setInfo($permissionInfo.' ('.$execPath.')');
			}	
		}
		
		$this->pushReportCheck('ANTIWORD', 'ANTIWORD', $reportCheck);
	}
	
	/**
	 * Verifica che la versione di PHP sia uguale o superiore alla 5.1.0
	 * 
	 * @return void
	 */
	private function checkPHPVersion() {
		// versione di PHP
		$phpversion = phpversion();		
		// inizializza l'oggetto per il report
		$reportCheck = new ReportCheck(false, 'PHP Version', 'KO', 'Version '.LuceneServerCheck::PHPVERSION.' or later', 'Version installed is '.$phpversion, 'http://www.php.net');
		// verifica che la versione di PHP
		if (version_compare($phpversion, LuceneServerCheck::PHPVERSION, '>=')) {
			$reportCheck->setCheck(true);
			$reportCheck->setMessage("OK");	
		}
		
		$this->pushReportCheck('PHP', 'PHPVersion', $reportCheck);
	}
	
	/**
	 * Verifica se ci sono le librerie necessarie
	 * 
	 * @return void 
	 */
	private function checkPHPLib() {
		// recupera la lista delle librerie installate 
		$extension = get_loaded_extensions();
		// librerie da verificare
		$checkExt = $this->getListExtension();
		// effettua un lower dei nomi delle librerie
		//array_walk($extension, create_function('&$v,$k','$v = strtolower($v);'));
		// ritorna un array con le librerie non installate
		$diff = array_diff($checkExt['ext'], $extension);
		$keyDiff = array_keys($diff);
		
		foreach ($checkExt['ext'] as $k => $ext) {
			// versione minima richiesta
			$version = ($checkExt['version'][$k]) ? 'Version '.$checkExt['version'][$k].' or later' : 'Not defined' ; 
			// inizializza l'oggetto per il report
			$reportCheck = new ReportCheck(false, $ext, 'KO', $version, 'Install library in PHP', $checkExt['link'][$k], $checkExt['use'][$k]);
			
			// controllo se la libreria e' installata  			
			if (!in_array($k, $keyDiff)) {
				$version = $checkExt['version'][$k];
				$extVersion = phpversion($ext); 
				// verifico la versione solo se esiste nell'estensione
				if ($version && !empty($extVersion) && (strnatcmp($extVersion, $version) < 0)) {
					$reportCheck->setInfo('Install new version in PHP');
				} else {
					$reportCheck->setCheck(true);
					$reportCheck->setMessage('OK');
					$version = (!empty($extVersion)) ? 'Version installed is '.$extVersion : 'Not check version';
					$reportCheck->setInfo($version);	
				} 
			}
			
			$this->pushReportCheck('Extension', $k, $reportCheck);
		}
	}
	
	/**
	 * Verifica se esistone la PCRE 
	 * @return void
	 */
	private function checkPCRE() {
		
		// inizializza l'oggetto per il report
			$reportCheck = new ReportCheck(false, 'PCRE', 'KO', 'Not defined', 'PCRE unicode support is not enabled in PHP', 'http://www.php.net/manual/en/book.pcre.php', 'Used by Zend Search Lucene Framework');
		if (@preg_match('/\pL/u', 'a') == 1) {
			$reportCheck->setCheck(true);
			$reportCheck->setMessage('OK');
			$reportCheck->setInfo('PCRE unicode support is enabled in PHP');
		}
		
		$this->pushReportCheck('Encoding', 'PCRE', $reportCheck);
	}
	
	/**
	 * Verifica se esistono le funzioni 
	 * @return void
	 */
	private function checkPHPFunction() {
		$funct = $this->getListFunction();
		
		foreach($funct['fun'] as $k => $fun) {
			// inizializza l'oggetto per il report
			$reportCheck = new ReportCheck(false, $fun, 'KO', 'Not defined', 'This function not exist in PHP', $funct['link'][$k], $funct['use'][$k]);
			if (function_exists($fun)) {
				$reportCheck->setCheck(true);
				$reportCheck->setMessage('OK');
				$reportCheck->setInfo('Function exists');
			}
			
			$this->pushReportCheck('Function', $k, $reportCheck);
		}
	}
	
	/**
	 * Inserisce un nuovo oggetto nel registro
	 * 
	 * @param string $cption
	 * @param object $reportCheck	 
	 * @return void
	 */
	private function pushReportCheck($caption, $type, $reportCheck) {
		if (!isset($this->registry[$caption])) $this->registry[$caption] = array();
		if (!isset($this->registry[$caption][$type])) $this->registry[$caption][$type] = array();
		$this->registry[$caption][$type] = $reportCheck;
	}
	
	/**
	 * Ritorna la lista delle funzioni di PHP
	 * 
	 * @return array
	 */
	private function getListFunction() {
		$fun = array();
		// funzioni
		$fun['fun']['popen'] = 'popen';
		$fun['fun']['strip_tags'] = 'strip_tags';
		
		// link
		$fun['link']['popen'] = 'http://www.php.net/manual/en/function.popen.php';
		$fun['link']['strip_tags'] = 'http://php.net/manual/en/function.strip-tags.php';
		
		// use
		$fun['use']['popen'] = 'Used only for PDF file parser';
		$fun['use']['strip_tags'] = 'Used only for XML file parser';
		
		return $fun;
	}
	
	/**
	 * Ritorna la lista delle estensioni necessarie
	 * 
	 * @return array
	 */
	private function getListExtension() {
		$ext = array();
		// librerie 
		$ext['ext']['libxml'] 	= 'libxml'; 	// Parserizzazione file OpenXml
		$ext['ext']['dom'] 		= 'dom';		// Parserizzazione file OpenXml	   
		$ext['ext']['SimpleXML']= 'SimpleXML';	// Parserizzazione file OpenXml
		$ext['ext']['mbstring'] = 'mbstring';	// Gestione multilingua
		$ext['ext']['zip'] 		= 'zip';		// Parserizzazione file OpenXml
		$ext['ext']['zlib'] 	= 'zlib';		// Parserizzazione file OpenXml
		$ext['ext']['iconv'] 	= 'iconv';		// Gestione multilingua
		// IFile dalla versione 1.3 usa la libreria getID3
		//$ext['ext']['id3'] 		= 'id3';		// Gestione TAG ID3
		$ext['ext']['mysqli'] 	= 'mysqli';		// Gestione Interfaccia MySqli
		$ext['ext']['exif'] 	= 'exif';		// Gestione TAG Exif
		$ext['ext']['com'] 		= 'com_dotnet';	// Gestione file DOC
		$ext['ext']['stem'] 	= 'stem';		// Gestione Stemming
		
		// versione minima della libreria
		$ext['version']['libxml'] 	= '2.6.0';
		$ext['version']['dom'] 		= false;
		$ext['version']['SimpleXML']= false;
		$ext['version']['mbstring'] = false;
		$ext['version']['zip'] 		= false;
		$ext['version']['zlib'] 	= '1.0.9';
		$ext['version']['iconv'] 	= false;
		// IFile dalla versione 1.3 usa la libreria getID3
		//$ext['version']['id3'] 		= '0.1';
		$ext['version']['mysqli'] 	= false;  
		$ext['version']['exif'] 	= '1.4';  
		$ext['version']['com'] 		= '0.1';  
		$ext['version']['stem'] 	= '1.5.0';
		
		// use 
		$ext['use']['libxml'] 	= 'Used for Office Open Xml (OOXML) and OpenDocument (ODF) file parser';
		$ext['use']['dom'] 		= 'Used for Office Open Xml (OOXML) and OpenDocument (ODF) file parser';
		$ext['use']['SimpleXML']= 'Used for Office Open Xml (OOXML) and OpenDocument (ODF) file parser';
		$ext['use']['mbstring'] = 'Used by Zend Search Lucene';
		$ext['use']['zip'] 		= 'Used for Office Open Xml (OOXML) and OpenDocument (ODF) file parser';
		$ext['use']['zlib'] 	= 'Used for Office Open Xml (OOXML) and OpenDocument (ODF) file parser';
		$ext['use']['iconv'] 	= 'Used by Zend Search Lucene'; 
		// IFile dalla versione 1.3 usa la libreria getID3
		//$ext['use']['id3'] 		= 'Used for MP3 file parser';
		$ext['use']['mysqli'] 	= 'Used only for MySqli Interface'; 
		$ext['use']['exif'] 	= 'Used for JPG file parser'; 
		$ext['use']['com'] 		= 'Used for DOC file parser (Not supported from Linux server)';
		$ext['use']['stem'] 	= 'Used for Stemming languages'; 
		
		// link
		$ext['link']['libxml'] 	 = 'http://www.php.net/manual/en/book.libxml.php';
		$ext['link']['dom'] 	 = 'http://www.php.net/manual/en/book.dom.php';
		$ext['link']['SimpleXML']= 'http://www.php.net/manual/en/book.simplexml.php';
		$ext['link']['mbstring'] = 'http://www.php.net/manual/en/book.mbstring.php';
		$ext['link']['zip'] 	 = 'http://www.php.net/manual/en/class.ziparchive.php';
		$ext['link']['zlib'] 	 = 'http://www.php.net/manual/en/book.zlib.php';
		$ext['link']['iconv'] 	 = 'http://www.php.net/manual/en/book.iconv.php'; 
		// IFile dalla versione 1.3 usa la libreria getID3
		//$ext['link']['id3'] 	 = 'http://www.php.net/manual/en/book.id3.php';
		$ext['link']['mysqli'] 	 = 'http://www.php.net/manual/en/book.mysqli.php';
		$ext['link']['exif'] 	 = 'http://www.php.net/manual/en/book.exif.php';
		$ext['link']['com'] 	 = 'http://www.php.net/manual/en/book.com.php';
		$ext['link']['stem'] 	 = 'http://pecl.php.net/package/stem';
		
		
		return $ext;
	}
	
	/**
	 * Verifica l'esistenza dell'ultimo "directory separetor"
	 * @param object $path
	 * @return void
	 */
	private function lastDS($path) {
		$path = realpath($path);
		
		$lastChar = $path{strlen($path)-1};
		if($lastChar != DIRECTORY_SEPARATOR) {
			$path .= DIRECTORY_SEPARATOR;
		}
		return $path;
	}
		
	/**
	 * Ritorna true se il file esiste
	 *  
	 * @param string $file
	 * @return bool
	 */
	private function checkPearFile($file) {
		// cicla per tutti i path definiti nel php.ini
		foreach ($this->include_path as $val) {
			if (file_exists($this->lastDS($val).$file)) {
				 return $val;
			}
		}
		
		return false;
	}
	
	/**
	 * Controlla i chmod dei file di esecuzione
	 * 
	 * @param string $path
	 * @param string $perm [optional]
	 * @param object $oct [optional]
	 * 
	 * @return boolean
	 */
	function checkPermits($path, $perm = 0755, $oct = false, $custom = false)
	{
		if (!$custom) {
			$path = $this->_binariesPath.$path;
		}

        if (!file_exists($path)) return false;
		
	    clearstatcache();
	    // recupera i permessi in formato ottale
	    $configmod = decoct(fileperms($path) & 0777);
		$trcss = ((int)$perm === (int)$configmod || 777 === (int)$configmod) ? true : false;
	    
		$this->configmod = $configmod;
		return $trcss;		  
	} 
}
?>