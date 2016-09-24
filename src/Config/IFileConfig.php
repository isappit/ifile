<?php
namespace Isappit\Ifile\Config;

use Isappit\Ifile\Config\Helpers\IFileXpdfConfig;
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
 * Class Configuration
 *
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileConfig {
	
	/**
	 * Token class
	 */
	const ZEND_TOKENFILTER = 'ZendSearch\Lucene\Analysis\TokenFilter\TokenFilterInterface';
	/**
	 * Analyzer class
	 */
	const ZEND_ANALYZER = 'ZendSearch\Lucene\Analysis\Analyzer\Common\AbstractCommon';
	/**
	 * IFileConfig instance
	 * 
	 * @var IFileConfig
	 */
	private static $_instance;
	/**
	 * XML file configuration
	 * 
	 * @var string
	 */
	private static $xml;
	/**
	 * XSD file validation
	 * 
	 * @var string
	 */
	private $xsd;
	/**
	 * Array configuration
	 * 
	 * @var array
	 */
	protected $config = array();
	/**
	 * Array original configuration
	 * 
	 * @var array
	 */
	private $originalConfig = null;
	/**
	 * Encoding default
	 * 
	 * @var string 
	 */
	private $encoding = '';
	/**
	 * Analyzer default
	 * 
	 * @var string
	 */
	private $analyzer = 'Utf8\\CaseInsensitive';
	
	/**
	 * Protected construct for the Singleton pattern  
	 *
	 * @return void 
	 */
	protected function __construct() {
		$this->xsd = dirname(__FILE__).DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.'IFileConfig.xsd';
		
		$this->parserConfig();		
	}
		
	/**
	 * Return IFileConfig instance
	 * 
	 * @return IFileConfig  
	 */
	static function getInstance() {
		if (self::$_instance == null) 
			self::$_instance = new IFileConfig();			
			
		return self::$_instance;		
	}
	
	/**
	 * Parserizza il file di configurazione per leggere i valori
	 * 
	 * @TODO
	 * 1. andrebbero gestiti anche altri parametri per la gestione delle
	 * wildcard, maxlength......
	 * 2. Serializzazione dei dati per evitare ogni volta di ricostruirli
	 * 
	 * @throws IFileException
	 * @return void
	 */
	private function parserConfig() {
		// recupera il file xml di configurazione
		$xml = $this->getXmlConfig();
		// permette di disabilitare la visualizzazione
		// degli errori creati dalla LIBXML - PHP 5.1.0
		libxml_use_internal_errors(true);
		// pulizia del buffer degli errori
		libxml_clear_errors();
		// istanzio un oggetto DOM
		$dom = new \DOMDocument();
		//carico il file XML
		if (!$dom->load($xml)) {
			$errors = libxml_get_errors();			
			throw new IFileException('The file IFileConfig.xml may not be formatted correctly');
		}
		// Valido il file XML
		if (@!$dom->schemaValidate($this->xsd)) {
			$errors = libxml_get_errors();
			
			foreach ($errors as $val) {
				if ($val->code == 1549) {
					throw new IFileException('IFileConfig.xsd File not found');
				}
			}
			
			throw new IFileException('File XML is not valid according to the XSD.&#10;Error: '.$errors[0]->message);
		}
		
		// creo un oggetto Xpath
		$xpath = new \DOMXPath($dom);
		// recupero i nodi action
		$nodeIFile = $xpath->query("//ifile");
		
		foreach ($nodeIFile as $ifile) {
			// root-application
			$this->config['root-application'] = ($xpath->query("root-application", $ifile)->item(0)) ? trim($xpath->query("root-application", $ifile)->item(0)->nodeValue) : null;
			// controlla che la root application sia una directory realmente esistente 
			if (!empty($this->config['root-application'])) {$this->checkRootApplication($this->config['root-application']);}

            // binaries
            $binariesPath = dirname(__FILE__)."/../Adapter/Helpers/ifile-binaries";
            $this->config['binaries-path'] = ($xpath->query("binaries", $ifile)->item(0)) ? trim($xpath->query("binaries", $ifile)->item(0)->nodeValue) : $binariesPath;
            // controlla che il path dei file bianri sia una directory realmente esistente
            if (!empty($this->config['binaries-path'])) {
                $this->config['binaries-path'] = $this->checkBinaries($this->config['binaries-path'])."/";
            }

			// table-name
			$this->config['table-name'] = ($xpath->query("table-name", $ifile)->item(0)) ? trim($xpath->query("table-name", $ifile)->item(0)->nodeValue) : null;
			if (!empty($this->config['table-name'])) {
				$collation  = ($xpath->query("table-name", $ifile)->item(0)->getAttributeNode("collation")) ? $xpath->query("table-name", $ifile)->item(0)->getAttributeNode("collation")->value : null;
                $engine     = ($xpath->query("table-name", $ifile)->item(0)->getAttributeNode("engine")) ? $xpath->query("table-name", $ifile)->item(0)->getAttributeNode("engine")->value : null;
				$this->config['table-collation'] = (empty($collation)) ? null : $collation;
                $this->config['table-engine']    = (empty($engine)) ? null : $engine;
			}
			
			// timelimit
			$this->config['timelimit'] = ($xpath->query("timelimit", $ifile)->item(0)) ? $xpath->query("timelimit", $ifile)->item(0)->nodeValue : null;
			// memorylimit
			$this->config['memorylimit'] = ($xpath->query("memorylimit", $ifile)->item(0)) ? $xpath->query("memorylimit", $ifile)->item(0)->nodeValue."M" : null;
			// resultlimit
			$this->config['resultlimit'] = ($xpath->query("resultlimit", $ifile)->item(0)) ? $xpath->query("resultlimit", $ifile)->item(0)->nodeValue : null;
			// default-search-field
			$this->config['default-search-field'] = ($xpath->query("default-search-field", $ifile)->item(0)) ? $xpath->query("default-search-field", $ifile)->item(0)->nodeValue : null;
			// duplicate
			$this->config['duplicate'] = ($xpath->query("duplicate", $ifile)->item(0)) ? $xpath->query("duplicate", $ifile)->item(0)->nodeValue : null;
			// encoding
			$this->config['encoding'] = ($xpath->query("encoding", $ifile)->item(0)) ? trim($xpath->query("encoding", $ifile)->item(0)->nodeValue) : $this->encoding;
			
			// doctotxt
			$doctotxt = ($xpath->query("doctotxt", $ifile)->item(0)) ? $xpath->query("doctotxt", $ifile)->item(0) : null;
			$this->config['doctotxt'] = array();
			if (!empty($doctotxt)) {
					
				$encoding = ($doctotxt->getAttributeNode("encoding")) ? $doctotxt->getAttributeNode("encoding")->value : null;
				$type = ($doctotxt->getAttributeNode("type")) ? $doctotxt->getAttributeNode("type")->value : null;
				
				$this->config['doctotxt']['encoding'] = $encoding;
				$this->config['doctotxt']['type'] 	  = $type;
				
			} else {
				$this->config['doctotxt']['encoding'] = null;
				$this->config['doctotxt']['type'] 	  = "PHP";
			}
			
			// servet
			$server = ($xpath->query("server", $ifile)->item(0)) ? $xpath->query("server", $ifile)->item(0) : null;
			$this->config['server'] = array();
			if (!empty($server)) {					
				$bit = ($server->getAttributeNode("bit")) ? $server->getAttributeNode("bit")->value : null;
				$this->config['server']['bit'] = $bit;
			} else {
				$this->config['server']['bit'] = 32;
			}
			
			// XPDF
			$nodeXpdf = ($xpath->query("xpdf", $ifile)) ? $xpath->query("xpdf", $ifile) : null;
			$this->config['xpdf'] = array();		
			if (!empty($nodeXpdf)) {
				foreach($nodeXpdf as $xpdf) {
					// opw
					$this->config['xpdf']['opw'] = ($xpath->query("opw", $xpdf)->item(0)) ? $xpath->query("opw", $xpdf)->item(0)->nodeValue : null;
					// pdftotext
					$this->config['xpdf']['pdftotext'] = array();
					if ($xpath->query("pdftotext", $xpdf)) {
						$pdftotext = $xpath->query("pdftotext", $xpdf);
						
						foreach($pdftotext as $txt) {
							// executable
							$this->config['xpdf']['pdftotext']['executable'] = ($xpath->query("executable", $txt)->item(0)) ? trim($xpath->query("executable", $txt)->item(0)->nodeValue) : null;
							$this->config['xpdf']['pdftotext']['xpdfrc'] = ($xpath->query("xpdfrc", $txt)->item(0)) ? trim($xpath->query("xpdfrc", $txt)->item(0)->nodeValue) : null;
						}
						
						if (!empty($this->config['xpdf']['pdftotext']['executable'])) {
							$this->checkCustomXPDF($this->config['xpdf']['pdftotext']['executable'], "pdftotext");
						} 
						
						if (!empty($this->config['xpdf']['pdftotext']['xpdfrc'])) {
							$this->checkCustomXPDF($this->config['xpdf']['pdftotext']['xpdfrc'], "xpdfrc for pdftotext");
						}
						
					}
					// pdfinfo
					$this->config['xpdf']['pdfinfo'] = array();
					if ($xpath->query("pdfinfo", $xpdf)) {
						$pdfinfo = $xpath->query("pdfinfo", $xpdf);
						
						foreach($pdfinfo as $txt) {
							// executable
							$this->config['xpdf']['pdfinfo']['executable'] = ($xpath->query("executable", $txt)->item(0)) ? trim($xpath->query("executable", $txt)->item(0)->nodeValue) : null;
							$this->config['xpdf']['pdfinfo']['xpdfrc'] = ($xpath->query("xpdfrc", $txt)->item(0)) ? trim($xpath->query("xpdfrc", $txt)->item(0)->nodeValue) : null;
						}
						
						if (!empty($this->config['xpdf']['pdfinfo']['executable'])) {
							$this->checkCustomXPDF($this->config['xpdf']['pdfinfo']['executable'], "pdfinfo");
						} 
						
						if (!empty($this->config['xpdf']['pdfinfo']['xpdfrc'])) {
							$this->checkCustomXPDF($this->config['xpdf']['pdfinfo']['xpdfrc'], "xpdfrc for pdfinfo");
						}
					}
				}				
			}			
			
			// questo permette di avere l'intero TAG <analyzer> 
			// opzionale all'interno del file di configurazione
			$this->config['analyzer'] = $this->analyzer;
			// analyzer type
			$nodeAnalyzerType = $xpath->query("//ifile/analyzer/type");	
			
			foreach($nodeAnalyzerType as $analyzer) {
				// default analyzer
				$this->config['analyzer'] = ($xpath->query("default", $analyzer)->item(0)) ? $xpath->query("default", $analyzer)->item(0)->nodeValue : $this->analyzer;
				// custom analyzer
				$fileAnalyzer = ($xpath->query("custom-default", $analyzer)->item(0)) ? trim($xpath->query("custom-default", $analyzer)->item(0)->nodeValue) : null;
				
				if (!empty($fileAnalyzer)) {
					$classAnalyzer = $xpath->query("custom-default", $analyzer)->item(0)->getAttributeNode("class")->value;				
					$obj = $this->checkAnalyzer($fileAnalyzer."\\".$classAnalyzer);
					$this->config['custom-analyzer'] = $obj;
					// salvo anche le stringhe di configurazione
					$this->config['xml-custom-analyzer'] = array('file' => $fileAnalyzer, 'class' => $classAnalyzer);
				}				
			}
			
			// fields
			$tmpField = $this->getDefineFieldsType();
			$nodeFields = $xpath->query("//ifile/zend-document/fields/field");			
			foreach($nodeFields as $field) {
				
				$fieldName = $field->getAttributeNode("name")->value;
				$fieldType = $field->getAttributeNode("type")->value;
				// se non trova l'encoding allora gli fissa quello di default
				$fieldEncoding = ($field->getAttributeNode("encoding")) ? $field->getAttributeNode("encoding")->value : $this->config['encoding'];
				
				$tmpField[$fieldName]['type'] = $fieldType;
				$tmpField[$fieldName]['encoding'] = $fieldEncoding;
			}
			// se non e' vuoto l'array temporaneo
			// setto l'array della zend-document
			$this->config['zend-document-fields'] = (!empty($tmpField)) ? $tmpField : null;
			
			// analyzer filter
			$nodeAnalyzerFiltes = $xpath->query("//ifile/analyzer/filters");			
			foreach($nodeAnalyzerFiltes as $filter) {
				// stop-words
				$this->config['stop-words'] = ($xpath->query("stop-words", $filter)->item(0)) ? trim($xpath->query("stop-words", $filter)->item(0)->nodeValue) : null;
				// short-words
				$this->config['short-words'] = ($xpath->query("short-words", $filter)->item(0)) ? trim($xpath->query("short-words", $filter)->item(0)->nodeValue) : null;
				// controlla che sia stato inserito un file esistente
				if (!empty($this->config['stop-words'])) {$this->checkStopWords($this->config['stop-words']);}
				
				// custom filters				
				$registryFilter = array();
				$xmlRegistryFilter = array();
				
				$nodeAnalyzerFiltesCustom = $xpath->query("//ifile/analyzer/filters/custom-filters/filter");
				foreach($nodeAnalyzerFiltesCustom as $customFilter) {
					
					$fileFilter = trim($customFilter->nodeValue);
					$classFilter = $customFilter->getAttributeNode("class")->value;
					
					if (!empty($fileFilter) && !empty($classFilter)) {					
						// conbtrollo esistenza della classe						
						$obj = $this->checkTokenFilter($fileFilter."\\".$classFilter);
						// inserisce il riferimento all'oggetto
						array_push($registryFilter, $obj);
						// salvo anche le stringhe di configurazione
						array_push($xmlRegistryFilter, (array('file'=>$fileFilter, 'class' => $classFilter )));						
					}
				}
				
				$this->config['filters'] = (!empty($registryFilter)) ? $registryFilter : null;
				// salvo anche le stringhe di configurazione
				$this->config['xml-filters'] = (!empty($xmlRegistryFilter)) ? $xmlRegistryFilter : null;
				
// 				echo __METHOD__.PHP_EOL;
// 				print_r($this->config);
			}
		}	
	}
	
	/**
	 * Configurazione di Default dei fields "Standard" di IFile	 
	 * Fields:
	 * - name:Binary
	 * - extensionfile:Keyword
	 * - path:Binary
	 * - filename:Binary
	 * - introtext:UnIndexed		
	 * - body:UnStored
	 * - title:Text
	 * - subject:Text
	 * - description:Text
	 * - creator:Text
	 * - keywords:Keyword
	 * - created:UnStored
	 * - modified:UnStored
	 * 
	 * Sono esclusi 
	 * - root
	 * - key
	 * 
	 * @return array 
	 */
	public function getDefineFieldsType() {
		$fields = array();
		$fields['name']['type'] 		= 'Binary';
		$fields['extensionfile']['type']= 'Keyword';
		$fields['path']['type'] 		= 'Binary';
		$fields['filename']['type'] 	= 'Binary';
		$fields['introtext']['type'] 	= 'UnIndexed';
		$fields['body']['type'] 		= 'UnStored';
		$fields['title']['type'] 		= 'Text';
		$fields['subject']['type'] 		= 'Text';
		$fields['description']['type'] 	= 'Text';
		$fields['creator']['type'] 		= 'Text';
		$fields['keywords']['type'] 	= 'Keyword';
		$fields['created']['type'] 		= 'UnStored';
		$fields['modified']['type'] 	= 'UnStored';
		$fields['pages']['type'] 	 	= 'UnStored';
		
		return $fields;
	}
	
	/**
	 * Definisce una lista di encoding che IFile supporta.
	 * Questi sono gli encoding che al momento sono stati testati e verificati.
	 * 
	 * Ritorna un array a due dimensioni dove sono presenti l'encoding e per quali alfabeti viene utilizzato
	 * @TODO 
	 * La lista dovrebbe essere recuperata dal XML Schema per evitare di 
	 * andare a modificare ogni volta questo metodo  
	 *
	 * @return array $encoding
	 */
	public function getEncodingType() {
		$encoding = array();
		// UTF-8
		$encoding['UTF-8']['encoding'] 			= 'UTF-8';
		$encoding['UTF-8']['description'] 		= 'UCS Transformation Format—8-bit';
		// ASCII
		$encoding['ASCII']['encoding'] 			= 'ASCII';
		$encoding['ASCII']['description']		= 'American Standard Code for Information Interchange';
		// CP1256
		$encoding['CP1256']['encoding']			= 'CP1256';
		$encoding['CP1256']['description']		= 'Code Page Windows-1256 (Latin)'; // 
		// Windows-1256
		$encoding['Windows-1252']['encoding'] 	= 'Windows-1252'; 
		$encoding['Windows-1252']['description']= 'Code Page Windows-1256 (Latin)'; 
		// ISO-8859-1
		$encoding['ISO-8859-1']['encoding']	 	= 'ISO-8859-1'; // 	
		$encoding['ISO-8859-1']['description'] 	= 'Western Europe (Latin 1)';	
		// ISO-8859-2	
		$encoding['ISO-8859-2']['encoding'] 	= 'ISO-8859-2';   
		$encoding['ISO-8859-2']['description'] 	= 'Central and East European (Latin 2)'; 
		// ISO-8859-6		 
		$encoding['ISO-8859-6']['encoding'] 	= 'ISO-8859-6';
		$encoding['ISO-8859-6']['description'] 	= 'Arabic'; 
		// ISO-8859-7		
		$encoding['ISO-8859-7']['encoding'] 	= 'ISO-8859-7';
		$encoding['ISO-8859-7']['description'] 	= 'Greek (Latin 7)';
		// ISO-8859-15
		$encoding['ISO-8859-15']['encoding'] 	= 'ISO-8859-15'; // 		
		$encoding['ISO-8859-15']['description']	= 'Western Europe but with the euro symbol (Latin 0)';
		// KOI8-R
		$encoding['KOI8-R']['encoding']			= 'KOI8-R';
		$encoding['KOI8-R']['description'] 		= 'Cyrillic'; 
		 
		return $encoding;
	}
	
	/**
	 * Verifica che sia stato configurato un path esistente per la root application
	 * 
	 * @return void
	 * @throws IFileException  
	 */
	protected function checkRootApplication ($root) {
		
		if (!is_dir(realpath($root))) {
			throw new IFileException('Root-application does not exist');
		}
	}

    /**
     * Verifica che sia stato configurato un path esistente per i file binari
     *
     * @return void
     * @throws IFileException
     */
    protected function checkBinaries ($binaries) {

        if (!is_dir(realpath($binaries))) {
            throw new IFileException('Binaries path does not exist');
        }

        return realpath($binaries);
    }
	
	/**
	 * Verifica che sia stato configurato un file esistente
	 * @param path $file
	 * @param string $type
	 * @return void
	 * @throws IFileException 
	 */
	protected function checkCustomXPDF($file, $type = "") {
		if (is_dir(realpath($file)) || !is_file($file)) {
			throw new IFileException("{$type} file does not exist");
		}
	}
	
	/**
	 * Verifica che sia stato configurato un file esistente
	 * 
	 * @param path $file
	 * @return void
	 * @throws IFileException  
	 */
	protected function checkStopWords ($file) {
		
		if (is_dir(realpath($file)) || !is_file($file)) {
			throw new IFileException('Stop-words file does not exist');
		}
	}
	
	/**
	 * Verifica che l'oggetto analyzer esista
	 * 
	 * @return \ZendSearch\Lucene\Analysis\Analyzer
	 * @throws IFileException
	 */
	protected function checkAnalyzer ($classFilter) {
		
		if (!class_exists($classFilter)) {
			throw new IFileException('Class Analyzer '.$classFilter.' does not exist');
		}
		
		// recupera tutte le classi che estende
		$classes = $this->getAncestors($classFilter);
		
		// verifico che la classe estenda la Zend_Search_Lucene_Analysis_TokenFilter			
		if(!in_array(self::ZEND_ANALYZER, $classes)) {
			throw new IFileException('The class does not extends '.self::ZEND_ANALYZER);
		}
		// L'oggetto Analyzer e' utilizzato solo dalla interfaccia Lucene
		// pertanto non dovrebbero essere istanziati qui, ma nella creazione di Lucene
		return $classFilter;
	}
	
	/**
	 * Verifica che l'oggetto Token Filter esista
	 * 
	 * @return \ZendSearch\Lucene\Analysis\TokenFilter\TokenFilterInterface
	 * @throws IFileException
	 */
	protected function checkTokenFilter ($classFilter) {
		
		if (!class_exists($classFilter)) {
			throw new IFileException('Class TokenFilters '.$classFilter.' does not exist');
		}
		
		// recupera tutte le classi che estende
		$interfaces = $this->getIntefaces($classFilter);
		
		// verifico che la classe estenda la Zend_Search_Lucene_Analysis_TokenFilter			
		if(!in_array(self::ZEND_TOKENFILTER, $interfaces)) {
			throw new IFileException('The class does not implement '.self::ZEND_TOKENFILTER);
		}
		
		// Gli oggetti TokenFilter sono utilizzati solo dalla interfaccia Lucene
		// pertanto non dovrebbero essere istanziati qui, ma nella creazione di Lucene
		return $classFilter;
	}
	
	/**
	 * Ritorna un array delle implementazioni
	 *
	 * @param string $class
	 * @return array
	 */
	private function getIntefaces ($class) {
		return class_implements($class);
	}
	
	/**
	 * Ritorna un array delle estensioni della classe
	 * 
	 * @param string $class
	 * @return array
	 */
	private function getAncestors ($class) {
    	$classes = array($class);
    	while($class = get_parent_class($class)) { $classes[] = $class; }
	    return $classes;
	}

    /**
     * Ritorna l'array dei tipi di Fields
     * @param string $fieldName
     * @return array
     */
    public function getBinariesPath() {
        return $this->config['binaries-path'];
    }

	/**
	 * Ritorna l'array dei tipi di Fields
	 * @param string $fieldName
	 * @return array
	 */
	public function getDocumentField($fieldName) {
		if (isset($this->config['zend-document-fields'][$fieldName])) {
			return $this->config['zend-document-fields'][$fieldName];
		} 
		
		return null;
	}
	
	/**
	 * Ritorna la proprieta' della configurazione per la XPDF
	 * @param string $property
	 * @return mixed
	 */
	public function getXpdf($property) {
		if (isset($this->config['xpdf'][$property])) {			
			return $this->config['xpdf'][$property];
		} 
		
		return null;
	}
	
	/**
	 * Sovrascrive o aggiunge elementi alla configurazione creando una copia di quella originale
	 * 
	 * @param string $key stringa separata da @ per sotto strutture 
	 * @param mixed $value 
	 * @return void;
	 */
	public function overrideConfig($replacements) {
		if ($this->originalConfig == null) {
			$this->originalConfig = $this->config;
		}
		// PHP < 5.3
		if (!function_exists('array_replace_recursive')) {
			$this->config = $this->array_replace_recursive($this->config, $replacements);	
		} else {
			$this->config = array_replace_recursive($this->config, $replacements);
		}
	}
	
	/**
	 * Versione per la versione di PHP < 5.3  
	 * 
	 * @param array $base
	 * @param array $replacements 
	 * @return array
	 */
	private function array_replace_recursive($base, $replacements) 
	{ 
		foreach (array_slice(func_get_args(), 1) as $replacements) { 
			$bref_stack = array(&$base); 
			$head_stack = array($replacements); 
			
			do { 
				end($bref_stack); 
				
				$bref = &$bref_stack[key($bref_stack)]; 
				$head = array_pop($head_stack); 
				
				unset($bref_stack[key($bref_stack)]); 
				
				foreach (array_keys($head) as $key) { 
					if (isset($key, $bref) && 
						@is_array($bref[$key]) && 
						@is_array($head[$key])) { 
						$bref_stack[] = &$bref[$key]; 
						$head_stack[] = $head[$key]; 
					} else { 
						$bref[$key] = $head[$key]; 
					} 
				} 
			} while(count($head_stack)); 
		} 
		
		return $base; 
	}
	
	/**
	 * Setta un file di configurazione esterno al vendor 
	 * @param path $xmlConfig
	 * @throws IFileException
	 */
	public static function setXmlConfig($xmlConfig) {
		// verifica se il file esiste
		if (is_dir(realpath($xmlConfig)) || !is_file($xmlConfig)) {
			throw new IFileException('Configuration file does not exist ['.$xmlConfig.']');
		}
		
		self::$xml = $xmlConfig;
	}
	
	/**
	 * Ritorna il path del file di configurazione utilizzato
	 * @return string
	 */
	public static function getXmlConfig() {
		return (empty(self::$xml)) ? self::$xml = dirname(__FILE__).DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.'IFileConfig.xml' : self::$xml;
	}
	
	/** 
	 * Setta la configurazione originale se e' stato effettuato un Override
	 * 
	 * @return void; 
	 */
	public function setOriginalConfig() {
		if ($this->originalConfig != null) {
			$this->config = $this->originalConfig;
		} 
	}	 
	
	/**
	 * Ritorna il valore della proprieta' o NULL se non esiste.
	 * Se non viene passata nessuna proprieta' ritorna tutta la struttura
	 * 	 
	 * @param string $config
	 * @return mixed
	 */
	public function getConfig($config = null) {
		
		if ($config === null) {
			return $this->config;
		} elseif (isset($this->config[$config])) {
			return $this->config[$config];
		}					
		
		return null;
	}		
}
