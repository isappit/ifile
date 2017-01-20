<?php
namespace Isappit\Ifile\Searchengine;

use Isappit\Ifile\IFileFactory;
use Isappit\Ifile\Config\IFileConfig;
use Isappit\Ifile\Exception\IFileException;
use Isappit\Ifile\Helpers\IFileHelper;
use Isappit\Ifile\Helpers\IFileInfoFile;
use Isappit\Ifile\Query\IFileQueryRegistry;
use ZendSearch\Lucene\Document as Zend_Search_Lucene_Document;
use ZendSearch\Lucene\Document\Field as Zend_Search_Lucene_Field;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManager;

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
 * Contiene tutti i medodi necessari all'indicizzazione di un file
 * 
 * Implementa l'interfaccia IFile_Indexing_Interface.
 *
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
abstract class IFileIndexingAbstract implements IFileIndexingInterface {
	
	/**
	 * IFileInfoFile
	 * 
	 * @var IFileInfoFile
	 */
	private $file = null;
	/**
	 * Handler della risorsa di indicizzazione
	 * 
	 * @var object
	 */
	private $handler = null;
	/**
	 * Limite di risultati
	 * 
	 * @var integer
	 */
	protected $resultlimit = null;
	/**
	 * Campo di ricerca di default
	 * 
	 * @var string
	 */
	protected $defaultField = null;
	/**
	 * Auto commit (default disattivato)
	 * 
	 * @var boolean
	 */
	protected $autoCommit = false;
	/**
	 * Registro dei campi personalizzati da aggiungere al documento
	 * 
	 * @var array
	 */
	protected $registryFields = array();
	/**
	 * Registro dei campi di ordinamento
	 * 
	 * @var array
	 */
	protected $registrySort = array();
	/**
	 * Registro dei termini dell'indice
	 * 
	 * @var array
	 */
	protected $terms = array();
	/**
	 * Registro dei termini per fields
	 * 
	 * @var array
	 */
	protected $termsForFields = array();

    /**
     * Event Manager
     *
     * @var \Zend\EventManager\EventManager
     */
    protected $eventManager = null;


    /**
     * IFileIndexingAbstract constructor.
     */
	protected function __construct() {}

