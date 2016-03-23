<?php
namespace Isappit\Ifile\Searchengine;

use Isappit\Ifile\Config\IFileConfig;
use Isappit\Ifile\Exception\IFileException;
use Isappit\Ifile\Query\IFileQueryRegistry;
use Isappit\Ifile\Servercheck\LuceneServerCheck;
use ZendSearch\Lucene\Lucene as Zend_Search_Lucene;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer as Zend_Search_Lucene_Analysis_Analyzer;

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
 * Wrapper delle librerie ZendSearch\\Lucene
 * 
 * Permette di indicizzare file e ricercarli mediante Lucene
 *
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileIndexingLucene extends IFileIndexingAbstract {
	
	/**
	 * Istanza di IFileIndexingLucene
	 * 
	 * @var IFileIndexingLucene
	 */
	private static $_instance;
	
	/**
	 * Istanza di ZendSearch\Lucene\Lucene
	 * 
	 * @var Lucene
	 */
	private $lucene = null; 
	
	/**
	 * Costruttore
	 * 
	 * @param string $indexDir Path to the directory. 
	 * @return void 
	 */
	public function __construct($indexDir) {
		$this->__createIndex($indexDir);		
	}
	
	/**
	 * Crea o apre un indice.
	 * 
	 * @param string $indexDir Path to the directory. 
	 * @return void 
	 * @throws Zend_Search_Lucene_Exception, IFileException
	 */
	private function __createIndex($indexDir) {
		// verifica che esista il framework Zend
		$serverCheck = LuceneServerCheck::getInstance();
		$serverCheck->serverCheck();
		$reportServerCheck = $serverCheck->getReportCheck();
		$reportCheck = $reportServerCheck['Zend Framework']['Search Lucene'];
		
		if (!$reportCheck->getCheck()) {
			throw new IFileException("Zend Framework is not installed");
		}
		
		// salva l'handler della risorsa di indicizzazione
		$this->setIndexResource($indexDir);
		// verifica se esiste la directory dell'indice
		$create = !is_dir($indexDir);		
		
		// @TODO
		// gestire il problema sulla creazione/apertura dell'indice 
		// se esiste già la directory ma non ci sono file all'interno 
		// dovrebbe essere cancellata per permettere al sistema ZEND 
		// di non generare l'errore sulla cartella gia' esistente
		
		// se esiste allora si dovrebbe invocare la ::open()
		// altrimenti la ::create() 
		
		$this->lucene = Zend_Search_Lucene::create($indexDir);				
		
		
		// @TODO: sembra che la nuova versione di Zend Search non implementi in Lucene 
		// 		  il metodo addREference();
		
		// per ovviare ai problemi sulle ricerche (multiple o  ricorsive) viene aggiunta 
		// una reference 
		// infatti la prima ricerca (chiamata alla find()) viene eliminata l'unica reference 
		// e pertanto Zend chiude l'indice dato che non si hanno piu' reference
		// non ho capito se e' un BUG di Zend_Search_Lucene oppure non e'stata
		// documentato bene il funzionamento delle reference dato che sembra 
		// non essere scritto da nessuna parte il funzionamento
		// Sembra essere stata eliminata nella versione di Lucene sotto git
		// $this->lucene->addReference();		
		
		// inizializza Lucene con la configurazione definita nel file IFileConfig.xml
		$this->__initializeLucene();
	}
		
	/**
     * Rimuove il riferimento a lucene creato nel costruttore
     * 
     * @return void
     */
    public function __destruct()
    {
    	// elimina la reference alla distruzione dell'oggetto
		// solo se l'istanza Zend_Search_Lucene e' stata creta
		//if ($this->lucene != null) {
		   	// $this->lucene->removeReference();
		//}
	}
	
	/**
	 * Inizializza Lucene con i parametri di configurazione definiti nel file IFileConfig.xml 
	 *
	 * @return void 
	 */
	private function __initializeLucene() {
		// setta il tipo di analyzer, se non valorizzato nella 
		// configurazione prende Utf8_CaseInsensitive
		$this->__setDefaultAnalyzer();		
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
	 * Indicizza il documento 
	 * 
	 * @param Zend_Search_Lucene_Document $doc
	 * @return void
	 * @throws Zend_Search_Lucene_Exception	 
	 */
	protected function __addDocument(Zend_Search_Lucene_Document $doc) {
		// Recupera l'istanza di configurazione 		
		$IfileConfig = IFileConfig::getInstance();
		
		// recupera eventuali Fields pesonalizzati
		$fields = $this->getCustomField();
		// aggiunge i fields al documento
	    if(!empty($fields)) {
	    	foreach($fields as $field) {
	    		$this->__addCustomFieldToDocument($doc, $field);
	    	}
	    }
		
		// A causa di un problema della libreria di Zend Lucene
		// che non riesce a tokenizzare i contenuti se l'encoding 
		// dei caratteri non sono corretti.
		// verifica se riesce a tokenizzare correttamente
		$analyzer = Zend_Search_Lucene_Analysis_Analyzer::getDefault();
		$tokens = $analyzer->tokenize($doc->getFieldValue('body'), $IfileConfig->getConfig('encoding'));
		if (empty($tokens)) {
			throw new IFileException("Text of body not indexing. Check the type of encoding");
		}
		
		// aggunge il documento all'indice
		$this->lucene->addDocument($doc);

		// committa se l'auto commit e' settato
		if ($this->autoCommit) $this->commit();
	}
	
	/**
	 * Setta l'analyzer ed eventuali filtri per l'indicizzazione e la ricerca 
	 * 
	 * Il metodo verifica l'esistenza nella configurazione dei filtri
	 * stop-words e short-words da aggiungere al processo di analyzer
	 * 
	 * @return void
	 * @throws ReflectionException, Zend_Search_Lucene_Exception
	 */
	private function __setDefaultAnalyzer() {
		// Recupera l'istanza di configurazione		
		$IfileConfig = IFileConfig::getInstance();
		// custom analyzer
		$customAnalyzer = $IfileConfig->getConfig('custom-analyzer');
		// creazione del nome della classe 
		if ($customAnalyzer == null) {
			// creazione del class name
			$className = 'ZendSearch\\Lucene\\Analysis\\Analyzer\\Common\\'.$IfileConfig->getConfig('analyzer');
			// Reflection		
			$reflection = new \ReflectionClass($className);
			// creazione dell'oggetto
			$analyzer = $reflection->newInstance();		
		} else {			
			// Reflection		
			$reflection = new ReflectionClass($customAnalyzer);
			// creazione dell'oggetto
			$analyzer = $reflection->newInstance();
		}
		
		// Recupero un eventuale file di stop-words
		$stopWords = $IfileConfig->getConfig('stop-words');
		// se esiste il file delle stop-words lo aggiungo come filtro
		if ($stopWords != null) {
			/** Zend_Search_Lucene_Analysis_TokenFilter_StopWords */
			require_once ('Zend/Search/Lucene/Analysis/TokenFilter/StopWords.php');
			$stopWordsFilter = new Zend_Search_Lucene_Analysis_TokenFilter_StopWords(); 
			$stopWordsFilter->loadFromFile($stopWords);
			// aggiunge il filtro sulle stop-words			
			$analyzer->addFilter($stopWordsFilter);
		}
		// Recupero il filtro per le short-words
		$shortWords = $IfileConfig->getConfig('short-words');
		if ($shortWords != null) {
			/** Zend_Search_Lucene_Analysis_TokenFilter_ShortWords */
			require_once ('Zend/Search/Lucene/Analysis/TokenFilter/ShortWords.php');
			$shortWordsFilter = new Zend_Search_Lucene_Analysis_TokenFilter_ShortWords($shortWords);
			// aggiunge il filtro sulle short-words			
			$analyzer->addFilter($shortWordsFilter);
		}
		
		// Recupero i filtri personalizzati
		$addFilters = $IfileConfig->getConfig('filters');
		if ($addFilters != null) {
			foreach ($addFilters as $filter) {
				// Reflection	
				$reflection = new ReflectionClass($filter);
				// aggiunge il filtro sulle short-words			
				//$analyzer->addFilter($filter);
				$analyzer->addFilter($reflection->newInstance());
			}			
		}
		
		// setta l'analizzare
		Zend_Search_Lucene_Analysis_Analyzer::setDefault($analyzer);
	}
	
	/**
	 * Aggiunge il field personalizzato all'oggetto Zend_Search_Lucene_Document
	 * 
	 * @param Zend_Search_Lucene_Document $doc
	 * @param string                      $field
	 * @return void
	 */
	private function __addCustomFieldToDocument(Zend_Search_Lucene_Document $doc, $field) {
		// Recupera l'istanza di configurazione		
		$IfileConfig = IFileConfig::getInstance();
		
		switch ($field->type) {
			case self::FIELD_TYPE_KEYWORD:
				$doc->addField(Zend_Search_Lucene_Field::Keyword($field->field, $field->term), $IfileConfig->getConfig('encoding'));
				break;
			case self::FIELD_TYPE_UNINDEXED:
				$doc->addField(Zend_Search_Lucene_Field::UnIndexed($field->field, $field->term), $IfileConfig->getConfig('encoding'));
				break;
			case self::FIELD_TYPE_BINARY:
				$doc->addField(Zend_Search_Lucene_Field::Binary($field->field, $field->term), $IfileConfig->getConfig('encoding'));
				break;
			case self::FIELD_TYPE_TEXT:
				$doc->addField(Zend_Search_Lucene_Field::Text($field->field, $field->term), $IfileConfig->getConfig('encoding'));
				break;
			case self::FIELD_TYPE_UNSTORED:
				$doc->addField(Zend_Search_Lucene_Field::UnStored($field->field, $field->term), $IfileConfig->getConfig('encoding'));
				break;
			default:
				throw new IFileException('Type Field not present');
		}
	} 
	
	/**
	 * Verifica se il file e' gia' stato indicizzato
	 * 
	 * @param string $key MD5
	 * @return void
	 * @throws IFileException
	 */
	protected function __checkIndexingFileFromKey($key) {
		// chiamata per verificare se il file e' gia' stato indicizzato
		// @TODO successivamente utilizzare il metodo query
		$hits = $this->lucene->find('key:'.$key);
		if(is_array($hits) && count($hits) == 1){ 
			if(!$this->isDeleted($hits[0]->id)) {
				throw new IFileException("File already in the index");
			}
		}
	}
	
	/**
	 * Esegue la query di ricerca per i termini
	 * 
	 * Ritorna un array di oggetti Zend_Search_Lucene_Search_QueryHit 
	 * o un array vuoto in caso la query non presenta match.
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
			// chiamate alle API di Lucene	
			$zendQuery = new Zend_Search_Lucene_Search_Query_MultiTerm();
			// Term query
			foreach ($listQuery as $term) {				
				// trasforma il termine in token.
				// necessario sopratutto in caso di TokenFilter 
				$tokens = Zend_Search_Lucene_Analysis_Analyzer::getDefault()->tokenize($term->getTerm(), $term->getEncoding());
				// se sono presenti piu' termini allora si sta ricercando una frase esatta
				if (count($tokens) >= 1) {
					foreach ($tokens as $token) {							
						// setta il termine per la ricerca
						$zendTerm = new Zend_Search_Lucene_Index_Term($token->getTermText(), $term->getField());
						$zendQuery->addTerm($zendTerm, $term->getMatch());	
					}		
				}								
			}
			// inserisce in testa dell'array di passaggio alla find	
			array_unshift($this->registrySort, $zendQuery);
			
			// esegue la query
			$hits = call_user_func_array(array($this->lucene, "find"), $this->registrySort);
		}
			
		return $hits;
	}
	
	/**
	 * Esegue la query di ricerca per frasi
	 * 
	 * Ritorna un array di oggetti Zend_Search_Lucene_Search_QueryHit
	 * o un array vuoto in caso la query non presenta match.
	 * I campi (fields) devono essere gli stessi per tutti i termini 
	 * altrimenti viene generata una eccezione di tipo Zend_Search_Lucene_Exception  
	 * 
	 * @param IFileQueryRegistry $query
	 * @return array di Zend_Search_Lucene_Search_QueryHit 
	 * @throws Zend_Search_Lucene_Exception
	 * 
	 * @TODO
	 * si potrebbe migliorare gestendo anche la posizione
	 * ovvero se arriva un solo elemento si tokenizza e si 
	 * lavora sui termini così come e' adesso.
	 * In caso arrivano piu' elementi allora si aspetta che 
	 * questi siano formati da un solo termine e quindi si puo'
	 * gestire anche la posizione del termine
	 */
	protected function __queryPhrase(IFileQueryRegistry $query) {
		// array dei risultati
		$hits = array();
		// numero di termini
		$countQuery = $query->count();
		
		// verifica che sia stato settato un solo elemento da ricercare
		if ($countQuery != 1) {
			throw new IFileException("The Phrase requires only one element of research");
		}
		
		$listQuery = $query->getQuery();
		// chiamate alle API di Lucene	
		$zendQuery = new Zend_Search_Lucene_Search_Query_Phrase();
		// Term query
		foreach ($listQuery as $term) {	
			// trasforma il termine in token 
			// necessario sopratutto in caso di TokenFilter
			$tokens = Zend_Search_Lucene_Analysis_Analyzer::getDefault()->tokenize($term->getTerm(), $term->getEncoding());
			// se sono presenti piu' termini allora si sta ricercando una frase esatta
			if (count($tokens) >= 1) {
				foreach ($tokens as $token) {							
					$zendTerm = new Zend_Search_Lucene_Index_Term($token->getTermText(), $term->getField());
					$zendQuery->addTerm($zendTerm);	
				}		
			}
			// inserisce in testa dell'array di passaggio alla find		
			array_unshift($this->registrySort, $zendQuery);
			// esegue la query
			$hits = call_user_func_array(array($this->lucene, "find"), $this->registrySort);
		}
		
		return $hits;		
	}
	
	
	/**
	 * Esegue la fuzzy query
	 * 
	 * Ritorna un array di oggetti Zend_Search_Lucene_Search_QueryHit
	 * o un array vuoto in caso la query non presenta match.
	 * Puo' essere ricercato solo un unico termine nella ricerca fuzzy 
	 * altrimenti viene generata una eccezione di tipo IFileException  
	 * 
	 * @param IFileQueryRegistry $query
	 * @return array di Zend_Search_Lucene_Search_QueryHit
	 * @throws Zend_Search_Lucene_Exception, IFileException
	 */
	protected function __queryFuzzy(IFileQueryRegistry $query) {
		// array dei risultati
		$hits = array();
		// numero di termini settati
		$countQuery = $query->count();
		// trasforma il termine in token 
		// necessario sopratutto in caso di TokenFilter
		$tokens = Zend_Search_Lucene_Analysis_Analyzer::getDefault()->tokenize($query->getTerm(0)->getTerm(), $query->getTerm(0)->getEncoding());
		// verifica che sia stato settato un solo termine da ricercare
		if ($countQuery != 1 || count($tokens) > 1) {
			throw new IFileException("The Fuzzy requires a single search term");
		}
		// recupero del termine da ricercare	
		$term = $query->getTerm(0); 
		// chiamate alle API di Lucene
		// Term query
		$zendTerm = new Zend_Search_Lucene_Index_Term($term->getTerm(), $term->getField());
		$zendQuery = new Zend_Search_Lucene_Search_Query_Fuzzy($zendTerm, $term->getPosition());
		
		// inserisce in testa dell'array di passaggio alla find		
		array_unshift($this->registrySort, $zendQuery);
		// esegue la query
		$hits = call_user_func_array(array($this->lucene, "find"), $this->registrySort);
		
		return $hits;		
	}
	
	/**
	 * Esegue una boolean query
	 * 
	 * Ritorna un array di oggetti Zend_Search_Lucene_Search_QueryHit
	 * 0 un array vuoto in caso la query non presenta match.
	 * L'argomento $query di tipo IFileQueryRegistry deve contenere a sua volta oggetti IFileQueryRegistry
	 * 
	 * @param IFileQueryRegistry $query
	 * @return array di Zend_Search_Lucene_Search_QueryHit
	 * @throws Zend_Search_Lucene_Exception, IFileException
	 * 
	 * @TODO
	 * Andrebbe implementata anche la gestione per le Fuzzy, Wildcard e Range
	 */
	protected function __queryBoolean(IFileQueryRegistry $query) {
				
		// array dei risultati
		$hits = array();
		// numero di termini
		$countQuery = $query->count();
		
		if (!empty($countQuery)) {
			$listQuery = $query->getQuery();
			// chiamate alle API di Lucene	
			$zendQuery = new Zend_Search_Lucene_Search_Query_Boolean();
			
			// Registry
			foreach ($listQuery as $registry) {
				// Terms
				$terms = $registry->getTerm();
				// array temporaneo degli zend Search
				$zendSearchRegistry = array();
				// eccezione se non e' una istanza della IFileQueryRegistry
				if (!($terms instanceof IFileQueryRegistry)) {
					throw new IFileException("The only accepts Boolean search terms such IFileQueryRegistry");
				} 
				
				switch ($registry->getSearchType()) {
						case IFileQuery::QUERY_RANGE:
							$countQueryRange = $terms->count();
							// verifica che siano stati settati solo i due termini del range
							if ($countQueryRange != 2) {
								throw new IFileException("The Search Range requires two search terms");
							}
							// chiamate alle API di Lucene				
							$term1 = $terms->getTerm(0); 
							$term2 = $terms->getTerm(1); 
							// controllo che il field sia uguale per entrambi 
							// i termini di ricerca settati
							if (strcmp($term1->getField(), $term2->getField()) !== 0) {
								throw new IFileException("The Range requires the same field (Field) research");
							}
							
							// Term query
							$zendTerm1 = new Zend_Search_Lucene_Index_Term($term1->getTerm(), $term1->getField());
							$zendTerm2 = new Zend_Search_Lucene_Index_Term($term2->getTerm(), $term2->getField());
							// costruiisce la query
							$zendSearchRange = new Zend_Search_Lucene_Search_Query_Range($zendTerm1, $zendTerm2, $term2->getMatch());
							
							$zendSearchRegistry[IFileQuery::QUERY_RANGE][] = $zendSearchRange;
							break;
						case IFileQuery::QUERY_PHRASE:
						case IFileQuery::QUERY_MULTITERM:
						default:
							// recupero i dati per la creazione della query
							$queryAPI = $terms->getQuery();
							// per i termini va definito un unico oggetto MultiTermine 
							$zendSearchMultiTerm = new Zend_Search_Lucene_Search_Query_MultiTerm();
							// lista di termini
							foreach ($queryAPI as $term) {
								// trasforma il termine in token 
								// necessario sopratutto in caso di TokenFilter
								$tokens = Zend_Search_Lucene_Analysis_Analyzer::getDefault()->tokenize($term->getTerm(), $term->getEncoding());
								// se sono presenti piu' termini allora si sta ricercando una frase esatta
								if (count($tokens) > 1) {
									// per le frasi andra' per ogni frase definito un nuovo oggetto
									$zendSearchPhrase = new Zend_Search_Lucene_Search_Query_Phrase();	
									// ciclo per le parole (token) e creo l'oggetto di ricerca
									foreach ($tokens as $token) {							
										$zendTerm = new Zend_Search_Lucene_Index_Term($token->getTermText(), $term->getField());
										$zendSearchPhrase->addTerm($zendTerm);	
									}
									$zendSearchRegistry[IFileQuery::QUERY_PHRASE][] = $zendSearchPhrase; 
								} else {
									foreach ($tokens as $token) {
										// aggiungo il termine alla multi-termine del gruppo													
										$zendTerm = new Zend_Search_Lucene_Index_Term($token->getTermText(), $term->getField());
										$zendSearchMultiTerm->addTerm($zendTerm, $term->getMatch());
									}
									
									$zendSearchRegistry[IFileQuery::QUERY_MULTITERM] = $zendSearchMultiTerm; 
								}
							}
							break;
				}
			
				// ciclo il registro delle query	
				foreach ($zendSearchRegistry as $key => $search) {
					switch ($key) {						
						case IFileQuery::QUERY_MULTITERM:
							$zendQuery->addSubquery($search, $registry->getMatch());
							break;
						case IFileQuery::QUERY_PHRASE:
							foreach ($search as $phrase) {
								$zendQuery->addSubquery($phrase, $registry->getMatch());	
							}
							break;
						case IFileQuery::QUERY_RANGE:
							foreach ($search as $range) {
								$zendQuery->addSubquery($range, $registry->getMatch());	
							}
							break;
						default:
					}					
				}
			}
			
			// inserisce in testa dell'array di passaggio alla find		
			array_unshift($this->registrySort, $zendQuery);
			// esegue la query
			$hits = call_user_func_array(array($this->lucene, "find"), $this->registrySort);						
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
	 * @param IFileQueryRegistry $query
	 * @return array di Zend_Search_Lucene_Search_QueryHit
	 * @throws Zend_Search_Lucene_Exception, IFileException
	 * 
	 * @TODO
	 * Andrebbe implementata anche la gestione per le Fuzzy, Wildcard e Range
	 */
	protected function __queryBoolean_orig(IFileQueryRegistry $query) {
		// array dei risultati
		$hits = array();
		// numero di termini
		$countQuery = $query->count();
		
		if (!empty($countQuery)) {
			$listQuery = $query->getQuery();
			// chiamate alle API di Lucene	
			$zendQuery = new Zend_Search_Lucene_Search_Query_Boolean();
			
			// Registry
			foreach ($listQuery as $registry) {
				// Terms
				$terms = $registry->getTerm();
				// array temporaneo degli zend Search
				$zendSearchRegistry = array();
				
				if (!($terms instanceof IFileQueryRegistry)) {
					throw new IFileException("The only accepts Boolean search terms such IFileQueryRegistry");
				} 
				
				// recupero i dati per la creazione della query
				$queryAPI = $terms->getQuery();
				// per i termini va definito un unico oggetto MultiTermine 
				$zendSearchMultiTerm = new Zend_Search_Lucene_Search_Query_MultiTerm();
				
				// lista di termini
				foreach ($queryAPI as $term) {
					// trasforma il termine in token 
					// necessario sopratutto in caso di TokenFilter
					$tokens = Zend_Search_Lucene_Analysis_Analyzer::getDefault()->tokenize($term->getTerm(), $term->getEncoding());
					// se sono presenti piu' termini allora si sta ricercando una frase esatta
					if (count($tokens) > 1) {
						// per le frasi andra' per ogni frase definito un nuovo oggetto
						$zendSearchPhrase = new Zend_Search_Lucene_Search_Query_Phrase();	
						// ciclo per le parole (token) e creo l'oggetto di ricerca
						foreach ($tokens as $token) {							
							$zendTerm = new Zend_Search_Lucene_Index_Term($token->getTermText(), $term->getField());
							$zendSearchPhrase->addTerm($zendTerm);	
						}
						$zendSearchRegistry['phrase'][] = $zendSearchPhrase; 
					} else {
						foreach ($tokens as $token) {
							// aggiungo il termine alla multi-termine del gruppo													
							$zendTerm = new Zend_Search_Lucene_Index_Term($token->getTermText(), $term->getField());
							$zendSearchMultiTerm->addTerm($zendTerm, $term->getMatch());
						}
						
						$zendSearchRegistry['multiterm'] = $zendSearchMultiTerm; 
					}
				}
				
				// ciclo il registro delle query	
				foreach ($zendSearchRegistry as $key => $search) {
					switch ($key) {
						case 'multiterm':
							$zendQuery->addSubquery($search, $registry->getMatch());
							break;
						case 'phrase':
							foreach ($search as $phrase) {
								$zendQuery->addSubquery($phrase, $registry->getMatch());	
							}
							break;
						default:
					}					
				}
			}
			
			// inserisce in testa dell'array di passaggio alla find		
			array_unshift($this->registrySort, $zendQuery);
			// esegue la query
			$hits = call_user_func_array(array($this->lucene, "find"), $this->registrySort);						
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
	 * @param IFileQueryRegistry $query
	 * @return array di Zend_Search_Lucene_Search_QueryHit
	 * @throws Zend_Search_Lucene_Exception, IFileException
	 */
	protected function __queryWildcard(IFileQueryRegistry $query) {
		// array dei risultati
		$hits = array();
		// numero di termini
		$countQuery = $query->count();
		
		// Se tokenizzo il termine potrebbe dare problemi con i caratteri Wildcard
		// ad esempio se si setta anti?ip* il token ritorna 2 elementi "anti" e "ip"
//		$tokens = Zend_Search_Lucene_Analysis_Analyzer::getDefault()->tokenize($query->getTerm(0)->getTerm(), $query->getTerm(0)->getEncoding());
//		
//		// Verifica che sia stato settato un solo termine da ricercare
//		// Utilizzo della Tokenizzazione
//		if ($countQuery != 1 || count($tokens) > 1) {
	
		// un problema di potrebbe verificare in presenza di caratteri speciali
		if ($countQuery != 1) {
			throw new IFileException("The Wildcard require a single search term");
		}
		
		// chiamate alle API di Lucene				
		$term = $query->getTerm(0); 
		
		// Term query
		$zendTerm = new Zend_Search_Lucene_Index_Term($term->getTerm(), $term->getField());
		$zendQuery = new Zend_Search_Lucene_Search_Query_Wildcard($zendTerm);
		// inserisce in testa dell'array di passaggio alla find		
		array_unshift($this->registrySort, $zendQuery);
		// esegue la query
		$hits = call_user_func_array(array($this->lucene, "find"), $this->registrySort);
		
		return $hits;		
	}
	
	/**
	 * Esegue la query per un range di dati
	 * 
	 * Ritorna un array di oggetti Zend_Search_Lucene_Search_QueryHit
	 * o un array vuoto in caso la query non presenta match.
	 * Puo' essere ricercato solo un range di termini per lo stesso field nella ricerca 
	 * (ovveto solo i termini di "From" e "To")  
	 * altrimenti viene generata una eccezione di tipo IFileException  
	 * 
	 * @param IFileQueryRegistry $query
	 * @return array di Zend_Search_Lucene_Search_QueryHit
	 * @throws Zend_Search_Lucene_Exception, IFileException
	 */
	protected function __queryRange(IFileQueryRegistry $query) {
		
		// array dei risultati
		$hits = array();
		// variabile per il controllo del field
		$field = null;
		// numero di termini
		$countQuery = $query->count();
				
		// verifica che siano stati settati solo i due termini del range
		if ($countQuery != 2) {
			throw new IFileException("The Range requires two search terms");
		}
		
		// chiamate alle API di Lucene				
		$term1 = $query->getTerm(0); 
		$term2 = $query->getTerm(1); 
		// controllo che il field sia uguale per entrambi 
		// i termini di ricerca settati
		if (strcmp($term1->getField(), $term2->getField()) !== 0) {
			throw new IFileException("The Range requires the same field (Field) research");
		}
		
		// Term query
		$zendTerm1 = new Zend_Search_Lucene_Index_Term($term1->getTerm(), $term1->getField());
		$zendTerm2 = new Zend_Search_Lucene_Index_Term($term2->getTerm(), $term2->getField());
		// costruiisce la query
		$zendQuery = new Zend_Search_Lucene_Search_Query_Range($zendTerm1, $zendTerm2, $term2->getMatch());
		
		// inserisce in testa dell'array di passaggio alla find		
		array_unshift($this->registrySort, $zendQuery);
		// esegue la query
		$hits = call_user_func_array(array($this->lucene, "find"), $this->registrySort);
		
		return $hits;		
	}
	
	/**
	 * Esegue una query parserizzando la stringa di ricerca
	 * 
	 * Ritorna un array di oggetti Zend_Search_Lucene_Search_QueryHit
	 * 0 un array vuoto in caso la query non presenta match.
	 * Il metodo e' più lento rispetto ai metodi di ricerca davuto al
	 * tempo di parserizzazione della stringa.
	 * 
	 * @param string $query
	 * @return array di Zend_Search_Lucene_Search_QueryHit
	 * @throws Zend_Search_Lucene_Exception, Zend_Search_Lucene_Search_QueryParserException
	 */
	protected function __queryParser($query) {
		$zendQuery = Zend_Search_Lucene_Search_QueryParser::parse($query);
		// inserisce in testa dell'array di passaggio alla find		
		array_unshift($this->registrySort, $zendQuery);
		// esegue la query
		$hits = call_user_func_array(array($this->lucene, "find"), $this->registrySort);
		
		return $hits;
	}
	
	/**
	 * Ottimizza l'indice
	 * @return void
	 */
	public function optimize() {
		$this->lucene->optimize();
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
		$this->lucene->delete($id);
		// committa se l'auto commit e' settato
		if ($this->autoCommit) $this->commit();
	}
	
	/**
	 * Setta il limite dei risultati da estrarre
	 * @param integer $limit
	 * @return void
	 */	
	public function setResultLimit($limit) {
		return $this->lucene->setResultSetLimit($limit);
	}
	
	/**
	 * Ritorna il limite dei risultati da estrarre
	 * @return integer
	 */	
	public function getResultLimit() {
		return $this->lucene->getResultSetLimit();
	}
	
	/**
	 * Setta il field di default su cui ricercare i termini
	 * @param string $field
	 * @return void
	 */	
	public function setDefaultSearchField($field) {
		$this->lucene->setDefaultSearchField($field);
	}
	
	/**
	 * Ritorna il field di default su cui ricercare i termini
	 * @return string
	 */	
	public function getDefaultSearchField() {
		return $this->lucene->getDefaultSearchField();
	}
	
	/**
	 * Ritorna il numero di documenti inseriti compresi quelli marcati come cancellati
	 * @return integer
	 */
	public function count() {
		return $this->lucene->count();
	}
	
	/**
	 * Ritorna il numero di documenti realmente presenti senza quelli marcati come cancellati
	 * @return integer
	 */
	public function numDocs() {
		return $this->lucene->numDocs();
	}
	
	/**
	 * Ritorna un array dei campi presenti nell'indice
	 * @param boolean $indexed se true torna solo quelli indicizzati
	 * @return array
	 */
	public function getFieldNames($indexed = false) {
		return $this->lucene->getFieldNames($indexed);
	}
	
	/**
	 * Ritorna l'oggetto documento
	 * Ritorna un eccezione Zend_Search_Lucene_Exception se $id non e'
	 * presente nel range degli id dell'indice 
	 * @param integer $id
	 * @return Zend_Searc_Lucene_Document
	 * @throws Zend_Search_Lucene_Exception 
	 */
	public function getDocument($id) {
		return $this->lucene->getDocument($id);
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
	 * @throws IFileException
	 */
	public function getAllDocument($deleted = false, $offset = null, $maxrow = null) {
		
		$document = null;
		$numDocs = $this->count();
		$countDocument = 0;
		$start = 0;
		
		// offset deve essere un intero		
		if ($offset !== null && !is_int($offset)) {
			throw new IFileException("Offset of the getAllDocument is an integer");
		} 
		
		// maxrow deve essere un intero
		if ($maxrow !== null && !is_int($maxrow)) {
			throw new IFileException("MaxRow of the getAllDocument is an integer");
		}
				
		// gestione dell'offset e della maxrow per il recupero dei documenti
		if ($offset === null) {
			// deve ritornare tutti i documenti a partire dal primo
			$start = 0;
			$maxrow = null;
		} elseif ($offset == 0 && $maxrow === null) {
			// non deve ritornare nemmeno un documento
			$start = $numDocs;
		} elseif ($offset == 0 && $maxrow !== null) {
			// parte dal primo elemento e ritorna il maxrow di documenti
			$start = 0;
		} elseif ($offset != 0 && $maxrow === null) {
			// parte dal primo elemento e recupera solo il numero di offset dei documenti
			$start = 0;
			$maxrow = $offset;
		} elseif ($offset != 0 && $maxrow !== null) {
			// parte dal documento con offset
			$start = $offset;
		}
		
		// cicla i documenti
		for ($id = $start; $id < $numDocs; $id++) {
			
			if ($maxrow !== null && $countDocument >= $maxrow ) {
				break;	
			}
			
			if ($deleted == false) {
				if (!$this->isDeleted($id)) {
					$document[$id] = $this->getDocument($id);
					$countDocument++;
				}
			} else {
				$document[$id] = $this->getDocument($id);
				$countDocument++;
			}
		}
		
		return $document;
	}
	
	
	/**
	 * Committa l'indice
	 * @return void
	 */
	public function commit() {
		$this->lucene->commit();
	}
	
	/**
	 * Verifica se ci sono documenti calcellati
	 * @return boolean
	 */
	public function hasDeletions() {
		return $this->lucene->hasDeletions();
	}
	
	/**
	 * Verifica se esiste il termine
	 * @param string $term
	 * @param string $field [0prional]
	 * @return boolean
	 */
	public function hasTerm($term, $field = null) {
		$term = new Zend_Search_Lucene_Index_Term($term, $field);
		return $this->lucene->hasTerm($term);
	}
	
	/**
	 * Ritorna un array di oggetti "Zend_Search_Lucene_Index_Term", termini, presenti nell'indice
	 * @return array di Zend_Search_Lucene_Index_Term
	 */
	public function terms() {
		if (empty($this->terms)) {
			$this->terms = $this->lucene->terms();
			// per ottimizzare il processo di ricerca per singolo fields
			// si costruisce anche un array già strutturato per fields
			// solo se Lucene ha ritornato degli elementi 
			if (!empty($this->terms)) {
				foreach ($this->terms as $term) {
					$this->termsForFields[$term->field][] = $term;
				}	
			}			
		}
		return $this->terms;
	}
	
	/**
	 * Ritorna un array di oggetti "Zend_Search_Lucene_Index_Term", termini, presenti in un field (campo)
	 * Se il field non e' presente nell'indice torna null 
	 * @param string $field 
	 * @return mixed
	 */
	public function getTermsForField($field) {		
		// non e' possibile passare field vuoti
		if (trim($field) == ''){
			throw new IFileException("Field not defined");
		}
		
		// se "termsForFields" e' vuoto richiama la term() per popolarlo
		if (empty($this->termsForFields)) {
			$this->terms();	
		} 
				
		return (isset($this->termsForFields[$field])) ? $this->termsForFields[$field] : null;
	}
	
	/**
	 * Verifica se un documento e' stato marcato come cancellato
	 * Ritorna un eccezione Zend_Search_Lucene_Exception se $id non e'
	 * presente nel range degli id dell'indice 
	 * @return boolean
	 * @throws Zend_Search_Lucene_Exception 
	 */
	public function isDeleted($id) {
		return $this->lucene->isDeleted($id);
	}
	
	/**
	 * Ripristina tutti i documenti marcati come cancellati
	 * Implementato in Zend_Search_Lucene dalla versione (x.x.x)
	 * @return void
	 */
	public function undeletedAll() {
		return $this->lucene->undeletedAll();
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
	 * Cancella una directory/file ricorsivamente
	 * 
	 * @param path $indexDir
	 * @return void 
	 * @throws IFileException
	 */
	private function __rmdirr($indexDir) {
		$objs = @glob($indexDir."/*");
		
		if (empty($objs) || !is_array($objs)) {
			throw new IFileException("Index directory not exists or empty");
		}
		
		foreach($objs as $obj) {
			
			if (@is_dir($obj)) {
				$this->rmdirr($obj);
			} else {
				if (@unlink($obj) === false) {
					throw new IFileException("Impossible delete $obj file. Delete this manually.");
				}	
			}
		}
		
		if (@rmdir($indexDir) === false) {
			throw new IFileException("Impossible delete $indexDir directory. Delete this manually.");
		}
	}	
	
	/**
	 * Cancella tutto l'indice compresa la cartella.
	 * Invoca una eccezione se non si può cancellare l'indice.
	 * Ritorna il numero di documenti cancellati.
	 * 
	 * @return integer
	 */
	private function __deleteIndex() {
		$numDocs = $this->count();
		// elimina tutti i riferimenti per permettere la chiusura
		// di tutti i processi di LUCENE 
		$this->lucene->removeReference();
		// elimino l'indice
		$indexDir = $this->getIndexResource();
		// cancella la cartella dell'indice  
		$this->__rmdirr($indexDir);		
		// Ricostruisce l'indice vuoto
		$this->__createIndex($indexDir);
		
		return $numDocs;	
	}

	/**
	 * Cancella tutti i documenti dall'indice
	 * Ritorna il numero di documenti cancellati.
	 * 
	 * @return integer
	 */
	private function __deleteAllDoc() {
		$numDocs = $this->count();
		$deleteDoc = 0;
		for ($id = 0; $id < $numDocs; $id++) {
			if (!$this->isDeleted($id)) {
				$this->delete($id);
				$deleteDoc++;
			}
		}
		$this->commit();
		return $deleteDoc;
	}	
}
?>