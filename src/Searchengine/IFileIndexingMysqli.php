<?php
namespace Isappit\Ifile\Searchengine;

use Isappit\Ifile\Exception\IFileException;
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license
 * @version    1.1.1 IFile_Indexing_Mysqli.php 2011-08-16 17:14:22
 */

/** Zend_Search_Lucene_Document */
require_once 'Zend/Search/Lucene/Document.php';
/** IFileAdapterFactory */
require_once 'helpers/IFileQueryHit.php';
/** LuceneDataIndexBean */
//require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'adapter/beans/LuceneDataIndexBean.php';
require_once 'adapter/beans/LuceneDataIndexBean.php';

/**
 * Utilizza MySql come motore di indicizzazione e ricerca
 *
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license
 */
class IFileIndexingMysqli extends IFileIndexingAbstract {
	
	/**
	 * Nome della Tabella
	 * 
	 * @var string
	 */
	private $ifileTable;
		
	/**
	 * Istanza di IFile_Indexing_MySqli
	 * 
	 * @var IFile_Indexing_MySqli
	 */
	private static $_instance;
	
	/**
	 * Serve nella ricerca di un termine all'interno dell'indice
	 * 
	 * @var boolean
	 */
	private $hasTerm = false;	
	
	/**
	 * Invoca un eccezione in caso non riesca ad aprire o creare l'indice.
	 * 
	 * Recupera il nome della tabella utilizzata per l'indicizzazione dal
	 * file di configurazione. Se non settata prende il valore della costante
	 * IFILE_TABLE
	 * 
	 * @return void 
	 * @throws Zend_Search_Lucene_Exception, IFileException 
	 */
	public function __construct(mysqli $connection) {
		
		// Recupera i dati di configurazione
		$IfileConfig = IFileConfig::getInstance();
		$ifileTable = $IfileConfig->getConfig('table-name');
		
		if ($ifileTable == null) {
			throw new IFileException('Table name configuration is empty');
		}
		
		// recupera la configurazione della tabella
		$this->ifileTable = $ifileTable;
		
		// controlla che sia una istanza della classe mysqli
		// questo e' dovuto perchè l'eccezione invocata da PHP non e' gestibile (Catchable)
		if (!($connection instanceof mysqli)) {
			throw new IFileException('Catchable fatal error: Argument 1 passed to IFile_Indexing_MySqli::__construct() must be an instance of mysqli');
		}
		
		// salva l'handler della risorsa di indicizzazione
		$this->setIndexResource($connection);
		// verifica se la tabella esiste altrimenti la crea
		$this->__createTable();
		// inizializza Mysql
		$this->__initializeMysqli();
	}
	
	/**
	 * Inizializza Lucene con i parametri di configurazione definiti nel file IFileConfig.xml 
	 *
	 * @return void 
	 */
	private function __initializeMysqli() {		
		// Recupera l'istanza di configurazione		
		$IfileConfig = IFileConfig::getInstance();
		// setta il result Limit se non è vuoto
		$resultLimit = $IfileConfig->getConfig('resultlimit');
		if (!empty($resultLimit)) {
			$this->setResultLimit($resultLimit);
		}
		// setta il field di default se non è vuoto
		$defaultFieldSearch = $IfileConfig->getConfig('default-search-field');
		if (!empty($defaultFieldSearch)) {
			$this->setDefaultSearchField($defaultFieldSearch);
		}
	}
	