    /**
     * Bootstrap della classe di indicizzazione
     *
     */
    public function bootstrap() {
        $this->eventManager = new EventManager();

        // recupero dati di configurazione
        $iFileConfig = IFileConfig::getInstance();
        // recupero la lista dei plugin configurati
        $plugins = $iFileConfig->getConfig('plugins');

        if ($plugins) {
            $sharedManager = $this->eventManager->getSharedManager();
            foreach ($plugins as $plugin) {
                // Reflection
                $reflection = new \ReflectionClass($plugin);
                // creazione dell'oggetto
                $pluginObject = $reflection->newInstance();
                // attach events
                $this->eventManager->attach("onDocumentBeforeAdd", array($pluginObject, 'onDocumentBeforeAdd'));
            }
        }
    }
	/**
	 * Aggiunge un documento ad un indice
	 * 
	 * Il metodo oltre ai fleld recuperati dalla parserizzazione del file
	 * (o del Zend_Search_Lucene_Document passato) aggiunge i seguenti filed
	 * - name: nome del file
	 * - path: il path relativo del file. 
	 *         Assoulto in caso non venga mecciato con la root-application configurata  
	 * - root: la root-application presente al momento dell'indicizzazione del file
	 * - key: chiave univoca del file recuperata come MD5 del contenuto del file
	 * 
	 * @param Zend_Search_Lucene_Document $doc [optional]
	 * @return Zend_Search_Lucene_Document 
	 * @throws IFileException
	 */
	public function addDocument(Zend_Search_Lucene_Document $doc = null) {
		
		// recupero dati di configurazione 	
		$iFileConfig = IFileConfig::getInstance();
		// recupero del limite della memoria definito nel php.ini
		$defaultMemory = ini_get("memory_limit");
		// recupero del valore settato nel file di configurazione
		$memoryLimit = $iFileConfig->getConfig('memorylimit');
		// definizione del valore di memory limit
		$memoryLimit = ($memoryLimit != null) ? $memoryLimit : $defaultMemory;
		// settaggio del nuovo valore di memory limit
		ini_set("memory_limit", $memoryLimit);	
		// recupera il timelimit per l'esecuzione della parserizzazione
		$timelimit = $iFileConfig->getConfig('timelimit');
		$timelimit = ($timelimit != null) ? $timelimit : 360; 
		// recupero del massimo tempo di esecuzione	
		$defaultTimeLimit = ini_get('max_execution_time');
		// settaggio del tempo limite per l'esecuzione 
		set_time_limit($timelimit);	
		
		// se non viene passato un oggetto Zend_Search_Lucene_Document  
		// chiama il metodo __createDocumentFromFile() per l'indicizzazione di un file
		if ($doc === null) {
			// controlla che sia stato settato il file da indicizzare
			if ($this->file == null) {
				throw new IFileException('Set a file to be indexed');
			}
			// creazione del documento dal file
			$doc = $this->__createDocumentFromFile();
		}
		
		// controlla che sia una istanza della classe Zend_Search_Lucene_Document
		// questo e' dovuto perchè l'eccezione invocata da PHP non e' gestibile (Catchable)
		if (!($doc instanceof Zend_Search_Lucene_Document)) {
			throw new IFileException('Catchable fatal error: Argument 1 passed to IFile_Indexing_Abstract::addDocument() must be an instance of Zend_Search_Lucene_Document');
		}
		
		// Aggiunta dei FIELD utili all'indicizzazione del file		
		// recupera il path dell'applicazione
		$rootApplication = IFileHelper::deleteLastSlash($iFileConfig->getConfig('root-application'));
		// aggiunge il fied "root" al documento 
		$doc->addField(Zend_Search_Lucene_Field::UnIndexed('root', $rootApplication, $iFileConfig->getConfig('encoding')));
			
		// aggiunge i fields associati al file solo se e' stato settato
		// questo perche' eseguendo l'indicizzazione manuale si potrebbe
		// utilizzare la libreria per indicizzare contenuti che non 
		// vengono recuperati da file ma da altre fonti (Esempio: DB)
		if ($this->file !== null) {
			// aggiunge il field "name" al documento
			// modificato nel default il type a binario per la gestione dei file con caratteri ed encoding speciali
			IFileHelper::setFieldType($doc, 'name', $this->file->getNameFile());
			// aggiunge il field "serchablename" al documento (per la ricerca sul nome del file)
			$doc->addField(Zend_Search_Lucene_Field::UnStored('serchablename', $this->file->getNameFile(), $iFileConfig->getConfig('encoding')));
			// aggiunge il field "key" al documento 
		    $doc->addField(Zend_Search_Lucene_Field::Keyword('key', $this->file->getKeyFile(), $iFileConfig->getConfig('encoding')));			
			// aggiunge il field "extensionfile" al documento 
			IFileHelper::setFieldType($doc, 'extensionfile', strtolower($this->file->getExtFile()));
			// aggiunge il field "path" al documento
			// modificato nel default il type a binario per la gestione dei file con caratteri ed encoding speciali 
			IFileHelper::setFieldType($doc, 'path', $this->file->getRelativePathFile());
			// aggiunge il field "filename" al documento 
			// modificato nel default il type a binario per la gestione dei file con caratteri ed encoding speciali
			IFileHelper::setFieldType($doc, 'filename', $this->file->getfile());	
		}
		// definisce una porzione del testo del corpo utilizzabile come testo
		// di introduzione al documento e aggiunge il field "introtext" al documento 
		IFileHelper::setFieldType($doc, 'introtext', IFileHelper::introText(mb_substr($doc->getFieldValue('body'), 0, 200)));

        // trigger onDocumentBeforeAdd
        $this->eventManager->trigger('onDocumentBeforeAdd', $this, array('doc', $doc));

		// aggiunge il documento all'indice (indicizzazione dei contenuti del file)
		$this->__addDocument($doc);
		// svuota il registro dei field personalizzati
		$this->registryFields = array();
		// svuota le proprieta' del file parserizzato
		$this->file = null;
		
		// settaggio del valore di memory limit presente nel file php.ini 
		ini_set("memory_limit", $defaultMemory);
		// riporta il limite allo stato precedente
		set_time_limit($defaultTimeLimit);
		
		return $doc;
	}
	
	/**
	 * Aggiunge un ordinamento alla query 
	 *
	 * @param string $field
	 * @param string $type [optional]
	 * @param string $order [optional]
	 * 
	 * @return void 
	 * @throws IFileException
	 */
	public function setSort($field, $type = SORT_REGULAR, $order = SORT_ASC) {
		
		if (trim($field)== '') {
			throw new IFileException('Field Missing');
		}
		
		// inserisce il filed
		array_push($this->registrySort, $field);
			
		// gestione del tipo
		switch ($type) {
			case SORT_REGULAR:
			case SORT_NUMERIC:
			case SORT_STRING:
				array_push($this->registrySort, $type);
				break;
			default:
				array_push($this->registrySort, SORT_REGULAR);
		}		
		// gestione dell'ordinamento
		switch ($order) {
			case SORT_ASC:
			case SORT_DESC:			
				array_push($this->registrySort, $order);
				break;
			default:
				array_push($this->registrySort, SORT_ASC);
		}
	}
	
	/**
	 * Aggiunge un campo personalizzato al documento da indicizzare 
	 *  
	 * @param string $field
	 * @param string $term
	 * @param string $type
	 * 
	 * @return void 
	 * @throws IFileException
	 */
	public function addCustomField($field, $term, $type) {
		if (trim($field)== '' || trim($term)== '') {
			throw new IFileException('Missing field name or term');
		}
		
		// recupera il namespace
		$ifileIndexingAbstarct = __NAMESPACE__.'\\IFileIndexingAbstract';
		
		$refl   = new \ReflectionClass($ifileIndexingAbstarct);
		$consts = $refl->getConstants();
		
		if (!in_array($type, $consts, true)) {
			throw new IFileException('Type Field not present.');
		}	
		
		// creazione di un oggetto standard
		$customField = new \stdClass();
		$customField->field = $field;
		$customField->term = $term;
		$customField->type = $type;
		
		// inserisce nel registro il nuvo campo
		array_push($this->registryFields, $customField);		
	}
	
	/**
	 * Ritorna un array dei campi personalizzati settati 
	 *  
	 * @return array 
	 */
	public function getCustomField() {
		return $this->registryFields;
	}
	
	/**
	 * Crea un oggetto Zend_Search_Lucene_Document da un file
	 * 
	 * Oltre ai metadati recuperati dal processo di parserizzazione del file
	 * (vedi LuceneDataIndexBean) vengono aggiunti all'indice altri campi importanti, ovvero:
	 * 
	 * @return Zend_Search_Lucene_Document
	 * @throws IFileException, Adapter_Search_Lucene_Exception
	 */
	private function __createDocumentFromFile() {
		// configurazione
		$iFileConfig = IFileConfig::getInstance();		
		// verifica solo se non e' stato configurato il TAG 
		// duplicate con il valore 1
		if ($iFileConfig->getConfig('duplicate') != 1) {
			// verifica che il file non sia gia' stato indicizzato
			$this->__checkIndexingFileFromKey($this->file->getKeyFile());	
		}
		// istanzia il factory degli Adapter
		$IFileFactory = IFileFactory::getInstance();
		// in caso di errori i n voca una eccezione
		$adapter = $IFileFactory->getAdapterSearchLuceneDocument($this->file->getExtFile());
		// setta il file su cui lavorare
		$adapter->setFilename($this->file->getFile());		
		// chiamata la metodo per il parser del file
		$doc = $adapter->loadParserFile();
		
		return $doc;		
	}
	
	/**
	 * Indicizza il documento
	 * @param Zend_Search_Lucene_Document $doc
	 * @return void
	 */
	abstract protected function __addDocument(Zend_Search_Lucene_Document $doc);
		
	/**
	 * Verifica se il file e' stato gia' indicizzato
	 * @param string $key MD5
	 * @return void
	 */
	abstract protected function __checkIndexingFileFromKey($key);
	
	/**
	 * Setta l'handler alla risorsa di indicizzazione
	 * @param string $connect
	 * @return void
	 */
	protected function setIndexResource($resource) {
		$this->handler = $resource;
	}
	
	/**
	 * Setta l'handler alla risorsa di indicizzazione
	 * @param string $connect
	 * @return void
	 */
	protected function getIndexResource() {
		return $this->handler;
	}
	