	/**
	 * Verifica l'esistenza della tabella. Se non esiste la crea
	 * 
	 * @return void
	 */
	private function __createTable() {
		$tableExist = false;
		
		$sql = "SHOW TABLES";
		$res = $this->output_mysqli($sql, Array('num_rows' => true, 'fetch' => false));
		
		// se ci sono tabelle 		
		if ($res['num_rows'] != 0) {
			// cicla tutte le tabella dello schema per verificarne l'esistenza
			foreach ($res['rows'] as $tables) {
				foreach ($tables as $table) {
					if (strtolower($table) == strtolower($this->ifileTable)) {
						$tableExist = true;
						break;
					}	
				}				 
			}	
		}		 		
		
		// se la tabella non esiste la crea
		if (!$tableExist) {
			// definizione del charset (verificare se recuperarlo dal TAG <encoding>)
			// in questo modo definisce il charset dello schema 
			$charset = $this->__getCharset();			
			if ($charset !== null) {
				$charset = "DEFAULT CHARSET = {$charset} ";
			}
			
			$collation = $this->__getCollation();
			if ($collation !== null) {
				$collation = "COLLATE = {$collation} ";
			}
			
			// crea la tabella
			$sqlCreate['query'] = "CREATE TABLE IF NOT EXISTS `#__TABLE__#` (
					  `id` int(22) NOT NULL auto_increment,
					  `deleted` tinyint(4) NOT NULL default '0',
					  `key` varchar(32),
					  `body` longtext NOT NULL,
					  PRIMARY KEY  (`id`),
					  FULLTEXT KEY `body` (`body`)  
					) ENGINE=MyISAM {$charset} {$collation}";
			
			$result = $this->input_mysqli($sqlCreate);
			
			$sqlKey = array();
			$sqlKey['query']  = "ALTER TABLE `#__TABLE__#` ADD FULLTEXT(`KEY`) ";
			$this->input_mysqli($sqlKey);
		}
	} 
	
	private function __getCollation() {
		// Recupera i dati di configurazione
		$IfileConfig = IFileConfig::getInstance();
		$collation = $IfileConfig->getConfig('table-collation');
		
		return $collation;
	}
	
	/**
	 * Ritorna il Charater-set associato al tipo di encoding definito
	 * 
	 * @return string
	 */
	private function __getCharset() {
		/*
		 * Attribute: encoding 
		 * - UTF-8
		 * - ASCII
		 * - ISO8859-1
		 * - ISO8859-15
		 * - ISO8859-2
 		 * - ISO8859-7
		 * - CP1256
		 * - Windows-1252 
		 */
		
		// Recupera i dati di configurazione
		$IfileConfig = IFileConfig::getInstance();
		$encoding = $IfileConfig->getConfig('encoding');
		
		$charset = Array();
		$charset['UTF-8'] = 'utf8';
		$charset['ASCII'] = 'ascii';
		$charset['ISO8859-1'] = 'latin1';
		$charset['ISO8859-15'] = 'latin1';
		$charset['ISO8859-2'] = 'latin2';
		$charset['ISO8859-7'] = 'latin7';
		$charset['CP1256'] = 'cp1256';
		$charset['Windows-1252'] = 'latin1';
		
		return ((isset($charset[$encoding])) ? $charset[$encoding] : null);
				
	}
		
	/**
	 * Indicizza il documento 
	 * 
	 * @param Zend_Search_Lucene_Document $doc
	 * @return void
	 */
	protected function __addDocument(Zend_Search_Lucene_Document $doc) {
				
		// SQL
		$sql = array();
		// FIELDS
		$sqlFields = array();

		// aggiunge i fields personalzzati al documento
		$fields = $this->getCustomField();
		// chiama il metodo per la creazione dei custom fields
		$this->__addCustomFieldToDocument($doc, $fields);
		
		// recupero della lista dei field del documento		
		$fieldNames = $doc->getFieldNames();
		// definisco i dati per la costruzione della query
		foreach ($fieldNames as $fieldname) {
			// recupero i dati della field
			$field = $doc->getField($fieldname);			
			// definisco nome e value per la bind
			$sqlFields['name'][] = "`$fieldname`";
			$sqlFields['value'][] = '?';
			// definisco la bind per i field
			if (!$field->isBinary) {
				// con la bind non e' necessario effettuare gli "ESCAPE" dei caratteri 
				$sql['bind'][]	= array('val' => $this->__getFieldValue($doc, $fieldname), 'type' => 's');
			} else {
				// binary
				$sql['bind'][]	= array('val' => $this->__getFieldValue($doc, $fieldname), 'type' => 'b');
			}
		}
		
		// implosione dei nomi e del value per la bind
		$name = implode (', ', $sqlFields['name']); 
		$value= implode (', ', $sqlFields['value']); 
		// query in formato sprintf
		$query = "INSERT INTO `#__TABLE__#` (%s) VALUES (%s) ";
		// creazione della query per la bind
		$sql['query'] = sprintf($query, $name, $value);
		// auto-increment
		$sql['id'] = true;
		// eseguo la insert		
		$result = $this->input_mysqli($sql);
		// committa se l'auto commit e' settato
		if ($this->autoCommit) $this->commit();
	}
	
	/**
	 * Aggiunge il field personalizzato all'oggetto Zend_Search_Lucene_Document
	 * E crea la nuova struttura dati in base ai fields presenti nel documento 
	 * 
	 * @param Zend_Search_Lucene_Document $doc
	 * @param obj $fields                      
	 * @return void
	 */
	private function __addCustomFieldToDocument(Zend_Search_Lucene_Document $doc, $fields) {
		// @TODO
		// Importante la gestione del CHARSET DEI CAMPI NUOVI DA CREARE  - 
		// si potrebbe utilizzare
		// 1. il TAG encoding del file di configurazione - oppure
		// 2. effettuare un information_schema.tables e recuperare la collection 
		// di default dovrebbe prendere il charset della tabella pertanto 
		// non dovrebbe essere utile in caso si gestisce il charset
		// durante la creazione della tabella
		
		// aggiungo al Document i nuovi custom field se esistono
		foreach($fields as $field) {
			switch ($field->type) {
				case self::FIELD_TYPE_KEYWORD:
					$doc->addField(Zend_Search_Lucene_Field::Keyword($field->field, $field->term));
					break;
				case self::FIELD_TYPE_UNINDEXED:
					$doc->addField(Zend_Search_Lucene_Field::UnIndexed($field->field, $field->term));
					break;
				case self::FIELD_TYPE_BINARY:
					$doc->addField(Zend_Search_Lucene_Field::Binary($field->field, $field->term));
					break;
				case self::FIELD_TYPE_TEXT:
					$doc->addField(Zend_Search_Lucene_Field::Text($field->field, $field->term));
					break;
				case self::FIELD_TYPE_UNSTORED:
					$doc->addField(Zend_Search_Lucene_Field::UnStored($field->field, $field->term));
					break;
				default:
					throw new IFileException('Type Field not present');
			}
		}
				
		// recupero di tutti campi (fields) della tabella		
		$tableFieldsName = $this->getFieldNames();
		// recupero tutti i campi (fields) del documento		
		$docFieldsName = $doc->getFieldNames();
		
		// ciclo i Fields del documento
		// se non esistono allora creo il nuovo campo nel DB
    	foreach($docFieldsName as $field) {
			// effettuo un lower sul nome del campo
			$fieldName = strtolower($field);
			// verifica che non ci siano i campi ID  e DELETED
			// campi che fanno parte del processo di indicizzazione
			switch ($fieldName ) {
				case "id":
				case "deleted":
					throw new IFileException("Not use field name: ID or DELETED");
				break;
			}
			
			// se il custom field non esiste lo creo
			if (!in_array($fieldName, $tableFieldsName)) {
				// recupero della struttura del field dal documento
				$docField = $doc->getField($field); 
				// gestione della creazione dei campi e dell'indice FULLTEXT
				// in base ai flug di indicizzazione e binary
				if ($docField->isIndexed) {
					// creazione del nuovo campo (field) per i tipi:
					// UNSTORED
					// KEYWORD
					// TEXT					
					$sqlAdd = array();
					$sqlAdd['query']  = "ALTER TABLE `#__TABLE__#` ADD `{$fieldName}` TEXT ";
					$this->input_mysqli($sqlAdd);
					// creazione dell'indice FULLTEXT
					$sqlKey = array();
					$sqlKey['query']  = "ALTER TABLE `#__TABLE__#` ADD FULLTEXT(`{$fieldName}`) ";
					$this->input_mysqli($sqlKey);
				} elseif (!$docField->isIndexed && !$docField->isBinary) {
					// creazione del nuovo campo (field) per i tipi:
					// UNINDEXED
					$sqlAdd = array();
					$sqlAdd['query']  = "ALTER TABLE `#__TABLE__#` ADD `{$fieldName}` TEXT ";
					$this->input_mysqli($sqlAdd);
				} elseif (!$docField->isIndexed && $docField->isBinary) {
					// creazione del nuovo campo (field) per i tipi:
					// BINARY
					$sqlAdd = array();
					$sqlAdd['query']  = "ALTER TABLE `#__TABLE__#` ADD `{$fieldName}` BLOB ";
					$this->input_mysqli($sqlAdd);
				}
				// commit dei dati				
				if ($this->autoCommit) $this->commit();
			} 
    	}
	} 
	
	/**
	 * Ritorna il valore di un field.
	 * 
	 * Gestisce l'eccezione del "Not field fuond" della classe Zend_Search_Lucene_Document
	 *  
	 * @param Zend_Search_Lucene_Document $doc
	 * @param string $field
	 * @return mixed
	 */
	private function __getFieldValue(Zend_Search_Lucene_Document $doc, $field) {
		try {
			$value = $doc->getFieldValue($field);
		} catch (Zend_Search_Lucene_Exception $e) {
			$value = '';
		}
		
		return $value;
	}
	
	/**
	 * Verifica se il file e' stato gia' indicizzato
	 * @param string $key MD5
	 * @return void
	 * @throws IFileException
	 */
	protected function __checkIndexingFileFromKey($key) {
		// chiamata per verificare se il file e' gia' stato indicizzato
		$sql = "SELECT 1 as doc FROM `#__TABLE__#` WHERE `KEY` IS NOT NULL AND `KEY` = '{$key}' AND `DELETED` = 0 ";
		$res = $this->output_mysqli($sql, Array('num_rows' => true, 'fetch' => true));
		
		if(!empty($res['num_rows'])){ 
			throw new IFileException("File already in the index");
		}
	}
	
	/**
	 * Restituisce un array con il risultato della query
	 * sui field/term passati
	 * 	 
	 * @internal 
	 * @param object $listQuery	 
	 * @return array
	 */
	private function __createFullTextQuery($listQuery, $expancion = 0) {
		
		switch ($expancion) {
			// QUERY BOOLEAN MODE
			case 0:
				$searchType = "IN BOOLEAN MODE" ;
				break;
			// QURY EXPANSION
			case 1:
				$searchType = "WITH QUERY EXPANSION";
				break;
			// NATURAL LANGUAGES
			case 2:
				$searchType = "" ;
				break;
			default:
			
		}
		
		//$searchType = ($expancion) ? "WITH QUERY EXPANSION" : "IN BOOLEAN MODE" ;
		
		// array dei termini opzionali / obbligatori
		$arrMatch 		= array();
		// array dei termini che non devono essere presenti 
		$arrMinusMatch  = array();
		// array utili alla gestione dei risultati
		$arrayDiff 		= array();
		$arrayMarge 	= array();
		$arrayMinusMarge= array();
				
		// ciclo di tutti i termini di ricerca				
		foreach ($listQuery as $query) {
			// array Fields
			$fields = array();
			
			if (!($query instanceof IFileQuery)) {
				throw new IFileException("The search accepts only IFileQuery terms");
			} 
			
			// recupera i dati di ricerca
			$field 		= $query->getField(); 
			$match 		= $query->getMatch();
			$term  		= $query->getTerm();
			$encoding  	= $query->getEncoding();
			
			// Dato che la SQL necessita dei nomi dei campi per la ricerca
			// in caso non venga settato nessun campo allora procede al 
			// recupero di tutti i campi indicizzati e crea la ricerca
			if (empty($field) && $this->getDefaultSearchField() == null) {
				$fields = $this->getFieldNames(true);				
			} else {
				$tmpField = (!empty($field)) ? $field : $this->getDefaultSearchField();
				$fields[$tmpField] = $tmpField;
			}
			
			// cicla tutti i fields per lo stesso termine
			foreach ($fields as $field) {
				if ($match === IFileQuery::MATCH_REQUIRED) {
					$arrMatch[$field]['term'][] = '+'.$term;
				} elseif ($match === IFileQuery::MATCH_PROHIBITEN) {
					$arrMinusMatch[$field]['term'][] = $term;
				} elseif ($match === IFileQuery::MATCH_OPTIONAL) {
					$arrMatch[$field]['term'][] = $term;
				} else {
					throw new IFileException("Not operators defined");
				}
			}
		}
		
		// creazione della stringa per l'ordinamento		
		$strOrder = $this->__createOrderString();
		
		// in caso sia vuota i dati verranno ordinati per SCORE
		if (empty($strOrder)) {
			$strOrder = "ORDER BY SCORE DESC";
		}		 
		
		// recupero tutti i documenti che riportano i termini ricercati
		foreach ($arrMatch as $field => $terms) {
			
			$term  = implode(" ", $terms['term']);

			$sql = "SELECT `id`, MATCH (`${field}`) AGAINST ('${term}' ${searchType}) as score
					FROM `#__TABLE__#`
					WHERE MATCH (`${field}`) AGAINST ('${term}' ${searchType})
					AND `DELETED` = 0
					${strOrder}";
			
			$res = $this->output_mysqli($sql, Array('num_rows' => true, 'fetch' => true));			
			
			// se esistono risultati allora faccio il merge
			if ($res['num_rows'] != 0) {
				$arrayMarge = array_merge($arrayMarge, $res['rows']);
				// Per migliorare le performance:
				// se si sta cercando solo l'esistenza del termine
				// allora se lo trova in uno dei campi non e' necessario
				// eseguire la ricerca su tutti gli altri
				if ($this->hasTerm) {
					break;
				}
			}
		}
		// elimino eventuali ID doppi
		$arrayMarge = $this->__arrayUnique($arrayMarge);
		
		// recupero tutti i documenti che non devono riportare i termini ricercati
		foreach ($arrMinusMatch as $field => $terms) {
			
			$term  = implode(" ", $terms['term']);

			$sql = "SELECT `id`
					FROM `#__TABLE__#`
					WHERE MATCH (`${field}`) AGAINST ('${term}' ${searchType})
					AND `DELETED` = 0";
			$res = $this->output_mysqli($sql, Array('num_rows' => true, 'fetch' => true));			
			
			if ($res['num_rows'] != 0) {
				$arrayMinusMarge = array_merge($arrayMinusMarge, $res['rows']);
			}
		}
		// elimino eventuali ID doppi 
		$arrayMinusMarge = $this->__arrayUnique($arrayMinusMarge);
		
		// elimino dall'insieme dei documenti l'elenco di quelli che non 
		// devono essere presenti nell'insieme (MATCH_PROHIBITEN)
		$arrayDiff = array_udiff($arrayMarge, $arrayMinusMarge, array("IFile_Indexing_Mysqli", "comp_func_cr"));	

		return $arrayDiff;
	}
	
	/**
	 * Ritorna un array con ID unico
	 *   
	 * @param array $array
	 * @return array
	 */
	private function __arrayUnique(&$array) {
		$uniqueArray = array();

		foreach ($array as $value) {
			// se non esiste l'ID lo memorizzo 			
			if (!isset($uniqueArray[$value['id']])) {
				$uniqueArray[$value['id']] = $value;
			}			 
		}
		
		return array_values($uniqueArray);
	}
		
	/**
	 * Ritorna la stringa per l'ordinamento
	 * @return string
	 */
	private function __createOrderString() {
		// stringa di ordinamento dei risultati
		$strOrder = '';
		
		// creazione della stringa di ordinamento
		if (!empty($this->registrySort)) {
			$arrOrder = array();
			$countOrder = count($this->registrySort);
			for ($i = 0; $i<$countOrder; $i+=3) {
				$fieldOrder = $this->registrySort[$i];
				$typeOrder  = $this->registrySort[$i+1];
				$order 		= ($this->registrySort[$i+2] == SORT_DESC) ? 'DESC' : 'ASC';
				// dato che tutti i campi sono FULLTEXT allora va gestito
				// il cast in caso di richiesta di ordinamento per NUMBER
				// I casi REGULAR e STRING sono visti e gestiti nello stesso modi				
				switch ($typeOrder) {
					case SORT_NUMERIC:
						$var = "CAST(`${fieldOrder}` as SIGNED) ${order}"; 
						array_push($arrOrder, $var);
						break;
					default:
						$var = "`${fieldOrder}` ${order}"; 
						array_push($arrOrder, $var);
				}
				
				$strOrder = "ORDER BY ".implode(",", $arrOrder);
			}
		} 
		
		return $strOrder;
	}
	
    /**
     * Callback per la gestione dell'array_diff sugli array di ricerca 
     * 
     * @param array $a
     * @param array $b
     * @return integer
     */
    public function comp_func_cr($a, $b)
    {
		if ($a['id'] === $b['id']) return 0;
        return ($a['id'] > $b['id'])? 1:-1;
    }
	
	/**
	 * Esegue la query di ricerca per i termini
	 * 
	 * Ritorna un array di oggetti Zend_Search_Lucene_Search_QueryHit 
	 * o un array vuoto in caso la query non presenta match.
	 * 
	 * Ritorna lo SCORE dato l'utilizza la ricerca per linguaggio naturale.
	 * Questo pero' potrebbe ritornare non tutti i risultati dato che 
	 * MySql non prende in considerazione termini rindondanti piu' del 50%
	 * all'interno dell'indicizzazione.
	 * http://dev.mysql.com/doc/refman/5.0/en/fulltext-natural-language.html
	 * 
	 * 
	 * @param IFileQueryRegistry $query
	 * @return array di Zend_Search_Lucene_Search_QueryHit
	 */
	protected function __query(IFileQueryRegistry $query) {

		// array dei risultati
		$hits = array();
		// numero di termini
		$countQuery = $query->count();
		
		if (!empty($countQuery)) {
			$listQuery = $query->getQuery();
			
			$result = $this->__createFullTextQuery($listQuery, 2);
			// creazione dell'oggetto Zend_Search_Lucene_Search_QueryHit
			$hits = $this->__setHits($result); 
		}
			
		return $hits;
	}
	
	/**
	 * Esegue la query di ricerca per frasi. 
	 * 
	 * Ritorna un array vuoto in caso la query non presenta match.
	 * I campi (fields) devono essere gli stessi per tutti i termini 
	 * altrimenti viene generata una eccezione di tipo Zend_Search_Lucene_Exception  
	 *
	 * Ritorna lo SCORE dato l'utilizza la ricerca per linguaggio naturale.
	 * Questo pero' potrebbe ritornare non tutti i risultati dato che 
	 * MySql non prende in considerazione termini rindondanti piu' del 50%
	 * all'interno dell'indicizzazione.
	 * http://dev.mysql.com/doc/refman/5.0/en/fulltext-natural-language.html
	 * 
	 * @param IFileQueryRegistry $query
	 * @return array
	 * @throws Zend_Search_Lucene_Exception
	 * 
	 */
	protected function __queryPhrase(IFileQueryRegistry $query) {
		// array dei risultati
		$hits = array();
		// numero di termini
		$countQuery = $query->count();
		
		if (!empty($countQuery)) {
			$listQuery = $query->getQuery();
			
			$result = $this->__createFullTextQuery($listQuery, 2);
			// creazione dell'oggetto Zend_Search_Lucene_Search_QueryHit
			$hits = $this->__setHits($result); 
		}
			
		return $hits;
	}
	
	/**
	 * Esegue la fuzzy query.
	 *  
	 * Ritorna un array di oggetti Zend_Search_Lucene_Search_QueryHit
	 * o un array vuoto in caso la query non presenta match.
	 * Questa ricerca non ritorna lo SCORE come documentato nelle reference di Mysql
	 * http://dev.mysql.com/doc/refman/5.0/en/fulltext-query-expansion.html
	 * 
	 * @param IFileQueryRegistry $query
	 * @return array di Zend_Search_Lucene_Search_QueryHit
	 * @throws Zend_Search_Lucene_Exception, IFileException
	 * 
	 */
	protected function __queryFuzzy(IFileQueryRegistry $query) {
		// array dei risultati
		$hits = array();
		// numero di termini
		$countQuery = $query->count();
		
		if (!empty($countQuery)) {
			$listQuery = $query->getQuery();
			
			$result = $this->__createFullTextQuery($listQuery, 1);
			// creazione dell'oggetto Zend_Search_Lucene_Search_QueryHit
			$hits = $this->__setHits($result); 
		}
			
		return $hits;
	}
	
	/**
	 * Esegue una boolean query
	 * 
	 * Ritorna un array di oggetti Zend_Search_Lucene_Search_QueryHit
	 * 0 un array vuoto in caso la query non presenta match.
	 * L'argomento $query di tipo IFileQueryRegistry deve contenere a sua volta oggetti IFileQueryRegistry
	 *
	 * MySql gestisce autonomamente le Wildcard e le frasi
	 * Questa ricerca non ritorna lo SCORE come documentato nelle reference di Mysql
	 * http://dev.mysql.com/doc/refman/5.0/en/fulltext-boolean.html
	 * 
	 * @param IFileQueryRegistry $query
	 * @return array di Zend_Search_Lucene_Search_QueryHit
	 * @throws Zend_Search_Lucene_Exception, IFileException
	 */
	protected function __queryBoolean(IFileQueryRegistry $query) {
		
		// array dei risultati
		$hits = array();
		// array utili alla gestione dei risultati
		$arrayDiff 		= array();
		$arrayMarge 	= array();
		$arrayMinusMarge= array();
				
		// numero di termini
		$countQuery = $query->count();
		
		if (!empty($countQuery)) {
			$listQuery = $query->getQuery();
			
			// Registry
			foreach ($listQuery as $registry) {
				// Terms
				$terms = $registry->getTerm();
				$match = $registry->getMatch();
				
				if (!($terms instanceof IFileQueryRegistry)) {
					throw new IFileException("The only accepts Boolean search terms such IFileQueryRegistry");
				} 
				
				// ricerca all'interno dell'indice
				$result = $this->__createFullTextQuery($terms->getQuery());
				
				// creo gli array in base alla tipologia del BOOLEAN	
				if ($match === IFileQuery::MATCH_REQUIRED || $match === IFileQuery::MATCH_OPTIONAL) {
					$arrayMarge = array_merge($arrayMarge, $result);
				} elseif ($match === IFileQuery::MATCH_PROHIBITEN) {
					$arrayMinusMarge = array_merge($arrayMinusMarge, $result);
				} else {
					throw new IFileException("Not operators defined");
				}
			}
			
			// elimino dall'insieme dei documenti l'elenco di quelli che non 
			// devono essere presenti nell'insieme (MATCH_PROHIBITEN)
			$arrayDiff = array_udiff($arrayMarge, $arrayMinusMarge, array("IFile_Indexing_Mysqli", "comp_func_cr"));

			// creazione dell'oggetto Zend_Search_Lucene_Search_QueryHit
			$hits = $this->__setHits($arrayDiff);
		}
		
		return $hits;
	}
	
	/**
	 * Esegue la query con caratteri Wildcard
	 * 
	 * Ritorna un array di oggetti Zend_Search_Lucene_Search_QueryHit
	 * o un array vuoto in caso la query non presenta match.
	 * Puo' essere ricercato solo un unico termine nella ricerca wildcard 
	 * altrimenti viene generata una eccezione di tipo IFileException 
	 * 
	 * Ritorna lo SCORE dato l'utilizza la ricerca per linguaggio naturale.
	 * Questo pero' potrebbe ritornare non tutti i risultati dato che 
	 * MySql non prende in considerazione termini rindondanti piu' del 50%
	 * all'interno dell'indicizzazione.
	 * http://dev.mysql.com/doc/refman/5.0/en/fulltext-natural-language.html
	 * 
	 * @param IFileQueryRegistry $query
	 * @return array di Zend_Search_Lucene_Search_QueryHit
	 * @throws Zend_Search_Lucene_Exception, IFileException
	 * 
	 */
	protected function __queryWildcard(IFileQueryRegistry $query) {
		// array dei risultati
		$hits = array();
		// numero di termini
		$countQuery = $query->count();
		
		if (!empty($countQuery)) {
			$listQuery = $query->getQuery();
			
			$result = $this->__createFullTextQuery($listQuery, 2);
			// creazione dell'oggetto Zend_Search_Lucene_Search_QueryHit
			$hits = $this->__setHits($result); 
		}
			
		return $hits;
	}
	
	/**
	 * Questo metodo non e' supportato per MySql.
	 * 
	 * Ritorna una IFileException se invocato  
	 * 
	 * @param IFileQueryRegistry $query
	 * @return array di Zend_Search_Lucene_Search_QueryHit
	 * @throws Zend_Search_Lucene_Exception, IFileException
	 * 
	 */
	protected function __queryRange(IFileQueryRegistry $query) {
		throw new IFileException("This method is not supported");
	}
	
	/**
	 * Esegue una query parserizzando la stringa di ricerca
	 * 
	 * Ritorna un array di oggetti Zend_Search_Lucene_Search_QueryHit
	 * o un array vuoto in caso la query non presenta match.
	 * 
	 * Ritorna lo SCORE dato l'utilizza la ricerca per linguaggio naturale.
	 * Questo pero' potrebbe ritornare non tutti i risultati dato che 
	 * MySql non prende in considerazione termini rindondanti piu' del 50%
	 * all'interno dell'indicizzazione.
	 * http://dev.mysql.com/doc/refman/5.0/en/fulltext-natural-language.html
	 * 
	 * @param string $query
	 * @return array di Zend_Search_Lucene_Search_QueryHit
	 * @throws Zend_Search_Lucene_Exception, Zend_Search_Lucene_Search_QueryParserException
	 * 
	 */
	protected function __queryParser($query) {
		// array dei risultati
		$hits = array();
		
		// andra' implementata la ricerca con la ricerca naturale
		// mediante una query costruita a mano dall'utente
		// creazione della stringa per l'ordinamento		
		$strOrder = $this->__createOrderString();
		
		// in caso sia vuota i dati verranno ordinati per SCORE
		if (empty($strOrder)) {
			$strOrder = "ORDER BY SCORE DESC";
		}		 
		
		$sql = "SELECT `id`, ${query} as score
				FROM `#__TABLE__#`
				WHERE ${query}
				AND `DELETED` = 0
				${strOrder}";
		
		
		$res = $this->output_mysqli($sql, Array('num_rows' => true, 'fetch' => true));			
		
		if ($res['num_rows'] != 0) {
			// creazione dell'oggetto Zend_Search_Lucene_Search_QueryHit
			$hits = $this->__setHits($res['rows']);
		}
		
		return $hits;
	}
	
	
	/**
	 * Ritorna un array di oggetti IFileQueryHit
	 * 
	 * Trasforma l'array in oggetti di Lucene Zend_Search_Lucene_Search_QueryHit 
	 * 
	 * @return array
	 */
	private function __setHits($hits) {
		$listHits = array();
		// recupero il limite di risultati settati
		$limit = $this->getResultLimit();
		$countRecord = 0;		
		
		foreach ($hits as $hit) {			
			// gestione del limite di risultati che puo' ritornare
			// se limit settato a zero allora torna tutti i risultati
			if ($limit != null && $limit <= $countRecord) break;
			
			$ifilehit = new IFileQueryHit($this);
			$ifilehit->id = $hit['id']; 	
			$ifilehit->score = $hit['score'];
			array_push($listHits, $ifilehit);
			
			$countRecord++; 	
		}
		
		return $listHits;
	}
	
	/**
	 * Ottimizza l'indice
	 * @return void
	 */
	public function optimize() {
		// 1. Cancella i documenti segnati come deleted
		// 2. Cancella i campi che non hanno contenuti
		// 3. Ottimizza la tabella
		
		// Cancella i record segnati come cancellati 
		$sqlDelRecord = array();
		$sqlDelRecord['query']  = "DELETE FROM #__TABLE__# WHERE DELETED = 1";
		$this->input_mysqli($sqlDelRecord);
		
		// Elimina le colonne che non hanno nessun contenuto
		$sql = "DESCRIBE #__TABLE__# ";
		$res = $this->output_mysqli($sql, Array('num_rows' => true, 'fetch' => true));
		
		foreach ($res['rows'] as $field) {
			$lowerField = strtolower($field['Field']);
			switch ($lowerField) {
				// Campi di sistema che non sono presentabili nella lista dei FIELD
				case "id":
				case "deleted":
				case "key":
					break;
				default:
					$sql = "SELECT count(1) as TOTAL FROM #__TABLE__# WHERE `{$lowerField}` IS NOT NULL ";
					$res = $this->output_mysqli($sql, Array('num_rows' => true, 'fetch' => true));
					
					// cancello il campo dalla TABELLA
					if ($res['rows'][0]['TOTAL'] == 0) {
						$sqlDel = array();
						$sqlDel['query']  = "ALTER TABLE `#__TABLE__#` DROP `{$lowerField}` ";
						$this->input_mysqli($sqlDel);
					}
			}
		}
		
		// Ottimizza la tabella 
		$sql = "OPTIMIZE TABLE #__TABLE__# ";
		$res = $this->output_mysqli($sql, Array('num_rows' => true, 'fetch' => true));
	}
	
	/**
	 * Marca un documento come cancellato
	 * Ritorna un eccezione Zend_Search_Lucene_Exception se $id non e'
	 * presente nel range degli id dell'indice 
	 * @param integer $id
	 * @return void
	 * @throws Zend_Search_Lucene_Exception 
	 */
	public function delete($id) {
		$sql = array();
		$sql['query']  = "UPDATE `#__TABLE__#` SET `DELETED` = 1 WHERE ID = ? ";
		$sql['bind'][] = array('val' => $id, 'type' => 'i');
		// cancellazione logica del documento
		$this->input_mysqli($sql);
		
		if ($this->autoCommit) $this->commit();
	}
	
	/**
	 * Ritorna il numero di documenti inseriti compresi quelli marcati come cancellati
	 * @return integer
	 */
	public function count() {
		$sql = "SELECT count(1) as doc FROM #__TABLE__# ";
		$res = $this->output_mysqli($sql, Array('num_rows' => true, 'fetch' => true));
		
		return $res['rows'][0]['doc'];
	}
	
	/**
	 * Ritorna il numero di documenti realmente presenti senza quelli marcati come cancellati
	 * @return integer
	 */
	public function numDocs() {
		$sql = "SELECT count(1) as doc FROM `#__TABLE__#` WHERE `DELETED` = 0 ";
		$res = $this->output_mysqli($sql, Array('num_rows' => true, 'fetch' => true));
		
		return $res['rows'][0]['doc'];
	}
	
	/**
	 * Ritorna un array dei campi presenti nell'indice
	 * 
	 * @param boolean $indexed se true torna solo quelli indicizzati
	 * @return array
	 */
	public function getFieldNames($indexed = false) {
		$fields = array();
		
		$sql = "DESCRIBE `#__TABLE__#` ";
		$res = $this->output_mysqli($sql, Array('num_rows' => true, 'fetch' => true));
		
		foreach ($res['rows'] as $field) {
			$lowerField = strtolower($field['Field']);
			switch ($lowerField) {
				// Campi di sistema che non sono presentabili nella lista dei FIELD
				case "id":
				case "deleted":
					break;
				default:
					// Se $indexed = TRUE ritorna solo quelli indicizzati
					// ovvero che sono chiave (PRI / MUL)
					if ($indexed) {
						if (!empty($field['Key'])) {
							$fields[$field['Field']] = $lowerField;	
						}	
					} else {
						$fields[$field['Field']] = $lowerField;
					}
			}
		}
		
		return $fields;
	}
	
	
	/**
	 * Ritorna un array contenente tutti gli oggetti Zend_Search_Lucene_Document
	 * presenti nell'indice, senza i documenti marcati come cancellati. 
	 * Se settato il parametro $deleted = true allora ritorna anche 
	 * i documenti cancellati.
	 * 
	 * Ritorna NULL se non sono presenti documenti 
	 * 
	 * @param boolean $deleted [optional]
	 * @param integer $offset [optional] 
	 * @param integer $maxrow [optional] 
	 * @return mixed 
	 */
	public function getAllDocument($deleted = false, $offset = null, $maxrow = null) {
		$sql = "SELECT id, deleted FROM `#__TABLE__#` %s %s";
		
		$limit = "";
		$where = "";
		
		if ($offset !== null) {
			$limit = "LIMIT $offset ";
			// definizione del numero massimo di elementi
			if ($maxrow !== null) {
				$limit .= ",".$maxrow;
			}	
		}
		
		if (!$deleted) {
			$where = " WHERE deleted != 1 ";
			// where deleted = %d
		}
		
		// costruzione della SQL 
		$sql = sprintf($sql, $where, $limit);

		// recupero dei dati
		$res = $this->output_mysqli($sql, Array('num_rows' => true, 'fetch' => true));
		
		$document = null;
				
		if ($res["num_rows"] != 0) {
			foreach ($res['rows'] as $key => $row) {
				$id = $row['id'];
				$document[$id] = $this->getDocument($id);
			}
		}

		return $document;
	}
		
	/**
	 * Ritorna l'oggetto documento
	 * 
	 * Ritorna un eccezione IFileException se $id non e'
	 * presente nel range degli id dell'indice 
	 * 
	 * @param integer $id
	 * @return Zend_Searc_Lucene_Document
	 * @throws IFileException
	 */
	public function getDocument($id) {
		
		$sql = "SELECT * FROM `#__TABLE__#` WHERE `ID` = {$id} ";
		$res = $this->output_mysqli($sql, Array('num_rows' => true, 'fetch' => true));
		
		// invoca un eccezione se l'id non e' presente nel range degli ID dell'indice
		if (empty($res['num_rows'])) {
			throw new IFileException("Document out of range");
		} 
		
		// associazione del singolo record set		
		$rs =& $res['rows'][0];
		
		// creazuione dell'oggetto Zend_Search_Lucene_Document 
		$doc = new Zend_Search_Lucene_Document();
		
		foreach ($rs as $field => $term) {
			if (!$term == null) {
				switch (strtolower($field)) {
					case 'id':
					case 'deleted':
					break;
					default:
						$doc->addField(Zend_Search_Lucene_Field::Text($field, $term));					
				}
			}
		}
				 
		return $doc;
	}
	
	/**
	 * Committa l'indice
	 * @return void
	 */
	public function commit() {
		$this->getIndexResource()->commit();
	}
	
	/**
	 * Verifica se ci sono documenti calcellati
	 * @return boolean
	 */
	public function hasDeletions() {
		$sql = "SELECT count(1) as doc FROM `#__TABLE__#` WHERE `DELETED` = 1 ";
		$res = $this->output_mysqli($sql, Array('num_rows' => true, 'fetch' => true));
		if (empty($res['rows'][0]['doc'])) {
			return false;
		} 		
		return true;
	}
	
	/**
	 * Verifica se esiste il termine all'interno dell'indice
	 * 
	 * Se il filed non e' settato e non e' stato definito
	 * un field di default allora ricerca su tutti i campi
	 * 
	 * @param string $term
	 * @param string $field [0prional]
	 * @return boolean
	 * 
	 */
	public function hasTerm($term, $field = null) {
		// flag
		$hasTerm = false;
		// campi indicizzati del DB
		$fieldNames = $this->getFieldNames(true);
		// definizione del FIELD
		$field = ($field == null) ? $this->getDefaultSearchField() : $field; 
		// istanzia un oggetto per la ricerca		
		$ifileQueryRegistry = new IFileQueryRegistry();
		// verifica la ricerca solo se il field e' NULL
		// oppure e' presente nella lista dei campi indicizzati
		if ($field == null || in_array($field, $fieldNames)) {
			// se non e' settato nessun field allora ricerca  
			// in tutti i campi indicizzati
			if ($field == null) {
				// definisco che la ricerca e' per il solo termine nell'indice
				$this->hasTerm = true;
				// setta per tutti i campi lo stesso termine
				foreach ($fieldNames as $value) {
					$ifileQueryRegistry->setQuery($term, $value);
				}
			} else {
				// setta il termine di ricerca per il field definito
				$ifileQueryRegistry->setQuery($term, $field, IFileQuery::MATCH_REQUIRED);	
			}
						
			// chiamata al metodo di ricerca Multi-temine
			$result = $this->query($ifileQueryRegistry);
			// se il risultato esiste
			if (!empty($result)) {
				$hasTerm = true;
			}
		}
		
		// azzero la varibile per la ricerca del solo termine
		$this->hasTerm = false;
		
		return $hasTerm;
	}
	
	/**
	 * Ritorna un array di oggetti "Zend_Search_Lucene_Index_Term", termini, presenti nell'indice
	 * @TODO implemented
	 * da verificare se MySql permette il ritorno dei termini indicizzati per campo
	 * @return array
	 */
	public function terms() {
		return array();
	}
	
	/**
	 * Ritorna un array di oggetti "Zend_Search_Lucene_Index_Term", termini, presenti in un field (campo)
	 * @TODO implemented
	 * da verificare se MySql permette il ritorno dei termini indicizzati per campo 
	 * @param string $field 
	 * @return 
	 */
	public function getTermsForField($field) {
		return array();
	}
	
	
	/**
	 * Verifica se un documento e' stato marcato come cancellato
	 * Ritorna un eccezione IFileException se $id non e'
	 * presente nel range degli id dell'indice 
	 * @return boolean
	 * @throws IFileException 
	 */
	public function isDeleted($id) {
		$sql = "SELECT deleted FROM `#__TABLE__#` WHERE `ID` = {$id} ";
		$res = $this->output_mysqli($sql, Array('num_rows' => true, 'fetch' => true));
		
		// invoca un eccezione se l'id non e' presente nel range degli ID dell'indice
		if (empty($res['num_rows'])) {
			throw new IFileException("Document out of range");
		} 		
		
		if ($res['rows'][0]['deleted'] == 0) {
			return false;
		}
		return true;
	}
	
	/**
	 * Ripristina tutti i documenti marcati come cancellati
	 * Implementato in Zend_Search_Lucene dalla versione (x.x.x)
	 * @return void
	 */
	public function undeletedAll() {
		$sql = array();
		$sql['query']  = "UPDATE `#__TABLE__#` SET `DELETED` = 0 WHERE `DELETED` = 1 ";
		// ripristino della cancellazione logica del documento
		$this->input_mysqli($sql);
		
		if ($this->autoCommit) $this->commit();
	}	
	
	
	/** 
	 * Cancella l'indice e ritorna il numero di documenti cancellati
	 * 
	 * Se viene passato TRUE cancella solo tutti i documenti dall'indice
	 * e ritorna il numero di documenti cancellati altrimenti elimina completamente l'indice
	 * 
	 * @param bool $doc [optional]
	 * @return integer
	 */
	public function deleteAll($doc = false) {
		$numDocs = 0;
		if ($doc) {
			$numDocs = $this->__deleteAllDoc();	
		} else {
			$numDocs = $this->__deleteIndex();
		}
		
		return $numDocs;
	}
	
	/**
	 * Cancella l'intera Tabella.
	 *
	 * Ritorna il numero di documenti cancellati.
	 * 
	 * @return integer
	 */
	private function __deleteIndex() {
		$numDocs = $this->count();
		$sqlDrop = array();
		// elimino la tabella
		$sqlDrop['query'] = "DROP TABLE IF EXISTS `#__TABLE__#`";
		$this->input_mysqli($sqlDrop);
		// Ricostruisce la tabella vuota 
		$this->__createTable();
		return $numDocs; 
	}
	
	/**
	 * Cancella tutti i documenti dalla tabella.
	 * Ritorna il numero di documenti cancellati.
	 * 
	 * @return integer
	 */
	private function __deleteAllDoc() {
		$numDocs = $this->numDocs();
		if ($numDocs > 0) {
			$sqlDelete = array();
			// Setta a 1 tutti i record
			$sqlDelete['query'] = "UPDATE `#__TABLE__#` SET `DELETED` = 1 ";
			$this->input_mysqli($sqlDelete);
			// committa
			$this->commit();	
		}
		
		return $numDocs;
	}
	
	/**
	 * Gestisce le query di OUTPUT con mysqli.
	 * 
	 * Ritorna il numero delle righe ottenute e un array contenente i dati della query
	 * 
	 * @param string $query
	 * @param array $param parametri di passaggio
	 * @return array	 
	 * @throws IFileException
	 */
	private function output_mysqli($query, $param = Array()) {
		
		$res = array();
		$query = $this->replaceNameTable($query);
		
		if ($result = @$this->getIndexResource()->query($query)) {
		  if (!empty($param)) {
		  	foreach ($param as $key => $val) {
		  		switch ($key) {
		  			case 'num_rows':
		  				$res['num_rows'] = $result->num_rows;		
		  			break;
		  			case 'fetch':
		  				// fetch array  associative(MYSQLI_ASSOC) | numeric(MYSQLI_NUM)
		  				if (!isset($param['type_fetch'])) {
		  					$param['type_fetch'] = MYSQLI_ASSOC;
		  				}	
		  				
	  					while ($row = $result->fetch_array($param['type_fetch'])) {
								$res['rows'][]= $row; 
							}	  				
		  			break;
		  		}
		  	}
		  }				   	
		  // chiude il result set
		  $result->close();
		} else {
			throw new IFileException("Error in query process: ".$this->getIndexResource()->error);
		}	
		
		return $res;	
	}
	
	/**
	 * Gestisce le query di INPUT con mysqli.
	 * Ritorna il numero delle righe cambiate / inserite e in caso di auto_increment l'id dell'insert
	 * 
	 * @param array $param parametri di passaggio
	 * @return array	
	 * @throws IFileException 
	 */
	private function input_mysqli($query) {
		
		$result = array();		
		$query['query'] = $this->replaceNameTable($query['query']);
		
		// preparo lo statment
		if (!$stmt = $this->getIndexResource()->prepare($query['query'])) {
			throw new IFileException("Error in the preparation of the bind: ".$this->getIndexResource()->error);
		}
		
		if (isset($query['bind'])) {
			// da effettuare i controlli se abbbiamo o no 
			// un processo di INPUT (INSERT - UPDATE - DELETE - DROP)
			$strType = array();
			$strBind = array();
			
			// costruzione delle stringhe di BIND ciclando l'array bind
			foreach ($query['bind'] as $k => &$val) {
				$strType[] = &$val['type'];
				$strBind[] = &$val['val'];
			}
			
			// costruisco i parametri per la bind
			$arrParams = array_merge(array(implode('',$strType)),$strBind);
			// richiamo la bind 
			$bind = call_user_func_array (array($stmt,'bind_param'),$arrParams);  
			
			if (!$bind) {
				throw new IFileException("Error in the bind: ".$this->getIndexResource()->error);
			}	
		}
				
		// eseguo la query
		if (!$stmt->execute()) {
			throw new IFileException("Error in the execute process: ".$this->getIndexResource()->error);
		}
		
		// valori di ritorno
		if (isset($query['id'])) {
			$result['id'] = $stmt->insert_id;
		}
		// recupero il numero di rigne cambiate
		$result['num_rows'] = $stmt->affected_rows;
		
		/* close statement */
	  $stmt->close();
	  
	  return $result;
	}	
	
	/**
	 * Cambia il nome della tabelle nella query
	 * @param string $query
	 * @return string
	 */
	private function replaceNameTable($query) {
		return str_replace("#__TABLE__#", $this->ifileTable, $query);
	}
}