	/**
	 * Esegue la query di ricerca per i termini
	 * 
	 * @param IFileQueryRegistry $query
	 * @return mixed
	 */
	public function query(IFileQueryRegistry $query) {
		// controlla che sia una istanza della classe IFileQueryRegistry
		// questo e' dovuto perchè l'eccezione invocata da PHP non e' gestibile (Catchable)
		if (!($query instanceof IFileQueryRegistry)) {
			throw new IFileException('Catchable fatal error: Argument 1 passed to IFile_Indexing_Abstract::query() must be an instance of IFileQueryRegistry.');
		}
		
		$hits = $this->__query($query);
		// azzera il registro dell'ordinamento
		$this->registrySort = array(); 
		return $hits;
	}

	/**
	 * Esegue la query di ricerca per frasi
	 * 
	 * @param IFileQueryRegistry $query
	 * @return mixed
     * @throws IFileException
	 */
	public function queryPhrase(IFileQueryRegistry $query) {
		// controlla che sia una istanza della classe IFileQueryRegistry
		// questo e' dovuto perchè l'eccezione invocata da PHP non e' gestibile (Catchable)
		if (!($query instanceof IFileQueryRegistry)) {
			throw new IFileException('Catchable fatal error: Argument 1 passed to IFile_Indexing_Abstract::queryPhrase() must be an instance of IFileQueryRegistry.');
		}
		
		$hits = $this->__queryPhrase($query);
		// azzera il registro dell'ordinamento
		$this->registrySort = array(); 
		return $hits;
	}
	
	/**
	 * Esegue la fuzzy query
	 * 
	 * @param IFileQueryRegistry $query
	 * @return mixed
     * @throws IFileException
	 */
	public function queryFuzzy(IFileQueryRegistry $query) {
		// controlla che sia una istanza della classe IFileQueryRegistry
		// questo e' dovuto perchè l'eccezione invocata da PHP non e' gestibile (Catchable)
		if (!($query instanceof IFileQueryRegistry)) {
			throw new IFileException('Catchable fatal error: Argument 1 passed to IFile_Indexing_Abstract::queryFuzzy() must be an instance of IFileQueryRegistry.');
		}
		
		$hits = $this->__queryFuzzy($query);
		// azzera il registro dell'ordinamento
		$this->registrySort = array(); 
		return $hits;		
	}
	
	/**
	 * Esegue una boolean query
	 * 
	 * @param IFileQueryRegistry $query
	 * @return mixed
     * @throws IFileException
     */
	public function queryBoolean(IFileQueryRegistry $query) {
		// controlla che sia una istanza della classe IFileQueryRegistry
		// questo e' dovuto perchè l'eccezione invocata da PHP non e' gestibile (Catchable)
		if (!($query instanceof IFileQueryRegistry)) {
			throw new IFileException('Catchable fatal error: Argument 1 passed to IFile_Indexing_Abstract::queryBoolean() must be an instance of IFileQueryRegistry.');
		}
		
		$hits = $this->__queryBoolean($query);
		// azzera il registro dell'ordinamento
		$this->registrySort = array(); 
		return $hits; 
	}
	
	/**
	 * Esegue la query con caratteri Wildcard
	 * 
	 * @param IFileQueryRegistry $query
	 * @return mixed
     * @throws IFileException
     */
	public function queryWildcard(IFileQueryRegistry $query) {
		// controlla che sia una istanza della classe IFileQueryRegistry
		// questo e' dovuto perchè l'eccezione invocata da PHP non e' gestibile (Catchable)
		if (!($query instanceof IFileQueryRegistry)) {
			throw new IFileException('Catchable fatal error: Argument 1 passed to IFile_Indexing_Abstract::queryWildcard() must be an instance of IFileQueryRegistry.');
		}
		
		$hits = $this->__queryWildcard($query);
		// azzera il registro dell'ordinamento
		$this->registrySort = array(); 
		return $hits; 
	}
	
	/**
	 * Esegue la query su un range di dati 
	 * 
	 * @param IFileQueryRegistry $query
	 * @return mixed
     * @throws IFileException
     */
	public function queryRange(IFileQueryRegistry $query) {
		// controlla che sia una istanza della classe IFileQueryRegistry
		// questo e' dovuto perchè l'eccezione invocata da PHP non e' gestibile (Catchable)
		if (!($query instanceof IFileQueryRegistry)) {
			throw new IFileException('Catchable fatal error: Argument 1 passed to IFile_Indexing_Abstract::queryRange() must be an instance of IFileQueryRegistry.');
		}
		
		$hits = $this->__queryRange($query);
		// azzera il registro dell'ordinamento
		$this->registrySort = array(); 
		return $hits; 
	}
	
	/**
	 * Esegue una query parserizzando la stringa di ricerca
	 * 
	 * @param IFileQueryRegistry $query
	 * @return mixed
	 */
	public function queryParser($query) {
		$hits = $this->__queryParser($query);
		// azzera il registro dell'ordinamento
		$this->registrySort = array(); 
		return $hits;
	}
	
	/**
	 * Esegue la query di ricerca per i termini
	 * 
	 * @param IFileQueryRegistry $query
	 * @return array
	 */
	abstract protected function __query(IFileQueryRegistry $query);
	
	/**
	 * Esegue la query di ricerca per frasi
	 * 
	 * @param IFileQueryRegistry $query
	 * @return array
	 */
	abstract protected function __queryPhrase(IFileQueryRegistry $query);
	
	/**
	 * Esegue fuzzy query
	 * 
	 * @param IFileQueryRegistry $query
	 * @return array
	 */
	abstract protected function __queryFuzzy(IFileQueryRegistry $query);
	
	/**
	 * Esegue una boolean query
	 * 
	 * @param IFileQueryRegistry $query
	 * @return array
	 */
	abstract protected function __queryBoolean(IFileQueryRegistry $query);
	
	/**
	 * Esegue la query con caratteri Wildcard
	 * 
	 * @param IFileQueryRegistry $query
	 * @return array
	 */
	abstract protected function __queryWildcard(IFileQueryRegistry $query);
	
	/**
	 * Esegue la query Parserizzaando la stringa
	 * 
	 * @param string $query
	 * @return array
	 */
	abstract protected function __queryParser($query);
	
	/**
	 * Ottimizza l'indice
	 * @return void
	 */
	public function optimize() {}
	
	/**
	 * Marca un documento come cancellato
	 * @param integer $id
	 * @return void
	 */
	public function delete($id) {}
	
	/**
	 * Setta il file da indicizzare
	 * 
	 * Il metodo recupera dal file altre informazioni quali:
	 * - Checksum del contenuto del file
	 * - Nome del file
	 * - Estensione
	 * - Path relativo del file a partire dalla root-application configurata nel file XML
	 * 
	 * @param stringa $indexFile
	 * @return void
     * @throws IFileException
     */
	public function setIndexFile($indexFile) {
		// controlla che sia un file esistente
		if(trim($indexFile) == '' || is_dir($indexFile) || !is_file($indexFile)) {
			throw new IFileException('File does not exist');
		}
		
		// definisce il realpath del file
		$indexFile = realpath($indexFile);
		// chiave univoca del file
		$key 	= IFileHelper::checksumFromFile($indexFile);
		// nome del file
		$name 	= IFileHelper::getName($indexFile);
		// estensione del file
		$ext 	= IFileHelper::getExt($name);
		// Recupera i dati di configurazione 		
		$IfileConfig = IFileConfig::getInstance();
		// recupera il path dell'applicazione
		$rootApplication = IFileHelper::deleteLastSlash($IfileConfig->getConfig('root-application'));
		// se il percorso della root-application e' presente nel nome del file
		// allora recupera solo il percorso relativo del file altrimenti lascia  
		// il percorso del file invariato per non perdere la posizione reale di questo		
		if (strstr($indexFile, $rootApplication) !== false) {
			$relativePathFile = substr($indexFile, strlen($rootApplication));	
		} else {
			$relativePathFile = $indexFile;
		}
		
		// istanza dell'oggetto contenitore dei dati del file
		$this->file = new IFileInfoFile();
		// setta il path reale del file
		$this->file->setFile($indexFile);
		// setta la chiave univoca del file
		$this->file->setKeyFile($key);
		// setta il nome del file
		$this->file->setNameFile($name);
		// setta l'estensione del file
		$this->file->setExtFile($ext);
		// setta il path relativo del file
		$this->file->setRelativePathFile($relativePathFile);
		
	}
	/**
	 * Ritorna un oggetto IFileInfoFile
	 *  
	 * @return IFileInfoFile
	 */
	public function getIndexFile() {
		return $this->file;
	}
	
	/**
	 * Setta il limite dei risultati da estrarre
	 * @param integer $limit
	 * @return void
	 */	
	public function setResultLimit($limit) {
		$this->resultlimit = $limit;
	}
	
	/**
	 * Ritorna il limite dei risultati da estrarre
	 * @return integer
	 */	
	public function getResultLimit() {
		return $this->resultlimit;
	}
	
	/**
	 * Setta il field di default su cui ricercare i termini
	 * @param string $field
	 * @return void
	 */	
	public function setDefaultSearchField($field) {
		$this->defaultField = $field;
	}
	
	/**
	 * Ritorna il field di default su cui ricercare i termini
	 * @return string
	 */	
	public function getDefaultSearchField() {
		return $this->defaultField;
	}
		
	/**
	 * Ritorna il numero di documenti inseriti compresi quelli marcati come cancellati
	 * @return integer
	 */
	public function count() {}
	
	/**
	 * Ritorna il numero di documenti realmente presenti senza quelli marcati come cancellati
	 * @return integer
	 */
	public function numDocs() {}
	
	/**
	 * Ritorna un array dei campi presenti nell'indice
	 * @param boolean $indexed se true torna solo quelli indicizzati
	 * @return array
	 */
	public function getFieldNames($indexed = false) {}
	
	/**
	 * Ritorna l'oggetto documento
	 * @return Zend_Searc_Lucene_Document
	 */
	public function getDocument($id) {}
	
	/**
	 * Ritorna un array contenente tutti gli oggetti documento
	 * presenti nell'indice, senza i documenti marcati come cancellati. 
	 * Se settato il parametro $deleted = true allora ritorna anche 
	 * i documenti cancellati.
	 * 
	 * Ritorna NULL se non sono presenti documenti 
	 * 
	 * @param boolean $deleted
	 * @param integer $offset [optional] 
	 * @param integer $maxrow [optional] 
	 * @return mixed 
	 */
	public function getAllDocument($deleted = false, $offset = null, $maxrow = null) {}
	
	/**
	 * Setta la gestire manualmente o in modo automatico del commit
	 * @param boolean $commit
	 * @return void
	 */
	public function autoCommit($autocommit) {
		$this->autoCommit = $autocommit;
	}
	
	/**
	 * Committa l'indice
	 * @return void
	 */
	public function commit() {}
	
	/**
	 * Verifica se ci sono documenti calcellati
	 * @return boolean
	 */
	public function hasDeletions() {}
	
	/**
	 * Verifica se esiste il termine
	 * @param string $term
	 * @param string $field [0prional]
	 * @return boolean
	 */
	public function hasTerm($term, $field = null) {}
	
	/**
	 * Ritorna un array di oggetti "Zend_Search_Lucene_Index_Term", termini, presenti nell'indice
	 * @return array
	 */
	public function terms() {}
	
	/**
	 * Ritorna un array di oggetti "Zend_Search_Lucene_Index_Term", termini, presenti in un field (campo) 
	 * @param string $field 
	 * @return 
	 */
	public function getTermsForField($field) {}
	
	/**
	 * Verifica se un documento e' stato marcato come cancellato
	 * Ritorna un eccezione Zend_Search_Lucene_Exception se $id non e'
	 * presente nel range degli id dell'indice 
	 * @return boolean
	 * @throws Zend_Search_Lucene_Exception 
	 */
	public function isDeleted($id) {}
	
	/**
	 * Ripristina tutti i documenti marcati come cancellati
	 * Implementato in Zend_Search_Lucene dalla versione (x.x.x)
	 * @return void
	 */
	public function undeletedAll() {}	
	
	/** 
	 * Cancella l'indice e ritorna il numero di documenti cancellati
	 * 
	 * Se viene passato TRUE cancella solo tutti i documenti dall'indice
	 * e ritorna il numero di documenti cancellati altrimenti elimina completamente l'indice
	 * 
	 * @param bool $doc [optional]
	 * @return integer
	 */
	public function deleteAll($doc = false) {}
}
?>